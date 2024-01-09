<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lead_manager_model extends App_Model

{
	public function __construct()

	{
		parent::__construct();
	}
	
    public function get($id = '', $where = [])
    {
        $this->db->select('*,' . db_prefix() . 'leads.name, ' . db_prefix() . 'leads.id,' . db_prefix() . 'leads_status.name as status_name,' . db_prefix() . 'leads_sources.name as source_name');
        $this->db->join(db_prefix() . 'leads_status', db_prefix() . 'leads_status.id=' . db_prefix() . 'leads.status', 'left');
        $this->db->join(db_prefix() . 'leads_sources', db_prefix() . 'leads_sources.id=' . db_prefix() . 'leads.source', 'left');

        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'leads.id', $id);
            $lead = $this->db->get(db_prefix() . 'leads')->row();
            if ($lead) {
                if ($lead->from_form_id != 0) {
                    $lead->form_data = $this->get_form([
                        'id' => $lead->from_form_id,
                    ]);
                }
                $lead->attachments = $this->get_lead_attachments($id);
                $lead->public_url  = leads_public_url($id);
            }

            return $lead;
        }

        return $this->db->get(db_prefix() . 'leads')->result_array();
    }

    public function get_source($id = false)
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'leads_sources')->row();
        }

        $this->db->order_by('name', 'asc');

        return $this->db->get(db_prefix() . 'leads_sources')->result_array();
    }
    
    public function get_status($id = '', $where = [])
    {
        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'leads_status')->row();
        }

        $statuses = $this->app_object_cache->get('leads-all-statuses');

        if (!$statuses) {
            $this->db->order_by('statusorder', 'asc');

            $statuses = $this->db->get(db_prefix() . 'leads_status')->result_array();
            $this->app_object_cache->add('leads-all-statuses', $statuses);
        }

        return $statuses;
    }


    public function update_status($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'leads_status', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Leads Status Updated [StatusID: ' . $id . ', Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    public function update_lead_status($data)
    {
        $this->db->select('status');
        $this->db->where('id', $data['leadid']);
        $_old = $this->db->get(db_prefix() . 'leads')->row();

        $old_status = '';

        if ($_old) {
            $old_status = $this->get_status($_old->status);
            if ($old_status) {
                $old_status = $old_status->name;
            }
        }

        $affectedRows   = 0;
        $current_status = $this->get_status($data['status'])->name;

        $this->db->where('id', $data['leadid']);
        $this->db->update(db_prefix() . 'leads', [
            'status' => $data['status'],
        ]);

        $_log_message = '';

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            if ($current_status != $old_status && $old_status != '') {
                $_log_message    = 'not_lead_activity_status_updated';
                $additional_data = serialize([
                    get_staff_full_name(),
                    $old_status,
                    $current_status,
                ]);

                hooks()->do_action('lead_status_changed', [
                    'lead_id'    => $data['leadid'],
                    'old_status' => $old_status,
                    'new_status' => $current_status,
                ]);
            }
            $this->db->where('id', $data['leadid']);
            $this->db->update(db_prefix() . 'leads', [
                'last_status_change' => date('Y-m-d H:i:s'),
            ]);
        }
        if (isset($data['order'])) {
            foreach ($data['order'] as $order_data) {
                $this->db->where('id', $order_data[0]);
                $this->db->update(db_prefix() . 'leads', [
                    'leadorder' => $order_data[1],
                ]);
            }
        }
        if ($affectedRows > 0) {
            if ($_log_message == '') {
                return true;
            }
            $this->log_lead_activity($data['leadid'], $_log_message, false, $additional_data);

            return true;
        }

        return false;
    }

    public function lead_manger_activity_log($data='')
    {
        $this->db->insert(db_prefix() . 'lead_manager_activity_log', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id){
            return true;
        }    
    }
    public function get_lead_manager_activity_log($id)
    {
        $sorting = hooks()->apply_filters('lead_activity_log_default_sort', 'ASC');

        $this->db->where('lead_id', $id);
        $this->db->order_by('date', $sorting);

        return $this->db->get(db_prefix() . 'lead_manager_activity_log')->result_array();
    }
    public function zoomMeetingDetails($id)
    {
        $sorting = hooks()->apply_filters('lead_activity_log_default_sort', 'ASC');

        $this->db->where('id', $id);
        $this->db->order_by('created_at', $sorting);

        return $this->db->get(db_prefix() . 'lead_manager_zoom_meeting')->row();
    }
    public function zoom_meeting_remarksDetails($id,$rel_type)
    {
        $this->db->where(['rel_type'=> $rel_type,'rel_id'=>$id]);

        $this->db->order_by('date','DESC');

        return $this->db->get(db_prefix() . 'lead_manager_meeting_remark')->result();
    }
    public function update_last_contact($lead_id='')
    {
        if($lead_id){
            $this->db->where('id',$lead_id);
            $this->db->update(db_prefix() . 'leads', [
                'lastcontact' => date('Y-m-d H:i:s'),
            ]);
            if ($this->db->affected_rows() > 0) {
                return true;
            }
        }
        return false;
    }

    public function save_zoom_meeting($data,$meeting_res_data)
    {
        if($meeting_res_data){
            $save_data = array(
                'name' =>$data['user_name'] ,
                'staff_email' =>$data['staff_email'] ,
                'staff_id' =>get_staff_user_id() ,
                'leadid' =>$data['lead_id'] ,
                'staff_name' =>$data['staff_name'] ,
                'email' =>$data['user_email'] ,
                'meeting_id' =>$meeting_res_data->id ,
                'join_url'=>$meeting_res_data->join_url ,
                'start_url'=>$meeting_res_data->start_url ,
                'country'=>$data['meeting_country'],
                'timezone'=>$data['zoom_timezone'],
                'meeting_date'=>$data['meeting_start_date'],
                'meeting_duration'=>$data['meeting_duration'],
                'meeting_option'=>implode(",",$data['meeting_option']),
                'meeting_agenda'=>$data['meeting_agenda'],
                'meeting_description'=>$data['meeting_description'],
                'status'=>1,
                'password'=>$meeting_res_data->password,
                'created_at'=>date('Y-m-d H:i:s'),
            );
            $this->db->insert(db_prefix() . 'lead_manager_zoom_meeting', $save_data);
            $insertId = $this->db->insert_id();
            if($insertId){
               $this->send_email_to_customer($insertId);
               $this->send_email_to_staff($insertId);
               return true;
           }
       }
       return false;
   }

   public function send_email_to_staff($id)
   {    
    $this->db->where(db_prefix() . 'lead_manager_zoom_meeting.id', $id);
    $meeting_data = $this->db->get(db_prefix() . 'lead_manager_zoom_meeting')->row();
    $sent = lead_manager_send_mail_template('zoom_link_send_to_staff',$meeting_data);
    if ($sent) {
        return true;
    }

    return false;
}
public function send_email_to_customer($id)
{    
    $this->db->where(db_prefix() . 'lead_manager_zoom_meeting.id', $id);
    $meeting_data = $this->db->get(db_prefix() . 'lead_manager_zoom_meeting')->row();
    $sent = lead_manager_send_mail_template('zoom_link_send_to_customer',$meeting_data);
    if ($sent) {
        return true;
    }
    return false;
}

public function get_zoom_statuses()
{
    $statuses = hooks()->apply_filters('before_get_zoom_statuses', [
        [
            'id'             => 0,
            'color'          => '#d9534f',
            'name'           => _l('Cancelled'),
            'order'          => 4,
            'filter_default' => true,
        ],
        [
            'id'             => 1,
            'color'          => '#f0ad4e',
            'name'           => _l('Waiting'),
            'order'          => 1,
            'filter_default' => true,
        ],

        [
            'id'             => 2,
            'color'          => '#d9534f',
            'name'           => _l('Closed'),
            'order'          => 3,
            'filter_default' => true,
        ],
        [
            'id'             => 3,
            'color'          => '#3c763d',
            'name'           => _l('Completed'),
            'order'          => 2,
            'filter_default' => true,
        ],

    ]);

    usort($statuses, function ($a, $b) {
        return $a['order'] - $b['order'];
    });

    return $statuses;
}

public function delete_zoom_meeting($id)
{
    if($id){
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'lead_manager_zoom_meeting');
        return true;
    }
    return false;
} 
public function update_meeting_status($data)
{
    if($data){
        $this->db->where('id', $data['leadid']);
        $this->db->update(db_prefix() . 'lead_manager_zoom_meeting', [
            'status' => $data['status'],
        ]);

        return true;
    }
    return false;
}

public function get_staff_number()
{
   $this->db->where(['relid'=>get_staff_user_id(),'fieldto'=>'staff']);
   $res2= $this->db->get(db_prefix() . 'customfieldsvalues')->row();
   return $res2->value;
}

public function save_meeting_remark($data)
{
    if($data){
        $saveData=array(
            'rel_id'=>$data['meeting_id'],
            'remark'=>$data['remark'],
            'date'=>date('Y-m-d H:i:s'),
            'rel_type'=>$data['rel_type'],
            'lm_follow_up_date' => isset($data['lm_follow_up']) ? $data['lm_follow_up'] : NULL
        );
        $this->db->insert(db_prefix() . 'lead_manager_meeting_remark',$saveData);
        if(isset($data) && isset($data['lm_follow_up']) && !empty($data['lm_follow_up'])){
            $this->db->where('id',$data['meeting_id']);
            $this->db->update(db_prefix() .'leads',array('lm_follow_up' => 1));
        }else{
            $this->db->where('id',$data['meeting_id']);
            $this->db->update(db_prefix() .'leads',array('lm_follow_up' => 0));
        }
        return true;
    }
    return false;
}

function get_total_calls($data=array()){
    $this->db->from(db_prefix() . 'lead_manager_activity_log');
    $this->db->select('direction,count("direction") as total');
    if(!is_admin()){
        $this->db->where(['staff_id'=>get_staff_user_id()]);
    }
    $this->db->where(['type'=>'audio_call']);
    if(isset($data) && !empty($data)){
        if(isset($data['staff_id']) && !empty($data['staff_id'])){
            $this->db->where(['staff_id'=>$data['staff_id']]);
            $this->db->where('date >','DATE_ADD(NOW(), INTERVAL -'.$data['days'].' DAY)', FALSE);
        }else{
            $this->db->where('date >','DATE_ADD(NOW(), INTERVAL -'.$data['days'].' DAY)', FALSE);
        }
    }else{
       $this->db->where('date >','DATE_ADD(NOW(), INTERVAL -1 DAY)', FALSE);
   }
   $query = $this->db->group_by('direction')->get();

   $data =  $query->result(); 
   $result = null;
   if(isset($data) && !empty($data)){
    foreach($data as $obj){
        $result[$obj->direction] = $obj->total ? $obj->total : 0;
    }
}
return $result;
}

public function get_total_sms($data=array())
{
 $this->db->from(db_prefix() . 'lead_manager_activity_log');
 $this->db->select('count("id") as total');
 if(!is_admin()){
    $this->db->where(['staff_id'=>get_staff_user_id()]);
}
$this->db->where(['type'=>'sms']);
if(isset($data) && !empty($data)){
    if(isset($data['staff_id']) && !empty($data['staff_id'])){
        $this->db->where(['staff_id'=>$data['staff_id']]);
        $this->db->where('date >','DATE_ADD(NOW(), INTERVAL -'.$data['days'].' DAY)', FALSE);
    }else{
        $this->db->where('date >','DATE_ADD(NOW(), INTERVAL -'.$data['days'].' DAY)', FALSE);
        ;
    }
}else{
    $this->db->where('date >','DATE_ADD(NOW(), INTERVAL -1 DAY)', FALSE);
}
$query = $this->db->get();
$data =  $query->result(); 
$result = null;
if(isset($data) && !empty($data)){
    foreach($data as $obj){
        $result['sent'] = $obj->total ? $obj->total : 0;
    }
}
return $result['sent'];
}
public function get_total_missed_call($data=array())
{
 $this->db->from(db_prefix() . 'lead_manager_missed_calls');
 $this->db->select('count("id") as total');
 if(!is_admin()){
    $this->db->where(['staff_id'=>get_staff_user_id()]);
}
if(isset($data) && !empty($data)){
    if(isset($data['staff_id']) && !empty($data['staff_id'])){
        $this->db->where(['staff_id'=>$data['staff_id']]);
        $this->db->where('date >','DATE_ADD(NOW(), INTERVAL -'.$data['days'].' DAY)', FALSE);
    }else{
        $this->db->where('date >','DATE_ADD(NOW(), INTERVAL -'.$data['days'].' DAY)', FALSE);
    }
}else{
    $this->db->where('date >','DATE_ADD(NOW(), INTERVAL -1 DAY)', FALSE);
}
$query = $this->db->get();
$data =  $query->result(); 
$result = null;
if(isset($data) && !empty($data)){
    foreach($data as $obj){
        $result['missed'] = $obj->total ? $obj->total : 0;
    }
}
return $result['missed'];
}

public function get_total_leads_converted($data=array())
{
 $this->db->from(db_prefix() . 'leads');
 $this->db->select('count("id") as total');
 if(!is_admin()){
    $this->db->where(['assigned'=>get_staff_user_id()]);
}
$this->db->where(['converted_by_lead_manager'=>TRUE]);

if(isset($data) && !empty($data)){
    if(isset($data['staff_id']) && !empty($data['staff_id'])){
        $this->db->where(['assigned'=>$data['staff_id']]);
        $this->db->where('last_status_change >','DATE_ADD(NOW(), INTERVAL -'.$data['days'].' DAY)', FALSE);
    }else{
        $this->db->where('last_status_change >','DATE_ADD(NOW(), INTERVAL -'.$data['days'].' DAY)', FALSE);
    }
}else{
    $this->db->where('last_status_change >','DATE_ADD(NOW(), INTERVAL -1 DAY)', FALSE);
}
$query = $this->db->get();
$data =  $query->result(); 
$result = null;
if(isset($data) && !empty($data)){
    foreach($data as $obj){
        $result['converted'] = $obj->total ? $obj->total : 0;
    }
}
return $result['converted'];
}

public function get_total_calls_duration($data=array())
{
 $this->db->from(db_prefix() . 'lead_manager_activity_log');
 $this->db->select('direction,SUM(`call_duration`) as total');
 if(!is_admin()){
    $this->db->where(['staff_id'=>get_staff_user_id()]);
}
$this->db->where(['type'=>'audio_call']);
if(isset($data) && !empty($data)){
    if(isset($data['staff_id']) && !empty($data['staff_id'])){
        $this->db->where(['staff_id'=>$data['staff_id']]);
        $this->db->where('date >','DATE_ADD(NOW(), INTERVAL -'.$data['days'].' DAY)', FALSE);
    }else{
        $this->db->where('date >','DATE_ADD(NOW(), INTERVAL -'.$data['days'].' DAY)', FALSE);
    }
}else{
    $this->db->where('date >','DATE_ADD(NOW(), INTERVAL -1 DAY)', FALSE);
}
$query = $this->db->group_by('direction')->get();
$data =  $query->result(); 
$result = null;
if(isset($data) && !empty($data)){
    foreach($data as $obj){
        $result[$obj->direction] = $obj->total ? gmdate("H:i:s", $obj->total) : '00:00:00';
    }
}
return $result;
}
public function get_total_zoom_sheduled($data=array())
{
 $this->db->from(db_prefix() . 'lead_manager_zoom_meeting');
 $this->db->select('status,count("id") as total');
 if(!is_admin()){
    $this->db->where(['staff_id'=>get_staff_user_id()]);
}

if(isset($data) && !empty($data)){
    if(isset($data['staff_id']) && !empty($data['staff_id'])){
        $this->db->where(['staff_id'=>$data['staff_id']]);
        $this->db->where('meeting_date >','DATE_ADD(NOW(), INTERVAL -'.$data['days'].' DAY)', FALSE);
    }else{
        $this->db->where('meeting_date >','DATE_ADD(NOW(), INTERVAL -'.$data['days'].' DAY)', FALSE);
    }
}else{
    $this->db->where('meeting_date >','DATE_ADD(NOW(), INTERVAL -1 DAY)', FALSE);
}

$query = $this->db->group_by('status')->get();
$data =  $query->result(); 
$result['cancelled'] = 0;
$result['sheduled'] = 0;
$result['closed'] = 0;
$result['completed'] = 0;
if(isset($data) && !empty($data)){
    foreach($data as $obj){
        if($obj->status == '0'){
            $result['cancelled'] = $obj->total ? $obj->total : 0;
        }elseif($obj->status == '1'){
            $result['sheduled'] = $obj->total ? $obj->total : 0;
        }elseif($obj->status == '3'){
            $result['completed'] = $obj->total ? $obj->total : 0;
        }elseif($obj->status == '2'){
            $result['closed'] = $obj->total ? $obj->total : 0;
        }
    }
}
return $result;
}
public function get_lead_attachments($id = '', $attachment_id = '', $where = [])
{
    $this->db->where($where);
    $idIsHash = !is_numeric($attachment_id) && strlen($attachment_id) == 32;
    if (is_numeric($attachment_id) || $idIsHash) {
        $this->db->where($idIsHash ? 'attachment_key' : 'id', $attachment_id);

        return $this->db->get(db_prefix() . 'files')->row();
    }
    $this->db->where('rel_id', $id);
    $this->db->where('rel_type', 'lead');
    $this->db->order_by('dateadded', 'DESC');

    return $this->db->get(db_prefix() . 'files')->result_array();
}
public function log_lead_activity($id, $description, $integration = false, $additional_data = '')
{
    $log = [
        'date'            => date('Y-m-d H:i:s'),
        'description'     => $description,
        'leadid'          => $id,
        'staffid'         => get_staff_user_id(),
        'additional_data' => $additional_data,
        'full_name'       => get_staff_full_name(get_staff_user_id()),
    ];
    if ($integration == true) {
        $log['staffid']   = 0;
        $log['full_name'] = '[CRON]';
    }

    $this->db->insert(db_prefix() . 'lead_activity_log', $log);

    return $this->db->insert_id();
}
public function get_follow_up_date($leadId)
{
    $this->db->where(['rel_id'=>$leadId]);
    $this->db->limit(1);
    $this->db->order_by('date','DESC');
    $this->db->select('lm_follow_up_date');
    return $this->db->get(db_prefix() . 'lead_manager_meeting_remark')->row();
}
public function delete($id)
{
    $affectedRows = 0;

    hooks()->do_action('before_lead_deleted', $id);

    $lead = $this->get($id);

    $this->db->where('id', $id);
    $this->db->delete(db_prefix() . 'leads');
    if ($this->db->affected_rows() > 0) {
        log_activity('Lead Deleted [Deleted by: ' . get_staff_full_name() . ', ID: ' . $id . ']');

        $attachments = $this->get_lead_attachments($id);
        foreach ($attachments as $attachment) {
            $this->delete_lead_attachment($attachment['id']);
        }

            // Delete the custom field values
        $this->db->where('relid', $id);
        $this->db->where('fieldto', 'leads');
        $this->db->delete(db_prefix() . 'customfieldsvalues');

        $this->db->where('leadid', $id);
        $this->db->delete(db_prefix() . 'lead_activity_log');

        $this->db->where('leadid', $id);
        $this->db->delete(db_prefix() . 'lead_integration_emails');

        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'lead');
        $this->db->delete(db_prefix() . 'notes');

        $this->db->where('rel_type', 'lead');
        $this->db->where('rel_id', $id);
        $this->db->delete(db_prefix() . 'reminders');

        $this->db->where('rel_type', 'lead');
        $this->db->where('rel_id', $id);
        $this->db->delete(db_prefix() . 'taggables');

        $this->load->model('proposals_model');
        $this->load->model('tasks_model');
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'lead');

        //lead_manager activity log

        $this->db->where('lead_id', $id);
        $this->db->delete(db_prefix() . 'lead_manager_activity_log');

        $this->db->where('rel_id', $id);
        $this->db->delete(db_prefix() . 'lead_manager_meeting_remark');

        $this->db->where('leadid', $id);
        $this->db->delete(db_prefix() . 'lead_manager_zoom_meeting');


        $proposals = $this->db->get(db_prefix() . 'proposals')->result_array();

        foreach ($proposals as $proposal) {
            $this->proposals_model->delete($proposal['id']);
        }

            // Get related tasks
        $this->db->where('rel_type', 'lead');
        $this->db->where('rel_id', $id);
        $tasks = $this->db->get(db_prefix() . 'tasks')->result_array();
        foreach ($tasks as $task) {
            $this->tasks_model->delete_task($task['id']);
        }

        if (is_gdpr()) {
            $this->db->where('(description LIKE "%' . $lead->email . '%" OR description LIKE "%' . $lead->name . '%" OR description LIKE "%' . $lead->phonenumber . '%")');
            $this->db->delete(db_prefix() . 'activity_log');
        }

        $affectedRows++;
    }
    if ($affectedRows > 0) {
        return true;
    }

    return false;
}
public function delete_lead_attachment($id)
{
    $attachment = $this->get_lead_attachments('', $id);
    $deleted    = false;

    if ($attachment) {
        if (empty($attachment->external)) {
            unlink(get_upload_path_by_type('lead') . $attachment->rel_id . '/' . $attachment->file_name);
        }
        $this->db->where('id', $attachment->id);
        $this->db->delete(db_prefix() . 'files');
        if ($this->db->affected_rows() > 0) {
            $deleted = true;
            log_activity('Lead Attachment Deleted [ID: ' . $attachment->rel_id . ']');
        }

        if (is_dir(get_upload_path_by_type('lead') . $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
            $other_attachments = list_files(get_upload_path_by_type('lead') . $attachment->rel_id);
            if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                delete_dir(get_upload_path_by_type('lead') . $attachment->rel_id);
            }
        }
    }

    return $deleted;
}
public function get_form($where)
{
    $this->db->where($where);

    return $this->db->get(db_prefix() . 'web_to_lead')->row();
}
}
?>