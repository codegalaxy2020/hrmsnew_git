<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->ci->load->model('gdpr_model');
$lockAfterConvert      = get_option('lead_lock_after_convert_to_customer');
$has_permission_delete = has_permission('leads', '', 'delete');
$custom_fields         = get_table_custom_fields('leads');
$consentLeads          = get_option('gdpr_enable_consent_for_leads');
$statuses              = $this->ci->lead_manager_model->get_status();
$aColumns = [
    '1',
    '2',
    db_prefix() . 'leads.id as id',
    db_prefix() . 'leads.name as name',
];
if (is_gdpr() && $consentLeads == '1') {
    $aColumns[] = '1';
}
$aColumns = array_merge($aColumns, ['company',db_prefix() . 'leads.phonenumber as phonenumber','firstname as assigned_firstname',db_prefix() . 'leads_status.name as status_name','lastcontact','dateadded', db_prefix() . 'leads.lm_follow_up']);
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'leads';
$join = [
    'LEFT JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'leads.assigned',
    'LEFT JOIN ' . db_prefix() . 'leads_status ON ' . db_prefix() . 'leads_status.id = ' . db_prefix() . 'leads.status',
];
foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
    array_push($customFieldsColumns, $selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'leads.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
}
$where  = [];
$filter = false;
if ($this->ci->input->post('custom_view')) {
    $filter = $this->ci->input->post('custom_view');
    if ($filter == 'lost') {
        array_push($where, 'AND lost = 1');
    } elseif ($filter == 'junk') {
        array_push($where, 'AND junk = 1');
    } elseif ($filter == 'not_assigned') {
        array_push($where, 'AND assigned = 0');
    } elseif ($filter == 'contacted_today') {
        array_push($where, 'AND lastcontact LIKE "' . date('Y-m-d') . '%"');
    } elseif ($filter == 'created_today') {
        array_push($where, 'AND dateadded LIKE "' . date('Y-m-d') . '%"');
    } elseif ($filter == 'public') {
        array_push($where, 'AND is_public = 1');
    } elseif (startsWith($filter, 'consent_')) {
        array_push($where, 'AND ' . db_prefix() . 'leads.id IN (SELECT lead_id FROM ' . db_prefix() . 'consents WHERE purpose_id=' . $this->ci->db->escape_str(strafter($filter, 'consent_')) . ' and action="opt-in" AND date IN (SELECT MAX(date) FROM ' . db_prefix() . 'consents WHERE purpose_id=' . $this->ci->db->escape_str(strafter($filter, 'consent_')) . ' AND lead_id=' . db_prefix() . 'leads.id))');
    }
}
if (!$filter || ($filter && $filter != 'lost' && $filter != 'junk')) {
    array_push($where, 'AND lost = 0 AND junk = 0');
}
if (has_permission('leads', '', 'view') && $this->ci->input->post('assigned')) {
    array_push($where, 'AND assigned =' . $this->ci->db->escape_str($this->ci->input->post('assigned')));
}
if ($this->ci->input->post('status')
    && count($this->ci->input->post('status')) > 0
    && ($filter != 'lost' && $filter != 'junk')) {
    array_push($where, 'AND status IN (' . implode(',', $this->ci->db->escape_str($this->ci->input->post('status'))) . ')');
}
if (!has_permission('leads', '', 'view')) {
    array_push($where, 'AND (assigned =' . get_staff_user_id() . ' OR addedfrom = ' . get_staff_user_id() . ' OR is_public = 1)');
}
$aColumns = hooks()->apply_filters('leads_table_sql_columns', $aColumns);
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}
$additionalColumns = hooks()->apply_filters('leads_table_additional_columns_sql', [
    'junk',
    'lost',
    'color',
    'status',
    'assigned',
    'lastname as assigned_lastname',
    db_prefix() . 'leads.addedfrom as addedfrom',
    '(SELECT count(leadid) FROM ' . db_prefix() . 'clients WHERE ' . db_prefix() . 'clients.leadid=' . db_prefix() . 'leads.id) as is_converted',
    'zip',
    'lm_follow_up'
]);
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalColumns);
$output  = $result['output']; 
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = [];
    $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
    $row[]    = $aRow['id'];
    $nameRow = '<a  href="' . admin_url('leads/index/'.$aRow['id']) . '" target="_blank" >'.$aRow['name'].'</a>';
    $nameRow .= '<div class="row-options">';
    $nameRow .= '<a href="javascript:void(0);" onclick="leadManagerActivity(' . $aRow['id'] . ');" title="' . _l('lead_manger_activity') . '"><i class="fa fa-eye" aria-hidden="true"></i></a>';
    $locked = false;
    if ($aRow['is_converted'] > 0) {
        $locked = ((!is_admin() && $lockAfterConvert == 1) ? true : false);
    }
    if ($aRow['addedfrom'] == get_staff_user_id() || $has_permission_delete) {
        $nameRow .= ' | <a href="' . admin_url('lead_manager/delete/' . $aRow['id']) . '" title="' . _l('delete') . '" class="_delete text-danger" onclick="return confirm("Are you sure?");"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
    }
    $nameRow .= '</div>';
    $row[] = $nameRow;
    $allow_call = '';    
    $allow_video_call = '';    
    $allow_sms = '';
    $no_permission = '';
    if (has_permission('lead_manager', '', 'can_audio_call') && get_option('call_twilio_active')) {
       $callerIdNumber = get_staff_own_twilio_number();
       if(isset($callerIdNumber) && !empty($callerIdNumber)){
        $allow_call = '<li><a href="javascript:void(0);" onclick="dialPhone('.$aRow["phonenumber"].','.$aRow['id'].','.$callerIdNumber.');"><i class="fa fa-phone" aria-hidden="true" data-toggle="tooltip" data-title="Call"></i></a></li>'; 
    }else{
        $allow_call = '<li class="fa-stack"><a href="javascript:void(0);" title="'._l('lead_manager_twilio_number_not_assigned').'"><i class="fa fa-phone fa-stack-1x"></i><i class="fa fa-ban fa-stack-2x text-danger"></i></li>'; 
    }
}if (has_permission('lead_manager', '', 'can_video_call') && get_option('call_zoom_active')) {
    $allow_video_call ='<li><a href="javascript:void(0);" onclick="leadManagerZoom('.$aRow['id'].');" data-toggle="tooltip" data-title="Zoom Meeting"><i class="fa fa-video-camera" aria-hidden="true"></i></a></li>';
}if (has_permission('lead_manager', '', 'can_sms')) {
    $allow_sms = '<li><a href="javascript:void(0);" onclick="leadManagerMessage('.$aRow['id'].');" data-toggle="tooltip" data-title="Message" ><i class="fa fa-comments-o" aria-hidden="true"></i></a></li>';
}if(empty($allow_call) && empty($allow_video_call) && empty($allow_sms)){
    $no_permission = '<ul class="list-inline"><li>'._l('lead_manger_no_permission').'</li></ul>';
}
$row[] = '<ul class="list-inline"><li>'.$allow_call.$allow_video_call.$allow_sms.$no_permission.'</li></ul>';
if (is_gdpr() && $consentLeads == '1') {
    $consentHTML = '<p class="bold"><a href="#" onclick="view_lead_consent(' . $aRow['id'] . '); return false;">' . _l('view_consent') . '</a></p>';
    $consents    = $this->ci->gdpr_model->get_consent_purposes($aRow['id'], 'lead');
    foreach ($consents as $consent) {
        $consentHTML .= '<p style="margin-bottom:0px;">' . $consent['name'] . (!empty($consent['consent_given']) ? '<i class="fa fa-check text-success pull-right"></i>' : '<i class="fa fa-remove text-danger pull-right"></i>') . '</p>';
    }
    $row[] = $consentHTML;
}
$row[] = $aRow['company'];
$row[] = ($aRow['phonenumber'] != '' ? '<a href="tel:' . $aRow['phonenumber'] . '">' . $aRow['phonenumber'] . '</a>' : '');
$assignedOutput = '';
if ($aRow['assigned'] != 0) {
    $full_name = $aRow['assigned_firstname'] . ' ' . $aRow['assigned_lastname'];
    $assignedOutput = '<a data-toggle="tooltip" data-title="' . $full_name . '" href="' . admin_url('profile/' . $aRow['assigned']) . '">' . staff_profile_image($aRow['assigned'], [
        'staff-profile-image-small',
    ]) . '</a>';
    $assignedOutput .= '<span class="hide">' . $full_name . '</span>';
}
$row[] = $assignedOutput;
if ($aRow['status_name'] == null) {
    if ($aRow['lost'] == 1) {
        $outputStatus = '<span class="label label-danger inline-block">' . _l('lead_lost') . '</span>';
    } elseif ($aRow['junk'] == 1) {
        $outputStatus = '<span class="label label-warning inline-block">' . _l('lead_junk') . '</span>';
    }
} else {
    $outputStatus = '<span class="inline-block lead-status-'.$aRow['status'].' label label-' . (empty($aRow['color']) ? 'default': '') . '" style="color:' . $aRow['color'] . ';border:1px solid ' . $aRow['color'] . '">' . $aRow['status_name'];
    if (!$locked) {
        $outputStatus .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
        $outputStatus .= '<a href="#" style="font-size:14px;vertical-align:middle;" class="dropdown-toggle text-dark" id="tableLeadsStatus-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        $outputStatus .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
        $outputStatus .= '</a>';
        $outputStatus .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableLeadsStatus-' . $aRow['id'] . '">';
        foreach ($statuses as $leadChangeStatus) {
            if ($aRow['status'] != $leadChangeStatus['id']) {
                $outputStatus .= '<li>
                <a href="javascript:void(0);" onclick="lead_manager_mark_as(' . $leadChangeStatus['id'] . ',' . $aRow['id'] . '); return false;">
                ' . $leadChangeStatus['name'] . '
                </a>
                </li>';
            }
        }
        $outputStatus .= '</ul>';
        $outputStatus .= '</div>';
    }
    $outputStatus .= '</span>';
}
$row[] = $outputStatus;
$row[] = ($aRow['lastcontact'] == '0000-00-00 00:00:00' || !is_date($aRow['lastcontact']) ? '' : '<span data-toggle="tooltip" data-title="' . _dt($aRow['lastcontact']) . '" class="text-has-action is-date">' . time_ago($aRow['lastcontact']) . '</span>');
if ($aRow['lm_follow_up'] == 1) {
   $result_data =  $this->ci->lead_manager_model->get_follow_up_date($aRow['id']);
    $row[] = '<span class="inline-block label label-warning">' . $result_data->lm_follow_up_date.'</span>';
}else{
    $row[] = '<span class="inline-block label label-primary">' . _l('lead_manger_dt_follow_up_no').'</span>';
}
$row[] = '<span data-toggle="tooltip" data-title="' . _dt($aRow['dateadded']) . '" class="text-has-action is-date">' . time_ago($aRow['dateadded']) . '</span>';
foreach ($customFieldsColumns as $customFieldColumn) {
    $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
}
$remarkadd_fields='<a href="javascript:void(0);" title="Add remarks"><i class="fa fa-file-text-o" aria-hidden="true" onclick="saveMeetingRemark('.$aRow['id'].',1);"></i></a>&nbsp;&nbsp;<a href="javascript:void(0);" title="View remarks"><i class="fa fa-eye" aria-hidden="true" onclick="showMeetingRemark('.$aRow['id'].',1);"></i></a>';
$row[]=$remarkadd_fields;
$row['DT_RowId'] = 'lead_' . $aRow['id'];
if ($aRow['assigned'] == get_staff_user_id()) {
    $row['DT_RowClass'] = 'alert-info';
}
if (isset($row['DT_RowClass'])) {
    $row['DT_RowClass'] .= ' has-row-options';
} else {
    $row['DT_RowClass'] = 'has-row-options';
}
$row = hooks()->apply_filters('leads_table_row_data', $row, $aRow);
$output['aaData'][] = $row;
}
