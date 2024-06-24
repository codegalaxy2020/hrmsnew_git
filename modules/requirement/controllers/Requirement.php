<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * hr payroll controler
 */
class Requirement extends AdminController {

	public function __construct() {
		parent::__construct();

		$this->load->model('common/Common_model');		//Added by DEEP BASAK on January 09, 2024
		$this->load->library('form_validation');        //Added by DEEP BASAK on May 21, 2024

        $this->load->model('staff_model');
		$this->load->model('departments_model');
	}


    /**
	 * Requirement Managment
	 * Added by DEEP BASAK on June 13, 2024
	 */
	public function manage_requirement(){
		
		$data['title'] = 'Requirement Manage';
		
		$this->load->view('requirement/requirement_manage', $data);
	}

    /**
	 * Requirement Modal
	 * Added by DEEP BASAK on June 13, 2024
	 */
	public function open_requirement_modal(){
		$data['form_id'] = getCaseId('form');
        if(post('id') != 0){
            $data['details'] = $this->Common_model->getAllData('tbl_form_link', '', 1, ['id' => post('id')]);
            $data['field_type'] = $this->Common_model->getAllData('tbl_field_type_master', '', '', ['is_active' => 'Y']);
        }
		$html = $this->load->view('requirement/components/requirement_manage_modal_body', $data, true);

		# response
        $result = array('status'=> 'success', 'message'=>'', 'html' => $html);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}

    /**
	 * Requirement Modal Table body
	 * Added by DEEP BASAK on June 13, 2024
	 */
    public function add_fields(){
        $data['count'] = post('count');
        $data['field_type'] = $this->Common_model->getAllData('tbl_field_type_master', '', '', ['is_active' => 'Y']);
        $html = $this->load->view('requirement/components/requirement_manage_modal_tbody', $data, true);

		# response
        $result = array('status'=> 'success', 'message'=>'', 'html' => $html);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
    }

    /**
	 * Disciplinary Managment List
	 * Added by DEEP BASAK on May 21, 2024
	 */
	public function requirement_list(){
		# customize filter
        $where = " is_active='Y' ";

		// Skip number of Rows count  
		$start = $_POST["start"];

		// Paging Length 10,20  
		$length = $_POST["length"];

		// Search Value from (Search box)  
		$searchValue = trim($_POST["search"]["value"]);
		$searchwhere = '';
		if(!empty($searchValue)){
			if($where != ' '){
				$f = ' AND ';
			}else{
				$f = ' WHERE ';
			}
			$searchwhere .= $f.' tbl_form_link.form_id LIKE "%'.$searchValue.'%"
				OR tbl_form_link.job_title LIKE "%'.$searchValue.'%"
				OR tblstaff.firstname LIKE "%'.$searchValue.'%"
				OR tblstaff.lastname LIKE "%'.$searchValue.'%"
				OR tbl_form_link.created_at LIKE "%'.$searchValue.'%" ';
		}

		//Paging Size (10, 20, 50,100)  
		$pageSize = $length != null ? intval($length) : 0;
		$skip = $start != null ? intval($start) : 0;

		#region order by column
		//Cr by DEEP BASAK on March 19, 2024
		if(!empty($_POST['order'][0])){
			$colArr = array(
				'', 
				'tbl_form_link.form_id', 
                'tbl_form_link.job_title',
                '',
				'tblstaff.firstname', 
				'tbl_form_link.created_at', 
				''
			);

			$columnIndex = $_POST['order'][0]['column'];
			$orderColName = $colArr[$columnIndex];
			$orderDir = $_POST['order'][0]['dir'];
			if($orderColName != ''){
				$orderQuery = ' ORDER BY '. $orderColName . ' ' . $orderDir . ' ';
			} else{
				$orderQuery = ' ORDER BY tbl_form_link.created_at DESC ';
			}
			
		} else{
			$orderQuery = ' ORDER BY tbl_form_link.created_at DESC ';
		}
		#endregion
		
		$select = ' tbl_form_link.*, 
					tblstaff.firstname, 
					tblstaff.lastname ';

		//Datatable view Query
		$query = 'SELECT
			'.$select.'
		FROM
			`tbl_form_link` 
		LEFT JOIN tblstaff ON tbl_form_link.created_by = tblstaff.staffid ';
		if($where != ' '):
			$query .=' WHERE
				'.$where.' ';
		endif;
		$query .=' '.$searchwhere.' 
			'. $orderQuery . ' 
		LIMIT '.$pageSize.' OFFSET '.$skip.' ';

		// prx($query);

		//Total records query
		$query_total = 'SELECT
			'.$select.'
		FROM
			`tbl_form_link` 
		LEFT JOIN tblstaff ON tbl_form_link.created_by = tblstaff.staffid ';
		if($where != ' '):
			$query_total .=' WHERE
					'.$where.' ';
		endif; 
		$query_total .=' '.$orderQuery.' ';


		$testdata = $this->Common_model->callSP($query);
		$testdata_total = $this->Common_model->callSP($query_total);
		$data = array();

		foreach ($testdata as $key => $fieldData){
			$action = '<a href="javascript:void(0)" onclick="openModal('.$fieldData['id'].', 2)"><i class="fa fa-eye"></i></a>';
			// if(($fieldData['is_approved'] == 'P') && ($fieldData['created_by'] == get_staff_user_id())){
				$action .= '<a href="javascript:void(0)" onclick="openModal('.$fieldData['id'].', 1)"><i class="fa fa-pencil"></i></a>';
			// }
			
			$link = '<a href="'.$fieldData['form_link'].'" class="btn btn-primary" target="_blank">Apply Link</a>';
			$link2 = '<a href="'.base_url('requirement/manage_requirement_data/').base64_encode($fieldData['form_id']).'" class="btn btn-success">View Application</a>';
			
			$data[] = array(
				$key + $skip + 1,
				$fieldData['form_id'],
				$fieldData['job_title'],
				$link,
				$link2,
				$fieldData['firstname'] . ' ' . $fieldData['lastname'],
				date('F d, Y', strtotime($fieldData['created_at'])),
				$action
			);

		}


		if (isset($_POST['draw']) && $_POST['draw']) {
            $draw = $_POST['draw'];
        } else {
            $draw = '';
        }

        $output = array(
            "draw" => $draw,
			"recordsTotal" => count($testdata_total),
            "recordsFiltered" => count($testdata_total),
            "data" => $data,
            "status" => 'success',
			"csrf" => update_csrf_session()
        );

        # response
        echo json_encode($output);
        unset($dttbl_model);
	}

    /**
	 * Disciplinary Managment Save
	 * Added by DEEP BASAK on May 21, 2024
	 */
	public function save_requirement(){

		#validation
		$this->form_validation->set_rules('form_id', 'Form ID', 'trim|required');
		$this->form_validation->set_rules('job_title', 'Job Title', 'trim|required');
        if(isset($_POST['field_name'])){
            foreach(post('field_name') as $key => $val){
                $this->form_validation->set_rules('field_name['.$key.']', 'Field Name of '.$key, 'trim|required');
                $this->form_validation->set_rules('field_name_slug['.$key.']', 'Field Name Slug of '.$key, 'trim|required');
                $this->form_validation->set_rules('field_type['.$key.']', 'Field Type of '.$key, 'trim|required');
            }
        }
		if ($this->form_validation->run() == FALSE) {
            $msg = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
			
			if(post('job_id') == 0){
				
				//Add
                if(!empty(post('field_name'))){
                    foreach(post('field_name') as $key => $val){
                        $form_fields[] = array(
                            'field_name'        => post('field_name')[$key],
                            'field_name_slug'   => post('field_name_slug')[$key],
                            'field_type'        => post('field_type')[$key]
                        );
                    }
                } else{
                    $form_fields = array();
                }
                $form_id = getCaseId('form');
				$data = array(
					'form_id'			=> $form_id,
					'job_title'			=> post('job_title'),
					'form_link'			=> base_url('forms/jobs/'.base64_encode($form_id)),
					'form_fields'		=> json_encode($form_fields),
					'is_active'			=> 'Y',
					'created_at'		=> date('Y-m-d H:i:s'),
					'created_by'		=> get_staff_user_id()
				);
				
				$save = $this->Common_model->add('tbl_form_link', $data);
				// prx($save);
			} else{
				//Edit
				if(!empty(post('field_name'))){
                    foreach(post('field_name') as $key => $val){
                        $form_fields[] = array(
                            'field_name'        => post('field_name')[$key],
                            'field_name_slug'   => post('field_name_slug')[$key],
                            'field_type'        => post('field_type')[$key]
                        );
                    }
                } else{
                    $form_fields = array();
                }
				$data = array(
					'form_id'			=> post('form_id'),
					'job_title'			=> post('job_title'),
					'form_link'			=> base_url('forms/jobs/'.base64_encode(post('form_id'))),
					'form_fields'		=> json_encode($form_fields),
					'is_active'			=> 'Y',
					'updated_at'		=> date('Y-m-d H:i:s'),
					'updated_by'		=> get_staff_user_id()
				);
				$save = $this->Common_model->UpdateDB('tbl_form_link', ['id' => post('job_id')], $data);
			}
			

			if ($save) {
                $array = array('status' => 'success', 'error' => '', 'message' => 'Staff Complain Added');
            } else {
                $array = array('status' => 'fail', 'error' => 'error_message', 'message' => '');
            }
		}

		# Response
		$array = array_merge($array,update_csrf_session());
        echo json_encode($array);
	}

    public function save_requirement_data(){
        $form_link_details = $this->Common_model->getAllData('tbl_form_link', '', 1, ['form_id' => base64_decode(post('form_id'))]);
		if(!empty($form_link_details)){
			$data = array();
			$form_fields = json_decode($form_link_details->form_fields);
			$details = $this->Common_model->getAllData('tbl_form_link_data', '', 1, ['form_id' => base64_decode( post('form_id'))]);

			foreach($form_fields as $key => $val){
				//Code for already applied with same email id or phone number
				$isAdd = true;
				if($val->field_type == 'email'){
					if(!empty($details)){
						$detailsEmail = json_decode($details->fields_data);
						$nameSlug = $val->field_name_slug;
						if(post($val->field_name_slug) == $detailsEmail->$nameSlug){
							$isAdd = false;
							break;
						}
					}
					
				}
			}

			if($isAdd == true){
				foreach($form_fields as $key => $val){
					if($val->field_type != 'file'){
						#validation
						$this->form_validation->set_rules($val->field_name_slug, $val->field_name, 'trim|required');
						$data[$val->field_name_slug] = post($val->field_name_slug);
					} else{
						
						$filetype = array('jpeg','jpg','png','PNG','JPEG','JPG', 'pdf');
						$document_url = multiUpload($val->field_name_slug, 'uploads/requirement', $filetype, 'single', '');
						$data[$val->field_name_slug] = "uploads\\requirement\\" . $document_url;
					}
					
				}
			}
			
			
			if($isAdd == true){
				if ($this->form_validation->run() == FALSE) {
					$msg = $this->form_validation->error_array();
					$array = array('status' => 'fail', 'error' => $msg, 'message' => '');
				} else {
					
					//Submit Form
					$tbl_data = array(
						'form_link_id'		=> $form_link_details->id,
						'form_id'			=> base64_decode(post('form_id')),
						'form_fields'		=> $form_link_details->form_fields,
						'fields_data'		=> json_encode($data),
						'is_active'			=> 'Y',
						'created_at'		=> date('Y-m-d H:i:s'),
						'created_by'		=> 1
					);
					$save = $this->Common_model->add('tbl_form_link_data', $tbl_data);
					if ($save) {
						$array = array('status' => 'success', 'error' => '', 'message' => 'Applied!');
					} else {
						$array = array('status' => 'fail', 'error' => 'error_message', 'message' => '');
					}
					
					
				}
			} else{
				$array = array('status' => 'fail', 'error' => 'Already Applied!', 'message' => '');
			}

		} else{
			$array = array('status' => 'fail', 'error' => 'Somthing wrong!', 'message' => '');
		}

		# Response
		$array = array_merge($array,update_csrf_session());
        echo json_encode($array);
    }



	/**
	 * Requirement data MANAGE
	 * Added by DEEP BASAK on June 17, 2024
	 */
	public function manage_requirement_data($form_id = ''){
		if(!empty($form_id)){
			$data['title'] = 'Application of '. base64_decode($form_id);
			$data['form_id'] = $form_id;
			$data['form_details'] = $this->Common_model->getAllData('tbl_form_link', '', 1, ['is_active'=> 'Y', 'form_id' => base64_decode($form_id)]);
			if(!empty($data['form_details'])){
				$this->load->view('requirement/requirement_manage_data', $data);
			} else{
				show_404();
			}
			
		} else{
			show_404();
		}
	}

	/**
	 * Requirement data List
	 * Added by DEEP BASAK on June 18, 2024
	 */
	public function requirement_data_list(){
		$data =array();
		$fields_name = array();
		$testdata = $this->Common_model->getAllData('tbl_form_link_data', '', '', ['is_active' => 'Y', 'form_id' => base64_decode(post('form_id'))], 'created_at DESC');
		if(!empty($testdata)){
			foreach($testdata as $key => $fieldData){
				$fields_data = json_decode($fieldData->fields_data);
				$fields = json_decode($fieldData->form_fields, true);
				$data_u = array();

				foreach($fields_data as $k => $v){
					$fieldType = $this->getFieldTypeBySlug($k, $fields);
					// pr($fieldType);
					$fields_name[] = $k;
					if($fieldType != 'file'){
						$data_u[$k] = $v;
					} else if(trim($fieldType) == 'email'){
						$email = $v;
					} else if(trim($fieldType) == 'phone'){
						$mobile = $v;
					} else {
						$convertedString = str_replace('\\', '/', $v);
						$data_u[$k] = '<a href="'.base_url().$convertedString.'" class="btn btn-primary btn-sm" target="_blank">View Resume</a>';
					}
					
				}
				// pr($fieldData);
				if(!empty($fieldData->interview_time)){
					$data_u['interview_time'] = date('F d, Y H:iA', strtotime($fieldData->interview_time));
				} else{
					$data_u['interview_time'] = '';
				}
				
				$data_u['action'] = '';
				if($fieldData->is_shortlisted == 'P'){
					$data_u['action'] .= '<a href="javascript:void(0)" onclick="shortlisted('.$fieldData->id.')"><i class="fa fa-list"></i></a>';
				} else if(($fieldData->is_shortlisted == 'Y') && empty($fieldData->interview_time)){
					$data_u['action'] .= '<a href="javascript:void(0)" onclick="scheduleInterview('.$fieldData->id.')"><i class="fa fa-calendar"></i></a>';
				} else if($fieldData->is_shortlisted == 'C'){
					$data_u['action'] .= '<span class="badge bg-success">Selected</span>';
				} else if($fieldData->is_shortlisted == 'N'){
					$data_u['action'] .= '<span class="badge bg-danger">Rejected</span>';
				}
				
				if(
					(!empty($fieldData->interview_time) && ($fieldData->is_shortlisted == 'Y')) 
					|| (!empty($fieldData->interview_time) && ($fieldData->is_shortlisted == 'N'))
					|| (!empty($fieldData->interview_time) && ($fieldData->is_shortlisted == 'C'))
				){
					$data_u['action'] .= '&nbsp;<a href="javascript:void(0)" onclick="interviewDetails('.$fieldData->id.')"><i class="fa fa-folder-open"></i></a>';
				}
				
				$data[] = $data_u;
				// exit;
			}
			// exit;
			$field_data_arr['data'] = $data;
			$field_data_arr['fields'] = $fields;
			$html = $this->load->view('requirement/components/requirement_manage_data_tbody', $field_data_arr, true);
			$status = 'success';
			$message = 'Data found!';
		} else{
			$status = 'fail';
			$message = 'No data found!';
			$html = '';
		}

		# response
        $result = array('status'=> $status, 'error' => $message, 'message'=> $message, 'data' => $data, 'fields' => $fields_name, 'html' => $html);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}

	public function getFieldTypeBySlug($slug, $fieldDefinitions) {
		foreach ($fieldDefinitions as $field) {
			if ($field['field_name_slug'] === $slug) {
				return $field['field_type'];
			}
		}
		return null; // If no matching slug is found
	}

	/**
	 * Shortlisted
	 * Added by DEEP BASAK on June 19, 2024
	 */
	public function shortlisted(){
		$this->Common_model->UpdateDB('tbl_form_link_data', ['id' => post('can_id')], ['is_shortlisted' => post('type'), 'shortlisted_at' => date('Y-m-d H:i:s'), 'shortlisted_by' => get_staff_user_id()]);
		if(post('type') == 'Y'){
			$message = 'Candidate Shortlisted!';
		} else{
			$message = 'Candidate Rejected!';
		}

		# response
        $result = array('status'=> 'success', 'message'=>$message);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}

	/**
	 * Schedule Interview Modal
	 * Added by DEEP BASAK on June 19, 2024
	 */
	public function schedule_interview(){
		$data['can_id'] = post('can_id');
		$html = $this->load->view('requirement/components/schedule_interview_modal_body', $data, true);

		# response
        $result = array('status'=> 'success', 'message'=>'', 'html' => $html);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}

	/**
	 * Schedule Interview Save
	 * Added by DEEP BASAK on June 19, 2024
	 */
	public function save_interview_schedule(){
		$this->Common_model->UpdateDB(
			'tbl_form_link_data', 
			['id' => post('can_id')], 
			[
				'interview_time'=> post('interview_datetime'), 
				'updated_at'=>date('Y-m-d H:i:s'), 
				'updated_by' => get_staff_user_id()
			]
		);
		$data = array(
			'form_link_data_id'		=> post('can_id'),
			'interview_datetime'	=> post('interview_datetime'),
			'interview_by'			=> get_staff_user_id(),
			'comments'				=> 'Schedule the interview',
			'is_active'				=> 'Y',
			'created_at'			=> date('Y-m-d H:i:s'),
			'created_by'			=> get_staff_user_id()
		);
		$this->Common_model->add('tbl_interview_history', $data);

		# response
        $result = array('status'=> 'success', 'message'=>'Interview Schedule completed!');
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}

	/**
	 * Interview Details
	 * Added by DEEP BASAK on June 21, 2024
	 */
	public function interview_details(){
		$data['can_id'] = post('can_id');
		$data['details'] = $details = $this->Common_model->getAllData('tbl_form_link_data', '', 1, ['id' => post('can_id')]);
		$html = $this->load->view('requirement/components/interview_details_modal_body', $data, true);

		# response
        $result = array('status'=> 'success', 'message'=>'', 'html' => $html, 'title' => $details->form_id);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}

	/**
	 * Get Interview Details LIST
	 * Added by DEEP BASAK on June 21, 2024
	 */
	public function get_interview_details_list(){
		$join = array(
			array(
				'table'		=> 'tblstaff',
				'on'		=> 'tblstaff.staffid = tbl_interview_history.interview_by',
				'type'		=> 'left'
			)
		);
		$select = 'tbl_interview_history.*, tblstaff.firstname, tblstaff.lastname';
		$data['details'] = $this->Common_model->getAllData('tbl_interview_history', $select, '', ['form_link_data_id' => post('can_id')], '', '', '', '', [], $join);
		$html = $this->load->view('requirement/components/interview_details_table_tbody', $data, true);

		# response
        $result = array('status'=> 'success', 'message'=>'', 'html' => $html);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}

	/**
	 * Submit Interview comments
	 * Added by DEEP BASAK on June 21, 2024
	 */
	public function submit_interview_comments(){
		#validation
		$this->form_validation->set_rules('can_id', 'Candidate ID', 'trim|required');
		$this->form_validation->set_rules('interview_datetime', 'Interview Datetime', 'trim|required');
		$this->form_validation->set_rules('comments', 'Comments', 'trim|required');
		if ($this->form_validation->run() == FALSE) {
            $msg = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
			$data = array(
				'form_link_data_id'		=> post('can_id'),
				'interview_datetime'	=> date('Y-m-d H:i:s', strtotime(post('interview_datetime'))),
				'interview_by'			=> get_staff_user_id(),
				'comments'				=> post('comments'),
				'is_active'				=> 'Y',
				'created_at'			=> date('Y-m-d H:i:s'),
				'created_by'			=> get_staff_user_id()
			);
			$save = $this->Common_model->add('tbl_interview_history', $data);

			if ($save) {
                $array = array('status' => 'success', 'error' => '', 'message' => 'Interview Comment added!');
            } else {
                $array = array('status' => 'fail', 'error' => 'error_message', 'message' => '');
            }
		}

		# Response
		$array = array_merge($array,update_csrf_session());
        echo json_encode($array);
		
	}

	/**
	 * Select the Employee
	 * Added by DEEP BASAK on June 24, 2024
	 */
	public function select_as_employee(){
		$this->Common_model->UpdateDB(
			'tbl_form_link_data', 
			['id' => post('can_id')], 
			[
				'is_shortlisted' 	=> post('type'),
				'updated_at'		=> date('Y-m-d H:i:s'),
				'updated_by'		=> get_staff_user_id()
			]
		);
		if(post('type') == 'C'){
			$message = 'Candidate Selected!';
		} else{
			$message = 'Candidate Rejected!';
		}

		# response
        $result = array('status'=> 'success', 'message'=>$message);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}

}

?>