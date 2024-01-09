<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

delete_option('call_twilio_account_sid');
delete_option('call_twilio_auth_token');
delete_option('call_twilio_phone_number');
delete_option('call_twiml_app_sid');
delete_option('call_twilio_recording_active');
delete_option('call_twilio_active');
delete_option('call_zoom_active');
delete_option('zoom_api_key');
delete_option('zoom_secret_key');

if ($CI->db->table_exists(db_prefix() . 'lead_manager_activity_log')) {
  $CI->db->query('DROP TABLE `' . db_prefix() . 'lead_manager_activity_log`');
}
if ($CI->db->table_exists(db_prefix() . 'lead_manager_meeting_remark')) {
$CI->db->query('DROP TABLE `' . db_prefix() . 'lead_manager_meeting_remark`');
}
if ($CI->db->table_exists(db_prefix() . 'lead_manager_missed_calls')) {
$CI->db->query('DROP TABLE `' . db_prefix() . 'lead_manager_missed_calls`');
}
if ($CI->db->table_exists(db_prefix() . 'lead_manager_zoom_meeting')) {
$CI->db->query('DROP TABLE `' . db_prefix() . 'lead_manager_zoom_meeting`');
}
if ($CI->db->field_exists('converted_by_lead_manager', db_prefix() . 'leads')) {
  $CI->db->query("ALTER TABLE `".db_prefix()."leads` DROP COLUMN converted_by_lead_manager");
}
if ($CI->db->field_exists('lm_follow_up', db_prefix() . 'leads')) {
  $CI->db->query("ALTER TABLE `".db_prefix()."leads` DROP COLUMN lm_follow_up");
}
