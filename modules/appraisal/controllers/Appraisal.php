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
                    'appraisal_time'    => post('appraisal_time'),
                    'is_active'			=> 'Y',
					'updated_at'		=> date('Y-m-d H:i:s'),
					'updated_by'		=> get_staff_user_id()
                );
                $save = $this->Common_model->UpdateDB('tbl_appraisal_time', ['id' => post('appraisal_time_id')], $data);
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

    public function open_rating_modal(){
        $select = 'AVG( rating ) AS average_rating,
                ( SELECT rating FROM `tbl_staff_krakpi` ORDER BY created_at DESC LIMIT 1 ) AS last_rating,
                ( SELECT created_at FROM `tbl_staff_krakpi` ORDER BY created_at DESC LIMIT 1 ) AS last_rating_at,
                ( SELECT SUM( rating ) FROM `tbl_staff_krakpi` ) AS total_rating,
                ( SELECT COUNT( rating ) FROM `tbl_staff_krakpi` ) AS total_rating_count,
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
}