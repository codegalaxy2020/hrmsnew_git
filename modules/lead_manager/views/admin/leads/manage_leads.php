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
                           <h4 class="no-margin">&nbsp&nbsp Manage Leads  </h4>
                          
                        </div>
                        <div class="col-md-4 col-xs-12 pull-right leads-search">
                           <?php echo form_hidden('sort_type'); ?>
                           <?php echo form_hidden('sort',(get_option('default_leads_kanban_sort') != '' ? get_option('default_leads_kanban_sort_type') : '')); ?>
                        </div>
                     </div>
                     <div class="clearfix"></div>
                     <div class="row hide leads-overview">
                        <hr class="hr-panel-heading" />
                        <div class="col-md-12">
                           <h4 class="no-margin"><?php echo _l('leads_summary'); ?></h4>
                        </div>
                        <?php
                        foreach($summary as $status) { ?>
                           <div class="col-md-2 col-xs-6 border-right">
                              <h3 class="bold">
                                 <?php
                                 if(isset($status['percent'])) {
                                    echo '<span data-toggle="tooltip" data-title="'.$status['total'].'">'.$status['percent'].'%</span>';
                                 } else {
                                    echo $status['total'];
                                 }
                                 ?>
                              </h3>
                              <span style="color:<?php echo $status['color']; ?>" class="<?php echo isset($status['junk']) || isset($status['lost']) ? 'text-danger' : ''; ?>"><?php echo $status['name']; ?></span>
                           </div>
                        <?php } ?>
                     </div>
                  </div>
                  <hr class="hr-panel-heading" />
                  <div class="tab-content">
                     <div class="row" id="lead_manager-table">
                        <div class="col-md-12">
                           <div class="row">
                              <div class="col-md-12">
                                 <p class="bold"><?php echo _l('filter_by'); ?></p>
                              </div>
                              <?php if(has_permission('leads','','view')){ ?>
                                 <div class="col-md-3 leads-filter-column">
                                    <?php echo render_select('view_assigned',$staff,array('staffid',array('firstname','lastname')),'','',array('data-width'=>'100%','data-none-selected-text'=>_l('leads_dt_assigned')),array(),'no-mbot'); ?>
                                 </div>
                              <?php } ?>
                              <div class="col-md-3 leads-filter-column">
                                 <?php
                                 $selected = array();
                                 if($this->input->get('status')) {
                                   $selected[] = $this->input->get('status');
                                } else {
                                   foreach($statuses as $key => $status) {
                                    if($status['isdefault'] == 0) {
                                     $selected[] = $status['id'];
                                  } else {
                                     $statuses[$key]['option_attributes'] = array('data-subtext'=>_l('leads_converted_to_client'));
                                  }
                               }
                            }
                            echo '<div id="leads-filter-status">';
                            echo render_select('view_status[]',$statuses,array('id','name'),'',$selected,array('data-width'=>'100%','data-none-selected-text'=>_l('leads_all'),'multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false);
                            echo '</div>';
                            ?>
                         </div>
                      </div>
                   </div>
                   <div class="clearfix"></div>
                   <hr class="hr-panel-heading" />
                   <div class="col-md-12">
                    <?php  if (has_permission('lead_manager', '', 'can_sms')) { ?>
                     <a href="#" data-toggle="modal" data-table=".table-lead-managerd" data-target="#lead_manager_bulk_actions" class="hide bulk-actions-btn table-btn"><?php echo _l('lead_manager_bulk_sms'); ?></a>
                  <?php } ?>
                  <div class="modal fade bulk_actions" id="lead_manager_bulk_actions" tabindex="-1" role="dialog">
                     <div class="modal-dialog" role="document">
                        <div class="modal-content">
                           <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                              <h4 class="modal-title"><?php echo _l('lead_manager_bulk_sms'); ?></h4>
                           </div>
                           <div class="modal-body">
                              <?= render_textarea('bulk_message_content',_l('lead_manager_message_data'),'',['required'=>'required']); ?>
                           </div>
                           <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                              <a href="#" class="btn btn-info" onclick="lead_manager_bulk_sms_actions(this); return false;"><?php echo _l('confirm'); ?></a>
                           </div>
                        </div>
                        <!-- /.modal-content -->
                     </div>
                     <!-- /.modal-dialog -->
                  </div>
                  <!-- /.modal -->
                  <?php
                  $table_data = array();
                  $_table_data = array(
                   '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="lead_manager"><label></label></div>',
                   array(
                     'name'=>_l('the_number_sign'),
                     'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-number')
                  ),
                   array(
                     'name'=>_l('leads_dt_name'),
                     'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-name')
                  ),
                );
                  if(is_gdpr() && get_option('gdpr_enable_consent_for_leads') == '1') {
                   $_table_data[] = array(
                     'name'=>_l('gdpr_consent') .' ('._l('gdpr_short').')',
                     'th_attrs'=>array('id'=>'th-consent', 'class'=>'not-export')
                  );
                }
                $_table_data[] =   array(
                 'name'=>_l('lead_manager_dt_connect'),
                 'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-connect')
              );
                $_table_data[] = array(
                 'name'=>_l('lead_company'),
                 'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-company')
              );
                $_table_data[] =  array(
                 'name'=>_l('leads_dt_phonenumber'),
                 'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-phone')
              );
                $_table_data[] = array(
                 'name'=>_l('leads_dt_assigned'),
                 'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-assigned')
              );
                $_table_data[] = array(
                 'name'=>_l('leads_dt_status'),
                 'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-status')
              );
                $_table_data[] = array(
                 'name'=>_l('leads_dt_last_contact'),
                 'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-last-contact')
              );
                $_table_data[] = array(
                 'name'=>_l('lead_manger_dt_follow_up'),
                 'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-follow_up')
              );
                $_table_data[] = array(
                   'name'=>_l('leads_dt_datecreated'),
                   'th_attrs'=>array('class'=>'date-created toggleable','id'=>'th-date-created')
                );
                $_table_data[] = array(
                   'name'=>_l('Remark'),
                   'th_attrs'=>array('class'=>' toggleable','id'=>'th-remark')
                );
                foreach($_table_data as $_t){
                 array_push($table_data,$_t);
              }
              $custom_fields = get_custom_fields('leads',array('show_on_table'=>1));
              foreach($custom_fields as $field){
               array_push($table_data,$field['name']);
            }
            $table_data = hooks()->apply_filters('leads_table_columns', $table_data);
            render_datatable($table_data,'lead-managerd',
               array('customizable-table'),
               array(
                 'id'=>'table-lead-managerd',
                 'data-last-order-identifier'=>'lead_manager',
                 'data-default-order'=>get_table_last_order('lead_manager'),
              )); ?>
           </div>
        </div>
     </div>
  </div>
</div>
</div>
</div> 
</div>
</div>
<div class="modal fade lead-modal" id="lead-manager-activity-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
 <div class="modal-dialog <?php echo get_option('lead_modal_class'); ?>">
  <div class="modal-content data">

  </div>
</div> 
</div>
<div class="modal fade" id="lead-manager-sms-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
 <div class="modal-dialog">
  <div class="modal-content data">

  </div>
</div>
</div>
<div class="modal fade" id="lead-manager-zoom-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
 <div class="modal-dialog">
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
<div class="modal fade lead-modal" id="lead-manager-meeting-show_remark" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
 <div class="modal-dialog">
  <div class="modal-content data">

  </div>
</div>
</div>
<?php init_tail(); ?>
</body>
</html>
