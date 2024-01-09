<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="_buttons">
                     <div class="row">
                        <div class="pull-left">
                       <h4 class="no-margin"> &nbsp;&nbsp; Schedule Appointments</h4>
                     </div>
                        
                     </div>
                     <div class="clearfix"></div>
                  </div>
                  <hr class="hr-panel-heading" />
                  <div class="tab-content">
                     <div class="row" id="lead_manager-table">
                        <div class="clearfix"></div>
                        <div class="col-md-12">
                          <!--  <a href="#" data-toggle="modal" data-table=".table-zoom-appointment" data-target="#lead_manager_bulk_actions" class="hide bulk-actions-btn table-btn"><?php echo _l('lead_manager_bulk_sms'); ?></a> -->
                                <?php
                           $table_data = array(
                              array(
                                'name'=>_l('#'),
                                'th_attrs'=> array('class'=>'')
                              ),
                               _l('customer_name'),
                               _l('customer_email'),
                               _l('staff_name'),
                               _l('created_date'),
                               _l('meeting_date'),
                               
                               _l('Status'),
                               _l('Remark'),
                               
                            );
                             render_datatable($table_data,'zoom-appointment',[],[
                                 'data-last-order-identifier' => 'zoom-appointment',
                                 'data-default-order'         => get_table_last_order('lead_manager'),
                             ]);
                           ?>
                        </div>
                     </div>
                  
                  </div>
               </div>
            </div>
         </div>
      </div> 
   </div>
</div>
<div class="modal fade" id="lead-manager-meeting-details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg">
    <div class="modal-content data">

    </div>
  </div>
</div>
<div class="modal fade" id="lead-manager-meeting-remark" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog">
    <div class="modal-content data">

    </div>
  </div>
</div>
<div class="modal fade" id="lead-manager-meeting-show_remark" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog">
    <div class="modal-content data">

    </div>
  </div>
</div>
<script id="hidden-columns-table-lead-manager" type="text/json">
   <?php echo get_staff_meta(get_staff_user_id(), 'hidden-columns-table-lead-manager'); ?>
</script>
<?php init_tail(); ?>
<script>
   var openLeadID = '<?php echo $leadid; ?>';
   $(function(){
       var Supply_Chain_ServerParams = {};
     // $.each($('._hidden_inputs._filters input'),function(){
     //   Supply_Chain_ServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
     // });
     initDataTable('.table-zoom-appointment', admin_url+'lead_manager/zoom_appointment_table', [0], [0], Supply_Chain_ServerParams, [0, 'desc']);
   });

   function update_meeting_status(status_id, lead_id) {
   var table_leads = $('table.table-zoom-appointment');
    var data = {};
    data.status = status_id;
    data.leadid = lead_id;
    //alert(data.status);
    $.post(admin_url + 'lead_manager/zoom_meeting/update_meeting_status', data).done(function (response) {
        table_leads.DataTable().ajax.reload(null, false);
    });
} 

</script>
</body>
</html>
