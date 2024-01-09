<?php

/**
 * Ensures that the module init file can't be accessed directly, only within the application.
 */
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Lead Manager
Description: Manage your lead by lead manager
Version: 1.0.1
Author: Zonvoir
Author URI: https://zonvoir.com/
Requires at least: 2.3.*
*/

if (!defined('MODULE_LEAD_MANAGER')) {
	define('MODULE_LEAD_MANAGER', basename(__DIR__));
}

$CI = &get_instance();
define('LEAD_MANAGER_ATTACHMENTS_FOLDER', FCPATH . 'uploads/lead_manager' . '/');
hooks()->add_action('admin_init', 'lead_manager_module_init_menu_items');
hooks()->add_action('admin_init', 'lead_manager_permissions');
hooks()->add_action('after_cron_run', 'busy_incoming_calls');

$CI->load->helper(MODULE_LEAD_MANAGER . '/lead_manager');

if (has_permission('lead_manager', '', 'can_audio_call') && get_option('call_twilio_active') && get_staff_own_twilio_number()) {
	hooks()->add_action('app_admin_head', 'lead_manager_soft_phone');
}

/**
* Register activation module hook
*/
register_activation_hook(MODULE_LEAD_MANAGER, 'lead_manager_module_activation_hook');

function lead_manager_module_activation_hook()
{
	$CI = &get_instance();
	require_once(__DIR__ . '/install.php');
}
/**

 * Register uninstall module hook

 */

register_uninstall_hook(MODULE_LEAD_MANAGER, 'lead_manager_module_uninstall_hook');

function lead_manager_module_uninstall_hook()
{
	$CI = &get_instance();
	require_once(__DIR__ . '/uninstall.php');
}

function lead_manager_module_init_menu_items() {
	$CI = &get_instance();

	$CI->app->add_quick_actions_link([
		'name'       => _l('lead_manager'),
		'url'        => 'lead_manager',
		'permission' => 'lead_manager',
		'position'   => 52,
	]);
	if (staff_can('view', 'settings')) {
		$CI->app_tabs->add_settings_tab('lead_manager', [
			'name'     => '' . _l('lead_manager') . '',
			'view'     => 'lead_manager/admin/settings',
			'position' => 36,
		]);
	}
	if (has_permission('lead_manager', '', 'view')) {
		$CI->app_menu->add_sidebar_menu_item('lead_manager', [
			'slug'     => 'lead_manager',
			'name'     => _l('lead_manager'),
			'position' => 5,
			'icon'     => 'fa fa-sitemap',
			'href'     => admin_url('lead_manager')
		]);
		$CI->app_menu->add_sidebar_children_item('lead_manager', [
			'slug'     => 'lead_manager_dashboard', 
			'name'     => _l('lead_manager_dashboard'),
			'href'     => admin_url('lead_manager/dashboard'), 
			'position' => 5,
		]);
		$CI->app_menu->add_sidebar_children_item('lead_manager', [
			'slug'     => 'lead_manager_appointment',  
			'name'     => _l('lead_manager_zoom_meetings'),
			'href'     => admin_url('lead_manager/shedule_appointment'),
			'position' => 5,
		]); 

		$CI->app_menu->add_sidebar_children_item('lead_manager', [
			'slug'     => 'lead_manager_leads', 
			'name'     => _l('lead_manager_lead'),
			'href'     => admin_url('lead_manager'), 
			'position' => 5,
		]);	
	}
	function get_staff_own_twilioNumber()
	{ 
		$id=get_staff_user_id();
		if($id){
			$CI = &get_instance();
			$CI->db->select('value');
			$CI->db->where(['relid'=>$id,'fieldto'=>'staff']);
			$res= $CI->db->get(db_prefix() . 'customfieldsvalues')->row();
			return ($res) ? str_replace(' ', '', $res->value) : '0';
		}
		return false;
	}

	$CI->app_scripts->add(MODULE_LEAD_MANAGER.'-js', base_url('modules/'.MODULE_LEAD_MANAGER.'/assets/js/'.MODULE_LEAD_MANAGER.'.js'));

	if (has_permission('lead_manager', '', 'can_audio_call') && get_option('call_twilio_active') && get_staff_own_twilio_number()) {

		$CI->app_css->add(MODULE_LEAD_MANAGER.'-soft-phone-css', base_url('modules/'.MODULE_LEAD_MANAGER.'/assets/css/soft_phone.css'));
		$CI->app_scripts->add(MODULE_LEAD_MANAGER.'-twilio-sdk-js', base_url('modules/'.MODULE_LEAD_MANAGER.'/assets/js/twilio.min.js'));
		$CI->app_scripts->add(MODULE_LEAD_MANAGER.'-soft-phone-js', base_url('modules/'.MODULE_LEAD_MANAGER.'/assets/js/soft_phone.js'));	
	}

}
function lead_manager_permissions()
{
	$capabilities = [];
	$capabilities['capabilities'] = [
		'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
		'delete' => _l('permission_delete'),
		'can_audio_call' => _l('lead_manger_permission_audio_call'),
		'can_video_call' => _l('lead_manger_permission_video_call'),
		'can_sms' => _l('lead_manger_permission_sms'),
	];

	register_staff_capabilities('lead_manager', $capabilities, _l('lead_manager'));
}

register_language_files(MODULE_LEAD_MANAGER, [MODULE_LEAD_MANAGER]);

function lead_manager_soft_phone()
{
	$CI = &get_instance();
	$softPhone = get_staff_own_twilioNumber();
	$data['staffPhone'] ='<script>let staffPhone="'.$softPhone.'"</script>';
	$data['staffPhoneNumber'] = $softPhone;
	$CI->load->view('lead_manager/soft_phone',$data);
}

