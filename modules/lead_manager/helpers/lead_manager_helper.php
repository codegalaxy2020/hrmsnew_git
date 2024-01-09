<?php 
defined('BASEPATH') or exit('No direct script access allowed');
$check =  __dir__ ;
$str= preg_replace('/\W\w+\s*(\W*)$/', '$1', $check);
$str.'/third_party/twilio-web/src/Twilio/autoload.php';
use Twilio\Rest\Client;
function call_api_setting(){
    $data['account_sid'] = get_option('call_twilio_account_sid');
    $data['auth_token'] = get_option('call_twilio_auth_token');
    $data['twilio_number'] = get_option('call_twilio_phone_number');
    $data['twiml_app_sid'] = get_option('call_twiml_app_sid');    
    return $data;
}
function lead_manager_send_mail_template()
{  
    $params = func_get_args();
    return lead_manager_mail_template(...$params)->send();
}

function lead_manager_mail_template($class)
{
    $CI = &get_instance();

    $params = func_get_args();

    unset($params[0]);

    $params = array_values($params);

    $path = lead_manager_get_mail_template_path($class, $params);

    if (!file_exists($path)) {
        if (!defined('CRON')) {
            show_error('Mail Class Does Not Exists [' . $path . ']');
        } else {

            return false;
        }
    }
    if (!class_exists($class, false)) {
        include_once($path);
    }
    $instance = new $class(...$params);
    return $instance;
}
function lead_manager_get_mail_template_path($class, &$params)
{
    $CI  = &get_instance();
    $dir = APP_MODULES_PATH . 'lead_manager/libraries/mails/';
    if (isset($params[0]) && is_string($params[0]) && is_dir(module_dir_path($params[0]))) {
        $module = $CI->app_modules->get($params[0]);

        if ($module['activated'] === 1) {
            $dir = module_libs_path($params[0]) . 'mails/';
        }
        unset($params[0]);
        $params = array_values($params);
    }

    return $dir . ucfirst($class) . '.php';
}

function get_zoom_status_by_id($id)
{
    $CI = &get_instance();
    if (!class_exists('lead_manager_model')) {
        $CI->load->model('lead_manager_model');
    }
    $statuses = $CI->lead_manager_model->get_zoom_statuses();
    $status = [
      'id'    => 0,
      'color' => '#333',
      'name'  => '[Status Not Found]',
      'order' => 1,
  ];
  foreach ($statuses as $s) {
    if ($s['id'] == $id) {
        $status = $s;

        break;
    }
}
return $status;
}

function get_latest_zoom_meeting_remark($id)
{ 
    if($id){
     $CI = &get_instance();
     $CI->db->where(['rel_id'=>$id]);
     $CI->db->order_by('date','desc');
     $res= $CI->db->get(db_prefix() . 'lead_manager_meeting_remark')->row();
     return ($res)?$res->remark:'...';
 }
 return false;
}

function get_staff_own_twilio_number()
{ 
    $id=get_staff_user_id();
    if($id){
     $CI = &get_instance();
     $twilio_result = $CI->db->get_where(db_prefix().'customfields',['slug' => 'staff_twilio_phone_number','fieldto'=>'staff'])->row();
     if(isset($twilio_result) && !empty($twilio_result)){
        $CI->db->select('value');
        $CI->db->where(['relid'=>$id,'fieldto'=>'staff','fieldid'=>$twilio_result->id]);
        $res= $CI->db->get(db_prefix() . 'customfieldsvalues')->row();
        return ($res)?$res->value:'0';
    }else{
        return '0';
    }
}
return false;
}
function get_staff_by_twilio_number($number)
{
 if($number){
     $CI = &get_instance();
     $CI->db->select('relid');
     $CI->db->where(['value'=>$number,'fieldto'=>'staff']);
     $res= $CI->db->get(db_prefix() . 'customfieldsvalues')->row();
     return ($res) ? $res->relid : '0';
 }
 return false;
}
function busy_incoming_calls()
{
    $now = new DateTime();
    $todayDate = $now->format('Y-m-d');
    $dateObj = $todayDate.'T00:00:00Z';
    $sid  = get_option('call_twilio_account_sid');
    $token  = get_option('call_twilio_auth_token');
    $twilio = new Client($sid, $token);
    $calls = $twilio->calls->read(["direction" => "inbound-dial", 'startTimeAfter' => new \DateTime($dateObj)], 10);
    $i = 0;
    $data = [];
    if(isset($calls) && !empty($calls)){
        foreach ($calls as $record) {
            $staffId = get_staff_by_twilio_number($record->to);
            if($staffId){
                $callDate = $record->dateCreated;
                $data[$i]['status'] = $record->status;
                $data[$i]['from'] = $record->from;
                $data[$i]['direction'] = $record->direction;
                $data[$i]['to'] = $record->to;
                $data[$i]['sid'] = $record->sid;
                $data[$i]['parentCallSid'] = $record->parentCallSid;
                $data[$i]['dateCreated'] = $callDate->format('Y-m-d H:i:s');
                $data[$i]['dateCreated1'] = $record->dateCreated;
                $data[$i]['staff_id'] = $staffId;
                $pcalls = $twilio->calls->read(["parentCallSid" => $data[$i]['sid']], 1);
                if(isset($pcalls) && !empty($pcalls)){
                    foreach($pcalls as $childcall){
                        $data[$i]['child_status'] = $childcall->status;
                    }
                }
                if($data[$i]['status'] == 'busy' || $data[$i]['child_status'] == 'no-answer' || $data[$i]['status'] == 'failed' || $data[$i]['child_status'] == 'failed' || $data[$i]['child_status'] == 'busy' || $data[$i]['status'] == 'no-answer')
                {
                    addMissedCalls($data, $i);
                }                 
            } 
            $i++;
        }
    }   
}
function get_lead_name_by_number($number)
{
 if($number){
     $CI = &get_instance();
     $CI->db->select('name');
     $CI->db->where(['phonenumber' => $number]);
     $res= $CI->db->get(db_prefix() . 'leads')->row();
     return ($res) ? $res->name : false;
 }
 return false;
}
function addMissedCalls($data, $j)
{
    $CI = &get_instance();
    $CI->db->where('call_sid', $data[$j]['sid']);
    $q = $CI->db->get(db_prefix() . 'lead_manager_missed_calls');
    $leadName = get_lead_name_by_number($data[$j]['from']);
    if($q->num_rows() == 0){
        $insert_data = array(
            'staff_id' => $data[$j]['staff_id'],
            'call_sid' => $data[$j]['sid'],
            'staff_twilio_number' => $data[$j]['to'],
            'date' => $data[$j]['dateCreated'],
        );
        $CI->db->insert(db_prefix() . 'lead_manager_missed_calls',$insert_data);
        $notifcationArr = array(
            'isread' => 0,
            'isread_inline' => 0,
            'date' => $data[$j]['dateCreated'],
            'description' => 'You have missed call from: '.$data[$j]['from'].' at '.$data[$j]['dateCreated'],
            'fromuserid' => 0,
            'fromclientid' => 0,
            'from_fullname' => '//',
            'touserid' => $data[$j]['staff_id'],
            'link' => null,
            'additional_data' => null
        );
        if($leadName){
           $notifcationArr['description']  = 'You have missed call from: lead '.$leadName.' ('.$data[$j]['from'].')';
       }else{
           $notifcationArr['description']  = 'You have missed call from: '.$data[$j]['from'];
       }
       $CI->db->insert(db_prefix() . 'notifications',$notifcationArr);
   }
}
?>