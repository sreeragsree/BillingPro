<!DOCTYPE html>
<html>

<head>
   <!-- TABLES CSS CODE -->
   <?php include "comman/code_css.php"; ?>
    <meta name="csrf-token-name" content="<?= $this->security->get_csrf_token_name(); ?>">
    <meta name="csrf-token-value" content="<?= $this->security->get_csrf_hash(); ?>">
   <!-- </copy> -->
</head>

<body class="hold-transition skin-blue  sidebar-mini">
   <!-- **********************MODALS***************** -->
   <?php include "modals/modal_brand.php"; ?>
   <?php include "modals/modal_category.php"; ?>
   <?php include "modals/modal_unit.php"; ?>
   <?php include "modals/modal_tax.php"; ?>
   <?php include "modals/modal_batch_edit.php"; ?>
   <!-- **********************MODALS END***************** -->

   <div class="wrapper">
      <?php include "sidebar.php"; ?>
      <?php
      if (!isset($item_name)) {
         $item_name = $sku = $hsn = $opening_stock = $brand_id = $category_id = $gst_percentage = $tax_type =
            $sales_price = $purchase_price = $profit_margin = $unit_id = $price = $alert_qty = $store_id = "";
         $stock = 0;
         $seller_points = 0;
         $supplier_item_code = '';
         $custom_barcode = '';
         $description = '';
         $mrp = '';
         $rac_no = '';
         $alph_price = '';
         $wholesale_price = '';
         $batch_no = '';
         $child_bit = '';
         $tax_id = '';
         $item_type = 'S';

         //$variants_selected='';
         $item_group = 'Single';

         $discount = '';
         $discount_type = 'Percentage';


         $opening_stock_readonly = '';


         $item_code = get_init_code('item');
      } else {
         $opening_stock_readonly = 'readonly';
      }
      //For new or update
      $opening_stock = '0';




      ?>
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
         <!-- **********************MODALS***************** -->
         <?php include "modals/modal_variant.php"; ?>
         <!-- **********************MODALS END***************** -->

         <!-- Content Header (Page header) -->
         <section class="content-header">
            <h1>
               <?= $page_title; ?>
               <small>Add/Update Items</small>
            </h1>
            <ol class="breadcrumb">
               <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i>Home</a></li>
               <li><a href="<?php echo $base_url; ?>items"><?= $this->lang->line('items_list'); ?></a></li>
               <li class="active"><?= $page_title; ?></li>
            </ol>
         </section>
         <!-- Main content -->
         <section class="content">
            <div class="row">
               <!-- ********** ALERT MESSAGE START******* -->
               <?php include "comman/code_flashdata.php"; ?>
               <!-- ********** ALERT MESSAGE END******* -->
               <!-- right column -->
               <div class="col-md-12">
                  <!-- Horizontal Form -->
                  <div class="box box-primary ">

                     <?= form_open('#', array('class' => 'form', 'id' => 'items-form', 'enctype' => 'multipart/form-data', 'method' => 'POST')); ?>
                     <input type="hidden" id="base_url" value="<?php echo $base_url;; ?>">
                     <div class="box-body">

                        <div class="row">
                           <!-- Store Code -->
                           <?php /*if(store_module() && is_admin()) {$this->load->view('store/store_code',array('show_store_select_box_1'=>true,'store_id'=>$store_id)); }else{*/
                           echo "<input type='hidden' name='store_id' id='store_id' value='" . get_current_store_id() . "'>";
                           /*}*/ ?>
                           <!-- Store Code end -->
                        </div>

                        <div class="row">
                           <div class="form-group col-md-4">
                              <label for="item_code"><?= $this->lang->line('item_code'); ?><span class="text-danger">*</span></label>
                              <input type="text" class="form-control" id="item_code" name="item_code" placeholder="" value="<?php print $item_code; ?>">
                              <span id="item_code_msg" style="display:none" class="text-danger"></span>
                           </div>


                           <div class="form-group col-md-4">
                              <label for="item_type"><?= $this->lang->line('item_type'); ?><span class="text-danger">*</span></label>
                              <select class="form-control select2" id="item_type" name="item_type" style="width: 100%;">

                                 <option value="S">Sales Item</option>
                                 <option value="R">Rental Item</option>
                              </select> <span id="item_type_msg" style="display:none" class="text-danger"></span>
                           </div>

                        </div>

                        <!-- SALE / RENTAL ITEM -->

                        <!-- SALE / RENTAL ITEM CLOSE -->


                        <div class="row">
                           <div class="form-group col-md-4">
                              <label for="item_name"><?= $this->lang->line('item_name'); ?><span class="text-danger">*</span></label>
                              <input type="text" autofocus="" class="form-control" id="item_name" name="item_name" placeholder="" value="<?php print $item_name; ?>">
                              <span id="item_name_msg" style="display:none" class="text-danger"></span>
                           </div>
                           <div class="form-group col-md-4">
                              <label for="brand_id"><?= $this->lang->line('brand'); ?></label>
                              <div class="input-group">
                                 <select class="form-control select2" id="brand_id" name="brand_id" style="width: 100%;">
                                    <option value="">-Select-</option>
                                    <?= get_brands_select_list($brand_id);  ?>
                                 </select>
                                 <span class="input-group-addon pointer" data-toggle="modal" data-target="#brand_modal" title="Add Customer"><i class="fa fa-plus-square-o text-primary fa-lg"></i></span>
                              </div>
                              <span id="brand_id_msg" style="display:none" class="text-danger"></span>
                           </div>
                           <div class="form-group col-md-4">
                              <label for="category_id"><?= $this->lang->line('category'); ?><span class="text-danger">*</span></label>
                              <div class="input-group">
                                 <select class="form-control select2" id="category_id" name="category_id" style="width: 100%;">
                                    <option value="">-Select-</option>
                                    <?= get_categories_select_list($category_id);  ?>
                                 </select>
                                 <span class="input-group-addon pointer" data-toggle="modal" data-target="#category_modal" title="Add Customer"><i class="fa fa-plus-square-o text-primary fa-lg"></i></span>
                              </div>
                              <span id="category_id_msg" style="display:none" class="text-danger"></span>
                           </div>
                           <div class="form-group col-md-4">
                              <label for="item_group"><?= $this->lang->line('item_group'); ?><span class="text-danger">*</span></label>
                              <select class="form-control select2" id="item_group" name="item_group" style="width: 100%;">

                                 <option value="Single">Single</option>
                                 <!-- <option value="Variants">Variants</option> -->
                              </select>
                              <span id="item_group_msg" style="display:none" class="text-danger"></span>

                           </div>
                           <div class="form-group col-md-4">
                              <label for="unit_id"><?= $this->lang->line('unit'); ?><span class="text-danger">*</span></label>
                              <div class="input-group">
                                 <select class="form-control select2" id="unit_id" name="unit_id" style="width: 100%;">
                                    <?= get_units_select_list($unit_id);  ?>
                                 </select>
                                 <span class="input-group-addon pointer" data-toggle="modal" data-target="#unit_modal" title="Add Customer"><i class="fa fa-plus-square-o text-primary fa-lg"></i></span>
                              </div>
                              <span id="unit_id_msg" style="display:none" class="text-danger"></span>
                           </div>
                           <div class="form-group col-md-4">
                              <label for="sku"><?= $this->lang->line('sku'); ?></label>
                              <input type="text" class="form-control" id="sku" name="sku" placeholder="" value="<?php print $sku; ?>">
                              <span id="sku_msg" style="display:none" class="text-danger"></span>
                           </div>
                           <div class="form-group col-md-4">
                              <label for="hsn"><?= $this->lang->line('hsn'); ?></label>
                              <input type="text" class="form-control" id="hsn" name="hsn" placeholder="" value="<?php print $hsn; ?>">
                              <span id="hsn_msg" style="display:none" class="text-danger"></span>
                           </div>
                           <div class="form-group col-md-4">
                              <label for="alert_qty"><?= $this->lang->line('alert_qty'); ?></label>
                              <input type="number" class="form-control no_special_char" id="alert_qty" name="alert_qty" placeholder="" min="0" value="<?php print $alert_qty; ?>">
                              <span id="alert_qty_msg" style="display:none" class="text-danger"></span>
                           </div>

                           <div class="form-group col-md-4">
                              <label for="seller_points"><?= $this->lang->line('seller_points'); ?></label>
                              <input type="text" class="form-control only_currency" id="seller_points" name="seller_points" placeholder="" value="<?php print $seller_points; ?>">
                              <span id="seller_points_msg" style="display:none" class="text-danger"></span>
                           </div>

                           <div class="form-group col-md-4">
                              <label for="custom_barcode"><?= $this->lang->line('barcode'); ?></label>
                              <input type="text" class="form-control " id="custom_barcode" name="custom_barcode" placeholder="" value="<?php print $custom_barcode == '' ? mt_rand(1000000000, 9999999999) : $custom_barcode; ?>">
                              <span id="custom_barcode_msg" style="display:none" class="text-danger"></span>
                           </div>

                           <div class="form-group col-md-4">
                              <label for="seller_code">Seller Code</label>
                              <input type="text" class="form-control " id="seller_code" name="seller_code" placeholder="seller code" value="<?php print $seller_code; ?>">
                           </div>

                           <div class="form-group col-md-4">
                              <label for="seller_name">Seller Name</label>
                              <input type="text" class="form-control " id="seller_name" name="seller_name" placeholder="seller name" value="<?php print $seller_name; ?>">
                           </div>

                           <div class="form-group col-md-4">
                              <label for="supplier_item_code">Supplier Item Code</label>
                              <input type="text" class="form-control " id="supplier_item_code" name="supplier_item_code" placeholder="supplier item code" value="<?php print $supplier_item_code; ?>">
                              <span id="supplier_item_code_msg" style="display:none" class="text-danger"></span>
                           </div>

                           <div class="form-group col-md-4">
                              <label for="custom_barcode"><?= $this->lang->line('description'); ?></label>
                              <textarea type="text" class="form-control" id="description" name="description" placeholder=""><?php print $description; ?></textarea>
                              <span id="description_msg" style="display:none" class="text-danger"></span>
                           </div>

                           <div class="form-group col-md-4">
                              <label for="item_image"><?= $this->lang->line('select_image'); ?></label>
                              <input type="file" name="item_image" id="item_image">
                              <span id="item_image_msg" style="display:block;" class="text-danger">Max Width/Height: 1000px * 1000px & Size: 1MB </span>
                           </div>


                        </div>
                        <hr>
                        <div class="row">
                           <div class="form-group col-md-4">
                              <label for="discount_type"><?= $this->lang->line('discount_type'); ?></label>
                              <select class="form-control" id="discount_type" name="discount_type" style="width: 100%;">
                                 <option value='Percentage'>Percentage(%)</option>
                                 <option value='Fixed'>Fixed(<?= $CI->currency() ?>)</option>
                              </select>
                              <span id="discount_type_msg" style="display:none" class="text-danger"></span>
                           </div>
                           <div class="form-group col-md-4">
                              <label for="discount"><?= $this->lang->line('discount'); ?></label>
                              <input type="text" class="form-control only_currency" id="discount" name="discount" value="<?php print $discount; ?>">
                              <span id="discount_msg" style="display:none" class="text-danger"></span>
                           </div>

                        </div>
                        <hr>
                        <div class="row">
                           <div class="form-group col-md-4 ">
                              <label for="price"><?= $this->lang->line('price'); ?><span class="text-danger">*</span></label>
                              <input type="text" class="form-control only_currency" id="price" name="price" placeholder="Price of Item without Tax" value="<?php print $price; ?>">
                              <span id="price_msg" style="display:none" class="text-danger"></span>
                           </div>
                           <div class="form-group col-md-4">
                              <label for="tax_id"><?= $this->lang->line('tax'); ?><span class="text-danger">*</span></label>
                              <div class="input-group">
                                 <select class="form-control select2" id="tax_id" name="tax_id" style="width: 100%;">
                                    <?= get_tax_select_list($tax_id);  ?>
                                 </select>
                                 <span class="input-group-addon pointer" data-toggle="modal" data-target="#tax_modal" title="Add Customer"><i class="fa fa-plus-square-o text-primary fa-lg"></i></span>
                              </div>
                              <span id="tax_id_msg" style="display:none" class="text-danger"></span>
                           </div>
                           <div class="form-group col-md-4">
                              <label for="purchase_price"><?= $this->lang->line('purchase_price'); ?><span class="text-danger">*</span></label>
                              <input type="text" class="form-control only_currency" id="purchase_price" name="purchase_price" placeholder="Total Price with Tax Amount" value="<?php print $purchase_price; ?>" readonly=''>
                              <span id="purchase_price_msg" style="display:none" class="text-danger"></span>
                           </div>

                           <div class="form-group col-md-4">
                              <label for="tax_type"><?= $this->lang->line('tax_type'); ?><span class="text-danger">*</span></label>
                              <select class="form-control select2" id="tax_type" name="tax_type" style="width: 100%;">
                                 <?php
                                 $inclusive_selected = $exclusive_selected = '';
                                 if ($tax_type == 'Inclusive') {
                                    $inclusive_selected = 'selected';
                                 }
                                 if ($tax_type == 'Exclusive') {
                                    $exclusive_selected = 'selected';
                                 }

                                 ?>
                                 <option <?= $inclusive_selected ?> value="Inclusive">Inclusive</option>
                                 <option <?= $exclusive_selected ?> value="Exclusive">Exclusive</option>
                              </select>
                              <span id="tax_type_msg" style="display:none" class="text-danger"></span>

                           </div>
                           <div class="form-group col-md-4">
                              <label for="profit_margin"><?= $this->lang->line('profit_margin'); ?>(%) <i class="hover-q " data-container="body" data-toggle="popover" data-placement="top" data-content="<?= $this->lang->line('based_on_purchase_price'); ?>" data-html="true" data-trigger="hover" data-original-title="">
                                    <i class="fa fa-info-circle text-maroon text-black hover-q"></i>
                                 </i></label>
                              <input type="text" class="form-control only_currency" id="profit_margin" name="profit_margin" placeholder="Profit in %" value="<?php print $profit_margin; ?>">
                              <span id="profit_margin_msg" style="display:none" class="text-danger"></span>
                           </div>
                           <div class="form-group col-md-4">
                              <label for="sales_price" class="control-label"><?= $this->lang->line('sales_price'); ?><span class="text-danger">*</span></label>
                              <input type="text" class="form-control only_currency " id="sales_price" name="sales_price" placeholder="Sales Price" value="<?php print $sales_price; ?>">
                              <span id="sales_price_msg" style="display:none" class="text-danger"></span>
                           </div>

                           <div class="form-group col-md-4">
                              <label for="mrp"><?= $this->lang->line('mrp'); ?><i class="hover-q " data-container="body" data-toggle="popover" data-placement="top" data-content="<?= $this->lang->line('mrp_definition'); ?>" data-html="true" data-trigger="hover" data-original-title="">
                                    <i class="fa fa-info-circle text-maroon text-black hover-q"></i>
                                 </i></label>
                              <input type="text" class="form-control only_currency" id="mrp" name="mrp" placeholder="Maximum Retail Price" value="<?php print $mrp; ?>">
                              <span id="mrp_msg" style="display:none" class="text-danger"></span>
                           </div>

                           <div class="form-group col-md-4">
                              <label for="alph_price"><?= $this->lang->line('alpha_price'); ?>
                              </label>
                              <input required type="text" class="form-control" id="alph_price" name="alph_price" placeholder="Selling price in Alphabet" value="<?php print $alph_price; ?>">
                              <span id="alphaprice_msg" style="display:none" class="text-danger"></span>
                           </div>

                           <div class="form-group col-md-4">
                              <label for="rac_no"><?= $this->lang->line('rac_no'); ?>
                              </label>
                              <input required type="text" class="form-control" id="rac_no" name="rac_no" placeholder="Rack No" value="<?php print $rac_no; ?>">
                              <span id="rac_no_msg" style="display:none" class="text-danger"></span>
                           </div>

                           <div class="form-group col-md-4">
                              <label for="wholesale_price"><?= $this->lang->line('wholesale_price'); ?>
                              </label>
                              <input required type="text" class="form-control" id="wholesale_price" name="wholesale_price" placeholder="Wholesale price" value="<?php print $wholesale_price; ?>">
                              <span id="wholesale_price_msg" style="display:none" class="text-danger"></span>
                           </div>

                           <!-- for default batch no manoj -->
                           <div class="form-group col-md-4">
                              <label for="batch_no"><?= $this->lang->line('batch_no'); ?> <span class="text-danger">*</span></label> </label>
                              <input required type="text" class="form-control" id="batch_no" name="batch_no" placeholder="Batch name" value="<?php print $batch_no; ?>">
                              <span id="batch_no_msg" style="display:none" class="text-danger"></span>
                           </div>
                           <!-- for default batch no manoj -->

                        </div>
                        <hr>
                        <div class="row">
                           <div class="form-group col-md-4">
                              <label for="warehouse_id"><?= $this->lang->line('warehouse'); ?></label>
                              <select class="form-control" id="warehouse_id" name="warehouse_id" style="width: 100%;">
                                 <?= get_warehouse_select_list(); ?>
                              </select>
                              <span id="warehouse_id_msg" style="display:none" class="text-danger"></span>
                           </div>
                           <div class="form-group col-md-4">
                              <label for="adjustment_qty"><?= $this->lang->line('opening_stock'); ?></label>
                              <input type="text" class="form-control only_currency" id="adjustment_qty" name="adjustment_qty" value="<?php print $opening_stock; ?>">
                              <span id="adjustment_qty_msg" style="display:none" class="text-danger"></span>
                           </div>

                        </div>

                        <!-- for displaying available batches -->
                        <div class="box-body">
                           <div class="table-responsive" style="width: 100%">
                              <table class="table table-hover table-bordered" style="width:100%" id="">
                                 <thead class="custom_thead">
                                    <tr class="bg-primary">
                                       <th rowspan='2' style="width:5%">SL NO</th>
                                       <th rowspan='2' style="width:10%">Batch No</th>
                                       <th rowspan='2' style="width:15%">Purchase Price</th>
                                       <th rowspan='2' style="width:15%">Sales Price</th>
                                       <th rowspan='2' style="width:15%">Wholesale Price</th>
                                       <th rowspan='2' style="width:15%">MRP Price</th>
                                       <th rowspan='2' style="width:10%">Alphabet Price</th>
                                       <th rowspan='2' style="width:5%">Available Stock</th>
                                       <th rowspan='2' style="width:10%">Action</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php $num = 1;
                                    if (!empty($batches)): ?>
                                       <?php foreach ($batches as $batch): ?>
                                          <tr>
                                             <td><?php echo $num; ?></td>
                                             <td><?php echo htmlspecialchars($batch['batch_no']); ?></td>
                                             <td><?php echo store_number_format($batch['purchase_price'], 0); ?></td>
                                             <td><?php echo store_number_format($batch['sales_price'], 2); ?></td>
                                             <td><?php echo store_number_format($batch['wholesale_price'], 2); ?></td>
                                             <td><?php echo store_number_format($batch['mrp_price'], 2); ?></td>
                                             <td><?php echo $batch['alphabet_price']; ?></td>
                                             <td><?php echo $batch['quantity']; ?></td>
                                             <td>
                                                <!--  -->
                                                <a data-batch-no="<?php echo $batch['batch_no']; ?>"
                                                   data-purchase-price="<?php echo $batch['purchase_price']; ?>"
                                                   data-sales-price="<?php echo $batch['sales_price']; ?>"
                                                   data-wholesale-price="<?php echo $batch['wholesale_price']; ?>"
                                                   data-mrp-price="<?php echo $batch['mrp_price']; ?>"
                                                   data-alphabet-price="<?php echo $batch['alphabet_price']; ?>"
                                                   data-pro-id="<?php echo $q_id; ?>"
                                                   data-bat-id="<?php echo $batch['id']; ?>"
                                                   class="editBatchDetails btn btn-primary">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M11.943 1.25h.114c2.309 0 4.118 0 5.53.19c1.444.194 2.584.6 3.479 1.494c.793.793 1.203 1.78 1.42 3.005c.215 1.203.254 2.7.262 4.558a.75.75 0 0 1-1.5.006a73 73 0 0 0-.025-1.753H2.777A121 121 0 0 0 2.75 12c0 2.378.002 4.086.176 5.386c.172 1.279.5 2.05 1.069 2.62c.484.484 1.112.79 2.067.978c.976.19 2.235.246 3.944.26a.75.75 0 1 1-.012 1.5c-1.704-.014-3.092-.067-4.22-.288c-1.15-.226-2.084-.634-2.84-1.39c-.895-.895-1.3-2.035-1.494-3.48c-.19-1.411-.19-3.22-.19-5.529v-.114c0-2.309 0-4.118.19-5.53c.194-1.444.6-2.584 1.494-3.479c.895-.895 2.035-1.3 3.48-1.494c1.411-.19 3.22-.19 5.529-.19m-9.086 6h3.731l2.856-4.488c-1.127.017-2.052.06-2.83.164c-1.279.172-2.05.5-2.62 1.069c-.569.57-.896 1.34-1.068 2.619q-.04.302-.07.636m8.35-4.5a1 1 0 0 1-.074.153L8.366 7.25h4.722l2.83-4.448C14.857 2.75 13.576 2.75 12 2.75zm6.391.207L14.866 7.25h6.277q-.049-.572-.133-1.048c-.184-1.036-.498-1.7-1.005-2.207c-.537-.538-1.254-.86-2.407-1.038m.85 10.031a2.52 2.52 0 1 1 3.564 3.563l-4.282 4.282a6 6 0 0 1-.572.532a3.7 3.7 0 0 1-.683.423c-.206.098-.422.17-.732.273l-1.878.626a1.227 1.227 0 0 1-1.552-1.552l.626-1.878c.103-.31.175-.526.273-.732q.175-.365.423-.683c.14-.18.301-.341.532-.572zm2.503 1.06a1.02 1.02 0 0 0-1.442 0l-.131.132l.016.05c.082.236.238.548.533.843a2.2 2.2 0 0 0 .893.55l.131-.132a1.02 1.02 0 0 0 0-1.442m-1.265 2.708a3.76 3.76 0 0 1-1.442-1.442L15.258 18.3c-.272.273-.364.366-.44.464a2.2 2.2 0 0 0-.252.406c-.053.113-.096.236-.218.601l-.345 1.037l.189.19l1.037-.347c.365-.121.488-.164.6-.217q.218-.104.407-.252c.098-.076.191-.168.464-.44z" clip-rule="evenodd"/></svg>
                                                   </a>

                                               <a data-pro-id="<?php echo $q_id; ?>"
                                                  data-bat-id="<?php echo $batch['id']; ?>"
                                                  class="btn btn-danger delete-batch">
                                                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                       viewBox="0 0 24 24"><g fill="none" stroke="currentColor"
                                                       stroke-width="1.5">
                                                       <path stroke-linecap="round" d="M20.5 6h-17m15.333 2.5l-.46 6.9c-.177 2.654-.265 3.981-1.13 4.79s-2.196.81-4.856.81h-.774c-2.66 0-3.991 0-4.856-.81c-.865-.809-.954-2.136-1.13-4.79l-.46-6.9M9.5 11l.5 5m4.5-5l-.5 5"/>
                                                       <path d="M6.5 6h.11a2 2 0 0 0 1.83-1.32l.034-.103l.097-.291c.083-.249.125-.373.18-.479a1.5 1.5 0 0 1 1.094-.788C9.962 3 10.093 3 10.355 3h3.29c.262 0 .393 0 .51.019a1.5 1.5 0 0 1 1.094.788c.055.106.097.23.18.479l.097.291A2 2 0 0 0 17.5 6"/>
                                                  </g></svg>
                                               </a>
                                                 
                                             </td>
                                             <!-- Add more fields as needed -->
                                          </tr>
                                       <?php $num++; endforeach; ?>
                                    <?php else: ?>
                                       <tr>
                                          <td colspan="9">No batch data available.</td>
                                       </tr>
                                    <?php endif; ?>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                        <!-- for displaying available batches close -->

                        <div class="row variant_div">
                           <div class="col-md-12">
                              <div class="box box-info ">
                                 <div class="">
                                    <div class="box-header">
                                       <div class="col-md-6 col-md-offset-3 d-flex justify-content">
                                          <div class="input-group">
                                             <span class="input-group-addon" title="Select Items"><i class="fa fa-search"></i></span>
                                             <input type="text" class="form-control " placeholder="Search Variant" id="variant_search">
                                             <span class="input-group-addon pointer text-green" data-toggle="modal" data-target="#variant-modal" title="Click to Add New Variant"><i class="fa fa-plus"></i></span>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="box-body">
                                       <div class="table-responsive" style="width: 100%">
                                          <input type="hidden" value='1' id="hidden_rowcount" name="hidden_rowcount">
                                          <table class="table table-hover table-bordered" style="width:100%" id="variant_table">
                                             <thead class="custom_thead">
                                                <tr class="bg-primary">
                                                   <th rowspan='2' style="width:15%"><?= $this->lang->line('variant_name'); ?></th>
                                                   <th rowspan='2' style="width:10%"><?= $this->lang->line('sku'); ?></th>
                                                   <th rowspan='2' style="width:10%"><?= $this->lang->line('hsn'); ?></th>
                                                   <th rowspan='2' style="width:10%"><?= $this->lang->line('barcode'); ?></th>
                                                   <th rowspan='2' style="width:10%"><?= $this->lang->line('price'); ?>(<?= $CI->currency() ?>)</th>
                                                   <th rowspan='2' style="width:10%"><?= $this->lang->line('purchase_price'); ?>(<?= $CI->currency() ?>)</th>
                                                   <th rowspan='2' style="width:10%"><?= $this->lang->line('profit_margin'); ?></th>
                                                   <th rowspan='2' style="width:10%"><?= $this->lang->line('sales_price'); ?>(<?= $CI->currency() ?>)</th>
                                                   <th rowspan='2' style="width:10%"><?= $this->lang->line('mrp'); ?>(<?= $CI->currency() ?>)</th>
                                                   <th rowspan='2' style="width:10%"><?= $this->lang->line('opening_stock'); ?></th>
                                                   <th rowspan='2' style="width:5%"><?= $this->lang->line('action'); ?></th>
                                                </tr>
                                             </thead>
                                             <tbody>
                                                <?php if ($item_group != 'Single') {
                                                   echo $this->items_model->get_variants_list_in_row($q_id);
                                                } ?>
                                             </tbody>
                                          </table>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>

                        </div>
                        <!-- /row -->
                        <!-- /.box-body -->
                        <div class="box-footer">
                           <div class="col-sm-8 col-sm-offset-2 text-center">
                              <!-- <div class="col-sm-4"></div> -->
                              <?php
                              if ($item_name != "") {
                                 $btn_name = "Update";
                                 $btn_id = "update";
                              ?>
                                 <input type="hidden" name="q_id" id="q_id" value="<?php echo $q_id; ?>" />
                              <?php
                              } else {
                                 $btn_name = "Save";
                                 $btn_id = "save";
                              }

                              ?>
                              <div class="col-md-3 col-md-offset-3">
                                 <button type="button" id="<?php echo $btn_id; ?>" class=" btn btn-block btn-success" title="Save Data"><?php echo $btn_name; ?></button>
                              </div>
                              <div class="col-sm-3">
                                 <a href="<?= base_url('dashboard'); ?>">
                                    <button type="button" class="col-sm-3 btn btn-block btn-warning close_btn" title="Go Dashboard">Close</button>
                                 </a>
                              </div>
                           </div>
                        </div>
                        <!-- /.box-footer -->
                        <?= form_close(); ?>
                     </div>
                     <!-- /.box -->
                  </div>
                  <!--/.col (right) -->
               </div>

               <!-- /.row -->
         </section>
         <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->
      <?php include "footer.php"; ?>
      <!-- Add the sidebar's background. This div must be placed
            immediately after the control sidebar -->
      <div class="control-sidebar-bg"></div>
   </div>
   <!-- ./wrapper -->
   <!-- SOUND CODE -->
   <?php include "comman/code_js_sound.php"; ?>
   <!-- TABLES CODE -->
   <?php include "comman/code_js.php"; ?>
   <script src="<?php echo $theme_link; ?>js/items.js"></script>
   <script src="<?php echo $theme_link; ?>js/modals.js"></script>
   <script type="text/javascript">
      $("#discount_type").val('<?= $discount_type; ?>');
      <?php if (isset($q_id)) { ?>
         $("#store_id").attr('readonly', true);
      <?php } ?>
      $("#item_group").val("<?= $item_group; ?>").select2().trigger("change");

      <?php if (!empty($item_name)) { ?>
         $("#hidden_rowcount").val($("#variant_table  tr").length) + 1;
         calculate_purchase_price_of_all_row();
         calculate_sales_price_of_all_row();
      <?php } ?>

      <?php if ($child_bit == 1 || !empty($item_name)) { ?>
         $("#item_group").parent().addClass('hide');
      <?php } ?>

      // batch edit
      $(document).ready(function() {
         $('.editBatchDetails').on('click', function() {
            // Get data attributes

            var batch_no = $(this).data('batch-no');
            var p_price = $(this).data('purchase-price');
            var s_price = $(this).data('sales-price');
            var w_price = $(this).data('wholesale-price');
            var mrp = $(this).data('mrp-price');
            var alp_price = $(this).data('alphabet-price');
            var pro_id = $(this).data('pro-id');
            var bat_id = $(this).data('bat-id');

            // Set values to input fields
            $('#edit_batch').val(batch_no);
            $('#edit_pprice').val(p_price);
            $('#edit_sprice').val(s_price);
            $('#edit_wprice').val(w_price);
            $('#edit_mrp').val(mrp);
            $('#edit_alpprice').val(alp_price);

            $('#edit_pid').val(pro_id);
            $('#edit_batid').val(bat_id);

            // Open modal (simple show logic, replace with your modal plugin if needed)
            $('#batch_edit_modal').modal('show');

         });
      });

      // batch edit close
   </script>
   <!-- Make sidebar menu hughlighter/selector -->
   <script>
      $(".<?php echo basename(__FILE__, '.php'); ?>-active-li").addClass("active");
   </script>

</body>

</html>