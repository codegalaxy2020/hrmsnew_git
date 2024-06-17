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
        // $data['']
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
			
			$data[] = array(
				$key + $skip + 1,
				$fieldData['form_id'],
				$fieldData['job_title'],
				$link,
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

}

?>