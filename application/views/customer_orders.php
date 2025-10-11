<!DOCTYPE html>
<html>

<head>
  <!-- TABLES CSS CODE -->
  <?php include "comman/code_css.php"; ?>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth'
            });
            calendar.render();
        });
    </script>
</head>

<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">

    <!-- Left side column. contains the logo and sidebar -->

    <?php include "sidebar.php"; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          <?= $this->lang->line('order_list') ?>
          <small>Add/Update Orders</small>
        </h1>
        <ol class="breadcrumb">
          <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
          <li class="active"><?= $this->lang->line('order_list'); ?></li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="row">
          <!-- ********** ALERT MESSAGE START******* -->
          <?php include "comman/code_flashdata.php"; ?>
          <!-- ********** ALERT MESSAGE END******* -->
          <div class="col-xs-12">
            <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title"><?= $this->lang->line('order_list') ?></h3>
                <?php if ($CI->permissions('order_form')) { ?>
                  <div class="box-tools">
                    <a class="btn btn-block btn-info" href="<?php echo $base_url; ?>customer_order/">
                      <i class="fa fa-plus"></i> <?= $this->lang->line('order_form'); ?></a>
                  </div>
                <?php } ?>
              </div>
              <!-- /.box-header -->
                <div id='calendar'></div>

                <div hidden="" class="box-body">
                <table id="example2" class="table table-bordered custom_hover" width="100%">
                  <thead class="bg-gray ">
                    <tr>
                      <th class="text-center">
                        <input type="checkbox" class="group_check checkbox">
                      </th>
                      <th>customer_name</th>
                      <th>customer_address</th>
                      <th>order_date</th>
                      <th>delivery_date</th>
                      <th>quantity</th>
                      <th>total_amount</th>
                    </tr>
                  </thead>
                  <tbody>
                  

                  </tbody>

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

  <script src="<?php echo $theme_link; ?>js/customer_order.js"></script>

  <script type="text/javascript">
    function load_datatable() {
      //datatables
      var table = $('#example2').DataTable({

        "aLengthMenu": [
          [10, 25, 50, 100, 500],
          [10, 25, 50, 100, 500]
        ],


        /* FOR EXPORT BUTTONS START*/
        dom: '<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr><"pull-right margin-left-10 "B>>>tip',
        /* dom:'<"row"<"col-sm-12"<"pull-left"B><"pull-right">>> <"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',*/
        buttons: {
          buttons: [{
              className: 'btn bg-red color-palette btn-flat hidden delete_btn pull-left',
              text: 'Delete',
              action: function(e, dt, node, config) {
                multi_delete();
              }
            },
            {
              extend: 'copy',
              className: 'btn bg-teal color-palette btn-flat',
              footer: true,
              exportOptions: {
                columns: [1, 2, 3, 4, 5, 6]
              }
            },
            {
              extend: 'excel',
              className: 'btn bg-teal color-palette btn-flat',
              footer: true,
              exportOptions: {
                columns: [1, 2, 3, 4, 5, 6]
              }
            },
            {
              extend: 'pdf',
              className: 'btn bg-teal color-palette btn-flat',
              footer: true,
              exportOptions: {
                columns: [1, 2, 3, 4, 5, 6]
              }
            },
            {
              extend: 'print',
              className: 'btn bg-teal color-palette btn-flat',
              footer: true,
              exportOptions: {
                columns: [1, 2, 3, 4, 5, 6]
              }
            },
            {
              extend: 'csv',
              className: 'btn bg-teal color-palette btn-flat',
              footer: true,
              exportOptions: {
                columns: [1, 2, 3, 4, 5, 6]
              }
            },
            {
              extend: 'colvis',
              className: 'btn bg-teal color-palette btn-flat',
              footer: true,
              text: 'Columns'
            },

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
          "url": "<?php echo site_url('customer_order/ajax_list') ?>",
          "type": "POST",
          "data": {

          },
          complete: function(data) {
           
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
        "columnDefs": [{
            "targets": [0, 10], //first column / numbering column
            "orderable": false, //set not orderable
          },
          {
            "targets": [0],
            "className": "text-center",
          },

        ],

      });
      new $.fn.dataTable.FixedHeader(table);
    }


    $(document).ready(function() {
      // load_datatable();
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: [
                {
                    title: 'Meeting with Client',
                    start: '2025-08-12',
                    end: '2025-08-12'
                },
                {
                    title: 'Project Deadline',
                    start: '2025-08-15',
                    end: '2025-08-15'
                },
                {
                    title: 'Conference',
                    start: '2025-08-20',
                    end: '2025-08-22'
                },
                {
                    title: 'Team Lunch',
                    start: '2025-08-25T13:00:00',
                    end: '2025-08-25T14:30:00'
                }
            ]
        });

        calendar.render();
    });
  </script>
  <script>
    $(".<?php echo basename(__FILE__, '.php'); ?>-active-li").addClass("active");
  </script>

</body>

</html>