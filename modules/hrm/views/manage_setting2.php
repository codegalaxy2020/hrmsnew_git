<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
 <div class="content">
    <div class="row">
     <?php if($this->session->flashdata('debug')){ ?>
       <div class="col-lg-12">
        <div class="alert alert-warning">
         <?php echo $this->session->flashdata('debug'); ?>
       </div>
     </div>
   <?php } ?>
  
  <div class="col-md-12">
    <div class="panel_s">
     <div class="panel-body">

        <?php $this->load->view($tabs['view']); ?>
        
     </div>
  </div>
</div>
<div class="clearfix"></div>
</div>
<?php echo form_close(); ?>
<div class="btn-bottom-pusher"></div>
</div>
</div>
<div id="new_version"></div>
<?php init_tail(); ?>
<?php hooks()->do_action('settings_tab_footer', $tab); ?>
</body>
</html>
