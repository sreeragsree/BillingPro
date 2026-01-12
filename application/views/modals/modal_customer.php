<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?= form_open('#', ['class' => '', 'id' => 'customer-form']); ?>

<div class="modal fade" id="customer-modal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header header-custom">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <label aria-hidden="true">&times;</label>
        </button>
        <h4 class="modal-title text-center"><?= $this->lang->line('add_customer'); ?></h4>
      </div>

      <div class="modal-body">

        <!-- BASIC DETAILS -->
        <div class="row">

          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="customer_name"><?= $this->lang->line('customer_name'); ?>*</label>
                <label id="customer_name_msg" class="text-danger pull-right"></label>
                <input type="text" class="form-control" id="customer_name" name="customer_name">
              </div>
            </div>
          </div>

              <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="customer_group_id">Customer Group*</label>
                <label id="customer_group_id_msg" class="text-danger pull-right"></label>
                <select class="form-control" id="customer_group_id" name="customer_group_id">
                  <?= get_customer_groups_select_list(null, get_current_store_id(), true); ?>
                </select>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="mobile"><?= $this->lang->line('mobile'); ?></label>
                <label id="mobile_msg" class="text-danger pull-right"></label>
                <input type="tel" class="form-control no_special_char_no_space" id="mobile" name="mobile" placeholder="+1234567890">
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="phone"><?= $this->lang->line('phone'); ?></label>
                <label id="phone_msg" class="text-danger pull-right"></label>
                <input type="tel" maxlength="10" class="form-control maxlength no_special_char_no_space" id="phone" name="phone">
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="email"><?= $this->lang->line('email'); ?></label>
                <label id="email_msg" class="text-danger pull-right"></label>
                <input type="email" class="form-control" id="email" name="email">
              </div>
            </div>
          </div>

          <?php if (gst_number()) { ?>
          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="gstin"><?= $this->lang->line('gst_number'); ?></label>
                <label id="gstin_msg" class="text-danger pull-right"></label>
                <input type="text" class="form-control maxlength" id="gstin" name="gstin">
              </div>
            </div>
          </div>
          <?php } ?>

          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="tax_number"><?= $this->lang->line('tax_number'); ?></label>
                <label id="tax_number_msg" class="text-danger pull-right"></label>
                <input type="text" class="form-control maxlength" id="tax_number" name="tax_number">
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="credit_limit"><?= $this->lang->line('credit_limit'); ?></label>
                <label class="text-success pull-right">-1 for No Limit</label>
                <label id="credit_limit_msg" class="text-danger pull-right"></label>
                <input type="text" class="form-control only_currency" id="credit_limit" name="credit_limit" value="-1">
              </div>
            </div>
          </div>

      

          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="opening_balance"><?= $this->lang->line('previous_due'); ?></label>
                <label id="opening_balance_msg" class="text-danger pull-right"></label>
                <input type="text" class="form-control only_currency" id="opening_balance" name="opening_balance">
              </div>
            </div>
          </div>

        </div><!-- /row -->

        <!-- ADDRESS SECTION -->
        <div class="row">
          <div class="col-md-4">
            <h5 class="box-title text-uppercase text-success">
              <i class="fa fa-fw fa-truck"></i> <ins><?= $this->lang->line('address'); ?></ins>
            </h5>
          </div>
        </div>

        <div class="row">

          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="country"><?= $this->lang->line('country'); ?></label>
                <label id="country_msg" class="text-danger pull-right"></label>
                <select class="form-control" id="country" name="country">
                  <?= get_country_select_list(null, true); ?>
                </select>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="state"><?= $this->lang->line('state'); ?></label>
                <label id="state_msg" class="text-danger pull-right"></label>
                <select class="form-control" id="state" name="state">
                  <option value="">-Select-</option>
                  <?php
                    $q2 = $this->db->query("SELECT * FROM db_states WHERE status = 1");
                    foreach ($q2->result() as $res1) {
                      echo "<option value='{$res1->id}'>{$res1->state}</option>";
                    }
                  ?>
                </select>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="city"><?= $this->lang->line('city'); ?></label>
                <label id="city_msg" class="text-danger pull-right"></label>
                <input type="text" class="form-control" id="city" name="city">
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="postcode"><?= $this->lang->line('postcode'); ?></label>
                <label id="postcode_msg" class="text-danger pull-right"></label>
                <input type="text" class="form-control" id="postcode" name="postcode">
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="location_link"><?= $this->lang->line('location_link'); ?></label>
                <label id="location_link_msg" class="text-danger pull-right"></label>
                <input type="text" class="form-control" id="location_link" name="location_link">
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="address"><?= $this->lang->line('address'); ?></label>
                <label id="address_msg" class="text-danger pull-right"></label>
                <textarea class="form-control" id="address" name="address"></textarea>
              </div>
            </div>
          </div>

        </div><!-- /row -->

        <!-- SHIPPING ADDRESS -->
        <div class="row">
          <div class="col-md-4">
            <h5 class="box-title text-uppercase text-success">
              <i class="fa fa-fw fa-truck"></i> <?= $this->lang->line('shipping_address'); ?>
            </h5>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label for="copy_address"><?= $this->lang->line('copy_address'); ?>?</label>
              <input type="checkbox" id="copy_address" name="copy_address" class="form-control">
            </div>
          </div>
        </div>

        <div class="row">

          <div class="col-md-4">
            <div class="form-group">
              <label for="shipping_country"><?= $this->lang->line('country'); ?></label>
              <select class="form-control" id="shipping_country" name="shipping_country">
                <?= get_country_select_list(null, true); ?>
              </select>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label for="shipping_state"><?= $this->lang->line('state'); ?></label>
              <select class="form-control" id="shipping_state" name="shipping_state">
                <option value="">-Select-</option>
                <?php
                  foreach ($q2->result() as $res1) {
                    echo "<option value='{$res1->id}'>{$res1->state}</option>";
                  }
                ?>
              </select>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label for="shipping_city"><?= $this->lang->line('city'); ?></label>
              <input type="text" class="form-control" id="shipping_city" name="shipping_city">
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label for="shipping_postcode"><?= $this->lang->line('postcode'); ?></label>
              <input type="text" class="form-control" id="shipping_postcode" name="shipping_postcode">
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label for="shipping_location_link"><?= $this->lang->line('location_link'); ?></label>
              <input type="text" class="form-control" id="shipping_location_link" name="shipping_location_link">
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label for="shipping_address"><?= $this->lang->line('address'); ?></label>
              <textarea class="form-control" id="shipping_address" name="shipping_address"></textarea>
            </div>
          </div>

        </div><!-- /row -->

      </div><!-- /.modal-body -->

      <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary add_customer">Save</button>
      </div>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?= form_close(); ?>
