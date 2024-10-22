<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $isRTL = (is_rtl() ? 'true' : 'false'); ?>

<!DOCTYPE html>
<html lang="<?php echo $locale; ?>" dir="<?php echo ($isRTL == 'true') ? 'rtl' : 'ltr' ?>">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title><?php echo isset($title) ? $title : get_option('companyname'); ?></title>

    <?php echo app_compile_css(); ?>
    <?php render_admin_js_variables(); ?>

    <!-- Added by DEEP BASAK on January 10, 2024 -->
    <style>
        .page_loader{
			position: fixed;
			z-index: 99999;
			background: rgba(255,255,255,.5);
			width: 100%;
			height: 100%;
			overflow: hidden;
            text-align: center;
		}
    </style>

    <script>
        var baseUrl = '<?= base_url() ?>';
    var totalUnreadNotifications = <?php echo $current_user->total_unread_notifications; ?>,
        proposalsTemplates = <?php echo json_encode(get_proposal_templates()); ?>,
        contractsTemplates = <?php echo json_encode(get_contract_templates()); ?>,
        billingAndShippingFields = ['billing_street', 'billing_city', 'billing_state', 'billing_zip', 'billing_country',
            'shipping_street', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country'
        ],
        isRTL = '<?php echo $isRTL; ?>',
        taskid, taskTrackingStatsData, taskAttachmentDropzone, taskCommentAttachmentDropzone, newsFeedDropzone,
        expensePreviewDropzone, taskTrackingChart, cfh_popover_templates = {},
        _table_api;
    </script>
    <?php app_admin_head(); ?>
</head>

<body <?php echo admin_body_class(isset($bodyclass) ? $bodyclass : ''); ?>>
    
    <!-- Added by DEEP BASAK on January 10, 2024 -->
    <div class="page_loader" style="display:none;">
		<div class="d-flex page_loader_content justify-content-center">
			<img src="<?= base_url('assets/images/preloader.gif') ?>" style="width: 60px; padding-top: 210px;">
		</div>
	</div>
    <?php hooks()->do_action('after_body_start'); ?>


<!-- For Attendance Auto Update if SOme employee forgot to logout by DEEP BASAK on January 20, 2024 -->
<?php
$CI = &get_instance();
$CI->load->model('common/Common_model');
$sql = "SELECT *
FROM `tbl_staff_attendance`
WHERE `is_active` = 'Y'
AND `check_out_date` IS NULL";
$attendance_details = $CI->Common_model->callSP($sql);

if(!empty($attendance_details)){
    foreach($attendance_details as $key => $val){
        $check_out_date = $val['check_in_date'] . ' 23:59:59';

        $startTime = new DateTime($val['check_in']);
        $endTime = new DateTime($check_out_date);
        $interval = $startTime->diff($endTime);
        
        // Calculate the difference in hours as a float
        $hours = $interval->h + $interval->i / 60 + $interval->s / 3600;
        $today_hours = round($hours, 2);
        
        $data = array(
            'check_out'             => $check_out_date,
            'today_hour'            => $today_hours,
            'check_out_date'        => $val['check_in_date'],
            'check_out_location'    => $val['check_in_location'],
            'updated_at'            => date('Y-m-d H:i:s'),
            'updated_by'            => get_staff_user_id()
        );
        if($val['check_in_date'] != date('Y-m-d')){
            $CI->Common_model->UpdateDB('tbl_staff_attendance', ['id' => $val['id']], $data);
        }
        
    }
}

//For staff Resignation automatcally inactive staff
//Added by DEEP BASAK on May 08, 2024
$sql = "SELECT * FROM tbl_staff_resignation WHERE is_active = 'Y' AND is_approve = 'A' AND notice_time = '".date('Y-m-d')."'";
$resignation_details = $CI->Common_model->callSP($sql);
if(!empty($resignation_details)){
    foreach($resignation_details as $key => $val){
        $data = array(
            'active'    => 0
        );
        $CI->Common_model->UpdateDB('tblstaff', ['staffid' => $val['staff_id']], $data);
    }
}

#Code for update the employee table for appraisal
//Added by DEEP BASAK on July 05, 2024
////CR by DEEP BASAK on July 08, 2024
$detail = $CI->Common_model->getAllData('tbl_staff_appraisal_history', '', '', ['is_active' => 'Y', 'is_approved' => 'A', 'appraisal_year' => date('Y')]);
// prx($CI->db->last_query());
if(!empty($detail)){
    foreach($detail as $key => $details){
        // prx(date('Y-m-d', strtotime($details->appraisal_time)));
        if(date('Y-m-d', strtotime($details->appraisal_time)) == date('Y-m-d')){
            $appraisal_type = json_decode($details->appraisal_type);
            foreach($appraisal_type as $key){
                if($key == 'S'){
                    $CI->Common_model->UpdateDB('tblstaff', ['staffid' => $details->staff_id], ['hourly_rate' => $details->new_salary]);
                } else if($key == 'D'){
                    $CI->Common_model->UpdateDB('tblstaff', ['staffid' => $details->staff_id], ['job_position' => $details->new_designation]);
                }
            }
            $CI->Common_model->UpdateDB('tbl_staff_appraisal_history', ['id' => $details->id], ['updated_at' => date('Y-m-d H:i:s'), 'updated_by' => get_staff_user_id(), 'appraisal_activate_at' => date('Y-m-d H:i:s')]);
        }
    }
}



?>