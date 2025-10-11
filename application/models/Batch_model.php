<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Batch_model extends CI_Model
{
    protected $table      = 'db_batches as  a'; // Table name
    protected $primaryKey = 'id';       // Primary key

    protected $allowedFields = ['batch_no', 'pro_id']; // Fields allowed for insertion

    protected $useTimestamps = false; // Automatically manage timestamps

    var $column_order = array('a.batch_no', 'a.pro_id'); //set column field database for datatable orderable
    var $column_search = array('a.batch_no', 'a.pro_id'); //set column field database for datatable searchable 
    var $order = array('a.id' => 'desc'); // default order 

    public function __construct()
    {
        parent::__construct();
    }

    public function verify_and_save()
    {
        //Filtering XSS and html escape from user inputs 
        extract($this->security->xss_clean(html_escape(array_merge($this->data, $_REQUEST))));

        $state = (!empty($state)) ? $state : 'NULL';

        //Validate This suppliers already exist or not		
        //Validate This customers already exist or not
        if (isset($_GET['js_store_id'])) {
            $store_id = $_GET['js_store_id'];
        } else {
            $store_id = (store_module() && is_admin()) ? $store_id : get_current_store_id();
        }
    
        if (!empty($batch_name)) {
            $query2 = $this->db->query("select * from db_batches where batch_no='$batch_name' and pro_id=$pro_id");
            if ($query2->num_rows() > 0) {
                return "Sorry! This Batch Number already Exist.";;
            }
        }

        $this->db->query("ALTER TABLE db_batches AUTO_INCREMENT = 1");

        #------------------------------------
        $info = array(
            'batch_no'            => $batch_name,
            'pro_id'         => $pro_id,
        );
        $query1 = $this->db->insert('db_batches', $info);
        #------------------------------------
        if ($query1) {
            $this->session->set_flashdata('success', 'Success!! New Batch Added Successfully!');
            return "success";
        } else {
            return "failed";
        }
    }

    public function get_all_batches()
    {
        //Filtering XSS and html escape from user inputs 
        extract($this->security->xss_clean(html_escape(array_merge($this->data, $_REQUEST))));

        $state = (!empty($state)) ? $state : 'NULL';

        //Validate This suppliers already exist or not		
        //Validate This customers already exist or not
        if (isset($_GET['pro_id'])) {
            $store_id = $_GET['pro_id'];
        } 
    
        $json_array = array();
        if (!empty($pro_id)) {
            $query2 = $this->db->query("select * from db_batches where pro_id=$pro_id");
            if ($query2->num_rows() > 0) {
                foreach ($query2->result() as $value) {
                    $json_array[] = ['id' => $value->id, 'text' => $value->batch_no];
                }
            }
            return json_encode($json_array);
        }
    }
}
