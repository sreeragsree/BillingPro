<!DOCTYPE html>
<html>

<head>
    <!-- TABLES CSS CODE -->
    <?php include "comman/code_css.php"; ?>
    <style type="text/css">
        .badge {
            color: #190b0b;
            background-color: #c2c2c2;
        }
    </style>

    <!-- DataTables CSS (Ensure your code_css.php includes this) -->
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <?php include "sidebar.php"; ?>

    <div class="content-wrapper">
        <section class="content-header">
            <h1>Customer Credits</h1>
        </section>

        <?= form_open('#', ['id' => 'table_form']); ?>
        <input type="hidden" id="base_url" value="<?= $base_url; ?>">

        <section class="content">
            <div class="row">
                <?php include "comman/code_flashdata.php"; ?>
                <div class="col-xs-12">
                    <div class="box box-primary">
                        <div class="box-body">
                            <table id="example2" class="table table-bordered custom_hover" style="width:100%">
                                <thead class="bg-gray ">
                                <tr>
                                    <th class="text-center">
                                        <input type="checkbox" class="group_check checkbox">
                                    </th>
                                    <th>Sales Bill</th>
                                    <th><?= $this->lang->line('total'); ?></th>
                                    <th>Paid Amount</th>
                                    <th>Balance Due</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                <tr>
                                    <th></th>
                                    <th>Total</th>
                                    <th></th>  <!-- totals will be populated by DataTables footerCallback if needed -->
                                    <th></th>
                                    <th></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?= form_close(); ?>
    </div>

    <?php include "footer.php"; ?>
    <div class="control-sidebar-bg"></div>
</div>

<!-- SOUND CODE -->
<?php include "comman/code_js_sound.php"; ?>
<!-- TABLES JS -->
<?php include "comman/code_js.php"; ?>

<!-- bootstrap datepicker -->
<script src="<?php echo $theme_link; ?>plugins/datepicker/bootstrap-datepicker.js"></script>
<script>
    $('.datepicker').datepicker({
        autoclose: true,
        format: 'dd-mm-yyyy',
        todayHighlight: true
    });
</script>

<script>
    function load_datatable(customer_id) {
        // Debug alert to confirm the function is called
        $('#example2').DataTable({
            destroy: true,  // To allow reinitialization if needed
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "<?php echo site_url('customers/viewcredits_detail'); ?>",
                type: "POST",
                data: function (d) {
                    d.customer_id = customer_id;
                }
            },
            order: [[1, 'desc']],  // order by sales_code descending
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            buttons: [
                { extend: 'copy', className: 'btn bg-teal btn-flat' },
                { extend: 'excel', className: 'btn bg-teal btn-flat' },
                { extend: 'pdf', className: 'btn bg-teal btn-flat' },
                { extend: 'print', className: 'btn bg-teal btn-flat' },
                { extend: 'csv', className: 'btn bg-teal btn-flat' },
                { extend: 'colvis', className: 'btn bg-teal btn-flat', text: 'Columns' }
            ],
            dom: '<"row margin-bottom-12"<"col-sm-12"<"pull-left"B><"pull-right"fr>>>tip',
            columnDefs: [
                {
                    targets: [0],  // Checkbox column, not orderable
                    orderable: false,
                    className: "text-center"
                }
            ],
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();

                var intVal = function (i) {
                    i = typeof i === 'string' ? i.replace(/[\$,]/g, '') : i;
                    return parseFloat(i) || 0;
                };

                // Total over all pages
                var totalGrand = api.column(2).data().reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

                var totalPaid = api.column(3).data().reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

                var totalDue = api.column(4).data().reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

                // Update footer
                $(api.column(2).footer()).html(totalGrand.toFixed(2));
                $(api.column(3).footer()).html(totalPaid.toFixed(2));
                $(api.column(4).footer()).html(totalDue.toFixed(2));
            }
        });
    }

    $(document).ready(function () {
        const customer_id = <?= json_encode($customer_id ?? 0); ?>;
        if (customer_id && customer_id > 0) {
            load_datatable(customer_id);
        } else {
            alert('Invalid or missing customer ID!');
        }
    });
</script>

<script src="<?php echo $theme_link; ?>js/customers.js"></script>
<script>
    $(".<?= basename(__FILE__, '.php'); ?>-active-li").addClass("active");
</script>

</body>

</html>
