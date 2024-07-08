<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * hr payroll controler
 */
class Appraisal extends AdminController {

    public function __construct() {
		parent::__construct();

		$this->load->model('common/Common_model');		//Added by DEEP BASAK on January 09, 2024
		$this->load->library('form_validation');        //Added by DEEP BASAK on May 21, 2024

        $this->load->model('staff_model');
		$this->load->model('departments_model');
        date_default_timezone_set('Asia/Kolkata');
	}


    /* -----------------------  Start KRA & KPI Managment ---------------- */
    /**
	 * KRA & KPI Managment
	 * Added by DEEP BASAK on June 26, 2024
	 */
	public function manage_krakpi(){
		$data['title'] = 'KRA & KPI Manage';
		$this->load->view('appraisal/krakpi/krakpi_manage', $data);
	}

    /**
	 * Open Appraisal Time Modal
	 * Added by DEEP BASAK on June 26, 2024
	 */
    public function open_appraisal_time_modal(){
        $data['details'] = $this->Common_model->getAllData('tbl_appraisal_time', '', 1, ['is_active' => 'Y']);
        $html = $this->load->view('appraisal/common/appraisal_time_modal_body', $data, true);

		# response
        $result = array('status'=> 'success', 'message'=>'', 'html' => $html);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
    }

    /**
	 * Appraisal Time Save
	 * Added by DEEP BASAK on June 26, 2024
	 */
    public function save_appraisal_time(){
        #validation
		$this->form_validation->set_rules('appraisal_time', 'Appraisal Time', 'trim|required');
		$this->form_validation->set_rules('appraisal_time_id', 'Appraisal Time Id', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $msg = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            if(post('appraisal_time_id') == 0){
                #add
                $data = array(
                    'appraisal_time'    => post('appraisal_time'),
                    'is_active'			=> 'Y',
					'created_at'		=> date('Y-m-d H:i:s'),
					'created_by'		=> get_staff_user_id()
                );
                $save = $this->Common_model->add('tbl_appraisal_time', $data);
            } else{
                #edit
                $data = array(
                    // 'appraisal_time'    => post('appraisal_time'),
                    'is_active'			=> 'N',
					'updated_at'		=> date('Y-m-d H:i:s'),
					'updated_by'		=> get_staff_user_id()
                );
                $save = $this->Common_model->UpdateDB('tbl_appraisal_time', ['id' => post('appraisal_time_id')], $data);

                //CR by DEEP BASAK on July 02, 2024 for history of appraisal time change
                $data = array(
                    'appraisal_time'    => post('appraisal_time'),
                    'is_active'			=> 'Y',
					'created_at'		=> date('Y-m-d H:i:s'),
					'created_by'		=> get_staff_user_id()
                );
                $save = $this->Common_model->add('tbl_appraisal_time', $data);
            }

            if ($save) {
                $array = array('status' => 'success', 'error' => '', 'message' => 'Appraisal Time Updated!');
            } else {
                $array = array('status' => 'fail', 'error' => 'error_message', 'message' => '');
            }
        }

        # Response
		$array = array_merge($array,update_csrf_session());
        echo json_encode($array);
    }

    /**
	 * KRA & KPI List
	 * Added by DEEP BASAK on June 26, 2024
	 */
    public function krakpi_list($date = ''){
        # customize filter
		$where = ' `is_active` = "Y" ';
        if(($date != '') && ($date != 'null')){
			$where .= ' AND `created_at` BETWEEN "'.$date.'-01" AND "'.$date.'-31" ';
		} else{
			$where .= ' AND `created_at` BETWEEN "'.date('Y-m').'-01" AND "'.date('Y-m').'-31" ';
		}
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
			$searchwhere .= $f.' tbl_staff_krakpi.rating LIKE "%'.$searchValue.'%"
				OR tblstaff.firstname LIKE "%'.$searchValue.'%"
				OR tblstaff.lastname LIKE "%'.$searchValue.'%"
				OR tbl_staff_krakpi.created_at LIKE "%'.$searchValue.'%" ';
		}

		//Paging Size (10, 20, 50,100)  
		$pageSize = $length != null ? intval($length) : 0;
		$skip = $start != null ? intval($start) : 0;

        #region order by column
		//Cr by DEEP BASAK on March 19, 2024
		if(!empty($_POST['order'][0])){
			$colArr = array(
				'', 
				'tblstaff.firstname', 
                'tbl_staff_krakpi.rating',
				'tbl_publish.firstname', 
				'tbl_staff_krakpi.created_at', 
				''
			);

			$columnIndex = $_POST['order'][0]['column'];
			$orderColName = $colArr[$columnIndex];
			$orderDir = $_POST['order'][0]['dir'];
			if($orderColName != ''){
				$orderQuery = ' ORDER BY '. $orderColName . ' ' . $orderDir . ' ';
			} else{
				$orderQuery = ' ORDER BY tbl_staff_krakpi.created_at DESC ';
			}
			
		} else{
			$orderQuery = ' ORDER BY tbl_staff_krakpi.created_at DESC ';
		}
		#endregion

        $select = ' tbl_staff_krakpi.*, 
					tblstaff.firstname, 
					tblstaff.lastname,
                    tbl_publish.firstname AS staff_firstname,
                    tbl_publish.lastname AS staff_lastname ';
        
        //Datatable view Query
		$query = 'SELECT
            '.$select.'
        FROM
            `tbl_staff_krakpi` 
        LEFT JOIN tblstaff AS tbl_publish ON tbl_staff_krakpi.created_by = tbl_publish.staffid 
        LEFT JOIN tblstaff ON tbl_staff_krakpi.staff_id = tblstaff.staffid ';
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
            `tbl_staff_krakpi` 
        LEFT JOIN tblstaff AS tbl_publish ON tbl_staff_krakpi.created_by = tbl_publish.staffid 
        LEFT JOIN tblstaff ON tbl_staff_krakpi.staff_id = tblstaff.staffid ';
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
            $action .= '&nbsp;<a href="javascript:void(0)" onclick="openRatingModal('.$fieldData['staff_id'].')"><i class="fa fa-star"></i></a>';
			
			$data[] = array(
				$key + $skip + 1,
				$fieldData['firstname'] . ' ' . $fieldData['lastname'],
				$fieldData['rating'],
				$fieldData['staff_firstname'] . ' ' . $fieldData['staff_lastname'],
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
	 * Open Modal
	 * Added by DEEP BASAK on June 26, 2024
	 */
    public function open_modal(){
        $data['staff_list'] = $this->Common_model->getAllData('tblstaff', '', '', []);
        if(post('id') != 0){
            $data['details'] = $this->Common_model->getAllData('tbl_staff_krakpi', '', 1, ['is_active' => 'Y']);
        }
        
        $html = $this->load->view('krakpi/components/modal_body', $data, true);

		# response
        $result = array('status'=> 'success', 'message'=>'', 'html' => $html);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
    }

    /**
	 * Save KRA & KPI
	 * Added by DEEP BASAK on June 26, 2024
	 */
    public function save_krakpi(){
        #validation
		$this->form_validation->set_rules('staff_id', 'Staff', 'trim|required');
		$this->form_validation->set_rules('rating', 'Rating', 'trim|required');
        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $msg = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            if(post('krakpi_id') == 0){
                #add
                $data = array(
                    'staff_id'          => post('staff_id'),
                    'rating'            => post('rating'),
                    'comments'          => post('comments'),
                    'is_active'			=> 'Y',
					'created_at'		=> date('Y-m-d H:i:s'),
					'created_by'		=> get_staff_user_id()
                );
                $save = $this->Common_model->add('tbl_staff_krakpi', $data);
            } else{
                #edit
                $data = array(
                    'staff_id'          => post('staff_id'),
                    'rating'            => post('rating'),
                    'comments'          => post('comments'),
                    'is_active'			=> 'Y',
					'updated_at'		=> date('Y-m-d H:i:s'),
					'updated_by'		=> get_staff_user_id()
                );
                $save = $this->Common_model->UpdateDB('tbl_staff_krakpi', ['id' => post('krakpi_id')], $data);
            }

            if ($save) {
                $array = array('status' => 'success', 'error' => '', 'message' => 'KRA & KPI Added!');
            } else {
                $array = array('status' => 'fail', 'error' => 'error_message', 'message' => '');
            }
        }

        # Response
		$array = array_merge($array,update_csrf_session());
        echo json_encode($array);
    }

    /**
	 * Rating Modal
	 * Added by DEEP BASAK on June 26, 2024
	 */
    public function open_rating_modal(){
        $select = 'AVG( rating ) AS average_rating,
                ( SELECT rating FROM `tbl_staff_krakpi` WHERE staff_id = '.post('staff_id').' ORDER BY created_at DESC LIMIT 1 ) AS last_rating,
                ( SELECT created_at FROM `tbl_staff_krakpi` WHERE staff_id = '.post('staff_id').' ORDER BY created_at DESC LIMIT 1 ) AS last_rating_at,
                ( SELECT SUM( rating ) FROM `tbl_staff_krakpi` WHERE staff_id = '.post('staff_id').' ) AS total_rating,
                ( SELECT COUNT( rating ) FROM `tbl_staff_krakpi` WHERE staff_id = '.post('staff_id').' ) AS total_rating_count,
                tblstaff.firstname,
                tblstaff.lastname ';
        $join = array(
            array(
                'table'     => 'tblstaff',
                'on'        => 'tbl_staff_krakpi.staff_id = tblstaff.staffid',
                'type'      => 'left'
            )
        );
        $data['details'] = $this->Common_model->getAllData('tbl_staff_krakpi', $select, 1, ['staff_id' => post('staff_id'), 'is_active' => 'Y'], '', '', '', '', [], $join);
        $html = $this->load->view('appraisal/common/krakpi_rating_modal_body', $data, true);

		# response
        $result = array('status'=> 'success', 'message'=>'', 'html' => $html);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
    }

    /* -----------------------  End KRA & KPI Managment ---------------- */

    /* -----------------------  Start Appraisal Managment ---------------- */

    /**
	 * Appraisal Managment
	 * Added by DEEP BASAK on June 27, 2024
	 */
	public function manage_appraisal(){
		$data['title'] = 'Appraisal Manage';
		$this->load->view('appraisal/appraisal/appraisal_manage', $data);
	}

    public function apprisal_list($year = ''){  
        # customize filter
		$where = ' is_active = "Y" ';
        
        if(($year != '') && ($year != 'null')){
			// $where .= ' AND `created_at` BETWEEN "'.$year.'-01-01" AND "'.$year.'-12-31" ';
            $where .= ' AND appraisal_year = "'.$year.'" ';
		} else{
			$where .= ' AND appraisal_year = "'.date('Y').'" ';
		}

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
			$searchwhere .= $f.' tbl_staff_krakpi.appraisal_year LIKE "%'.$searchValue.'%"
				OR tblstaff.firstname LIKE "%'.$searchValue.'%"
				OR tblstaff.lastname LIKE "%'.$searchValue.'%"
				OR tbl_staff_krakpi.created_at LIKE "%'.$searchValue.'%" ';
		}

		//Paging Size (10, 20, 50,100)  
		$pageSize = $length != null ? intval($length) : 0;
		$skip = $start != null ? intval($start) : 0;

        #region order by column
		//Cr by DEEP BASAK on March 19, 2024
		if(!empty($_POST['order'][0])){
			$colArr = array(
				'', 
				'tblstaff.firstname', 
                '',
				'history.appraisal_year', 
				'history.current_salary',
                'history.new_salary',
                'history.current_designation',
                'history.new_designation',
                'history.krakpi_avg_rating',
                'history.krakpi_last_rating',
                'history.is_active', 
				''
			);

			$columnIndex = $_POST['order'][0]['column'];
			$orderColName = $colArr[$columnIndex];
			$orderDir = $_POST['order'][0]['dir'];
			if($orderColName != ''){
				$orderQuery = ' ORDER BY '. $orderColName . ' ' . $orderDir . ' ';
			} else{
				$orderQuery = ' ORDER BY history.created_at DESC ';
			}
			
		} else{
			$orderQuery = ' ORDER BY history.created_at DESC ';
		}
		#endregion

        $select = ' history.*, 
					tblstaff.firstname, 
					tblstaff.lastname,
                    new_position.position_name AS new_dgt,
                    old_position.position_name AS current_dgt ';    //CR by DEEP BASAK on July 08, 2024
        
        //Datatable view Query
        //CR by DEEP BASAK on July 08, 2024
		$query = 'SELECT
            '.$select.'
        FROM
            tbl_staff_appraisal_history AS history
        LEFT JOIN tblstaff ON history.staff_id = tblstaff.staffid 
        LEFT JOIN tbljob_position new_position ON history.new_designation = new_position.position_id AND history.new_designation <> 0 
        LEFT JOIN tbljob_position old_position ON history.current_designation = old_position.position_id AND history.current_designation <> 0 ';
        if($where != ' '):
            $query .=' WHERE
                '.$where.' ';
        endif;
        $query .=' '.$searchwhere.' 
            '. $orderQuery . ' 
        LIMIT '.$pageSize.' OFFSET '.$skip.' ';

        // prx($query);

        //Total records query
        //CR by DEEP BASAK on July 08, 2024
        $query_total = 'SELECT
            '.$select.'
        FROM
            tbl_staff_appraisal_history AS history
        LEFT JOIN tblstaff ON history.staff_id = tblstaff.staffid 
        LEFT JOIN tbljob_position new_position ON history.new_designation = new_position.position_id AND history.new_designation <> 0 
        LEFT JOIN tbljob_position old_position ON history.current_designation = old_position.position_id AND history.current_designation <> 0 ';
        if($where != ' '):
            $query_total .=' WHERE
                    '.$where.' ';
        endif; 
        $query_total .=' '.$orderQuery.' ';


        $testdata = $this->Common_model->callSP($query);
        $testdata_total = $this->Common_model->callSP($query_total);
        $data = array();
        foreach ($testdata as $key => $fieldData){
			$action = '<a href="javascript:void(0)" onclick="openAppraisalModal('.$fieldData['id'].', 2)"><i class="fa fa-eye"></i></a>';
            
            if($fieldData['is_approved'] == 'P'){
                $action .= '&nbsp;<a href="javascript:void(0)" onclick="openAppraisalModal('.$fieldData['id'].', 1)"><i class="fa fa-edit"></i></a>';  //CR by DEEP BASAK on July 08, 2024
                $action .= '&nbsp;<a href="javascript:void(0)" onclick="approveOrRejectAppraisal('.$fieldData['id'].')"><i class="fa fa-pause"></i></a>';
            }

            $appraisalType = json_decode($fieldData['appraisal_type']);
            $app_type = '';
            foreach($appraisalType as $k){
                if($k == 'S'){
                    $app_type .= '<span class="badge bg-success">Salary Hike</span>';
                } else if($k == 'D'){
                    $app_type .= '<span class="badge bg-primary">Designation Hike</span>';
                }
            }

            //CR by DEEP BASAK on July 08, 2024
            if(!empty($fieldData['appraisal_document'])){
                $download = '<a href="'.base_url($fieldData['appraisal_document']).'" target="_blank"><i class="fa fa-download"></i></a>';
            } else{
                $download = '';
            }
            

            if($fieldData['is_approved'] == 'P'){
                $status = '<span class="badge bg-secondary">Pending</span>';
            } else if($fieldData['is_approved'] == 'A'){
                $status = '<span class="badge bg-success">Approved</span>';
            } else if($fieldData['is_approved'] == 'R'){
                $status = '<span class="badge bg-danger">Rejected</span>';
            }
			
			$data[] = array(
				$key + $skip + 1,
				$fieldData['firstname'] . ' ' . $fieldData['lastname'],
                $app_type,
				$fieldData['appraisal_year'],
				$fieldData['current_salary'],
                $fieldData['new_salary'],
                $fieldData['current_dgt'],      //CR by DEEP BASAK on July 08, 2024
                $fieldData['new_dgt'],          //CR by DEEP BASAK on July 08, 2024
                $fieldData['krakpi_avg_rating'],
                $fieldData['krakpi_last_rating'],
                $download,
                $status,
				!empty($fieldData['approved_at'])?date('F d, Y', strtotime($fieldData['approved_at'])):'',
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
	 * Open Appraisal Managment Modal
	 * Added by DEEP BASAK on June 27, 2024
	 */
    public function open_appraisal_modal(){
        $sql = 'SELECT
                s.staffid,
                s.firstname,
                s.lastname,
                s.staff_identifi,
                s.datecreated,
                sah.created_at,
                TIMESTAMPDIFF(MONTH, s.datecreated, NOW()),
                TIMESTAMPDIFF(MONTH, sah.created_at, NOW()) as appraisal
            FROM
                tblstaff s
            LEFT JOIN
                tbl_staff_appraisal_history sah ON s.staffid = sah.staff_id
            WHERE
                    sah.is_approved = \'A\'
                    AND sah.is_active = \'Y\'
                    AND
                (
                    sah.created_at IS NOT NULL AND
                    TIMESTAMPDIFF(MONTH, sah.created_at, NOW()) >= (SELECT appraisal_time FROM tbl_appraisal_time WHERE is_active = \'Y\') - 1
                ) OR
                (
                    sah.created_at IS NULL AND
                    TIMESTAMPDIFF(MONTH, s.datecreated, NOW()) >= (SELECT appraisal_time FROM tbl_appraisal_time WHERE is_active = \'Y\') - 1
                )';
        $data['staff_list'] = $this->Common_model->callSP($sql);
        $data['appraisal_type'] = $this->Common_model->getAllData('tbl_appraisal_type', '', '', ['is_active'=> 'Y']);
        $html = $this->load->view('appraisal/appraisal/components/appraisal_manage_modal_body', $data, true);

		# response
        $result = array('status'=> 'success', 'message'=>'', 'html' => $html);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
    }

    /**
	 * Get Appraisal data
	 * Added by DEEP BASAK on June 27, 2024
	 */
    public function get_appraisal_details(){
        $data['salary'] = post('salary');
        $data['designation'] = post('designation');
        $data['staff_id'] = $staff_id = post('staff_id');
        $join = array(
            array(
                'table'     => 'tbljob_position',
                'on'        => 'tblstaff.job_position = tbljob_position.position_id',
                'type'      => 'left'
            )
        );
        $select = 'tblstaff.*, tbljob_position.position_name';
        $data['staff_details'] = $this->Common_model->getAllData('tblstaff', $select, 1, ['staffid' => $staff_id], '', '', '', '', [], $join);
        $data['job_position'] = $this->Common_model->getAllData('tbljob_position', '', '', [], 'position_name ASC');
        $html = $this->load->view('appraisal/appraisal/components/get_appraisal_details_body', $data, true);
        $type = true;
        if(empty($html)){
            $type = false;
        }

		# response
        $result = array('status'=> 'success', 'message'=>'', 'html' => $html, 'type' => $type);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
    }

    /**
	 * Save Appraisal
	 * Added by DEEP BASAK on July 03, 2024
	 */
    public function save_appraisal(){
        #validation
		$this->form_validation->set_rules('hdn_staff_id', 'Staff', 'trim|required');
        if(!empty(post('appraisal_type_salary'))){
            $this->form_validation->set_rules('current_salary', 'Current Salary', 'trim|required');
            $this->form_validation->set_rules('new_salary', 'New Salary', 'trim|required');
        } else if(!empty(post('appraisal_type_designation'))){
            $this->form_validation->set_rules('hdn_current_designation', 'Current Designation', 'trim|required');
            $this->form_validation->set_rules('new_designation', 'New Designation', 'trim|required');
        }
        
        if ($this->form_validation->run() == FALSE) {
            $msg = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            if(empty(post('appraisal_type_designation')) && empty(post('appraisal_type_salary'))){
                $msg = 'Please select any Appraisal Type';
                $status = 'fail';
            } else if(empty($_FILES['appraisal_docs']['name'])){
                $msg = 'Please select Appraisal Document!';
                $status = 'fail';
            } else{
                $select = 'AVG( rating ) AS average_rating,
                        ( SELECT rating FROM `tbl_staff_krakpi` WHERE staff_id = '.post('hdn_staff_id').' ORDER BY created_at DESC LIMIT 1 ) AS last_rating,
                        ( SELECT created_at FROM `tbl_staff_krakpi` WHERE staff_id = '.post('hdn_staff_id').' ORDER BY created_at DESC LIMIT 1 ) AS last_rating_at,
                        ( SELECT SUM( rating ) FROM `tbl_staff_krakpi` WHERE staff_id = '.post('hdn_staff_id').' ) AS total_rating,
                        ( SELECT COUNT( rating ) FROM `tbl_staff_krakpi` WHERE staff_id = '.post('hdn_staff_id').' ) AS total_rating_count,
                        tblstaff.firstname,
                        tblstaff.lastname ';
                $join = array(
                    array(
                        'table'     => 'tblstaff',
                        'on'        => 'tbl_staff_krakpi.staff_id = tblstaff.staffid',
                        'type'      => 'left'
                    )
                );
                $krakpi_details = $this->Common_model->getAllData('tbl_staff_krakpi', $select, 1, ['staff_id' => post('hdn_staff_id'), 'is_active' => 'Y'], '', '', '', '', [], $join);
                if(post('appraisal_id') == 0){
                    #Add
                    $appraisal_type = json_encode(
                        array(
                            !empty(post('appraisal_type_salary'))?'S':'N', 
                            !empty(post('appraisal_type_designation'))?'D':'N'
                        )
                    );

                    $filetype = array('jpeg','jpg','png','PNG','JPEG','JPG', 'pdf');
                    $document_url = multiUpload('appraisal_docs', 'uploads/appraisal', $filetype, 'single', '');
                    $document_url = 'uploads/appraisal/' . $document_url;

                    $data = array(
                        'staff_id'              => post('hdn_staff_id'),
                        'appraisal_type'        => $appraisal_type,
                        'current_salary'        => !empty(post('current_salary'))?post('current_salary'):0.00,
                        'new_salary'            => !empty(post('new_salary'))?post('new_salary'):0.00,
                        'current_designation'   => !empty(post('hdn_current_designation'))?post('hdn_current_designation'):0,
                        'new_designation'       => !empty(post('new_designation'))?post('new_designation'):0,
                        'krakpi_avg_rating'     => $krakpi_details->average_rating,
                        'krakpi_last_rating'    => $krakpi_details->last_rating,
                        'appraisal_document'    => $document_url,
                        'is_approved'           => 'P',
                        'appraisal_year'        => date('Y'),
                        'is_active'             => 'Y',
                        'created_at'		    => date('Y-m-d H:i:s'),
					    'created_by'		    => get_staff_user_id()
                    );
                    $save = $this->Common_model->add('tbl_staff_appraisal_history', $data);
                    
                    if($save){
                        $status = 'success';
                        $msg = 'Appraisal Added!';
                    } else{
                        $status = 'fail';
                        $msg = 'Appraisal Not Added!';
                    }
                    
                } else{
                    #Edit
                    $msg = 'Appraisal Updated!';
                    $status = 'success';
                }

                
            }
            $array = array('status' => $status, 'error' => $msg, 'message' => $msg);
            
        }

        # Response
		$array = array_merge($array,update_csrf_session());
        echo json_encode($array);
    }

    /**
	 * Appraisal Approval Modal
	 * Added by DEEP BASAK on July 05, 2024
	 */
    public function appraisal_approve_reject_open_modal(){
        $data['appraisal_id'] = post('appraisal_id');
        $html = $this->load->view('appraisal/appraisal/components/appraisal_approve_modal_body', $data, true);

        # response
        $result = array('status'=> 'success', 'message'=>'', 'html' => $html);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
    }

    /**
	 * Approve/Reject Appraisal
	 * Added by DEEP BASAK on July 05, 2024
	 */
    public function appraisal_approve_reject(){
        
        if(post('type') == 'A'){
            $status = 'success';
            $message = 'Appraisal Approved!';

            $data = array(
                'is_approved'   => post('type'),
                'approved_at'   => date('Y-m-d H:i:s'),
                'approved_by'   => get_staff_user_id(),
                'appraisal_time'=> post('appraisal_start_date'),
                'updated_at'    => date('Y-m-d H:i:s'),
                'updated_by'    => get_staff_user_id()
            );
        } else if(post('type') == 'R'){
            $status = 'warning';
            $message = 'Appraisal Rejected!';

            $data = array(
                'is_approved'   => post('type'),
                'approved_at'   => date('Y-m-d H:i:s'),
                'approved_by'   => get_staff_user_id(),
                'updated_at'    => date('Y-m-d H:i:s'),
                'updated_by'    => get_staff_user_id()
            );
        }

        $this->Common_model->UpdateDB('tbl_staff_appraisal_history', ['id' => post('appraisal_id')], $data);

        # response
        $result = array('status'=> $status, 'message'=>$message);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
    }

    /* -----------------------  End Appraisal Managment ---------------- */
}