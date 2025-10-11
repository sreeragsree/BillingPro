<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Items extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load_global();
		$this->load->model('items_model','items');
	}
	
	public function get_item_details() {
	    $this->permission_check('items_view');
	    $item_id = $this->input->post('item_id');
	    
	    if(empty($item_id)) {
	        echo json_encode(['status' => 'error', 'message' => 'Item ID is required']);
	        return;
	    }
	    
	    try {
	        // Debug: Log the item ID and store ID
	        $current_store_id = get_current_store_id();
	        
	        // First, let's try a simple query without joins to see if the item exists
	        $this->db->select('*');
	        $this->db->from('db_items');
	        $this->db->where('id', $item_id);
	        $this->db->where('store_id', $current_store_id);
	        
	        $simple_query = $this->db->get();
	        
	        if($simple_query->num_rows() == 0) {
	            echo json_encode([
	                'status' => 'error',
	                'message' => 'Item not found. Item ID: ' . $item_id . ', Store ID: ' . $current_store_id
	            ]);
	            return;
	        }
	        
	        // Now try the full query with joins
	        $this->db->select('i.*, c.category_name, u.unit_name, b.brand_name');
	        $this->db->from('db_items i');
	        $this->db->join('db_category c', 'c.id = i.category_id', 'left');
	        $this->db->join('db_units u', 'u.id = i.unit_id', 'left');
	        $this->db->join('db_brands b', 'b.id = i.brand_id', 'left');
	        $this->db->where('i.id', $item_id);
	        $this->db->where('i.store_id', $current_store_id);
	        
	        $query = $this->db->get();
	        
	        // Check if query was successful
	        if($query === FALSE) {
	            echo json_encode([
	                'status' => 'error',
	                'message' => 'Database query failed: ' . $this->db->error()['message']
	            ]);
	            return;
	        }
	        
	        if($query->num_rows() > 0) {
	            $item = $query->row_array();
	            
	            // For now, set stock to 0 since the stock calculation is complex
	            // You can implement proper stock calculation later using the custom helper
	            $item['stock'] = 0;
	            
	            // Get batches for this item
	            $this->db->select('*');
	            $this->db->from('db_batches');
	            $this->db->where('pro_id', $item_id);
	            $this->db->order_by('id', 'desc');
	            $batches_query = $this->db->get();
	            
	            $item['batches'] = array();
	            if($batches_query && $batches_query->num_rows() > 0) {
	                $item['batches'] = $batches_query->result_array();
	            }
	            
	            // Get supplier information from the most recent purchase
	            $this->db->select('s.supplier_name');
	            $this->db->from('db_purchaseitems pi');
	            $this->db->join('db_purchase p', 'p.id = pi.purchase_id', 'left');
	            $this->db->join('db_suppliers s', 's.id = p.supplier_id', 'left');
	            $this->db->where('pi.item_id', $item_id);
	            $this->db->where('p.store_id', $current_store_id);
	            $this->db->order_by('p.purchase_date', 'desc');
	            $this->db->limit(1);
	            
	            $supplier_query = $this->db->get();
	            $item['supplier_name'] = 'N/A';
	            if($supplier_query && $supplier_query->num_rows() > 0) {
	                $supplier_data = $supplier_query->row_array();
	                $item['supplier_name'] = $supplier_data['supplier_name'] ?: 'N/A';
	            }
	            
	            // Format prices
	            $item['price_formatted'] = number_format($item['price'], 2);
	            $item['purchase_price_formatted'] = number_format($item['purchase_price'], 2);
	            
	            echo json_encode([
	                'status' => 'success',
	                'data' => $item
	            ]);
	        } else {
	            echo json_encode([
	                'status' => 'error',
	                'message' => 'Item not found after join query. Item ID: ' . $item_id . ', Store ID: ' . $current_store_id
	            ]);
	        }
	    } catch (Exception $e) {
	        echo json_encode([
	            'status' => 'error',
	            'message' => 'Database error: ' . $e->getMessage()
	        ]);
	    }
	}
	
	public function index()
	{
		if(!$this->permissions('items_view') && !$this->permissions('services_view')){
			$this->show_access_denied_page();exit;
		}
		$data=$this->data;
		$data['page_title']=$this->lang->line('items_list');
		$this->load->view('items-list',$data);
	}

	public function updateBatchDetails()
    {
        $edit_pid = $this->request->getPost('edit_pid');
        $edit_batch = $this->request->getPost('edit_batch');
        $edit_pprice = $this->request->getPost('edit_pprice');
        $edit_sprice = $this->request->getPost('edit_sprice');
        $edit_wprice = $this->request->getPost('edit_wprice');
        $edit_mrp = $this->request->getPost('edit_mrp');
        $edit_alpprice = $this->request->getPost('edit_alpprice');



    }

     public function deleteItemBatch()
     {
      $pro_id   = $this->input->post('pro_id');
          $batch_id = $this->input->post('batch_id');

          if (empty($pro_id) || empty($batch_id)) {
              echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
              return;
          }

          // 1. Check if batch is used in sales items
          $exists = $this->db->where('batch_id', $batch_id)
                             ->count_all_results('db_salesitems');

          if ($exists > 0) {
              echo json_encode(['status' => 'error', 'message' => 'Cannot delete: batch is already used in sales items']);
              return;
          }

          // 2. Check if batch quantity is 0
          $batch = $this->db->select('quantity')
                            ->from('db_batches')
                            ->where('id', $batch_id)
                            ->where('pro_id', $pro_id)
                            ->get()
                            ->row();

          if (!$batch) {
              echo json_encode(['status' => 'error', 'message' => 'Batch not found']);
              return;
          }

          if ($batch->quantity != 0) {
              echo json_encode(['status' => 'error', 'message' => 'Cannot delete: batch quantity is not zero']);
              return;
          }

          // 3. Safe to delete
          $this->db->where('id', $batch_id);
          $this->db->where('pro_id', $pro_id);
          $deleted = $this->db->delete('db_batches');

          if ($deleted) {
              echo json_encode(['status' => 'success', 'message' => 'Batch deleted successfully']);
          } else {
              echo json_encode(['status' => 'error', 'message' => 'Failed to delete batch']);
          }
     }

	public function add()
	{
		$this->permission_check('items_add');
		$data=$this->data;
		$data['page_title']=$this->lang->line('items');
		$this->load->view('items',$data);
	}

	public function newitems(){
		$this->form_validation->set_rules('item_name', 'Item Name', 'trim|required');
		$this->form_validation->set_rules('category_id', 'Category Name', 'trim|required');
		$this->form_validation->set_rules('unit_id', 'Unit', 'trim|required');
		$this->form_validation->set_rules('tax_id', 'Tax', 'trim|required');

		if($this->input->post('item_group')=='Single'){
		$this->form_validation->set_rules('price', 'Item Price', 'trim|required');
		$this->form_validation->set_rules('purchase_price', 'Purchase Price', 'trim|required');
		$this->form_validation->set_rules('sales_price', 'Sales Price', 'trim|required');
		}
		else{
			if($this->input->post('existing_row_count')==1){
				echo "Variants List Not Added, Please Select Variants!!";exit();
			}
		}		
		if ($this->form_validation->run() == TRUE) {
			$result=$this->items->save_record(array('command' =>'save'));
			echo $result;
		} else {
			echo "Please Fill Compulsory(* marked) Fields.";
		}
	}

	public function editBatchRecordModal()
    {
        $result = $this->items->editBatchRecordModal();
        echo $result;
    }

	//PopUP Modal
	public function addItemFromModal(){

		$this->form_validation->set_rules('m_item_name', 'Item Name', 'trim|required');
		$this->form_validation->set_rules('m_category_id', 'Category Name', 'trim|required');
		$this->form_validation->set_rules('m_unit_id', 'Unit', 'trim|required');
		$this->form_validation->set_rules('m_tax_id', 'Tax', 'trim|required');

		if($this->input->post('item_group')=='Single'){
		$this->form_validation->set_rules('m_price', 'Item Price', 'trim|required');
		$this->form_validation->set_rules('m_purchase_price', 'Purchase Price', 'trim|required');
		$this->form_validation->set_rules('m_sales_price', 'Sales Price', 'trim|required');
		}
		else{
			if($this->input->post('existing_row_count')==1){
				echo "Variants List Not Added, Please Select Variants!!";exit();
			}
		}		
		if ($this->form_validation->run() == TRUE) {
			$modal_post=array(
								'item_name' => $this->input->post('m_item_name'),
								'brand_id' => $this->input->post('m_brand_id'),
								'category_id' => $this->input->post('m_category_id'),
								'unit_id' => $this->input->post('m_unit_id'),
								'tax_id' => $this->input->post('m_tax_id'),
								'price' => $this->input->post('m_price'),
								'purchase_price' => $this->input->post('m_purchase_price'),
								'sales_price' => $this->input->post('m_sales_price'),
								'hsn' => $this->input->post('m_hsn'),
								'sku' => $this->input->post('m_sku'),
								'alert_qty' => $this->input->post('m_alert_qty'),
								'seller_points' => $this->input->post('m_seller_points'),
								'custom_barcode' => $this->input->post('m_custom_barcode'),
								'item_group' => $this->input->post('m_item_group'),
								'description' => $this->input->post('m_description'),
								'discount_type' => $this->input->post('m_discount_type'),
								'discount' => $this->input->post('m_discount'),
								'price' => $this->input->post('m_price'),
								'tax_id' => $this->input->post('m_tax_id'),
								'purchase_price' => $this->input->post('m_purchase_price'),
								'tax_type' => $this->input->post('m_tax_type'),
								'profit_margin' => $this->input->post('m_profit_margin'),
								'sales_price' => $this->input->post('m_sales_price'),
								'mrp' => $this->input->post('m_mrp'),

								'adjustment_qty' => $this->input->post('adjustment_qty'),

								'wholesale_price' => $this->input->post('m_wholesaleprice'),
								'alph_price' => $this->input->post('m_alphabetprice'),
								'batch_no' => $this->input->post('m_batchname'),
								'seller_code' => $this->input->post('m_sellercode'),
								'seller_name' => $this->input->post('m_sellername'),

								'warehouse_id' => $this->input->post('m_warehouse_id'),
								'command' => 'save',
							);
			$result=$this->items->save_record($modal_post);
			echo $result;
		} else {
			echo "Please Fill Compulsory(* marked) Fields.";
		}
	}

	public function update($id){
		$this->belong_to('db_items',$id);
		$this->permission_check('items_edit');
		//Check is direct Access of the variant by id in item ?
		/*$parent_id = $this->db->select("parent_id")->where("store_id",get_current_store_id())->where("id",$id)->get("db_items")->row()->parent_id;
		if(!empty($parent_id)){
			show_error("You can't access variant Item!!", 403, $heading = "Invalid Access!!");
		}*/

		$data=$this->data;
		$this->load->model('items_model');
		$result=$this->items_model->get_details($id,$data);
		$data=array_merge($data,$result);
		$data['page_title']=$this->lang->line('items');
		//$data['variant_tbody']=$this->items_model->get_variants_list_in_row($id);
		$this->load->view('items', $data);
	}
	public function update_items(){
		$this->form_validation->set_rules('item_name', 'Item Name', 'trim|required');
		$this->form_validation->set_rules('category_id', 'Category Name', 'trim|required');
		$this->form_validation->set_rules('unit_id', 'Unit', 'trim|required');
		$this->form_validation->set_rules('tax_id', 'Tax', 'trim|required');

		if($this->input->post('item_group')=='Single'){
		$this->form_validation->set_rules('price', 'Item Price', 'trim|required');
		$this->form_validation->set_rules('purchase_price', 'Purchase Price', 'trim|required');
		$this->form_validation->set_rules('sales_price', 'Sales Price', 'trim|required');
		}
		else{
			if($this->input->post('existing_row_count')==1){
				echo "Variants List Not Added, Please Select Variants!!";exit();
			}
		}

		
		if ($this->form_validation->run() == TRUE) {
			$result=$this->items->save_record(array('command'=>'update'));
			echo $result;
		} else {
			echo "Please Fill Compulsory(* marked) Fields.";
		}

	}

	public function get_brand_name($brand_id=''){
		if($brand_id==NULL || $brand_id=='' || $brand_id ==0){
			return;
		}
		return $this->db->query('select brand_name from db_brands where id="'.$brand_id.'"')->row()->brand_name;
	}
	public function ajax_list()
	{
		$warehouse_id = $_REQUEST['warehouse_id'];

		$list = $this->items->get_datatables();
		
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $items) {
			
			$no++;
			$row = array();
			$row[] = '<input type="checkbox" name="checkbox[]" value='.$items->id.' class="checkbox column_checkbox" >';
						

			$row[] = (!empty($items->item_image)) ? "
						<a title='Click for Bigger!' href='".base_url($items->item_image)."' data-toggle='lightbox'>
						<image style='border:1px #72afd2 solid;' src='".base_url(return_item_image_thumb($items->item_image))."' width='75%' height='50%'> </a>" : "
						<image style='border:1px #72afd2 solid;' src='".base_url()."theme/images/no_image.png' title='No Image!' width='75%' height='50%' >";
			
			$row[] = $items->item_code;

			$str = "";

			$str = "<label class='text-blue'>".$items->item_name."</label>";
				if($items->service_bit){
					$str .="<br><b>SAC</b>:".$items->sac;
					$str .="<br><b>HSN</b>:".$items->hsn;
				}
				else{
					$str .="<br><b>HSN</b>:".$items->hsn;
					$str .="<br><b>SKU</b>:".$items->sku;
				}
				

			$row[] = $str;

			$row[] = $items->brand_name;

			$service_or_item_name = ($items->service_bit) ? 'SERVICE' : "ITEM";

			$row[] = $items->category_name."<br>[<label class='text-orange'>".$service_or_item_name."</label>]";

			$item_group = '';// (!empty($items->item_group)) ? "<br>[<label class='text-green'>".$items->item_group."</label>]" : '';
			$row[] = $items->unit_name.$item_group;

					 $str='';
					 if(warehouse_module() && warehouse_count()>0 && $items->stock>0){ 
			 			$str= "<i class='fa fa-building-o pointer bg-blue text-dark' title='Click to view Warehouse Wise Stock' data-toggle='tooltip' onclick='view_warehouse_wise_stock_item(".$items->id.")'> </i>";
			 		 }
			$warehouse_ids  = (!empty($warehouse_id)) ? $warehouse_id : get_privileged_warehouses_ids();

			
			$row[] = format_qty(total_available_qty_items_of_warehouse($warehouse_ids,null,$items->id))." $str";

			$row[] = $items->alert_qty;
			$row[] = store_number_format($items->sales_price);
			$row[] = $items->tax_name."<br>(".store_number_format($items->tax)."%)";

			 		if($items->status==1){ 
			 			$str= "<span onclick='update_status(".$items->id.",0)' id='span_".$items->id."'  class='label label-success' style='cursor:pointer'>Active </span>";}
					else{ 
						$str = "<span onclick='update_status(".$items->id.",1)' id='span_".$items->id."'  class='label label-danger' style='cursor:pointer'> Inactive </span>";
					}
			$row[] = $str;		

			 		$str2 = '<div class="btn-group" title="View Account">
										<a class="btn btn-primary btn-o dropdown-toggle" data-toggle="dropdown" href="#">
											Action <span class="caret"></span>
										</a>
										<ul role="menu" class="dropdown-menu dropdown-light pull-right">';

											if($this->permissions('items_edit') || $this->permissions('services_edit'))
											$str2.='<li>
												<a title="Edit Record ?" href="'.base_url(($items->service_bit)? 'services/update/'.$items->id : 'items/update/'.$items->id).'">
													<i class="fa fa-fw fa-edit text-blue"></i>Edit
												</a>
											</li>';

											if($this->permissions('items_delete')|| $this->permissions('services_delete'))
											$str2.='<li>
												<a style="cursor:pointer" title="Delete Record ?" onclick="delete_items('.$items->id.')">
													<i class="fa fa-fw fa-trash text-red"></i>Delete
												</a>
											</li>
											
										</ul>
									</div>';			
			$row[] = $str2;

			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->items->count_all(),
						"recordsFiltered" => $this->items->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}
	public function update_status(){
		$this->permission_check_with_msg('items_edit');
		$id=$this->input->post('id');
		$status=$this->input->post('status');

		$this->load->model('items_model');
		$result=$this->items_model->update_status($id,$status);
		return $result;
	}

	public function delete_items(){
		$this->permission_check_with_msg('items_delete');
		$id=$this->input->post('q_id');
		return $this->items->delete_items_from_table($id);
	}
	public function multi_delete(){
		$this->permission_check_with_msg('items_delete');
		$ids=implode (",",$_POST['checkbox']);
		return $this->items->delete_items_from_table($ids);
	}


    public function get_json_items_details() {
        $store_id = $this->input->get('store_id');
        $warehouse_id = $this->input->get('warehouse_id');
        $search_for = $this->input->get('search_for');
        $show_purchase_price = $this->permissions('show_purchase_price');

        $display_json = array();

        $search = strtolower(trim($this->input->get('name')));
        $barcode = '';
        $batch = '';
        $is_barcode_batch = false;

        // Detect barcode-batch combination
        if (strpos($search, '-') !== false) {
            $parts = explode('-', $search);
            if (count($parts) === 2) {
                $barcode = trim($parts[0]);
                $batch = trim($parts[1]);
                $is_barcode_batch = true;
            }
        }

        // Build query based on context
        if (isset($search_for) && $search_for == 'purchase') {
            $this->db->select("a.service_bit, a.purchase_price, a.id, a.item_name, a.item_code,
             a.custom_barcode, COALESCE(SUM(a.stock), 0) as stock, a.item_group, 
             a.sales_price, a.wholesale_price, a.rac_no");
            $this->db->from("db_items as a");
        } else if (isset($search_for) && ($search_for == 'labels' || $search_for == 'sales')) {
            $this->db->select("a.*");
            $this->db->from("db_items as a");
        } else {
            $this->db->where('a.service_bit = 0');
            $this->db->select("a.service_bit, a.purchase_price, a.id, a.item_name, a.item_code, a.custom_barcode, COALESCE(b.available_qty, 0) as stock, a.item_group, a.sales_price, a.wholesale_price, a.rac_no");
            $this->db->from("db_items as a");
            $this->db->join("db_warehouseitems as b", "b.item_id = a.id", 'left');
            $this->db->where("b.warehouse_id", $warehouse_id);
        }

        $this->db->where("a.status", 1);
        $this->db->where("a.store_id", $store_id);

        // Apply search filter
        if ($is_barcode_batch) {
            $this->db->where("LOWER(a.custom_barcode)", $barcode);
//            $this->db->where("LOWER(a.batch_code)", $batch);
        } else {
            $this->db->where("(LOWER(a.custom_barcode) LIKE '%$search%' OR LOWER(a.item_name) LIKE '%$search%' OR LOWER(a.item_code) LIKE '%$search%')");
        }

        $this->db->group_by("a.id");
        $this->db->limit(20);

        $query = $this->db->get();

        foreach ($query->result() as $res) {
            if ($res->item_group != 'Variants') {
                $json_arr = array();
                $json_arr["id"] = $res->id;
                $json_arr["value"] = $res->item_name;
                $json_arr["label"] = $res->item_name;
                $json_arr["item_code"] = $res->item_code;
                $json_arr["stock"] = (isset($search_for) && $search_for == 'sales')
                    ? total_available_qty_items_of_warehouse($warehouse_id, $store_id, $res->id)
                    : $res->stock;
                $json_arr["purchase_price"] = ($show_purchase_price) ? store_number_format($res->purchase_price) : '';
                $json_arr["service_bit"] = $res->service_bit;
                $json_arr["rac_no"] = $res->rac_no ?? '';
                $json_arr["sales_price"] = $res->sales_price ?? '';
                $json_arr["wholesale_price"] = $res->wholesale_price ?? '';
                $json_arr["bar_code"] = $res->custom_barcode ?? '';
                $json_arr["batch_id"] = $batch ?? '';

                $display_json[] = $json_arr;
            }
        }

        echo json_encode($display_json); exit;
    }


    public function labels($purchase_id=''){
		$this->permission_check('print_labels');
		$data=$this->data;
		$data['page_title']=$this->lang->line('print_labels');
		$data['purchase_id']=$purchase_id;
		$this->load->view('labels',$data);
	}

	/*Labels Print request*/
	public function return_row_with_data($rowcount,$item_id){
		echo $this->items->get_items_info($rowcount,$item_id);
	}

	public function preview_labels(){
		echo $this->items->preview_labels();
	}

	//GET Labels from Purchase Invoice
    public function show_labels($purchase_id = '')
    {
        $i = 1;
        $result = '';

        // Fetch item_id, purchase_qty, batch_id for that purchase
        $q2 = $this->db->query("
        SELECT item_id, purchase_qty, batch_id
        FROM db_purchaseitems
        WHERE purchase_id = '$purchase_id'
    ");

        if ($q2->num_rows() > 0) {
            foreach ($q2->result() as $res2) {
                // Pass batch_id also to get_purchase_items_info
                $result .= $this->items->get_purchase_items_info(
                    $i++,
                    $res2->item_id,
                    $res2->purchase_qty,
                    $res2->batch_id
                );
            }
        }
        echo $result;
    }

	public function get_json_variant_details(){
		
		$data = array();
		$display_json = array();
			$name = strtolower(trim($_GET['name']));

				$this->db->select("id,variant_name,description");
				$this->db->from("db_variants");
				$this->db->where("(UPPER(variant_name) LIKE UPPER('%$name%') OR (UPPER(description) LIKE UPPER('%$name%')))");
				$this->db->where("status=1");
				$this->db->where("store_id",get_current_store_id());
			$this->db->limit("10");
			//$this->db->get_compiled_select();exit;
			$sql =$this->db->get();
			
			foreach ($sql->result() as $res) {
			      $json_arr["id"] = $res->id;
				  $json_arr["variant_name"] = $res->variant_name;
				  $json_arr["description"] = $res->description;
				  array_push($display_json, $json_arr);
			}
		echo json_encode($display_json);exit;
	}
	public function return_variant_data_in_row($rowcount,$item_id){
		echo $this->items->return_variant_data_in_row($rowcount,$item_id);
	}

	public function getItems($id=''){
		echo $this->items->getItemsJson($id);
	}

}
