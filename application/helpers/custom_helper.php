<?php

/*

	Custome helper
	Author: Deep Basak
	On: Febuary 14, 2023

    Employee Name     |   Version     |   Date Range      |   CR ID       |   Propose
	Deep Basak				1.0			  Febuary 15, 2023	    1			Add New Method to get the user role
	Deep Basak				1.0			  Febuary 20, 2023	    1			Add New Method to update the CSRF Token
    
*/

defined('BASEPATH') or exit('No direct script access allowed');

/* 
	* Single and MULTIPLE FILE upload by Deep
*/
if (!function_exists('multiUpload')) {
	function multiUpload($file_name = "", $uploadPath = "", $alow_types = "", $flag = "multi", $upload_name = '')
	{
		$CI = &get_instance();

		$files = [];
		$output = '';
		$config["upload_path"] = './' . $uploadPath;
		$config["allowed_types"] = $alow_types;
		$CI->load->library('upload', $config);
		$CI->upload->initialize($config);

		if ($flag == "multi") {

			for ($count = 0; $count < count($_FILES[$file_name]["name"]); $count++) {
				$_FILES["file"]["name"] = $upload_name == "" ? $_FILES[$file_name]["name"][$count] : $upload_name . "_" . $count . "." . pathinfo($_FILES[$file_name]["name"][$count], PATHINFO_EXTENSION);
				//$_FILES["file"]["name"] = $_FILES[$file_name]["name"][$count];
				$_FILES["file"]["type"] = $_FILES[$file_name]["type"][$count];
				$_FILES["file"]["tmp_name"] = $_FILES[$file_name]["tmp_name"][$count];
				$_FILES["file"]["error"] = $_FILES[$file_name]["error"][$count];
				$_FILES["file"]["size"] = $_FILES[$file_name]["size"][$count];

				if ($CI->upload->do_upload('file')) {
					$data = $CI->upload->data();
					array_push($files, $data["file_name"]);
				}
			}
		} else {

			$_FILES["file"]["name"] = $upload_name == "" ? $_FILES[$file_name]["name"] : $upload_name . "." . pathinfo($_FILES[$file_name]["name"], PATHINFO_EXTENSION);
			$_FILES["file"]["type"] = $_FILES[$file_name]["type"];
			$_FILES["file"]["tmp_name"] = $_FILES[$file_name]["tmp_name"];
			$_FILES["file"]["error"] = $_FILES[$file_name]["error"];
			$_FILES["file"]["size"] = $_FILES[$file_name]["size"];

			if ($CI->upload->do_upload('file')) {
				$data = $CI->upload->data();
				$files = $data["file_name"];
			} else {
				$files = 'tttt';
			}
		}

		return $files;
	}
}

if (!function_exists('getUserRole')) {
	function getUserRole($role_id = 0){
		if($role_id != 0){
			$CI = &get_instance();
			$user_role = $CI->db->query("SELECT `role` FROM user_roles WHERE id=".$role_id)->row();
			return $user_role->role;
		} else{
			return 0;
		}
	}
}

//helper for Update csrf
//Added by Deep Basak on Febuary 20, 2023
function update_csrf_session()
{
	$CI = &get_instance();
	$csrf = array(
		'csrfName' => $CI->security->get_csrf_token_name(),
		'csrfHash' => $CI->security->get_csrf_hash()
	);
	return $csrf;
}


//helper for Auto Logout
//Added by Suhrid Sarkar 05-06-2023
function check_login_time()
{
    $CI = &get_instance();
	$user_details = $CI->LoginModel->get_user_data('user_masters', $CI->session->userdata('user_id'));
    // Check if user is logged in
	if ($CI->session->userdata('user_id')) {
        $last_activity = $CI->session->userdata('last_activity');

        // Check if the user's last activity time is older than 15 minutes
        if (time() - $last_activity > $user_details->auto_logout_after_second) { // 60 seconds = 1 minutes
            // Destroy the session and log the user out
            $CI->session->sess_destroy();
            redirect('login'); // Replace 'login' with your logout URL
        } else {
            // Update the user's last activity time
            $CI->session->set_userdata('last_activity', time());
        }
	}
}
//helper for Auto Screen Lock
function screen_lock()
{
    $CI = &get_instance();
	$user_details = $CI->LoginModel->get_user_data('user_masters', $CI->session->userdata('user_id'));
	// echo $user_details->screen_lock_after_second;
    // Check if user is logged in
    if ($CI->session->userdata('user_id')) {
        $last_activity = $CI->session->userdata('last_activity');

        // Check if the user's last activity time is older than 15 minutes
        if (time() - $last_activity > $user_details->screen_lock_after_second) { // 900 seconds = 15 minutes
            // Destroy the session and log the user out
            return "1";
        }else{
			$CI->session->set_userdata('last_activity', time());
			return "0";
		}
    }
}


function check_last_active_time(){		
	#=====================================
	# Added By Suhrid Sarkar on 06-06-2023
	# Check Auto Logout
	#=====================================
	$CI = &get_instance();
	$user_details = $CI->LoginModel->get_user_data('user_masters', $CI->session->userdata('user_id'));
	if($user_details->auto_logout == 'Y'){
		check_login_time();
	}
	
	#=====================================
	# Check Auto Screen Lock
	#=====================================
	if($user_details->screen_lock == 'Y'){
		if(screen_lock() == '1'){
			redirect("lock_screen");
		}
	}
	// End

}
?>
