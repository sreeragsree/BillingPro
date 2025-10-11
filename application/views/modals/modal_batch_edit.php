<div class="modal fade " id="batch_edit_modal" tabindex='-1'>
    <?= form_open('#', array('class' => '', 'id' => 'batch_edit_form')); ?>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header header-custom">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center"> Edit batch details </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    
                    <div class="col-md-4">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="brand">Batch Name *</label>
                                <span id="brand_msg" class="text-danger text-right pull-right"></span>
                                <input type="hidden" class="form-control" id="edit_batid" name="edit_batid" placeholder="">
                                <input type="hidden" class="form-control" id="edit_pid" name="edit_pid" placeholder="">
                                <input required type="text" class="form-control" id="edit_batch" name="edit_batch" placeholder="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="brand">Purchase Price *</label>
                                <span id="brand_msg" class="text-danger text-right pull-right"></span>
                                <input required type="text" class="form-control" id="edit_pprice" name="edit_pprice" placeholder="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="brand">Sales Price *</label>
                                <span id="brand_msg" class="text-danger text-right pull-right"></span>
                                <input required type="text" class="form-control" id="edit_sprice" name="edit_sprice" placeholder="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="brand">Wholesale Price *</label>
                                <span id="brand_msg" class="text-danger text-right pull-right"></span>
                                <input required type="text" class="form-control" id="edit_wprice" name="edit_wprice" placeholder="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="brand">MRP *</label>
                                <span id="brand_msg" class="text-danger text-right pull-right"></span>
                                <input required type="text" class="form-control" id="edit_mrp" name="edit_mrp" placeholder="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="brand">Alphabet Price *</label>
                                <span id="brand_msg" class="text-danger text-right pull-right"></span>
                                <input required type="text" class="form-control" id="edit_alpprice" name="edit_alpprice" placeholder="">
                            </div>
                        </div>
                    </div>
                    

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                <button name="saveBatchEdit" id="saveBatchEdit" type="button" class="btn btn-primary edit_batch_details">Save</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
    <?= form_close(); ?>
</div>
<!-- /.modal -->