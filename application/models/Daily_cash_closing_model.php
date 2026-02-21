<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Daily_cash_closing_model extends CI_Model {

    protected $table = 'daily_cash_closing';

    public function __construct(){
        parent::__construct();
    }

    public function get_all($store_id = null){
        if ($store_id !== null) {
            $this->db->where('store_id', $store_id);
        }
        $this->db->order_by('closing_date', 'desc');
        $query = $this->db->get($this->table);
        if ($query === false) {
            $dberr = $this->db->error();
            log_message('error', 'Daily_cash_closing_model::get_all DB error: ' . ($dberr['message'] ?? 'unknown'));
            return array();
        }
        return $query->result();
    }

    public function create($data){
        return $this->db->insert($this->table, $data);
    }

    /**
     * Get a single closing by id
     */
    public function get($id)
    {
        $query = $this->db->where('id', $id)->get($this->table);
        if ($query === false) {
            $dberr = $this->db->error();
            log_message('error', 'Daily_cash_closing_model::get DB error: ' . ($dberr['message'] ?? 'unknown'));
            return null;
        }
        return $query->row();
    }

    /**
     * Get last closing for store
     */
    public function get_last_closing($store_id)
    {
        $query = $this->db->where('store_id', $store_id)->order_by('closing_date', 'desc')->limit(1)->get($this->table);
        if ($query === false) {
            $dberr = $this->db->error();
            log_message('error', 'Daily_cash_closing_model::get_last_closing DB error: ' . ($dberr['message'] ?? 'unknown'));
            return null;
        }
        return $query->row();
    }

    /**
     * Calculate summary for a date (totals by payment type, expenses, refunds)
     */
    public function calculate_summary($date, $store_id)
    {
        $result = [
            'cash_sales' => 0.00,
            'card_sales' => 0.00,
            'upi_sales' => 0.00,
            'other_sales' => 0.00,
            'expenses' => 0.00,
            'refunds' => 0.00,
            'cash_in' => 0.00,
            'cash_out' => 0.00,
        ];

        // sales payments
        $payments = $this->db->select("UPPER(payment_type) as ptype, SUM(payment) as total")
            ->from('db_salespayments')
            ->where('store_id', $store_id)
            ->where('created_date', $date)
            ->group_by('ptype')
            ->get();

        if ($payments && $payments->num_rows()>0) {
            // keywords to detect UPI/online payments
            $upi_keywords = ['UPI','ONLINE','PAYTM','GPAY','GOOGLE','PHONEPE','PHONE PE','BHIM','RAZORPAY','PAYU','WALLET','NETBANKING'];
            foreach ($payments->result() as $row) {
                $ptype = $row->ptype;
                $total = floatval($row->total);
                if (strpos($ptype, 'CASH') !== false) {
                    $result['cash_sales'] += $total;
                } elseif (strpos($ptype, 'CARD') !== false || strpos($ptype, 'VISA') !== false || strpos($ptype, 'MASTERCARD') !== false || strpos($ptype, 'DEBIT') !== false || strpos($ptype, 'CREDIT') !== false) {
                    $result['card_sales'] += $total;
                } else {
                    // detect UPI/online keywords
                    $found_upi = false;
                    foreach ($upi_keywords as $kw) {
                        if (strpos($ptype, $kw) !== false) { $found_upi = true; break; }
                    }
                    if ($found_upi) {
                        $result['upi_sales'] += $total;
                    } else {
                        $result['other_sales'] += $total;
                    }
                }
            }
        }

        // refunds: use change_return from salespayments (fallback) and sales return payments
        $refunds = $this->db->select('IFNULL(SUM(change_return),0) as total')->from('db_salespayments')->where('store_id',$store_id)->where('created_date',$date)->get();
        if ($refunds && $refunds->num_rows()>0) $result['refunds'] = floatval($refunds->row()->total);

        // expenses
        $exp = $this->db->select('IFNULL(SUM(expense_amt),0) as total')->from('db_expense')->where('store_id',$store_id)->where('created_date',$date)->get();
        if ($exp && $exp->num_rows()>0) $result['expenses'] = floatval($exp->row()->total);

        // cash adjustments (cash_transactions module) - look for ac_transactions or cash entries
        // Try db_cash_transactions or unified cash transactions view via Cash_transactions_model query
        $cash_in = $this->db->select('IFNULL(SUM(payment),0) as total')->from('db_salespayments')->where('store_id',$store_id)->where('created_date',$date)->where("UPPER(payment_type) LIKE '%CASH%'")->get();
        // We already counted cash sales; for cash_in/cash_out custom adjustments we'll set 0 by default
        $result['cash_in'] = 0.00;
        $result['cash_out'] = 0.00;

        return $result;
    }

    /**
     * Check if a closing for date already exists
     */
    public function exists_for_date($date, $store_id)
    {
        $query = $this->db->where('closing_date',$date)->where('store_id',$store_id)->get($this->table);
        if ($query === false) {
            $dberr = $this->db->error();
            log_message('error', 'Daily_cash_closing_model::exists_for_date DB error: ' . ($dberr['message'] ?? 'unknown'));
            return false;
        }
        return $query->num_rows() > 0;
    }

    /**
     * Get closing record for a specific date and store
     */
    public function get_for_date($date, $store_id)
    {
        $query = $this->db->where('closing_date',$date)->where('store_id',$store_id)->get($this->table);
        if ($query === false) {
            $dberr = $this->db->error();
            log_message('error', 'Daily_cash_closing_model::get_for_date DB error: ' . ($dberr['message'] ?? 'unknown'));
            return null;
        }
        return $query->row();
    }

}

/*
Sample SQL to create the table if not present:

CREATE TABLE `daily_cash_closing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `closing_date` date DEFAULT NULL,
  `opening_cash` decimal(15,2) DEFAULT '0.00',
  `cash_sales` decimal(15,2) DEFAULT '0.00',
  `expenses` decimal(15,2) DEFAULT '0.00',
  `closing_cash` decimal(15,2) DEFAULT '0.00',
  `note` text,
  `store_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);

*/
