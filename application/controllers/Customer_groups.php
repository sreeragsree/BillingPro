<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_groups extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load_global();
		$this->load->model('Customer_groups_model', 'customer_groups');
	}

	public function add() {
		// Reuse customer add permission for customer groups
		$this->permission_check('customers_add');
		$data = $this->data;
		$data['page_title'] = 'Customer Group';
		$this->load->view('customer-group', $data);
	}

	public function newgroup() {
		$this->form_validation->set_rules('group_name', 'Customer Group', 'trim|required');
		if ($this->form_validation->run() == TRUE) {
			$result = $this->verify_and_save();
			echo $result;
		} else {
			echo "Please Enter Customer Group name.";
		}
	}

	public function update($id) {
		$this->belong_to('db_customer_groups', $id);
		// Reuse customer edit permission for customer groups
		$this->permission_check('customers_edit');
		$data = $this->data;

		$result = $this->get_details($id, $data);
		$data = array_merge($data, $result);
		$data['page_title'] = 'Customer Group';
		$this->load->view('customer-group', $data);
	}

	public function update_group() {
		$this->form_validation->set_rules('group_name', 'Customer Group', 'trim|required');
		$this->form_validation->set_rules('q_id', '', 'trim|required');

		if ($this->form_validation->run() == TRUE) {
			$result = $this->update_group_in_db();
			echo $result;
		} else {
			echo "Please Enter Customer Group name.";
		}
	}

	public function view() {
		// Reuse customer view permission for customer groups
		$this->permission_check('customers_view');
		$data = $this->data;
		$data['page_title'] = 'Customer Groups List';
		$this->load->view('customer-group-view', $data);
	}

	public function ajax_list() {
		$list = $this->get_datatables();

		$data = array();
		$no = $_POST['start'];
		foreach ($list as $group) {
			$no++;
			$row = array();
			$row[] = '<input type="checkbox" name="checkbox[]" value=' . $group->id . ' class="checkbox column_checkbox" >';
			$row[] = $group->group_name;
			$row[] = $group->description;

			if ($group->status == 1) {
				$str = "<span onclick='update_status(" . $group->id . ",0)' id='span_" . $group->id . "'  class='label label-success' style='cursor:pointer'>Active </span>";}
			else {
				$str = "<span onclick='update_status(" . $group->id . ",1)' id='span_" . $group->id . "'  class='label label-danger' style='cursor:pointer'> Inactive </span>";
			}
			$row[] = $str;
			$str2 = '<div class="btn-group" title="View Account">
										<a class="btn btn-primary btn-o dropdown-toggle" data-toggle="dropdown" href="#">
											Action <span class="caret"></span>
										</a>
										<ul role="menu" class="dropdown-menu dropdown-light pull-right">';

			if ($this->permissions('customers_edit')) {
				$str2 .= '<li>
												<a title="Edit Record ?" href="' . base_url() . 'customer_groups/update/' . $group->id . '">
													<i class="fa fa-fw fa-edit text-blue"></i>Edit
												</a>
											</li>';
			}

			if ($this->permissions('customers_edit')) {
				$group_name_js = json_encode($group->group_name, JSON_HEX_TAG | JSON_HEX_AMP);
				$str2 .= '<li>
												<a style="cursor:pointer" title="Add existing customers to group" onclick="add_existing_customers(' . $group->id . ', ' . $group_name_js . ')">
													<i class="fa fa-fw fa-user-plus text-green"></i>Add existing customers
												</a>
											</li>';
			}

			if ($this->permissions('customers_delete')) {
				$str2 .= '<li>
												<a style="cursor:pointer" title="Delete Record ?" onclick="delete_group(' . $group->id . ')">
													<i class="fa fa-fw fa-trash text-red"></i>Delete
												</a>
											</li>';
			}

			$str2 .= '</ul>
									</div>';
			$row[] = $str2;
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->count_all(),
			"recordsFiltered" => $this->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function update_status() {
		// Reuse customer edit permission for customer groups
		$this->permission_check_with_msg('customers_edit');
		$id = $this->input->post('id');
		$status = $this->input->post('status');

		$result = $this->update_status_in_db($id, $status);
		return $result;
	}

	public function delete_group() {
		// Reuse customer delete permission for customer groups
		$this->permission_check_with_msg('customers_delete');
		$id = $this->input->post('q_id');
		return $this->delete_groups_from_table($id);
	}

	public function multi_delete() {
		// Reuse customer delete permission for customer groups
		$this->permission_check_with_msg('customers_delete');
		$ids = implode(",", $_POST['checkbox']);
		return $this->delete_groups_from_table($ids);
	}

	/* === Local model-like methods (mirror brand_model pattern) === */
	private $table = 'db_customer_groups as a';
	private $column_order = array('a.id','a.group_name','a.description','a.status','a.store_id');
	private $column_search = array('a.id','a.group_name','a.description','a.status','a.store_id');
	private $order = array('a.id' => 'desc');

	private function _get_datatables_query() {
		$this->db->select($this->column_order);
		$this->db->from($this->table);
		$this->db->where('a.store_id', get_current_store_id());
		$i = 0;
		foreach ($this->column_search as $item) {
			if($_POST['search']['value']) {
				if($i===0) {
					$this->db->group_start();
					$this->db->like($item, $_POST['search']['value']);
				} else {
					$this->db->or_like($item, $_POST['search']['value']);
				}
				if(count($this->column_search) - 1 == $i)
					$this->db->group_end();
			}
			$i++;
		}

		if(isset($_POST['order'])) {
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if(isset($this->order)) {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	private function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	private function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	private function count_all() {
		$this->db->where('store_id', get_current_store_id());
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	private function verify_and_save() {
		extract($this->security->xss_clean(html_escape(array_merge($this->data,$_POST))));
		$store_id = get_current_store_id();

		$this->db->query("ALTER TABLE db_customer_groups AUTO_INCREMENT = 1");
		$info = array(
			'store_id'    => $store_id,
			'group_name'  => $group_name,
			'description' => $description,
			'created_date'=> $CUR_DATE,
			'created_time'=> $CUR_TIME,
			'created_by'  => $CUR_USERNAME,
			'status'      => 1,
		);
		$query1 = $this->db->insert('db_customer_groups', $info);
		if ($query1){
			$this->session->set_flashdata('success', 'Success!! New Customer Group Added Successfully!');
			return "success";
		}
		return "failed";
	}

	private function get_details($id,$data) {
		$query=$this->db->query("select * from db_customer_groups where upper(id)=upper('$id')");
		if($query->num_rows()==0){
			show_404();exit;
		}
		$query=$query->row();
		$data['q_id']       = $query->id;
		$data['store_id']   = $query->store_id;
		$data['group_name'] = $query->group_name;
		$data['description']= $query->description;
		return $data;
	}

	private function update_group_in_db() {
		extract($this->security->xss_clean(html_escape(array_merge($this->data,$_POST))));
		$store_id = get_current_store_id();
		$info = array(
			'group_name'  => $group_name,
			'description' => $description,
			'created_date'=> $CUR_DATE,
			'created_time'=> $CUR_TIME,
			'created_by'  => $CUR_USERNAME,
			'store_id'    => $store_id,
		);
		$query1 = $this->db->where('id',$q_id)->update('db_customer_groups', $info);
		if ($query1){
			$this->session->set_flashdata('success', 'Success!! Customer Group Updated Successfully!');
			return "success";
		}
		return "failed";
	}

	private function update_status_in_db($id,$status) {
		if (set_status_of_table($id,$status,'db_customer_groups')){
			echo "success";
		}
		else{
			echo "failed";
		}
	}

	private function delete_groups_from_table($ids) {
		$this->db->where("id in ($ids)");
		if(!is_admin()){
			$this->db->where("store_id",get_current_store_id());
		}
		$query = $this->db->delete("db_customer_groups");
		if ($query){
			echo "success";
		}
		else{
			echo "failed";
		}
	}

	public function assign_customers_to_group() {
		$this->permission_check_with_msg('customers_edit');
		
		$customer_ids = $this->input->post('customer_ids');
		$group_id = $this->input->post('group_id');
		
		if (!empty($customer_ids) && !empty($group_id)) {
			// Validate that the group exists and belongs to the current store
			$group_exists = $this->db->where('id', $group_id)
									->where('store_id', get_current_store_id())
									->get('db_customer_groups')
									->num_rows();
									
			if ($group_exists == 0) {
				echo "failed";
				return;
			}
			
			// Update customers to assign them to the group
			$this->db->where_in('id', $customer_ids);
			$this->db->where('store_id', get_current_store_id());
			$result = $this->db->update('db_customers', array('customer_group_id' => $group_id));
			
			if ($result) {
				echo "success";
			} else {
				echo "failed";
			}
		} else {
			echo "failed";
		}
	}
}