<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CustomerOrder_model extends CI_Model {
	

	protected $table      = 'db_customerorders as a';  // Table name
	var $column_order = array(
		'a.id',
		'a.store_id',
		'a.warehouse_id',
		'a.customer_name',
		'a.customer_email',
		'b.customer_contact',
		'a.customer_address',
		'a.customer_pincode',
		'a.order_date',
		'a.delivery_date',
		'a.dress_type',
		'a.dress_color',
		'a.fabric_type',
		'a.design_pref',
		'a.stiching_inst',
		'a.quantity',
		'a.total_amount',
		'a.advance_amount',
		'a.balance_amount',
		'a.payment_mode',
		'a.collected_by',
		'a.delivered_by',
		);
	var $column_search = array(
		'a.id',
		'a.store_id',
		'a.warehouse_id',
		'a.customer_name',
		'a.customer_email',
		'b.customer_contact',
		'a.customer_address',
		'a.customer_pincode',
		'a.order_date',
		'a.delivery_date',
		'a.dress_type',
		'a.dress_color',
		'a.fabric_type',
		'a.design_pref',
		'a.stiching_inst',
		'a.quantity',
		'a.total_amount',
		'a.advance_amount',
		'a.balance_amount',
		'a.payment_mode',
		'a.collected_by',
		'a.delivered_by',
		);

	public function __construct()
	{
		parent::__construct();
	}

	function get_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	private function _get_datatables_query()
	{
		$this->db->select($this->column_order);
		$this->db->from($this->table);
		//if(!is_admin()){
	    //   $this->db->where("a.store_id",get_current_store_id());
	    //}
	    //echo $this->db->get_compiled_select();exit();

		$i = 0;
	
		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}


}
