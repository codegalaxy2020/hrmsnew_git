<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Lead_manager_merge_fields extends App_merge_fields
{
    public function build()
    {  
        return [
            [
                'name'      => 'Lead Name',
                'key'       => '{lead_name}',
                'available' => [
                    'leads',
                ],
                'templates' => [
                    'gdpr-removal-request-lead',
                ],
            ],
            [
                'name'      => 'Lead Email',
                'key'       => '{lead_email}',
                'available' => [
                    'leads',
                ],
                'templates' => [
                    'gdpr-removal-request-lead',
                ],
            ],
            [
                'name'      => 'Lead Position',
                'key'       => '{lead_position}',
                'available' => [
                    'leads',
                ],
                'templates' => [
                    'gdpr-removal-request-lead',
                ],
            ],
            [
                'name'      => 'Lead Website',
                'key'       => '{lead_website}',
                'available' => [
                    'leads',
                ],
                'templates' => [
                    'gdpr-removal-request-lead',
                ],
            ],
            [
                'name'      => 'Lead Description',
                'key'       => '{lead_description}',
                'available' => [
                    'leads',
                ],
            ],
            [
                'name'      => 'Lead Phone Number',
                'key'       => '{lead_phonenumber}',
                'available' => [
                    'leads',
                ],
                'templates' => [
                    'gdpr-removal-request-lead',
                ],
            ],
            [
                'name'      => 'Lead Company',
                'key'       => '{lead_company}',
                'available' => [
                    'leads',
                ],
                'templates' => [
                    'gdpr-removal-request-lead',
                ],
            ],
            [
                'name'      => 'Lead Country',
                'key'       => '{lead_country}',
                'available' => [
                    'leads',
                ],
                'templates' => [
                    'gdpr-removal-request-lead',
                ],
            ],
            [
                'name'      => 'Lead Zip',
                'key'       => '{lead_zip}',
                'available' => [
                    'leads',
                ],
            ],
            [
                'name'      => 'Lead City',
                'key'       => '{lead_city}',
                'available' => [
                    'leads',
                ],
            ],
            [
                'name'      => 'Lead State',
                'key'       => '{lead_state}',
                'available' => [
                    'leads',
                ],
            ],
            [
                'name'      => 'Lead Address',
                'key'       => '{lead_address}',
                'available' => [
                    'leads',
                ],
                'templates' => [
                    'gdpr-removal-request-lead',
                ],
            ],
            [
                'name'      => 'Lead Assigned',
                'key'       => '{lead_assigned}',
                'available' => [
                    'leads',
                ],
            ],
            [
                'name'      => 'Lead Status',
                'key'       => '{lead_status}',
                'available' => [
                    'leads',
                ],
            ],
            [
                'name'      => 'Lead Souce',
                'key'       => '{lead_source}',
                'available' => [
                    'leads',
                ],
            ],
            [
                'name'      => 'Lead Link',
                'key'       => '{lead_link}',
                'available' => [
                    'leads',
                ],
                'templates' => [
                    'gdpr-removal-request-lead',
                ],
            ],
            [
                'name'      => is_gdpr() && get_option('gdpr_enable_lead_public_form') == '1' ? 'Lead Public Form URL' : '',
                'key'       => is_gdpr() && get_option('gdpr_enable_lead_public_form') == '1' ? '{lead_public_form_url}' : '',
                'available' => [

                ],
                'templates' => [
                    'new-web-to-lead-form-submitted',
                ],
            ],
            [
                'name'      => is_gdpr() && get_option('gdpr_enable_consent_for_leads') == '1' ? 'Lead Consent Link' : '',
                'key'       => is_gdpr() && get_option('gdpr_enable_consent_for_leads') == '1' ? '{lead_public_consent_url}' : '',
                'available' => [

                ],
                'templates' => [
                    'new-web-to-lead-form-submitted',
                ],
            ],

            [
                'name'      => 'Customer Name',
                'key'       => '{customer_name}',
                'available' => [
                  
                ],
            ],
            [
                'name'      => 'Staff Name',
                'key'       => '{staff_name}',
                'available' => [
                  
                ],
            ],
            [
                'name'      => 'Topic',
                'key'       => '{topic}',
                'available' => [
                 
                ],
            ],
            [
                'name'      => 'Meeting ID',
                'key'       => '{meeting_id}',
                'available' => [
                   
                ],
            ],
            [
                'name'      => 'Meeting Time',
                'key'       => '{meeting_time}',
                'available' => [
                    
                ],
            ],
            [
                'name'      => 'Duration',
                'key'       => '{meeting_duration}',
                'available' => [
                    
                ],
            ],
            [
                'name'      => 'Password',
                'key'       => '{meeting_password}',
                'available' => [
                    
                ],
            ],
            [
                'name'      => 'Join Link',
                'key'       => '{join_url}',
                'available' => [
                    
                ],
            ],
        ];

    }

    /**
     * Lead merge fields
     * @param  mixed $id lead id
     * @return array
     */
    public function format($id,$meeting_data)
    {
  //echo $id; die();
        $fields = [];

        $fields['{lead_name}']               = '';
        $fields['{lead_email}']              = '';
        $fields['{lead_position}']           = '';
        $fields['{lead_company}']            = '';
        $fields['{lead_country}']            = '';
        $fields['{lead_zip}']                = '';
        $fields['{lead_city}']               = '';
        $fields['{lead_state}']              = '';
        $fields['{lead_address}']            = '';
        $fields['{lead_assigned}']           = '';
        $fields['{lead_status}']             = '';
        $fields['{lead_source}']             = '';
        $fields['{lead_phonenumber}']        = '';
        $fields['{lead_link}']               = '';
        $fields['{lead_website}']            = '';
        $fields['{lead_description}']        = '';
        $fields['{lead_public_form_url}']    = '';
        $fields['{lead_public_consent_url}'] = '';

        $fields['{customer_name}'] = '';
        $fields['{staff_name}'] = '';
        $fields['{topic}'] = '';
        $fields['{meeting_id}'] = '';
        $fields['{meeting_time}'] = '';
        $fields['{meeting_duration}'] = '';
        $fields['{meeting_password}'] = '';
        $fields['{join_url}'] = '';

        if (is_numeric($id)) {
            $this->ci->db->where('id', $id);
            $lead = $this->ci->db->get(db_prefix().'leads')->row();
        } else {
            $lead = $id;
        }

        if (!$lead) {
            return $fields;
        }

        $fields['{lead_public_form_url}']    = leads_public_url($lead->id);
        $fields['{lead_public_consent_url}'] = lead_consent_url($lead->id);
        $fields['{lead_link}']               = admin_url('leads/index/' . $lead->id);
        $fields['{lead_name}']               = $lead->name;
        $fields['{lead_email}']              = $lead->email;
        $fields['{lead_position}']           = $lead->title;
        $fields['{lead_phonenumber}']        = $lead->phonenumber;
        $fields['{lead_company}']            = $lead->company;
        $fields['{lead_zip}']                = $lead->zip;
        $fields['{lead_city}']               = $lead->city;
        $fields['{lead_state}']              = $lead->state;
        $fields['{lead_address}']            = $lead->address;
        $fields['{lead_website}']            = $lead->website;
        $fields['{lead_description}']        = $lead->description;
        $fields['{customer_name}'] = $meeting_data->name;
        $fields['{staff_name}'] = $meeting_data->staff_name;
        $fields['{topic}'] = $meeting_data->meeting_agenda;
        $fields['{meeting_id}'] = $meeting_data->meeting_id;
        $fields['{meeting_time}'] = $meeting_data->meeting_date;
        $fields['{meeting_duration}'] = $meeting_data->meeting_duration;
        $fields['{meeting_password}'] = $meeting_data->password;
        $fields['{join_url}'] = $meeting_data->join_url;
        return $fields;
    }
}
