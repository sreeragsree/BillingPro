<div class="modal fade " id="batch-modal" tabindex='-1'>
  <?= form_open('#', array('class' => '', 'id' => 'batch-form')); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header header-custom">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <stax_number aria-hidden="true">&times;</stax_number></button>
        <h4 class="modal-title text-center">Create new Batch</h4>
      </div>
      <div class="modal-body">
          <div class="row">

            <div class="col-md-4">
              <div class="box-body">
                <div class="form-group">
                  <label for="supplier_name">Batch Name *</label>
                  <stax_number id="supplier_name_msg" class="text-danger text-right pull-right"></stax_number>


                  <input type="hidden" class="form-control" id="batchpop_pro_id" name="pro_id" placeholder="" >
                  <input type="hidden" class="form-control" id="batchpop_row_id" name="row_id" placeholder="" >
                  
                  <input type="text" class="form-control" id="batchpop_batch_name" name="batch_name" placeholder="Enter batch name" >

                </div>
              </div>
            </div>

            

          </div>
         
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary add_batch">Save</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
 <?= form_close();?>
</div>
<!-- /.modal -->