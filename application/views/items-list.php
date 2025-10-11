<!DOCTYPE html>
<html>

<head>
<!-- CSRF Meta Tags -->
<meta name="csrf-token-name" content="<?php echo $this->security->get_csrf_token_name(); ?>">
<meta name="csrf-token-value" content="<?php echo $this->security->get_csrf_hash(); ?>">

<!-- TABLES CSS CODE -->
<?php include"comman/code_css.php"; ?>

<!-- Lightbox -->
<link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/lightbox/ekko-lightbox.css">
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Left side column. contains the logo and sidebar -->
  
  <?php include"sidebar.php"; ?>
  <style>
    @media(max-width: 480px){
      .box-header>.box-tools {
          position: absolute;
          right: -13px;
          top: -106px;
      }
    }
  </style>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?=$page_title;?>
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><?=$page_title;?></li>
      </ol>
    </section>
    
    <!-- Warehouse wise stock view -->
    <div class="view_warehouse_wise_stock_item">
    </div>
    <!-- Warehouse wise stock view end-->

    <!-- Main content -->
    <?= form_open('#', array('class' => '', 'id' => 'table_form')); ?>
    <input type="hidden" id='base_url' value="<?=$base_url;?>">

    <section class="content">
      <div class="row">
        <!-- ********** ALERT MESSAGE START******* -->
        <?php include"comman/code_flashdata.php"; ?>
        <!-- ********** ALERT MESSAGE END******* -->
        <div class="col-xs-12">
          
          <div class="box box-primary">
            <div class="box-header with-border">
              <!-- <h3 class="box-title"><?=$page_title;?></h3> -->
              

                <div class="row">
                    <div class="col-md-12">                                  
                      <!-- Warehouse Code -->
                      <?php if(warehouse_module()){ ?>
                        <div class="col-md-3">
                    <?php $this->load->view('warehouse/warehouse_code',array('show_warehouse_select_box'=>true,'div_length'=>'',
                      'label_length'=>'','show_all'=>'true','show_all_option'=>true,'remove_star'=>true)); ?>
                    <!-- Warehouse Code end -->
                    </div>
                    <?php } ?>
                    <?php if(service_module() && $CI->permissions('services_view')){ ?>
                    <div class="col-md-3">
                        <label for="item_type" class=" control-label">Item Type</label>
                          <select class="form-control select2" id="item_type" name="item_type"  style="width: 100%;">
                            <?php if($CI->permissions('items_view') && $CI->permissions('services_view')){?>
                              <option value=''>All</option>
                            <?php } ?>  
                            <?php if($CI->permissions('items_view')){?>
                              <option value='Items'>Items</option>
                            <?php } ?>
                            <?php if($CI->permissions('services_view')){?>
                              <option value='Services'>Services</option>
                            <?php } ?>
                          </select>
                    </div>
                  <?php }else{ ?>
                    <input type="hidden" id="item_type" value="Items">
                    <?php } ?>
                    
                  </div>
                </div>

              <?php if($CI->permissions('items_add') || $CI->permissions('services_add')) { ?>
              <div class="box-tools">      
                <?php if($CI->permissions('items_add')){ ?>          
                <a class="btn btn-info margin" href="<?php echo $base_url; ?>items/add">
                <i class="fa fa-plus " ></i> <?= $this->lang->line('create_item'); ?></a>
                <?php } ?>
                <?php if(service_module() && $CI->permissions('services_add')){ ?>
                <a class="btn btn-success margin" href="<?php echo $base_url; ?>services/add">
                <i class="fa fa-plus " ></i> <?= $this->lang->line('create_service'); ?></a>
              <?php } ?>
             <?php } ?>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example2" class="table table-bordered custom_hover" width="100%">
                <thead class="bg-gray">
                <tr>
                  <th class="text-center">
                    <input type="checkbox" class="group_check checkbox">
                  </th>
                  <th><?= $this->lang->line('image'); ?></th>
                  <th><?= $this->lang->line('item_code'); ?></th>
                  <th><?= $this->lang->line('item_name'); ?></th>
                  <th><?= $this->lang->line('brand'); ?></th>
                  <th><?= $this->lang->line('category'); ?>/<br><?= $this->lang->line('item_type'); ?></th>
                  <th><?= $this->lang->line('unit'); ?></th>
                  <th><?= $this->lang->line('stock'); ?></th>
                  <th><?= $this->lang->line('alert_quantity'); ?></th>
                  <th><?= $this->lang->line('sales_price'); ?></th>
                  <th><?= $this->lang->line('tax'); ?></th>
                  <th><?= $this->lang->line('status'); ?></th>
                  <th class="no-sort" data-orderable="false"><?= $this->lang->line('action'); ?></th>
                </tr>
                </thead>
                <tbody>
				
                </tbody>
{{ ... }}
               
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
     <?= form_close();?>
  </div>
  <!-- /.content-wrapper -->
  <?php include"footer.php"; ?>
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- SOUND CODE -->
<?php include"comman/code_js_sound.php"; ?>
<!-- TABLES CODE -->
<?php include"comman/code_js.php"; ?>
<!-- Lightbox -->
<script src="<?php echo $theme_link; ?>plugins/lightbox/ekko-lightbox.js"></script>
<!-- Bootstrap Modal JS -->
<script src="<?php echo $theme_link; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- View Item Modal -->
<?php $this->load->view('modals/view_item_modal'); ?>

<!-- Initialize modals -->
<script>
$(document).ready(function() {
    // Initialize lightbox
    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });
    
    // Make sure modals can be shown
    $('body').on('shown.bs.modal', '.modal', function() {
        $(this).css('display', 'block');
    });
});
</script>

<script type="text/javascript">
// Global AJAX setup to include CSRF token in all AJAX requests
$.ajaxSetup({
    data: function() {
        // Get CSRF token values
        var csrfTokenName = $('meta[name="csrf-token-name"]').attr('content') || '';
        var csrfTokenValue = $('meta[name="csrf-token-value"]').attr('content') || '';
        
        // Return object with CSRF token if both name and value exist
        if (csrfTokenName && csrfTokenValue) {
            var data = {};
            data[csrfTokenName] = csrfTokenValue;
            return data;
        }
        return {};
    }(),
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token-value"]').attr('content') || ''
    }
});

  $(document).on('click', '[data-toggle="lightbox"]', function(event) {
    event.preventDefault();
    $(this).ekkoLightbox();
  });

  // Function to view item details
  function viewItemDetails(itemId) {
    console.log('=== DEBUG: viewItemDetails called with ID:', itemId);
    
    // Validate itemId
    if (!itemId || itemId === 'undefined' || itemId === 'null') {
        console.error('Invalid item ID:', itemId);
        alert('Error: Invalid item ID');
        return;
    }
    
    var $modal = $('#view-item-modal');
    
    // Check if modal exists
    if ($modal.length === 0) {
      console.error('Modal with ID #view-item-modal not found in the DOM');
      alert('Error: Could not find the modal element. Please check if the modal HTML is properly loaded.');
      return;
    }
    
    // Show loading state
    $modal.find('.modal-body').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Loading item details...</p></div>');
    
    // Show modal first
    $modal.modal('show');
    
    // Get base URL
    var baseUrl = $('#base_url').val() || window.location.origin + '/';
    var ajaxUrl = baseUrl + 'items/get_item_details';
    
    // Get CSRF token values
    var csrfTokenName = $('meta[name="csrf-token-name"]').attr('content') || '';
    var csrfTokenValue = $('meta[name="csrf-token-value"]').attr('content') || '';
    
    console.log('Sending AJAX request to:', ajaxUrl);
    console.log('Item ID:', itemId);
    console.log('CSRF Token Name:', csrfTokenName);
    console.log('CSRF Token Value:', csrfTokenValue ? '*** (exists)' : 'MISSING!');
    
    // Make the AJAX request
    $.ajax({
      url: ajaxUrl,
      type: 'POST',
      dataType: 'json',
      data: {
        item_id: itemId,
        [csrfTokenName]: csrfTokenValue
      },
      headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfTokenValue
      },
      success: function(response) {
        console.log('AJAX Response:', response);
        
        if (response.status == 'success' && response.data) {
          // Restore the modal structure and populate with data
          var modalContent = `
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label><strong>Item Name:</strong></label>
                  <p id="view_item_name">${response.data.item_name || 'N/A'}</p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label><strong>Item Code:</strong></label>
                  <p id="view_item_code">${response.data.item_code || 'N/A'}</p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label><strong>Category:</strong></label>
                  <p id="view_category">${response.data.category_name || 'N/A'}</p>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label><strong>Brand:</strong></label>
                  <p id="view_brand">${response.data.brand_name || 'N/A'}</p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label><strong>Unit:</strong></label>
                  <p id="view_unit">${response.data.unit_name || 'N/A'}</p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label><strong>Stock:</strong></label>
                  <p id="view_stock">${response.data.stock || '0'}</p>
                </div>
              </div>
            </div>
            <div class="row">

              <div class="col-md-4">
                <div class="form-group">
                  <label><strong>Purchase Price:</strong></label>
                  <p id="view_purchase_price">${response.data.purchase_price_formatted || '0.00'}</p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label><strong>Sales Price:</strong></label>
                  <p id="view_sales_price">${response.data.price_formatted || '0.00'}</p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label><strong>Alert Quantity:</strong></label>
                  <p id="view_alert_qty">${response.data.alert_quantity || '0'}</p>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Supplier:</strong></label>
                  <p id="view_supplier">${response.data.supplier_name || 'N/A'}</p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Supplier Item Code:</strong></label>
                  <p id="view_supplier_item_code">${response.data.supplier_item_code || 'N/A'}</p>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label><strong>Description:</strong></label>
                  <p id="view_description">${response.data.description || 'No description available'}</p>
                </div>
              </div>
            </div>
            ${response.data.batches && response.data.batches.length > 0 ? `
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label><strong>Batches:</strong></label>
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>Batch No</th>
                          <th>Purchase Price</th>
                          <th>Sales Price</th>
                          <th>Wholesale Price</th>
                          <th>MRP Price</th>
                          <th>Alphabet Price</th>
                          <th>Quantity</th>
                        </tr>
                      </thead>
                      <tbody>
                        ${response.data.batches.map(batch => `
                          <tr>
                            <td>${batch.batch_no || 'N/A'}</td>
                            <td>${batch.purchase_price ? parseFloat(batch.purchase_price).toFixed(2) : '0.00'}</td>
                            <td>${batch.sales_price ? parseFloat(batch.sales_price).toFixed(2) : '0.00'}</td>
                            <td>${batch.wholesale_price ? parseFloat(batch.wholesale_price).toFixed(2) : '0.00'}</td>
                            <td>${batch.mrp_price ? parseFloat(batch.mrp_price).toFixed(2) : '0.00'}</td>
                            <td>${batch.alphabet_price ? parseFloat(batch.alphabet_price).toFixed(2) : '0.00'}</td>
                            <td>${batch.quantity || '0'}</td>
                          </tr>
                        `).join('')}
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            ` : `
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label><strong>Batches:</strong></label>
                  <p class="text-muted">No batches found for this item.</p>
                </div>
              </div>
            </div>
            `}`;
          
          // Update the modal body with the complete content
          $modal.find('.modal-body').html(modalContent);
        } else {
          var errorMsg = response.message || 'Failed to load item details';
          console.error('Error:', errorMsg);
          $modal.find('.modal-body').html(`
            <div class="alert alert-danger">
              <i class="fa fa-exclamation-triangle"></i> ${errorMsg}
            </div>
          `);
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX Error Details:');
        console.error('Status:', status);
        console.error('Error:', error);
        console.error('Response Text:', xhr.responseText);
        console.error('Status Code:', xhr.status);
        
        var errorMsg = 'Error loading item details. ';
        if (xhr.status === 0) {
          errorMsg += 'No connection. Please check your network.';
        } else if (xhr.status === 404) {
          errorMsg += 'Requested page not found. [404]';
        } else if (xhr.status === 500) {
          errorMsg += 'Internal Server Error [500].';
        } else if (error === 'parsererror') {
          errorMsg += 'Requested JSON parse failed.';
        } else if (error === 'timeout') {
          errorMsg += 'Time out error.';
        } else if (error === 'abort') {
          errorMsg += 'Ajax request aborted.';
        } else {
          errorMsg += 'Uncaught Error: ' + xhr.responseText;
        }
        
        $modal.find('.modal-body').html(`
          <div class="alert alert-danger">
            <i class="fa fa-exclamation-triangle"></i> ${errorMsg}
            <hr>
            <p><small>URL: ${ajaxUrl}</small></p>
            <p><small>Status: ${xhr.status} (${xhr.statusText})</small></p>
            <pre style="max-height: 200px; overflow: auto; background: #f8f9fa; padding: 10px; border-radius: 4px; margin-top: 10px;">${xhr.responseText || 'No response data'}</pre>
          </div>
        `);
      }
    });
  }
</script>
<script type="text/javascript">
  function load_datatable(){

    

    var table = $('#example2').DataTable({ 
        "aLengthMenu": [[10, 25, 50, 100, 500], [10, 25, 50, 100, 500]],
      /* FOR EXPORT BUTTONS START*/
  dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr><"pull-right margin-left-10 "B>>>tip',
 /* dom:'<"row"<"col-sm-12"<"pull-left"B><"pull-right">>> <"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',*/
      buttons: {
        buttons: [
            {
                className: 'btn bg-red color-palette btn-flat hidden delete_btn pull-left',
                text: 'Delete',
                action: function ( e, dt, node, config ) {
                    multi_delete();
                }
            },
            { extend: 'copy', className: 'btn bg-teal color-palette btn-flat',exportOptions: { columns: [2,3,4,5,6,7,8,9,10,11]} },
            { extend: 'excel', className: 'btn bg-teal color-palette btn-flat',exportOptions: { columns: [2,3,4,5,6,7,8,9,10,11]} },
            { extend: 'pdf', className: 'btn bg-teal color-palette btn-flat',exportOptions: { columns: [2,3,4,5,6,7,8,9,10,11]} },
            { extend: 'print', className: 'btn bg-teal color-palette btn-flat',exportOptions: { columns: [2,3,4,5,6,7,8,9,10,11]} },
            { extend: 'csv', className: 'btn bg-teal color-palette btn-flat',exportOptions: { columns: [2,3,4,5,6,7,8,9,10,11]} },
            { extend: 'colvis', className: 'btn bg-teal color-palette btn-flat',text:'Columns' },  

            ]
        },
        /* FOR EXPORT BUTTONS END */

        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
        "responsive": true,
        language: {
            processing: '<div class="text-primary bg-primary" style="position: relative;z-index:100;overflow: visible;">Processing...</div>'
        },
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo site_url('items/ajax_list')?>",
            "type": "POST",
            "data": {
                      warehouse_id: $("#warehouse_id").val(),
                      item_type: $("#item_type").val(),
                    },
            complete: function (data) {
             $('.column_checkbox').iCheck({
                checkboxClass: 'icheckbox_square-orange',
                /*uncheckedClass: 'bg-white',*/
                radioClass: 'iradio_square-orange',
                increaseArea: '10%' // optional
              });
             call_code();
              //$(".delete_btn").hide();
             },

        },

        //Set column definition initialisation properties.
        "columnDefs": [
        { 
            "targets": [0], // checkbox column
            "orderable": false,
            "className": "text-center"
        },
        {
            "targets": -1, // action column (last column)
            "orderable": false,
            "searchable": false,
            "className": "text-center",
            "render": function(data, type, row, meta) {
                try {
                    // Get the item ID from the first column (row[0]) which contains checkbox HTML
                    var itemId = '';
                    if (row[0] && typeof row[0] === 'string') {
                        // Extract ID from checkbox value attribute
                        var match = row[0].match(/value=([^'"]*)/);
                        if (match) {
                            itemId = match[1];
                        }
                    }
                    
                    // Fallback: try to get from row data if available
                    if (!itemId && row.length > 0) {
                        itemId = row[0];
                    }
                    
                    console.log('Extracted item ID:', itemId, 'from row[0]:', row[0]);
                    
                    // Check if data is empty or not a string
                    if (typeof data !== 'string') {
                        data = '';
                    }
                    
                    // Create view button with data attribute for event delegation
                    var viewButton = '<li><a href="javascript:void(0);" class="view-item-btn" data-item-id="' + itemId + '"><i class="fa fa-fw fa-eye text-info"></i> View</a></li>';
                    
                    // If data already contains a dropdown, add view button to it
                    if (data.includes('dropdown-menu')) {
                        return data.replace('</ul>', viewButton + '</ul>');
                    }
                    
                    // Otherwise, create a new dropdown with view button
                    return '<div class="btn-group">' +
                        '<button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown">' +
                            'Actions <span class="caret"></span>' +
                        '</button>' +
                        '<ul class="dropdown-menu dropdown-menu-right">' +
                            viewButton +
                        '</ul>' +
                    '</div>';
                } catch (e) {
                    console.error('Error rendering action buttons:', e);
                    return '';
                }
            }
        }
        ],
    });
    new $.fn.dataTable.FixedHeader( table );
    
    // Add event delegation for view buttons
    $(document).on('click', '.view-item-btn', function(e) {
        e.preventDefault();
        var itemId = $(this).data('item-id');
        viewItemDetails(itemId);
    });
  }
$(document).ready(function() {
    // Initialize datatables
    load_datatable();
});
$("#warehouse_id,#item_type").on("change",function(){
    $('#example2').DataTable().destroy();
    load_datatable();
});
</script>


<script src="<?php echo $theme_link; ?>js/items.js"></script>

<!-- Make sidebar menu hughlighter/selector -->
<script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>
		
</body>
</html>
