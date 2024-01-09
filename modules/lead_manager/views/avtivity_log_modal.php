<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-header">
 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
 <h4 class="modal-title">
  <?php if(isset($lead)){
    if(!empty($lead->name)){
      $name = $lead->name;
    } else if(!empty($lead->company)){
      $name = $lead->company;
    } else {
      $name = _l('lead');
    }
    echo '#'.$lead->id . ' - ' .  $name;
  } 
  ?>
</h4>
</div>
<div class="modal-body">
 <?php
 if(isset($lead)){
   if($lead->lost == 1){
     echo '<div class="ribbon danger"><span>'._l('lead_lost').'</span></div>';
   } else if($lead->junk == 1){
    echo '<div class="ribbon warning"><span>'._l('lead_junk').'</span></div>';
  } else {
    if (total_rows(db_prefix().'clients', array(
      'leadid' => $lead->id))) {
      echo '<div class="ribbon success"><span>'._l('lead_is_client').'</span></div>';
    }
  }
}
?>
<div class="row">
 <div class="col-md-12">
   <?php if(isset($lead)){
     echo form_hidden('leadid',$lead->id);
   } ?>
   <div class="top-lead-menu">
    <div class="horizontal-scrollable-tabs preview-tabs-top">
     <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
     <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
     <div class="horizontal-tabs">
      <ul class="nav-tabs-horizontal nav nav-tabs<?php if(!isset($lead)){echo ' lead-new';} ?>" role="tablist">
       <?php if(isset($lead)){ ?>
         <li role="presentation" class="active">
          <a href="#lead_activity" aria-controls="lead_activity" role="tab" data-toggle="tab">
            <?php echo _l('lead_manger_all'); ?>
          </a>
        </li>
        <li role="presentation" class="">
          <a href="#lead_activity_audio" aria-controls="lead_activity_audio" role="tab" data-toggle="tab">
            <?php echo _l('lead_manger_activity_audio'); ?>
          </a>
        </li>
        <li role="presentation" class="">
          <a href="#lead_activity_sms" aria-controls="lead_activity_sms" role="tab" data-toggle="tab">
            <?php echo _l('lead_manger_activity_sms'); ?>
          </a>
        </li>
        <li role="presentation" class="">
          <a href="#lead_activity_recordings" aria-controls="lead_activity_recordings" role="tab" data-toggle="tab">
            <?php echo _l('lead_manger_activity_recordings'); ?>
          </a>
        </li>
      <?php } ?>
    </ul>
  </div>
</div>
</div>
<!-- Tab panes -->
<div class="tab-content mtop20">
  <!-- from leads modal -->
  <?php if(isset($lead)){ ?>
    <div role="tabpanel" class="tab-pane active" id="lead_activity">
     <div class="panel_s no-shadow">
      <div class="activity-feed">
       <?php foreach($activity_log as $log){ ?>
         <div class="feed-item">
          <div class="row">
            <div class="col-lg-3">
              <div class="date">
                <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo $log['type'] == 'audio_call' ? _l('lead_manger_audio_call') : ($log['type'] == 'video_call' ? _l('lead_manger_video_call') : _l('lead_manger_sms')); ?>">
                  <?php echo $log['type'] == 'audio_call' ? _l('lead_manger_audio_call') : ($log['type'] == 'video_call' ? _l('lead_manger_video_call') : _l('lead_manger_sms')); ?>
                </span>
              </div>
              <div class="date">
                <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($log['date']); ?>">
                  <?php echo time_ago($log['date']); ?>
                </span>
              </div>
              <div class="text">
               <?php if($log['staff_id'] != 0){ ?>
                 <a href="<?php echo admin_url('profile/'.$log["staff_id"]); ?>">
                   <?php echo staff_profile_image($log['staff_id'],array('staff-profile-xs-image pull-left mright5'));
                   ?>
                 </a>
                 <?php
               }
               $additional_data = '';
               if(!empty($log['additional_data'])){
                 $additional_data = json_decode($log['additional_data']);
                 echo ($log['staff_id'] == 0) ? _l($log['description'],$additional_data) : get_staff_full_name($log['staff_id']);
               } else {      
                 $description = json_decode($log['description']);
                 echo ($log['staff_id'] == 0) ? _l($log['description'],$description) : get_staff_full_name($log['staff_id']);
               }
               ?>
             </div>
           </div>
           <div class="col-lg-9">
             <div class="lm_media_div">
              <?php
              if(!empty($log['additional_data'])){
                $additional_data = json_decode($log['additional_data']);
                if(isset($additional_data->RecordingUrl)){
                  echo '<audio controls>
                  <source src="'.$additional_data->RecordingUrl.'" type="audio/ogg">
                  <source src="'.$additional_data->RecordingUrl.'" type="audio/mpeg">
                  Your browser does not support the audio tag.
                  </audio>';
                }else{
                  echo '<p>'.$log['description'].'</p>';
                }
              }else{
                echo '<p>'.$log['description'].'</p>';
              }
              ?>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
  <div class="clearfix"></div>
</div>
</div>
<div role="tabpanel" class="tab-pane" id="lead_activity_audio">
  <div class="panel_s no-shadow">
    <div class="activity-feed">
     <?php 
        foreach($activity_log as $log){ 
          if($log['type'] == 'audio_call' && !$log['is_audio_call_recorded']){
      ?>
       <div class="feed-item">
        <div class="row">
          <div class="col-lg-3">
            <div class="date">
              <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _l('lead_manger_audio_call'); ?>"><?php echo _l('lead_manger_audio_call'); ?>
              </span>
            </div>
            <div class="date">
              <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($log['date']); ?>">
                <?php echo time_ago($log['date']); ?>
              </span>
            </div>
            <div class="text">
             <?php if($log['staff_id'] != 0){ ?>
               <a href="<?php echo admin_url('profile/'.$log["staff_id"]); ?>">
                 <?php echo staff_profile_image($log['staff_id'],array('staff-profile-xs-image pull-left mright5'));
                 ?>
               </a>
               <?php
             }
             $additional_data = '';
             if(!empty($log['additional_data'])){
               $additional_data = json_decode($log['additional_data']);
               echo ($log['staff_id'] == 0) ? _l($log['description'],$additional_data) : get_staff_full_name($log['staff_id']);
             } else {      
               $description = json_decode($log['description']);
               echo ($log['staff_id'] == 0) ? _l($log['description'],$description) : get_staff_full_name($log['staff_id']);
             }
             ?>
           </div>
         </div>
         <div class="col-lg-9">
          <?php echo '<p>'.$log['description'].'</p>';?>
        </div>
      </div>
    </div>
  <?php }} ?>
</div>
<div class="clearfix"></div>
</div>
</div>
<div role="tabpanel" class="tab-pane" id="lead_activity_sms">
 <div class="panel_s no-shadow">
    <div class="activity-feed">
     <?php 
        foreach($activity_log as $log){ 
          if($log['type'] == 'sms'){
      ?>
       <div class="feed-item">
        <div class="row">
          <div class="col-lg-3">
            <div class="date">
              <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _l('lead_manger_sms'); ?>"><?php echo _l('lead_manger_sms'); ?>
              </span>
            </div>
            <div class="date">
              <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($log['date']); ?>">
                <?php echo time_ago($log['date']); ?>
              </span>
            </div>
            <div class="text">
             <?php if($log['staff_id'] != 0){ ?>
               <a href="<?php echo admin_url('profile/'.$log["staff_id"]); ?>">
                 <?php echo staff_profile_image($log['staff_id'],array('staff-profile-xs-image pull-left mright5'));
                 ?>
               </a>
               <?php
             }
             $additional_data = '';
             if(!empty($log['additional_data'])){
               $additional_data = json_decode($log['additional_data']);
               echo ($log['staff_id'] == 0) ? _l($log['description'],$additional_data) : get_staff_full_name($log['staff_id']);
             } else {      
               $description = json_decode($log['description']);
               echo ($log['staff_id'] == 0) ? _l($log['description'],$description) : get_staff_full_name($log['staff_id']);
             }
             ?>
           </div>
         </div>
         <div class="col-lg-9">
           <div class="lm_media_div">
            <?php echo '<p>'.$log['description'].'</p>';?>
          </div>
        </div>
      </div>
    </div>
  <?php }} ?>
</div>
<div class="clearfix"></div>
</div>
</div>
<div role="tabpanel" class="tab-pane" id="lead_activity_recordings">
<div class="panel_s no-shadow">
    <div class="activity-feed">
     <?php 
        foreach($activity_log as $log){ 
          if($log['type'] == 'audio_call' && $log['is_audio_call_recorded']){
      ?>
       <div class="feed-item">
        <div class="row">
          <div class="col-lg-3">
            <div class="date">
              <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _l('lead_manger_activity_recordings'); ?>"><?php echo _l('lead_manger_activity_recordings'); ?>
              </span>
            </div>
            <div class="date">
              <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($log['date']); ?>">
                <?php echo time_ago($log['date']); ?>
              </span>
            </div>
            <div class="text">
             <?php if($log['staff_id'] != 0){ ?>
               <a href="<?php echo admin_url('profile/'.$log["staff_id"]); ?>">
                 <?php echo staff_profile_image($log['staff_id'],array('staff-profile-xs-image pull-left mright5'));
                 ?>
               </a>
               <?php
             }
             $additional_data = '';
             if(!empty($log['additional_data'])){
               $additional_data = json_decode($log['additional_data']);
               echo ($log['staff_id'] == 0) ? _l($log['description'],$additional_data) : get_staff_full_name($log['staff_id']);
             } else {      
               $description = json_decode($log['description']);
               echo ($log['staff_id'] == 0) ? _l($log['description'],$description) : get_staff_full_name($log['staff_id']);
             }
             ?>
           </div>
         </div>
         <div class="col-lg-9">
           <div class="lm_media_div">
           <?php
              if(!empty($log['additional_data'])){
                $additional_data = json_decode($log['additional_data']);
                if(isset($additional_data->RecordingUrl)){
                  echo '<audio controls>
                  <source src="'.$additional_data->RecordingUrl.'" type="audio/ogg">
                  <source src="'.$additional_data->RecordingUrl.'" type="audio/mpeg">
                  Your browser does not support the audio tag.
                  </audio>';
                }else{
                  echo '<p>'.$log['description'].'</p>';
                }
              }
              ?>
          </div>
        </div>
      </div>
    </div>
  <?php }} ?>
</div>
<div class="clearfix"></div>
</div>
</div>
<?php } ?>
</div>
</div>
</div>
</div>
