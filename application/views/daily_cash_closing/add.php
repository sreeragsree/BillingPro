<!DOCTYPE html>
<html>
<head>
<?php $this->load->view('comman/code_css.php');?>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
  <?php $this->load->view('sidebar');?>
  <div class="content-wrapper">
    <section class="content-header">
      <h1><?= $page_title?></h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Add Daily Cash Closing</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <?php $this->load->view('comman/code_flashdata');?>
        <div class="col-md-8">
          <div class="box box-primary">
            <div class="box-body">
              <?= form_open('daily_cash_closing/save', array('id' => 'dcc_form')); ?>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="closing_date">Closing Date</label>
                      <input type="text" class="form-control datepicker" name="closing_date" id="closing_date" value="<?= date('Y-m-d'); ?>">
                    </div>

                    <div class="form-group">
                      <label for="opening_cash">Opening Cash</label>
                      <input type="text" class="form-control" name="opening_cash" id="opening_cash" value="<?= isset($opening_cash) ? store_number_format($opening_cash) : '0.00'; ?>" <?= (!can_override_opening()) ? 'readonly' : ''; ?>>
                    </div>

                    <div class="form-group">
                      <label for="closing_cash">Actual Cash Counted</label>
                      <input type="text" class="form-control" name="closing_cash" id="closing_cash" value="<?= isset($closing_cash) ? store_number_format($closing_cash) : '0.00'; ?>" <?= (!empty($already_closed)) ? 'readonly' : ''; ?> >
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <h4>Summary (Today)</h4>
                    <?php $s = isset($summary) ? $summary : array(); ?>
                    <table class="table table-condensed">
                      <tr><th>Cash Sales</th><td class="text-right" id="sum_cash_sales"><?= isset($s['cash_sales']) ? store_number_format($s['cash_sales']) : '0.00'; ?></td></tr>
                      <tr><th>Card Sales</th><td class="text-right"><?= isset($s['card_sales']) ? store_number_format($s['card_sales']) : '0.00'; ?></td></tr>
                      <tr><th>UPI/Online</th><td class="text-right"><?= isset($s['upi_sales']) ? store_number_format($s['upi_sales']) : '0.00'; ?></td></tr>
                      <tr><th>Expenses</th><td class="text-right" id="sum_expenses"><?= isset($s['expenses']) ? store_number_format($s['expenses']) : '0.00'; ?></td></tr>
                      <tr><th>Refunds</th><td class="text-right" id="sum_refunds"><?= isset($s['refunds']) ? store_number_format($s['refunds']) : '0.00'; ?></td></tr>
                      <tr class="active"><th>Expected Cash</th><th class="text-right" id="expected_cash">0.00</th></tr>
                      <tr class="active"><th>Difference</th><th class="text-right" id="difference">0.00</th></tr>
                    </table>
                  </div>
                </div>

                <div class="form-group">
                  <label for="note">Note</label>
                  <textarea class="form-control" name="note" id="note" rows="3" <?= (!empty($already_closed)) ? 'readonly' : ''; ?>><?= isset($note) ? html_escape($note) : ''; ?></textarea>
                </div>

                <div class="form-group">
                  <?php if (!empty($already_closed)) { ?>
                    <div class="alert alert-warning">Closing already recorded for today. You cannot add another closing.</div>
                    <a href="<?php echo $base_url; ?>daily_cash_closing" class="btn btn-default">Back</a>
                  <?php } else { ?>
                    <button type="button" id="btn_confirm_close" class="btn btn-primary">Close Day</button>
                    <a href="<?php echo $base_url; ?>daily_cash_closing" class="btn btn-default">Cancel</a>
                  <?php } ?>
                </div>

                <input type="hidden" name="expected_cash" id="expected_cash_input" value="<?= isset($expected_cash) ? $expected_cash : '0.00'; ?>">
                <input type="hidden" name="difference" id="difference_input" value="<?= isset($difference) ? $difference : '0.00'; ?>">

              <?= form_close(); ?>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="box box-info">
            <div class="box-header with-border"><h4>Help</h4></div>
            <div class="box-body">
              <p>Opening balance is carried from last closing. Only admins can override.</p>
              <p>Expected Cash = Opening + Cash Sales - Expenses - Refunds + Cash In - Cash Out</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <?php $this->load->view('footer.php');?>
  <div class="control-sidebar-bg"></div>
</div>

<?php $this->load->view('comman/code_js.php');?>
<script>
function parseNum(v){ return parseFloat(String(v).replace(/[,\s]/g,'')) || 0; }
function updateExpected(){
  var opening = parseNum($('#opening_cash').val());
  var cash_sales = parseNum($('#sum_cash_sales').text());
  var expenses = parseNum($('#sum_expenses').text());
  var refunds = parseNum($('#sum_refunds').text());
  var cash_in = 0; var cash_out = 0;
  var expected = opening + cash_sales - expenses - refunds + cash_in - cash_out;
  $('#expected_cash').text(expected.toFixed(2));
  $('#expected_cash_input').val(expected.toFixed(2));
  var actual = parseNum($('#closing_cash').val());
  var diff = actual - expected;
  $('#difference').text(diff.toFixed(2));
  $('#difference_input').val(diff.toFixed(2));
  $('#difference').closest('tr').removeClass('text-danger text-success');
  if (diff < 0) $('#difference').closest('tr').addClass('text-danger');
  if (diff > 0) $('#difference').closest('tr').addClass('text-success');
}

$(function(){
  // Ensure we initialize only the closing_date picker with the correct format
  try { $('#closing_date').datepicker('destroy'); } catch(e) {}
  $('#closing_date').datepicker({format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true});
  // Force the picker's displayed date to the server-provided value to avoid parsing mismatches
  (function(){
    var serverDate = '<?= date('Y-m-d'); ?>';
    if (serverDate && serverDate.indexOf('-')>-1) {
      var p = serverDate.split('-');
      if (p.length===3) {
        var sd = new Date(parseInt(p[0],10), parseInt(p[1],10)-1, parseInt(p[2],10));
        if (!isNaN(sd.getTime())) {
          try { $('#closing_date').datepicker('update', sd); } catch(e) {}
        }
      }
    }
  })();
  // Force canonical YYYY-MM-DD display on change to avoid plugin formatting issues
  $('#closing_date').on('changeDate', function(e){
    var d = e.date instanceof Date ? e.date : new Date($(this).val());
    if (isNaN(d.getTime())) return;
    var yyyy = d.getFullYear();
    var mm = ('0'+(d.getMonth()+1)).slice(-2);
    var dd = ('0'+d.getDate()).slice(-2);
    $(this).val(yyyy+'-'+mm+'-'+dd);
    updateExpected();
  });
  // Also normalize on blur (in case user picks via keyboard)
  $('#closing_date').on('blur', function(){
    var v = $(this).val();
    var d = new Date(v);
    if (isNaN(d.getTime())) {
      // try parsing dd-mm-yyyy
      var parts = v.split(/[-\/\.\s]/);
      if (parts.length==3) {
        // detect dd-mm-yyyy vs yyyy-mm-dd
        if (parts[0].length==4) { d = new Date(parts[0]+'-'+parts[1]+'-'+parts[2]); }
        else { d = new Date(parts[2]+'-'+parts[1]+'-'+parts[0]); }
      }
    }
    if (!isNaN(d.getTime())) {
      var yyyy = d.getFullYear();
      var mm = ('0'+(d.getMonth()+1)).slice(-2);
      var dd = ('0'+d.getDate()).slice(-2);
      $(this).val(yyyy+'-'+mm+'-'+dd);
      updateExpected();
    }
  });
  updateExpected();
  $('#opening_cash,#closing_cash').on('keyup change', updateExpected);
  $('#btn_confirm_close').on('click', function(){
    updateExpected();
    var diff = parseNum($('#difference_input').val());
    var msg = 'Confirm closing for '+$('#closing_date').val()+"\n"+
              'Expected Cash: '+$('#expected_cash').text()+"\n"+
              'Actual Cash: '+$('#closing_cash').val()+"\n"+
              'Difference: '+$('#difference').text()+"\n\n"+
              'Proceed to close day?';
    if (confirm(msg)){
      $('#dcc_form').submit();
    }
  });
});
</script>
</body>
</html>
