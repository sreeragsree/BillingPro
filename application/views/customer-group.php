<!DOCTYPE html>
<html>
   <head>
      <?php include"comman/code_css.php"; ?>
   </head>
   <body class="hold-transition skin-blue sidebar-mini">
      <div class="wrapper">
         <?php include"sidebar.php"; ?>
         <?php
            if(!isset($group_name)){
                 $group_name=$description=$store_id="";
            }
            ?>
         <div class="content-wrapper">
            <section class="content-header">
               <h1>
                  <?=$page_title;?>
                  <small>Add/Update Customer Group</small>
               </h1>
               <ol class="breadcrumb">
                  <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
                  <li><a href="<?php echo $base_url; ?>customer_groups/view">Customer Groups List</a></li>
                  <li class="active"><?=$page_title;?></li>
               </ol>
            </section>
            <section class="content">
               <div class="row">
                  <div class="col-md-12">
                     <div class="box box-primary ">
                        <div class="box-header with-border">
                           <h3 class="box-title">Please Enter Valid Data</h3>
                        </div>
                        <form class="form-horizontal" id="customer-group-form" onkeypress="return event.keyCode != 13;">
                           <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                           <input type="hidden" id="base_url" value="<?php echo $base_url;; ?>">
                           <div class="box-body">
                              <?php 
                                echo "<input type='hidden' name='store_id' id='store_id' value='".get_current_store_id()."'>";
                              ?>
                              <div class="form-group">
                                 <label for="group_name" class="col-sm-2 control-label">Group Name<label class="text-danger">*</label></label>
                                 <div class="col-sm-4">
                                    <input type="text" class="form-control input-sm" id="group_name" name="group_name" placeholder="" value="<?php print $group_name; ?>" autofocus >
                                    <span id="group_name_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label for="description" class="col-sm-2 control-label"><?= $this->lang->line('description'); ?></label>
                                 <div class="col-sm-4">
                                    <textarea type="text" class="form-control" id="description" name="description" placeholder=""><?php print $description; ?></textarea>
                                    <span id="description_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                           </div>
                           <div class="box-footer">
                              <div class="col-sm-8 col-sm-offset-2 text-center">
                                 <?php
                                    if(isset($q_id)){
                                         $btn_name="Update";
                                         $btn_id="update";
                                        ?>
                                 <input type="hidden" name="q_id" id="q_id" value="<?php echo $q_id;?>"/>
                                 <?php
                                    }
                                              else{
                                                  $btn_name="Save";
                                                  $btn_id="save";
                                              }
                                              ?>
                                 <div class="col-md-3 col-md-offset-3">
                                    <button type="button" id="<?php echo $btn_id;?>" class=" btn btn-block btn-success" title="Save Data"><?php echo $btn_name;?></button>
                                 </div>
                                 <div class="col-sm-3">
                                    <a href="<?=base_url('dashboard');?>">
                                    <button type="button" class="col-sm-3 btn btn-block btn-warning close_btn" title="Go Dashboard">Close</button>
                                    </a>
                                 </div>
                              </div>
                           </div>
                        </form>
                     </div>
                  </div>
               </div>
            </section>
         </div>
         <?php include"footer.php"; ?>
         <div class="control-sidebar-bg"></div>
      </div>
      <?php include"comman/code_js_sound.php"; ?>
      <?php include"comman/code_js.php"; ?>
      <script src="<?php echo $theme_link; ?>js/customer_group.js"></script>
      <script type="text/javascript">
        <?php if(isset($q_id)){ ?>
          $("#store_id").attr('readonly',true);
        <?php }?>
      </script>
      <script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>
   </body>
</html>