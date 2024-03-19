<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Class Hr profile
 */
class Hr_faculty extends AdminController {
	/**
	 * __construct
	 */
	public function __construct() {
		parent::__construct();
		$this->load->model('hr_profile_model');
		$this->load->model('departments_model');
		$this->load->model('staff_model');

		$this->load->model('common/Common_model');

		$this->load->dbforge();
		header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
	}

	public function manage_faculty($id = '') {

		// 		$data['title'] = $title;
				$data['faculty_data'] = $this->hr_profile_model->faculty_data();
		// 		print_r($data['faculty_data']);die();
				$this->app_scripts->add('surveys-js', module_dir_url('surveys', 'assets/js/surveys.js'), 'admin', ['app-js']);
				$this->app_css->add('surveys-css', module_dir_url('hr_profile', 'assets/css/training/training_post.css'), 'admin', ['app-css']);
		
				$this->load->view('hr_profile/training/manage_faculty', $data);
			}

//end file
}
