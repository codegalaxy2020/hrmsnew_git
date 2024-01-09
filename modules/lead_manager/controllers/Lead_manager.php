<?php defined('BASEPATH') or exit('No direct script access allowed');
$check =  __dir__ ;
$str= preg_replace('/\W\w+\s*(\W*)$/', '$1', $check);
$str.'/third_party/twilio-web/src/Twilio/autoload.php';
use Twilio\Rest\Client;


class Lead_manager extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('lead_manager_model');
        $this->load->model('clients_model');
        $this->load->library('mails/lead_manager_mail_template');
    }

    /* List all leads */
    public function index($id = '')
    {
        close_setup_menu();
        if (!is_staff_member()) {
            access_denied('Leads');
        }
        $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        if (is_gdpr() && get_option('gdpr_enable_consent_for_leads') == '1') {
            $this->load->model('gdpr_model');
            $data['consent_purposes'] = $this->gdpr_model->get_consent_purposes();
        }
        $data['summary']  = get_leads_summary();
        $data['statuses'] = $this->lead_manager_model->get_status();
        $data['sources']  = $this->lead_manager_model->get_source();
        $data['title']    = _l('lead_manager');
        $data['leadid'] = $id;
        $this->load->view('admin/leads/manage_leads', $data);
    }

    public function shedule_appointment($id = '')
    {
        if (!is_staff_member()) {
            access_denied('Leads');
        }
        $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        $data['title']    = _l('lead_manager_zoom_meetings');
        $data['leadid'] = $id;
        $this->load->view('admin/leads/manage_zoom_appointment', $data);
    }
    public function table()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }
        $this->app->get_table_data(module_views_path('lead_manager', 'table'));
    }
    public function zoom_appointment_table()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }
        $this->app->get_table_data(module_views_path('lead_manager', 'zoom_appointment_table'));
    }

    public function export($id)
    {
        if (is_admin()) {
            $this->load->library('gdpr/gdpr_lead');
            $this->gdpr_lead->export($id);
        }
    }

    /* Delete lead from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('leads'));
        }

        if (!is_lead_creator($id) && !has_permission('leads', '', 'delete')) {
            access_denied('Delte Lead');
        }
        $this->load->model('leads_model');
        $response = $this->leads_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('lead_lowercase')));
        } elseif ($response === true) {
            set_alert('success', _l('deleted', _l('lead')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('lead_lowercase')));
        }

        $ref = $_SERVER['HTTP_REFERER'];

        if (!$ref || strpos($ref, 'index/' . $id) !== false) {
            redirect(admin_url('leads'));
        }

        redirect($ref);
    }

    public function update_lead_status()
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            $this->lead_manager_model->update_lead_status($this->input->post());
        }
    }

    public function activity_log(){
        $id = $this->input->get('id');
        $data['activity_log']         = $this->lead_manager_model->get_lead_manager_activity_log($id);
        $leadWhere = (has_permission('leads', '', 'view') ? [] : '(assigned = ' . get_staff_user_id() . ' OR addedfrom=' . get_staff_user_id() . ' OR is_public=1)');
        $data['lead'] = $this->lead_manager_model->get($id, $leadWhere);
        $view = $this->load->view('lead_manager/avtivity_log_modal', $data, true);
        echo $view; exit();
    }
    public function send_sms_modal(){
        $id = $this->input->get('id');
        $leadWhere = (has_permission('leads', '', 'view') ? [] : '(assigned = ' . get_staff_user_id() . ' OR addedfrom=' . get_staff_user_id() . ' OR is_public=1)');
        $data['lead'] = $this->lead_manager_model->get($id, $leadWhere);
        $view = $this->load->view('lead_manager/send_sms_modal', $data, true);
        echo $view; exit();
    }
    public function send_zoom_link_modal(){
        $id = $this->input->get('id');
        $leadWhere = (has_permission('leads', '', 'view') ? [] : '(assigned = ' . get_staff_user_id() . ' OR addedfrom=' . get_staff_user_id() . ' OR is_public=1)');
        $data['lead'] = $this->lead_manager_model->get($id, $leadWhere);
        if(isset($data['lead']->email) && !empty($data['lead']->email)){
            $view = $this->load->view('lead_manager/send_zoom_link_modal', $data, true);
            echo $view; exit();    
        }else{
            echo 'email not found!'; exit;
        }

    }
    public function send_sms(){
        $activeSmsGateway = $this->app_sms->get_active_gateway();
        $data =array();
        if (isset($activeSmsGateway) && !empty($activeSmsGateway)) {
            $lead = $this->lead_manager_model->get($this->input->post('lm_leadid'));
            $phoneNumber = $lead->phonenumber;
            app_init_sms_gateways();
            $retval = $this->{'sms_'.$activeSmsGateway['id']}->send(
                $phoneNumber,
                clear_textarea_breaks(nl2br($this->input->post('message')))
            );
            $response = ['success' => false];
            if (isset($GLOBALS['sms_error'])) {
                $response['error'] = $GLOBALS['sms_error'];
            } else {
                $response['success'] = true;
                $data['type'] = 'sms';
                $data['lead_id'] = $this->input->post('lm_leadid');
                $data['date'] = date("Y-m-d H:i:s");
                $data['description'] = $this->input->post('message');
                $data['additional_data'] = null;
                $data['staff_id'] = $lead->assigned;
                $data['direction'] = 'outgoing';
                $response_activity = $this->lead_manager_model->lead_manger_activity_log($data);
                $this->lead_manager_model->update_last_contact($this->input->post('lm_leadid'));
            }
            echo json_encode($response);
            die;
        }else{
            $response['error'] = "Not sent. Gatway is undefined/inactive!";
            echo json_encode($response);
            die;
        }

    }
    public function bulk_action()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }
        if ($this->input->post()) {
            $ids                   = $this->input->post('ids');
            $message                = $this->input->post('message');
            $failedData = array();
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    $activeSmsGateway = $this->app_sms->get_active_gateway();
                    $data =array();
                    if ($message) {
                        $lead = $this->lead_manager_model->get($id);
                        $phoneNumber = $lead->phonenumber;
                        app_init_sms_gateways();
                        $retval = $this->{'sms_'.$activeSmsGateway['id']}->send(
                            $phoneNumber,
                            clear_textarea_breaks(nl2br($message))
                        );
                        $response = ['success' => false];
                        if (isset($GLOBALS['sms_error'])) {
                            $failedData[$id] = $GLOBALS['sms_error'];
                        } else {
                            $data['type'] = 'sms';
                            $data['is_audio_call_recorded'] = 0;
                            $data['lead_id'] = $id;
                            $data['date'] = date("Y-m-d H:i:s");
                            $data['description'] = $message;
                            $data['additional_data'] = null;
                            $data['staff_id'] = $lead->assigned;
                            $data['direction'] = 'outgoing';
                            $response_activity = $this->lead_manager_model->lead_manger_activity_log($data);
                            $this->lead_manager_model->update_last_contact($id);
                        }
                    }
                }
                echo json_encode([
                    'success'  => _l('lead_manager_bulk_sms_sent'),
                    'message'  => json_encode($failedData)
                ]);
                die;
            }else{
                set_alert('danger', _l('lead_manager_bulk_sms_empty_array'));
            }
        }
    }
    public function dashboard()
    {
        if (!$this->input->is_ajax_request()) {
         $data['audio_calls'] = $this->lead_manager_model->get_total_calls(); 
         $data['audio_calls_duration'] = $this->lead_manager_model->get_total_calls_duration(); 
         $data['sms'] = $this->lead_manager_model->get_total_sms(); 
         $data['missed_call'] = $this->lead_manager_model->get_total_missed_call(); 
         $data['leads_converted'] = $this->lead_manager_model->get_total_leads_converted(); 
         $data['zoom'] = $this->lead_manager_model->get_total_zoom_sheduled(); 
         $data['twilio'] = $this->active_twilio_account();
         $data['staff'] = $this->staff_model->get('', ['active' => 1]);
         $this->load->view('admin/leads/dashboard', $data);
     }else{
        $staff_id = '';
        if($this->input->get('staff_id')){
            $staff_id = $this->input->get('staff_id');
        }
        $request_data['staff_id'] = $staff_id;
        $request_data['days'] = $this->input->get('days');
        $data['audio_calls'] = $this->lead_manager_model->get_total_calls($request_data); 
        $data['audio_calls_duration'] = $this->lead_manager_model->get_total_calls_duration($request_data); 
        $data['sms'] = $this->lead_manager_model->get_total_sms($request_data); 
        $data['missed_call'] = $this->lead_manager_model->get_total_missed_call($request_data); 
        $data['leads_converted'] = $this->lead_manager_model->get_total_leads_converted($request_data); 
        $data['zoom'] = $this->lead_manager_model->get_total_zoom_sheduled($request_data); 
        $data['staff'] = $this->staff_model->get($staff_id, ['active' => 1]);
        $data['twilio'] = $this->active_twilio_account();
        $this->load->view('admin/leads/dashboard-ajax', $data);
    }

}

public function active_twilio_account()
{
 $sid  = get_option('call_twilio_account_sid');
 $token  = get_option('call_twilio_auth_token');
 $response = array();
 try { 
   $twilio = new Client($sid, $token);
   $incomingPhoneNumbers = $twilio->incomingPhoneNumbers
   ->read([]);
   $response['numbers'] = count($incomingPhoneNumbers);

   $account = $twilio->api->v2010->accounts($sid)
   ->fetch();
   $response['balance'] = $this->active_twilio_account_curl($account->subresourceUris['balance']);
   return $response; 

} catch (Exception $e) {
  set_alert('warning', 'Twilio '.$e->getMessage());
}

}

public function active_twilio_account_curl($url)
{
 $sid  = get_option('call_twilio_account_sid');
 $token  = get_option('call_twilio_auth_token');
 $curl = curl_init();
 curl_setopt($curl, CURLOPT_USERPWD, $sid . ":" . $token);
 curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.twilio.com/'.$url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));
 $response = curl_exec($curl);
 curl_close($curl);
 $data = json_decode($response);
 return $data->balance;
}

public function get_convert_data($id)
{
     $this->load->model('leads_model');
    if (!is_staff_member() || !$this->leads_model->staff_can_access_lead($id)) {
        ajax_access_denied();
    }
    if (is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1') {
        $this->load->model('gdpr_model');
        $data['purposes'] = $this->gdpr_model->get_consent_purposes($id, 'lead');
    }
    $data['lead'] = $this->lead_manager_model->get($id);
    $this->load->view('admin/leads/convert_to_customer', $data);
}
 public function convert_to_customer()
    {
        if (!is_staff_member()) {
            access_denied('Lead Convert to Customer');
        }
        $this->load->model('leads_model');
        if ($this->input->post()) {
            $default_country  = get_option('customer_default_country');
            $data             = $this->input->post();
            $data['password'] = $this->input->post('password', false);

            $original_lead_email = $data['original_lead_email'];
            unset($data['original_lead_email']);
            unset($data['converted_by_lead_manager']);
            
            if (isset($data['transfer_notes'])) {
                $notes = $this->misc_model->get_notes($data['leadid'], 'lead');
                unset($data['transfer_notes']);
            }

            if (isset($data['transfer_consent'])) {
                $this->load->model('gdpr_model');
                $consents = $this->gdpr_model->get_consents(['lead_id' => $data['leadid']]);
                unset($data['transfer_consent']);
            }

            if (isset($data['merge_db_fields'])) {
                $merge_db_fields = $data['merge_db_fields'];
                unset($data['merge_db_fields']);
            }

            if (isset($data['merge_db_contact_fields'])) {
                $merge_db_contact_fields = $data['merge_db_contact_fields'];
                unset($data['merge_db_contact_fields']);
            }

            if (isset($data['include_leads_custom_fields'])) {
                $include_leads_custom_fields = $data['include_leads_custom_fields'];
                unset($data['include_leads_custom_fields']);
            }

            if ($data['country'] == '' && $default_country != '') {
                $data['country'] = $default_country;
            }

            $data['billing_street']  = $data['address'];
            $data['billing_city']    = $data['city'];
            $data['billing_state']   = $data['state'];
            $data['billing_zip']     = $data['zip'];
            $data['billing_country'] = $data['country'];

            $data['is_primary'] = 1;
            $id                 = $this->clients_model->add($data, true);
            if ($id) {
                $primary_contact_id = get_primary_contact_user_id($id);

                if (isset($notes)) {
                    foreach ($notes as $note) {
                        $this->db->insert(db_prefix() . 'notes', [
                            'rel_id'         => $id,
                            'rel_type'       => 'customer',
                            'dateadded'      => $note['dateadded'],
                            'addedfrom'      => $note['addedfrom'],
                            'description'    => $note['description'],
                            'date_contacted' => $note['date_contacted'],
                            ]);
                    }
                }
                if (isset($consents)) {
                    foreach ($consents as $consent) {
                        unset($consent['id']);
                        unset($consent['purpose_name']);
                        $consent['lead_id']    = 0;
                        $consent['contact_id'] = $primary_contact_id;
                        $this->gdpr_model->add_consent($consent);
                    }
                }
                if (!has_permission('customers', '', 'view') && get_option('auto_assign_customer_admin_after_lead_convert') == 1) {
                    $this->db->insert(db_prefix() . 'customer_admins', [
                        'date_assigned' => date('Y-m-d H:i:s'),
                        'customer_id'   => $id,
                        'staff_id'      => get_staff_user_id(),
                    ]);
                }
                $this->leads_model->log_lead_activity($data['leadid'], 'not_lead_activity_converted', false, serialize([
                    get_staff_full_name(),
                ]));
                $default_status = $this->leads_model->get_status('', [
                    'isdefault' => 1,
                ]);
                $this->db->where('id', $data['leadid']);
                $this->db->update(db_prefix() . 'leads', [
                    'date_converted' => date('Y-m-d H:i:s'),
                    'status'         => $default_status[0]['id'],
                    'junk'           => 0,
                    'lost'           => 0,
                ]);
                // Check if lead email is different then client email
                $contact = $this->clients_model->get_contact(get_primary_contact_user_id($id));
                if ($contact->email != $original_lead_email) {
                    if ($original_lead_email != '') {
                        $this->leads_model->log_lead_activity($data['leadid'], 'not_lead_activity_converted_email', false, serialize([
                            $original_lead_email,
                            $contact->email,
                        ]));
                    }
                }
                if (isset($include_leads_custom_fields)) {
                    foreach ($include_leads_custom_fields as $fieldid => $value) {
                        // checked don't merge
                        if ($value == 5) {
                            continue;
                        }
                        // get the value of this leads custom fiel
                        $this->db->where('relid', $data['leadid']);
                        $this->db->where('fieldto', 'leads');
                        $this->db->where('fieldid', $fieldid);
                        $lead_custom_field_value = $this->db->get(db_prefix() . 'customfieldsvalues')->row()->value;
                        // Is custom field for contact ot customer
                        if ($value == 1 || $value == 4) {
                            if ($value == 4) {
                                $field_to = 'contacts';
                            } else {
                                $field_to = 'customers';
                            }
                            $this->db->where('id', $fieldid);
                            $field = $this->db->get(db_prefix() . 'customfields')->row();
                            // check if this field exists for custom fields
                            $this->db->where('fieldto', $field_to);
                            $this->db->where('name', $field->name);
                            $exists               = $this->db->get(db_prefix() . 'customfields')->row();
                            $copy_custom_field_id = null;
                            if ($exists) {
                                $copy_custom_field_id = $exists->id;
                            } else {
                                // there is no name with the same custom field for leads at the custom side create the custom field now
                                $this->db->insert(db_prefix() . 'customfields', [
                                    'fieldto'        => $field_to,
                                    'name'           => $field->name,
                                    'required'       => $field->required,
                                    'type'           => $field->type,
                                    'options'        => $field->options,
                                    'display_inline' => $field->display_inline,
                                    'field_order'    => $field->field_order,
                                    'slug'           => slug_it($field_to . '_' . $field->name, [
                                        'separator' => '_',
                                    ]),
                                    'active'        => $field->active,
                                    'only_admin'    => $field->only_admin,
                                    'show_on_table' => $field->show_on_table,
                                    'bs_column'     => $field->bs_column,
                                ]);
                                $new_customer_field_id = $this->db->insert_id();
                                if ($new_customer_field_id) {
                                    $copy_custom_field_id = $new_customer_field_id;
                                }
                            }
                            if ($copy_custom_field_id != null) {
                                $insert_to_custom_field_id = $id;
                                if ($value == 4) {
                                    $insert_to_custom_field_id = get_primary_contact_user_id($id);
                                }
                                $this->db->insert(db_prefix() . 'customfieldsvalues', [
                                    'relid'   => $insert_to_custom_field_id,
                                    'fieldid' => $copy_custom_field_id,
                                    'fieldto' => $field_to,
                                    'value'   => $lead_custom_field_value,
                                ]);
                            }
                        } elseif ($value == 2) {
                            if (isset($merge_db_fields)) {
                                $db_field = $merge_db_fields[$fieldid];
                                // in case user don't select anything from the db fields
                                if ($db_field == '') {
                                    continue;
                                }
                                if ($db_field == 'country' || $db_field == 'shipping_country' || $db_field == 'billing_country') {
                                    $this->db->where('iso2', $lead_custom_field_value);
                                    $this->db->or_where('short_name', $lead_custom_field_value);
                                    $this->db->or_like('long_name', $lead_custom_field_value);
                                    $country = $this->db->get(db_prefix() . 'countries')->row();
                                    if ($country) {
                                        $lead_custom_field_value = $country->country_id;
                                    } else {
                                        $lead_custom_field_value = 0;
                                    }
                                }
                                $this->db->where('userid', $id);
                                $this->db->update(db_prefix() . 'clients', [
                                    $db_field => $lead_custom_field_value,
                                ]);
                            }
                        } elseif ($value == 3) {
                            if (isset($merge_db_contact_fields)) {
                                $db_field = $merge_db_contact_fields[$fieldid];
                                if ($db_field == '') {
                                    continue;
                                }
                                $this->db->where('id', $primary_contact_id);
                                $this->db->update(db_prefix() . 'contacts', [
                                    $db_field => $lead_custom_field_value,
                                ]);
                            }
                        }
                    }
                }
                // set the lead to status client in case is not status client
                $this->db->where('isdefault', 1);
                $status_client_id = $this->db->get(db_prefix() . 'leads_status')->row()->id;
                $this->db->where('id', $data['leadid']);
                $this->db->update(db_prefix() . 'leads', [
                    'status' => $status_client_id,
                    'converted_by_lead_manager' => 1,
                    'last_status_change' => date('Y-m-d H:i:s'),
                ]);

                set_alert('success', _l('lead_to_client_base_converted_success'));

                if (is_gdpr() && get_option('gdpr_after_lead_converted_delete') == '1') {
                    // When lead is deleted
                    // move all proposals to the actual customer record
                    $this->db->where('rel_id', $data['leadid']);
                    $this->db->where('rel_type', 'lead');
                    $this->db->update('proposals', [
                        'rel_id'   => $id,
                        'rel_type' => 'customer',
                    ]);

                    $this->leads_model->delete($data['leadid']);

                    $this->db->where('userid', $id);
                    $this->db->update(db_prefix() . 'clients', ['leadid' => null]);
                }

                log_activity('Created Lead Client Profile [LeadID: ' . $data['leadid'] . ', ClientID: ' . $id . ']');
                hooks()->do_action('lead_converted_to_customer', ['lead_id' => $data['leadid'], 'customer_id' => $id]);
                redirect(admin_url('clients/client/' . $id));
            }
        }
    }

}
?>