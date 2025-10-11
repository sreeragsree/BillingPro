<?php $CI = &get_instance(); ?>
<style>
/* Debug styles */
#view-item-modal {
    color: #333;
    z-index: 1060; /* Higher than default */
}
#view-item-modal .modal-dialog {
    margin-top: 100px;
}
#view-item-modal .modal-header {
    background-color: #3c8dbc;
    color: white;
    border-radius: 5px 5px 0 0;
}
#view-item-modal .modal-body {
    padding: 20px;
    max-height: 70vh;
    overflow-y: auto;
}
/* Debug highlight */
.debug-highlight {
    border: 3px solid #f39c12 !important;
    box-shadow: 0 0 20px rgba(243, 156, 18, 0.5) !important;
}
</style>

<div class="modal fade debug-highlight" id="view-item-modal" tabindex='-1' role="dialog" aria-labelledby="viewItemModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header header-custom">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center">ITEM DETAILS</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label><strong><?= $this->lang->line('item_name'); ?>:</strong></label>
              <p id="view_item_name"></p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label><strong><?= $this->lang->line('item_code'); ?>:</strong></label>
              <p id="view_item_code"></p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label><strong><?= $this->lang->line('category'); ?>:</strong></label>
              <p id="view_category"></p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label><strong><?= $this->lang->line('brand'); ?>:</strong></label>
              <p id="view_brand"></p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label><strong><?= $this->lang->line('unit'); ?>:</strong></label>
              <p id="view_unit"></p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label><strong><?= $this->lang->line('stock'); ?>:</strong></label>
              <p id="view_stock"></p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label><strong><?= $this->lang->line('purchase_price'); ?>:</strong></label>
              <p id="view_purchase_price"></p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label><strong><?= $this->lang->line('sales_price'); ?>:</strong></label>
              <p id="view_sales_price"></p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label><strong><?= $this->lang->line('alert_quantity'); ?>:</strong></label>
              <p id="view_alert_qty"></p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label><strong><?= $this->lang->line('description'); ?>:</strong></label>
              <p id="view_description"></p>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= $this->lang->line('close'); ?></button>
      </div>
    </div>
  </div>
</div>
