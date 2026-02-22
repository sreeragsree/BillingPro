<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Daily_cash_closing extends MY_Controller {
    public function __construct(){
        parent::__construct();
        $this->load_global();
        $this->load->model('Daily_cash_closing_model','dcc');
    }

    public function index()
    {
        $this->permission_check('daily_cash_closing_view');
        $data = $this->data;
        $data['page_title'] = 'Daily Cash Closing';
        $data['records'] = $this->dcc->get_all(get_current_store_id());
        $this->load->view('daily_cash_closing/index', $data);
    }

    public function add()
    {
        $this->permission_check('daily_cash_closing_add');
        $data = $this->data;
        $data['page_title'] = 'Add Daily Cash Closing';
        $store_id = get_current_store_id();
        // opening balance = last closing closing_cash
        $last = $this->dcc->get_last_closing($store_id);
        $data['opening_cash'] = $last ? $last->closing_cash : 0.00;
        // summary for today
        $today = date('Y-m-d');
        $data['summary'] = $this->dcc->calculate_summary($today, $store_id);
        $data['already_closed'] = $this->dcc->exists_for_date($today, $store_id);
        // if already closed, load existing record to populate inputs
        if ($data['already_closed']) {
            $rec = $this->dcc->get_for_date($today, $store_id);
            if ($rec) {
                $data['opening_cash'] = isset($rec->opening_cash) ? $rec->opening_cash : $data['opening_cash'];
                $data['closing_cash'] = isset($rec->closing_cash) ? $rec->closing_cash : 0.00;
                $data['note'] = isset($rec->note) ? $rec->note : '';
                $data['expected_cash'] = isset($rec->expected_cash) ? $rec->expected_cash : null;
                $data['difference'] = isset($rec->difference) ? $rec->difference : null;
                // expose full record to view for display of stored fields like other_sales
                $data['record'] = $rec;
            }
        }
        $this->load->view('daily_cash_closing/add', $data);
    }

    public function save()
    {
        $this->permission_check('daily_cash_closing_add');
        $this->form_validation->set_rules('closing_date','Closing Date','trim|required');
        $this->form_validation->set_rules('opening_cash','Opening Cash','trim|required');
        $this->form_validation->set_rules('closing_cash','Closing Cash','trim|required');

        if ($this->form_validation->run() == TRUE) {
            $post = $this->input->post();
            $store_id = get_current_store_id();
            // Normalize closing_date early to Y-m-d to prevent DB errors from dd-mm-yyyy input
            if (!empty($post['closing_date'])) {
                $ts = strtotime(str_replace('/', '-', $post['closing_date']));
                if ($ts !== false) {
                    $post['closing_date'] = date('Y-m-d', $ts);
                }
            }
            // Prevent multiple closing for same date
            if ($this->dcc->exists_for_date($post['closing_date'], $store_id)) {
                $this->session->set_flashdata('error', 'Closing already exists for this date');
                redirect('daily_cash_closing');
            }

            // If user cannot override, enforce opening_cash as last closing; otherwise sanitize provided value
            if (!function_exists('can_override_opening')) {
                // fallback: treat only admin as allowed to override
                $can_override = is_admin();
            } else {
                $can_override = can_override_opening();
            }
            if (!$can_override) {
                $last = $this->dcc->get_last_closing($store_id);
                $post['opening_cash'] = $last ? $last->closing_cash : 0.00;
            } else {
                // sanitize formatted numbers like '1,000.00'
                $post['opening_cash'] = isset($post['opening_cash']) ? str_replace([',',' '], '', $post['opening_cash']) : 0;
            }

            // sanitize other numeric inputs
            $post['closing_cash'] = isset($post['closing_cash']) ? str_replace([',',' '], '', $post['closing_cash']) : 0;
            $post['cash_out_to_home'] = isset($post['cash_out_to_home']) ? str_replace([',',' '], '', $post['cash_out_to_home']) : 0;
            $post['expected_cash'] = isset($post['expected_cash']) ? str_replace([',',' '], '', $post['expected_cash']) : 0;
            $post['difference'] = isset($post['difference']) ? str_replace([',',' '], '', $post['difference']) : 0;

            // calculate expected cash using summary but allow user override for other_sales
            $summary = $this->dcc->calculate_summary($post['closing_date'], $store_id);
            // allow post override for other_sales if provided (non-empty)
            if (isset($post['other_sales']) && $post['other_sales'] !== '') {
                $post_other_sales = str_replace([',',' '], '', $post['other_sales']);
                $summary['other_sales'] = floatval($post_other_sales);
            }
            // Expected Cash = Opening + Cash Sales - Expenses - Refunds + Cash In - Cash Out + Other Sales - Cash Out To Home
            $expected_calc = floatval($post['opening_cash']) + floatval($summary['cash_sales']) - floatval($summary['expenses']) - floatval($summary['refunds']) + floatval($summary['cash_in']) - floatval($summary['cash_out']) + floatval($summary['other_sales']) - floatval($post['cash_out_to_home']);

            $insert = array(
                'closing_date' => $post['closing_date'],
                'opening_cash' => number_format(floatval($post['opening_cash']),2,'.',''),
                'cash_sales'   => isset($summary['cash_sales']) ? number_format(floatval($summary['cash_sales']),2,'.','') : (isset($post['cash_sales']) ? number_format(floatval($post['cash_sales']),2,'.','') : '0.00'),
                'card_sales'   => isset($summary['card_sales']) ? number_format(floatval($summary['card_sales']),2,'.','') : (isset($post['card_sales']) ? number_format(floatval($post['card_sales']),2,'.','') : '0.00'),
                'upi_sales'    => isset($summary['upi_sales']) ? number_format(floatval($summary['upi_sales']),2,'.','') : (isset($post['upi_sales']) ? number_format(floatval($post['upi_sales']),2,'.','') : '0.00'),
                // prefer user-provided other_sales when present, otherwise use computed summary
                'other_sales'  => (isset($post['other_sales']) && $post['other_sales'] !== '') ? number_format(floatval(str_replace([',',' '], '', $post['other_sales'])),2,'.','') : (isset($summary['other_sales']) ? number_format(floatval($summary['other_sales']),2,'.','') : '0.00'),
                'cash_out_to_home' => number_format(floatval($post['cash_out_to_home']),2,'.',''),
                'expenses'     => isset($summary['expenses']) ? number_format(floatval($summary['expenses']),2,'.','') : (isset($post['expenses']) ? number_format(floatval($post['expenses']),2,'.','') : '0.00'),
                'closing_cash' => number_format(floatval($post['closing_cash']),2,'.',''),
                'expected_cash'=> number_format(floatval($post['expected_cash']) ? floatval($post['expected_cash']) : $expected_calc,2,'.',''),
                'difference'   => number_format(floatval($post['difference']) ? floatval($post['difference']) : (floatval($post['closing_cash']) - $expected_calc),2,'.',''),
                'note'         => isset($post['note']) ? $post['note'] : '',
                'store_id'     => $store_id,
                'created_by'   => $this->session->userdata('inv_userid'),
                'created_at'   => date('Y-m-d H:i:s')
            );

            // normalize closing_date
            $cd = null;
            if (!empty($post['closing_date'])) {
                $ts = strtotime($post['closing_date']);
                if ($ts !== false) {
                    $cd = date('Y-m-d', $ts);
                }
            }
            if (!$cd) {
                $this->session->set_flashdata('error', 'Invalid closing date');
                redirect('daily_cash_closing/add');
            }
            $insert['closing_date'] = $cd;

            // Use DB transaction to ensure integrity
            $this->db->trans_begin();
            $result = $this->dcc->create($insert);
            if (!$result) {
                $dberr = $this->db->error();
                log_message('error', 'Daily_cash_closing::save insert failed: ' . ($dberr['message'] ?? 'unknown') . ' last_query: ' . $this->db->last_query());
                // retry without optional columns if unknown column error
                if (isset($dberr['message']) && stripos($dberr['message'],'unknown column') !== false) {
                    unset($insert['expected_cash'], $insert['difference']);
                    $result = $this->dcc->create($insert);
                    if (!$result) {
                        $dberr2 = $this->db->error();
                        log_message('error','Daily_cash_closing::save retry failed: '.($dberr2['message'] ?? 'unknown'));
                    }
                }
            }

            if ($result && $this->db->trans_status() !== FALSE) {
                $this->db->trans_commit();
            } else {
                $this->db->trans_rollback();
            }
            if ($result) {
                $this->session->set_flashdata('success', 'Daily cash closing saved successfully');
                redirect('daily_cash_closing');
            } else {
                $dberr = $this->db->error();
                $msg = !empty($dberr['message']) ? 'DB error: '.$dberr['message'] : 'Failed to save record';
                $this->session->set_flashdata('error', $msg);
                redirect('daily_cash_closing/add');
            }

        } else {
            $this->session->set_flashdata('error','Please fill required fields');
            redirect('daily_cash_closing/add');
        }
    }

    /**
     * Generate PDF report for a closing
     */
    public function report($id = null)
    {
        $this->permission_check('daily_cash_closing_view');
        $id = intval($id);
        if (!$id) show_404();

        $record = $this->dcc->get($id);
        if (!$record) show_404();

        $store_id = $record->store_id;
        $summary = $this->dcc->calculate_summary($record->closing_date, $store_id);

        $data = $this->data;
        $data['page_title'] = 'Daily Cash Closing Report';
        $data['record'] = $record;
        $data['summary'] = $summary;

        if (file_exists(FCPATH . 'vendor/autoload.php')) {
            require_once FCPATH . 'vendor/autoload.php';
        }

        $html = $this->load->view('daily_cash_closing/report', $data, true);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'daily_cash_closing_' . ($record->closing_date) . '.pdf';
        $dompdf->stream($filename, array('Attachment' => 0));
        exit;
    }
}
