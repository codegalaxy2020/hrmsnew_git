<?php
defined('BASEPATH') or exit('No direct script access allowed');

$statuses              = $this->ci->lead_manager_model->get_zoom_statuses();
$this->ci->db->query("SET sql_mode = ''");
$aColumns = [
    'id',
    'name',
    'email',
    'staff_name',
    'meeting_date',
    'status',
    'remark',
    'created_at',
];

$sIndexColumn = 'id';
$sTable       = db_prefix().'lead_manager_zoom_meeting';

$where  = [];
$filter = [];
$join = [];
if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}

$result =   data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    db_prefix().'lead_manager_zoom_meeting.email',
    db_prefix().'lead_manager_zoom_meeting.meeting_id',
    
]);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    $row[] = $aRow['id']; 
    $nameRow = '<a  href="#" onclick="zoomMeetingDetails(' . $aRow['id'] . ');">'.$aRow['name'].'</a>';
    $nameRow .= '<div class="row-options">';
    $nameRow .= '<a href="javascript:void(0);" onclick="zoomMeetingDetails(' . $aRow['id'] . ');" >View</a>';
    $nameRow .= ' | <a href="' . admin_url('lead_manager/zoom_meeting/delete_zoom_meeting/' . $aRow['id']) . '" title="' . _l('delete') . '" class="_delete text-danger"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
    $nameRow .= '</div>';

    $row[] = $nameRow;
    $row[] = $aRow['email'];
    $row[] = $aRow['staff_name'];
    $row[] = _dt($aRow['created_at']); 
    $row[] = _dt($aRow['meeting_date']);
    if($aRow['status'] !=null){
        $status = get_zoom_status_by_id($aRow['status']);
        $outputStatus = '<span class="inline-block lead-status-'.$aRow['status'].'"style="color:' . $status['color'] . ';border:1px solid ' . $status['color'] . '">'  . $status['name'];
        $locked=false;
        if (!$locked) {
            $outputStatus .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
            $outputStatus .= '<a href="#" style="font-size:14px;vertical-align:middle;" class="dropdown-toggle text-dark" id="tableLeadsStatus-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            $outputStatus .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
            $outputStatus .= '</a>';

            $outputStatus .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableLeadsStatus-' . $aRow['id'] . '">';
            foreach ($statuses as $leadChangeStatus) {
                if ($aRow['status'] != $leadChangeStatus['id']) {
                    $outputStatus .= '<li>
                    <a href="javascript:void(0);" onclick="update_meeting_status(' . $leadChangeStatus['id'] . ',' . $aRow['id'] . '); return false;">
                    ' . $leadChangeStatus['name'] . '
                    </a>
                    </li>';
                }
            }
            $outputStatus .= '</ul>';
            $outputStatus .= '</div>';
        }
        $outputStatus .= '</span>';
    }else{
        $outputStatus="Closed";
    }
    $row[] = $outputStatus;
    $remarkadd_fields='<a href="javascript:void(0);"><i class="fa fa-file-text-o" aria-hidden="true" onclick="saveMeetingRemark('.$aRow['id'].',2);"></i></a>&nbsp;&nbsp;<a href="javascript:void(0);"><i class="fa fa-eye" aria-hidden="true" onclick="showMeetingRemark('.$aRow['id'].',2);"></i></a>';
    $row[] = $remarkadd_fields;
    $row = hooks()->apply_filters('supply_chain_table_row_data', $row, $aRow);
    $output['aaData'][] = $row;
}