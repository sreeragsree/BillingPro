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
        <li class="active">Daily Cash Closing</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <?php $this->load->view('comman/code_flashdata');?>
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <a class="btn btn-primary" href="<?php echo $base_url; ?>daily_cash_closing/add">Add Daily Cash Closing</a>
            </div>
            <div class="box-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Opening Cash</th>
                    <th>Cash Sales</th>
                    <th>Expenses</th>
                    <th>Closing Cash</th>
                    <th>Note</th>
                    <th>Created At</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(!empty($records)) { $i=1; foreach($records as $r){ ?>
                    <tr>
                      <td><?= $i++; ?></td>
                      <td><?= $r->closing_date; ?></td>
                      <td class="text-right"><?= store_number_format($r->opening_cash); ?></td>
                      <td class="text-right"><?= store_number_format($r->cash_sales); ?></td>
                      <td class="text-right"><?= store_number_format($r->expenses); ?></td>
                      <td class="text-right"><?= store_number_format($r->closing_cash); ?></td>
                      <td><?= html_escape($r->note); ?></td>
                      <td><?= $r->created_at; ?></td>
                      <td>
                        <?php if (permissions('daily_cash_closing_view')) { ?>
                          <a href="<?php echo $base_url; ?>daily_cash_closing/report/<?= $r->id; ?>" target="_blank" class="btn btn-xs btn-default" title="View PDF"><i class="fa fa-file-pdf-o text-danger"></i></a>
                        <?php } ?>
                      </td>
                    </tr>
                  <?php } } else { ?>
                    <tr><td colspan="9" class="text-center">No records found.</td></tr>
                  <?php } ?>
                </tbody>
              </table>
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
</body>
</html>
