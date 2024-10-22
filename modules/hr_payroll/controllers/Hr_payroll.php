<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * hr payroll controler
 */
class hr_payroll extends AdminController {

	public function __construct() {
		parent::__construct();
		$this->load->model('hr_payroll_model');
		hooks()->do_action('hr_payroll_init'); 

		$this->load->model('common/Common_model');		//Added by DEEP BASAK on January 09, 2024
		$this->load->library('form_validation');        //Added by DEEP BASAK on May 21, 2024
	}

	/**
	 * setting
	 * @return view
	 */
	public function setting() {
		if (!has_permission('hrp_setting', '', 'view') && !has_permission('hrp_setting', '', 'edit') && !is_admin() && !has_permission('hrp_setting', '', 'create')) {
			access_denied('hrp_settting');
		}

		$data['group'] = $this->input->get('group');
		$data['title'] = _l('setting');

		$data['tab'][] = 'income_tax_rates';
		$data['tab'][] = 'income_tax_rebates';
		if (hr_payroll_get_status_modules('hr_profile') && (get_hr_payroll_option('integrated_hrprofile') == 1)) {
			$data['tab'][] = 'hr_records_earnings_list';
		} else {
			$data['tab'][] = 'earnings_list';
		}
		$data['tab'][] = 'salary_deductions_list';
		$data['tab'][] = 'insurance_list';
		$data['tab'][] = 'payroll_columns';
		$data['tab'][] = 'data_integration';

		if (is_admin()) {
			$data['tab'][] = 'permissions';
			$data['tab'][] = 'reset_data';
		}

		if ($data['group'] == '') {
			$data['group'] = 'payroll_columns';
			$data['payroll_column_value'] = $this->hr_payroll_model->get_hrp_payroll_columns();
			$data['order_display_in_paylip'] = $this->hr_payroll_model->count_payroll_column();
		} elseif ($data['group'] == 'payroll_columns') {
			$data['payroll_column_value'] = $this->hr_payroll_model->get_hrp_payroll_columns();
			$data['order_display_in_paylip'] = $this->hr_payroll_model->count_payroll_column();

		} elseif ($data['group'] == 'income_tax_rates') {
			$data['title'] = _l('income_tax_rates');
			$data['income_tax_rates'] = json_encode($this->hr_payroll_model->get_income_tax_rate());
		} elseif ($data['group'] == 'income_tax_rebates') {
			$data['title'] = _l('income_tax_rebates');
			$data['income_tax_rebates'] = json_encode($this->hr_payroll_model->get_income_tax_rebates());
		} elseif ($data['group'] == 'earnings_list') {

			$earnings_value = [];
			$earnings_value[] = [
				'id' => 'monthly',
				'label' => _l('monthly'),
			];
			$earnings_value[] = [
				'id' => 'annual',
				'label' => _l('annual'),
			];

			$data['title'] = _l('earnings_list');
			$data['basis_value'] = $earnings_value;
			$data['earnings_list'] = json_encode($this->hr_payroll_model->get_earnings_list());
		} elseif ($data['group'] == 'salary_deductions_list') {
			$earn_inclusion_value = [];
			$earn_inclusion_value[] = [
				'id' => 'fullvalue',
				'label' => _l('fullvalue'),
			];
			$earn_inclusion_value[] = [
				'id' => 'taxable',
				'label' => _l('taxable'),
			];

			$basis_value = [];
			$basis_value[] = [
				'id' => 'gross',
				'label' => _l('gross'),
			];
			$basis_value[] = [
				'id' => 'fixed_amount',
				'label' => _l('fixed_amount'),
			];

			if (hr_payroll_get_status_modules('hr_profile') && (get_hr_payroll_option('integrated_hrprofile') == 1)) {
				$earnings_list = $this->hr_payroll_model->hr_records_get_earnings_list();

				foreach ($earnings_list as $value) {
					switch ($value['rel_type']) {
						case 'salary':
						
						$basis_value[] = [
							'id' => 'st_'.$value['rel_id'],
							'label' => $value['description'],
						];
						break;

						case 'allowance':
						$basis_value[] = [
							'id' => 'al_'.$value['rel_id'],
							'label' => $value['description'],
						];
						
						break;

						default:
						# code...
						break;
					}

				}


			} else {
				$earnings_list = $this->hr_payroll_model->get_earnings_list();

				foreach ($earnings_list as $value) {
					$basis_value[] = [
						'id' => 'earning_'.$value['id'],
						'label' => $value['description'],
					];
				}
			}

			$data['title'] = _l('salary_deductions_list');
			$data['basis_value'] = $basis_value;
			$data['earn_inclusion'] = $earn_inclusion_value;
			$data['salary_deductions_list'] = json_encode($this->hr_payroll_model->get_salary_deductions_list());

		} elseif ($data['group'] == 'insurance_list') {
			$basis_value = [];
			$basis_value[] = [
				'id' => 'gross',
				'label' => _l('gross'),
			];
			$basis_value[] = [
				'id' => 'fixed_amount',
				'label' => _l('fixed_amount'),
			];

			$data['title'] = _l('insurance_list');
			$data['basis_value'] = $basis_value;
			$data['insurance_list'] = json_encode($this->hr_payroll_model->get_insurance_list());

		} elseif ($data['group'] == 'company_contributions_list') {
			$earn_inclusion_value = [];
			$earn_inclusion_value[] = [
				'id' => 'fullvalue',
				'label' => _l('fullvalue'),
			];
			$earn_inclusion_value[] = [
				'id' => 'taxable',
				'label' => _l('taxable'),
			];
			$earn_inclusion_value[] = [
				'id' => 'none',
				'label' => _l('none'),
			];

			$data['title'] = _l('company_contributions_list');
			$data['earn_inclusion'] = $earn_inclusion_value;
			$data['company_contributions_list'] = json_encode($this->hr_payroll_model->get_company_contributions_list());
		} elseif ($data['group'] == 'data_integration') {
			$data['hr_profile_active'] = hr_payroll_get_status_modules('hr_profile');
			$data['timesheets_active'] = hr_payroll_get_status_modules('timesheets');
			$data['commissions_active'] = hr_payroll_get_status_modules('commission');

			$hr_profile_title = '';
			$timesheets_title = '';
			//title
			if ($data['hr_profile_active'] == false) {
				$hr_profile_title = _l('active_hr_profile_to_integration');
			} else {
				$hr_profile_title = _l('hr_profile_integration_data');
			}

			if ($data['timesheets_active'] == false) {
				$timesheets_title = _l('active_timesheets_to_integration');
			} else {
				$timesheets_title = _l('timesheets_to_integration');
			}

			if ($data['commissions_active'] == false) {
				$commissions_title = _l('active_commissions_to_integration');
			} else {
				$commissions_title = _l('commissions_to_integration');
			}

			$data['hr_profile_title'] = $hr_profile_title;
			$data['timesheets_title'] = $timesheets_title;
			$data['commissions_title'] = $commissions_title;

			//get data each type
			$get_attendance_type = $this->hr_payroll_model->setting_get_attendance_type();

			$data['actual_workday_type'] = $get_attendance_type['actual_workday'];
			$data['paid_leave_type'] = $get_attendance_type['paid_leave'];
			$data['unpaid_leave_type'] = $get_attendance_type['unpaid_leave'];

		} elseif ($data['group'] == 'hr_records_earnings_list') {
			$earnings_value = [];
			$earnings_value[] = [
				'id' => 'monthly',
				'label' => _l('monthly'),
			];
			$earnings_value[] = [
				'id' => 'annual',
				'label' => _l('annual'),
			];

			$data['title'] = _l('earnings_list');
			$data['basis_value'] = $earnings_value;
			$data['earnings_list_hr_records'] = json_encode($this->hr_payroll_model->hr_records_get_earnings_list());
		}

		$data['tabs']['view'] = 'includes/' . $data['group'];

		$this->load->view('includes/manage_setting', $data);
	}

	/**
	 * setting incometax rates
	 * @return [type]
	 */
	public function setting_incometax_rates() {
		if ($this->input->post()) {

			$data = $this->input->post();
			if (!$this->input->post('id')) {

				$mess = $this->hr_payroll_model->update_income_tax_rates($data);
				if ($mess) {
					set_alert('success', _l('hrp_updated_successfully'));

				} else {
					set_alert('warning', _l('hrp_updated_failed'));
				}

				redirect(admin_url('hr_payroll/setting?group=income_tax_rates'));

			}

		}
	}

	/**
	 * setting incometax rebates
	 * @return [type]
	 */
	public function setting_incometax_rebates() {
		if ($this->input->post()) {

			$data = $this->input->post();
			if (!$this->input->post('id')) {

				$mess = $this->hr_payroll_model->update_income_tax_rebates($data);
				if ($mess) {
					set_alert('success', _l('hrp_updated_successfully'));

				} else {
					set_alert('warning', _l('hrp_updated_failed'));
				}

				redirect(admin_url('hr_payroll/setting?group=income_tax_rebates'));

			}

		}
	}

	/**
	 * setting earnings list
	 * @return [type]
	 */
	public function setting_earnings_list() {
		if ($this->input->post()) {

			$data = $this->input->post();
			if (!$this->input->post('id')) {

				$mess = $this->hr_payroll_model->update_earnings_list($data);
				if ($mess) {
					set_alert('success', _l('hrp_updated_successfully'));

				} else {
					set_alert('warning', _l('hrp_updated_failed'));
				}

				redirect(admin_url('hr_payroll/setting?group=earnings_list'));

			}

		}
	}

	/**
	 * setting salary deductions list
	 * @return [type]
	 */
	public function setting_salary_deductions_list() {
		if ($this->input->post()) {

			$data = $this->input->post();
			if (!$this->input->post('id')) {

				$mess = $this->hr_payroll_model->update_salary_deductions_list($data);
				if ($mess) {
					set_alert('success', _l('hrp_updated_successfully'));

				} else {
					set_alert('warning', _l('hrp_updated_failed'));
				}

				redirect(admin_url('hr_payroll/setting?group=salary_deductions_list'));

			}

		}
	}

	/**
	 * setting insurance list
	 * @return [type]
	 */
	public function setting_insurance_list() {
		if ($this->input->post()) {

			$data = $this->input->post();
			if (!$this->input->post('id')) {

				$mess = $this->hr_payroll_model->update_insurance_list($data);
				if ($mess) {
					set_alert('success', _l('hrp_updated_successfully'));

				} else {
					set_alert('warning', _l('hrp_updated_failed'));
				}

				redirect(admin_url('hr_payroll/setting?group=insurance_list'));

			}

		}
	}

	/**
	 * setting company contributions list
	 * @return [type]
	 */
	public function setting_company_contributions_list() {
		if ($this->input->post()) {

			$data = $this->input->post();
			if (!$this->input->post('id')) {

				$mess = $this->hr_payroll_model->update_company_contributions_list($data);
				if ($mess) {
					set_alert('success', _l('hrp_updated_successfully'));

				} else {
					set_alert('warning', _l('hrp_updated_failed'));
				}

				redirect(admin_url('hr_payroll/setting?group=company_contributions_list'));

			}

		}
	}

	/**
	 * data integration
	 * @return [type]
	 */
	public function data_integration() {
		if (!is_admin()) {
			access_denied('hr_payroll');
		}

		$data = $this->input->post();

		$mess = $this->hr_payroll_model->update_data_integration($data);
		if ($mess) {
			set_alert('success', _l('hrp_updated_successfully'));

		} else {
			set_alert('warning', _l('hrp_updated_failed'));
		}

		redirect(admin_url('hr_payroll/setting?group=data_integration'));

	}

	/**
	 * timesheet integration type change
	 * @return [type]
	 */
	public function timesheet_integration_type_change() {
		if ($this->input->post()) {
			$data = $this->input->post();

			$results = $this->hr_payroll_model->get_timesheet_type_for_setting($data);

			echo json_encode([
				'actual_workday_v' => $results['actual_workday'],
				'paid_leave_v' => $results['paid_leave'],
				'unpaid_leave_v' => $results['unpaid_leave'],
			]);
			die;
		}
	}

	/**
	 * setting earnings list hr records
	 * @return [type]
	 */
	public function setting_earnings_list_hr_records() {
		if ($this->input->post()) {

			$data = $this->input->post();
			if (!$this->input->post('id')) {

				$mess = $this->hr_payroll_model->earnings_list_synchronization($data);
				set_alert('success', _l('hrp_successful_data_synchronization'));
				if ($mess) {
					set_alert('success', _l('hrp_updated_successfully'));

				} else {
					set_alert('warning', _l('hrp_updated_failed'));
				}

				redirect(admin_url('hr_payroll/setting?group=hr_records_earnings_list'));
			}
		}
	}

	/**
	 * hr payroll permission table
	 * @return [type]
	 */
	public function hr_payroll_permission_table() {
		if ($this->input->is_ajax_request()) {

			$select = [
				'staffid',
				'CONCAT(firstname," ",lastname) as full_name',
				'firstname', //for role name
				'email',
				'phonenumber',
			];
			$where = [];
			$where[] = 'AND ' . db_prefix() . 'staff.admin != 1';

			$arr_staff_id = hr_payroll_get_staff_id_hr_permissions();

			if (count($arr_staff_id) > 0) {
				$where[] = 'AND ' . db_prefix() . 'staff.staffid IN (' . implode(', ', $arr_staff_id) . ')';
			} else {
				$where[] = 'AND ' . db_prefix() . 'staff.staffid IN ("")';
			}

			$aColumns = $select;
			$sIndexColumn = 'staffid';
			$sTable = db_prefix() . 'staff';
			$join = ['LEFT JOIN ' . db_prefix() . 'roles ON ' . db_prefix() . 'roles.roleid = ' . db_prefix() . 'staff.role'];

			$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'roles.name as role_name', db_prefix() . 'staff.role']);

			$output = $result['output'];
			$rResult = $result['rResult'];

			$not_hide = '';

			foreach ($rResult as $aRow) {
				$row = [];

				$row[] = '<a href="' . admin_url('staff/member/' . $aRow['staffid']) . '">' . $aRow['full_name'] . '</a>';

				$row[] = $aRow['role_name'];
				$row[] = $aRow['email'];
				$row[] = $aRow['phonenumber'];

				$options = '';

				if (has_permission('hrm_setting', '', 'edit')) {
					$options = icon_btn('#', 'edit', 'btn-default', [
						'title' => _l('hr_edit'),
						'onclick' => 'hr_payroll_permissions_update(' . $aRow['staffid'] . ', ' . $aRow['role'] . ', ' . $not_hide . '); return false;',
					]);
				}

				if (has_permission('hrm_setting', '', 'delete')) {
					$options .= icon_btn('hr_payroll/delete_hr_payroll_permission/' . $aRow['staffid'], 'remove', 'btn-danger _delete', ['title' => _l('delete')]);
				}

				$row[] = $options;

				$output['aaData'][] = $row;
			}

			echo json_encode($output);
			die();
		}
	}

	/**
	 * permission modal
	 * @return [type]
	 */
	public function permission_modal() {
		if (!$this->input->is_ajax_request()) {
			show_404();
		}
		$this->load->model('staff_model');

		if ($this->input->post('slug') === 'update') {
			$staff_id = $this->input->post('staff_id');
			$role_id = $this->input->post('role_id');

			$data = ['funcData' => ['staff_id' => isset($staff_id) ? $staff_id : null]];

			if (isset($staff_id)) {
				$data['member'] = $this->staff_model->get($staff_id);
			}

			$data['roles_value'] = $this->roles_model->get();
			$data['staffs'] = hr_payroll_get_staff_id_dont_permissions();
			$add_new = $this->input->post('add_new');

			if ($add_new == ' hide') {
				$data['add_new'] = ' hide';
				$data['display_staff'] = '';
			} else {
				$data['add_new'] = '';
				$data['display_staff'] = ' hide';
			}

			$this->load->view('includes/permission_modal', $data);
		}
	}

	/**
	 * hr payroll update permissions
	 * @param  string $id
	 * @return [type]
	 */
	public function hr_payroll_update_permissions($id = '') {
		if (!is_admin()) {
			access_denied('hr_payroll');
		}
		$data = $this->input->post();

		if (!isset($id) || $id == '') {
			$id = $data['staff_id'];
		}

		if (isset($id) && $id != '') {

			$data = hooks()->apply_filters('before_update_staff_member', $data, $id);

			if (is_admin()) {
				if (isset($data['administrator'])) {
					$data['admin'] = 1;
					unset($data['administrator']);
				} else {
					if ($id != get_staff_user_id()) {
						if ($id == 1) {
							return [
								'cant_remove_main_admin' => true,
							];
						}
					} else {
						return [
							'cant_remove_yourself_from_admin' => true,
						];
					}
					$data['admin'] = 0;
				}
			}

			$this->db->where('staffid', $id);
			$this->db->update(db_prefix() . 'staff', [
				'role' => $data['role'],
			]);

			$response = $this->staff_model->update_permissions((isset($data['admin']) && $data['admin'] == 1 ? [] : $data['permissions']), $id);
		} else {
			$this->load->model('roles_model');

			$role_id = $data['role'];
			unset($data['role']);
			unset($data['staff_id']);

			$data['update_staff_permissions'] = true;

			$response = $this->roles_model->update($data, $role_id);
		}

		if (is_array($response)) {
			if (isset($response['cant_remove_main_admin'])) {
				set_alert('warning', _l('staff_cant_remove_main_admin'));
			} elseif (isset($response['cant_remove_yourself_from_admin'])) {
				set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
			}
		} elseif ($response == true) {
			set_alert('success', _l('updated_successfully', _l('staff_member')));
		}
		redirect(admin_url('hr_payroll/setting?group=permissions'));

	}

	/**
	 * staff id changed
	 * @param  [type] $staff_id
	 * @return [type]
	 */
	public function staff_id_changed($staff_id) {
		$role_id = '';
		$status = 'false';
		$r_permission = [];

		$staff = $this->staff_model->get($staff_id);

		if ($staff) {
			if (count($staff->permissions) > 0) {
				foreach ($staff->permissions as $permission) {
					$r_permission[$permission['feature']][] = $permission['capability'];
				}
			}

			$role_id = $staff->role;
			$status = 'true';

		}

		if (count($r_permission) > 0) {
			$data = ['role_id' => $role_id, 'status' => $status, 'permission' => 'true', 'r_permission' => $r_permission];
		} else {
			$data = ['role_id' => $role_id, 'status' => $status, 'permission' => 'false', 'r_permission' => $r_permission];
		}

		echo json_encode($data);
		die;
	}

	/**
	 * delete hr payroll permission
	 * @param  [type] $id
	 * @return [type]
	 */
	public function delete_hr_payroll_permission($id) {
		if (!is_admin()) {
			access_denied('hr_profile');
		}

		$response = $this->hr_payroll_model->delete_hr_payroll_permission($id);

		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('hr_is_referenced', _l('department_lowercase')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('hr_department')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('department_lowercase')));
		}
		redirect(admin_url('hr_payroll/setting?group=permissions'));

	}

	/**
	 * manage employees
	 * @return [type]
	 */
	public function manage_employees() {
		if (!has_permission('hrp_employee', '', 'view') && !has_permission('hrp_employee', '', 'view_own') && !is_admin()) {
			access_denied('hrp_employee');
		}

		$this->load->model('staff_model');
		$this->load->model('departments_model');

		$rel_type = hrp_get_hr_profile_status();

		//get current month
		$current_month = date('Y-m-d', strtotime(date('Y-m') . '-01'));
		$employees_data = $this->hr_payroll_model->get_employees_data($current_month, $rel_type);
		$employees_value = [];
		foreach ($employees_data as $key => $value) {
			$employees_value[$value['staff_id'] . '_' . $value['month']] = $value;
		}
		//get employee data for the first
		$format_employees_value = $this->hr_payroll_model->get_format_employees_data($rel_type);

		//load staff
		if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		//get current month

		$data_object_kpi = [];

		foreach ($staffs as $staff_key => $staff_value) {
			/*check value from database*/
			$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

			$staff_i = $this->hr_payroll_model->get_staff_info($staff_value['staffid']);
			if ($staff_i) {

				if ($rel_type == 'hr_records') {
					$data_object_kpi[$staff_key]['employee_number'] = $staff_i->staff_identifi;
				} else {
					$data_object_kpi[$staff_key]['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_i->staffid, 5);
				}

				$data_object_kpi[$staff_key]['employee_name'] = $staff_i->firstname . ' ' . $staff_i->lastname;

				$arr_department = $this->hr_payroll_model->get_staff_departments($staff_i->staffid, true);

				$list_department = '';
				if (count($arr_department) > 0) {

					foreach ($arr_department as $key => $department) {
						$department_value = $this->departments_model->get($department);

						if ($department_value) {
							if (strlen($list_department) != 0) {
								$list_department .= ', ' . $department_value->name;
							} else {
								$list_department .= $department_value->name;
							}
						}
					}
				}

				$data_object_kpi[$staff_key]['department_name'] = $list_department;

			} else {
				$data_object_kpi[$staff_key]['employee_number'] = '';
				$data_object_kpi[$staff_key]['employee_name'] = '';
				$data_object_kpi[$staff_key]['department_name'] = '';
			}

			if ($rel_type == 'hr_records') {
				$data_object_kpi[$staff_key]['job_title'] = $staff_value['position_name'];
				$data_object_kpi[$staff_key]['income_tax_number'] = $staff_value['Personal_tax_code'];
				$data_object_kpi[$staff_key]['residential_address'] = $staff_value['resident'];
			} else {
				if (isset($employees_value[$staff_value['staffid'] . '_' . $current_month])) {
					$data_object_kpi[$staff_key]['job_title'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['job_title'];
					$data_object_kpi[$staff_key]['income_tax_number'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['income_tax_number'];
					$data_object_kpi[$staff_key]['residential_address'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['residential_address'];

				} else {
					$data_object_kpi[$staff_key]['job_title'] = '';
					$data_object_kpi[$staff_key]['income_tax_number'] = '';
					$data_object_kpi[$staff_key]['residential_address'] = '';
				}
			}

			if (isset($employees_value[$staff_value['staffid'] . '_' . $current_month])) {

				$data_object_kpi[$staff_key]['income_rebate_code'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['income_rebate_code'];
				$data_object_kpi[$staff_key]['income_tax_rate'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['income_tax_rate'];

				// array merge: staff information + earning list (probationary contract) + earning list (formal)
				if (isset($employees_value[$staff_value['staffid'] . '_' . $current_month]['contract_value'])) {

					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $employees_value[$staff_value['staffid'] . '_' . $current_month]['contract_value']);
				} else {
					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_employees_value['probationary'], $format_employees_value['formal']);
				}

				$data_object_kpi[$staff_key]['probationary_effective'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['probationary_effective'];
				$data_object_kpi[$staff_key]['probationary_expiration'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['probationary_expiration'];
				$data_object_kpi[$staff_key]['primary_effective'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['primary_effective'];
				$data_object_kpi[$staff_key]['primary_expiration'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['primary_expiration'];

				$data_object_kpi[$staff_key]['id'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['id'];
				$data_object_kpi[$staff_key]['bank_name'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['bank_name'];
				$data_object_kpi[$staff_key]['account_number'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['account_number'];


			} else {
				$data_object_kpi[$staff_key]['income_rebate_code'] = 'A';
				$data_object_kpi[$staff_key]['income_tax_rate'] = 'A';

				// array merge: staff information + earning list (probationary contract) + earning list (formal)
				$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_employees_value['probationary'], $format_employees_value['formal']);

				$data_object_kpi[$staff_key]['probationary_effective'] = '';
				$data_object_kpi[$staff_key]['probationary_expiration'] = '';
				$data_object_kpi[$staff_key]['primary_effective'] = '';
				$data_object_kpi[$staff_key]['primary_expiration'] = '';

				$data_object_kpi[$staff_key]['id'] = 0;
				$data_object_kpi[$staff_key]['bank_name'] = '';
				$data_object_kpi[$staff_key]['account_number'] = '';

			}

			$data_object_kpi[$staff_key]['rel_type'] = $rel_type;
		}
		//check is add new or update data
		if (count($employees_value) > 0) {
			$data['button_name'] = _l('hrp_update');
		} else {
			$data['button_name'] = _l('submit');
		}

		$data['departments'] = $this->departments_model->get();
		$data['roles'] = $this->roles_model->get();
		$data['staffs'] = $staffs;

		$data['body_value'] = json_encode($data_object_kpi);
		$data['columns'] = json_encode($format_employees_value['column_format']);
		$data['col_header'] = json_encode($format_employees_value['header']);

		$this->load->view('employees/employees_manage', $data);
	}

	/**
	 * employees filter
	 * @return [type]
	 */
	public function employees_filter() {
		$this->load->model('departments_model');
		$data = $this->input->post();

		$rel_type = hrp_get_hr_profile_status();

		$months_filter = $data['month'];
		$department = $data['department'];
		$staff = '';
		if (isset($data['staff'])) {
			$staff = $data['staff'];
		}
		$role_attendance = '';
		if (isset($data['role_attendance'])) {
			$role_attendance = $data['role_attendance'];
		}

		$newquerystring = $this->render_filter_query($months_filter, $staff, $department, $role_attendance);

		//get current month
		$month_filter = date('Y-m-d', strtotime($data['month'] . '-01'));
		$employees_data = $this->hr_payroll_model->get_employees_data($month_filter, $rel_type);
		$employees_value = [];
		foreach ($employees_data as $key => $value) {
			$employees_value[$value['staff_id'] . '_' . $value['month']] = $value;
		}

		//get employee data for the first
		$format_employees_value = $this->hr_payroll_model->get_format_employees_data($rel_type);

		// data return
		$data_object_kpi = [];
		$index_data_object = 0;
		if ($newquerystring != '') {

			//load deparment by manager
			if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
				//View own
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission($newquerystring));
			} else {
				//admin or view global
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object($newquerystring);
			}

			$data_object_kpi = [];

			foreach ($staffs as $staff_key => $staff_value) {
				/*check value from database*/
				$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

				$staff_i = $this->hr_payroll_model->get_staff_info($staff_value['staffid']);
				if ($staff_i) {

					if ($rel_type == 'hr_records') {
						$data_object_kpi[$staff_key]['employee_number'] = $staff_i->staff_identifi;
					} else {
						$data_object_kpi[$staff_key]['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_i->staffid, 5);
					}

					$data_object_kpi[$staff_key]['employee_name'] = $staff_i->firstname . ' ' . $staff_i->lastname;

					$arr_department = $this->hr_payroll_model->get_staff_departments($staff_i->staffid, true);

					$list_department = '';
					if (count($arr_department) > 0) {

						foreach ($arr_department as $key => $department) {
							$department_value = $this->departments_model->get($department);

							if ($department_value) {
								if (strlen($list_department) != 0) {
									$list_department .= ', ' . $department_value->name;
								} else {
									$list_department .= $department_value->name;
								}
							}
						}
					}

					$data_object_kpi[$staff_key]['department_name'] = $list_department;

				} else {
					$data_object_kpi[$staff_key]['employee_number'] = '';
					$data_object_kpi[$staff_key]['employee_name'] = '';
					$data_object_kpi[$staff_key]['department_name'] = '';
				}

				if ($rel_type == 'hr_records') {
					$data_object_kpi[$staff_key]['job_title'] = $staff_value['position_name'];
					$data_object_kpi[$staff_key]['income_tax_number'] = $staff_value['Personal_tax_code'];
					$data_object_kpi[$staff_key]['residential_address'] = $staff_value['resident'];
				} else {
					if (isset($employees_value[$staff_value['staffid'] . '_' . $month_filter])) {
						$data_object_kpi[$staff_key]['job_title'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['job_title'];
						$data_object_kpi[$staff_key]['income_tax_number'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['income_tax_number'];
						$data_object_kpi[$staff_key]['residential_address'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['residential_address'];

					} else {
						$data_object_kpi[$staff_key]['job_title'] = '';
						$data_object_kpi[$staff_key]['income_tax_number'] = '';
						$data_object_kpi[$staff_key]['residential_address'] = '';
					}
				}

				if (isset($employees_value[$staff_value['staffid'] . '_' . $month_filter])) {

					$data_object_kpi[$staff_key]['income_rebate_code'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['income_rebate_code'];
					$data_object_kpi[$staff_key]['income_tax_rate'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['income_tax_rate'];

					$data_object_kpi[$staff_key]['probationary_effective'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['probationary_effective'];
					$data_object_kpi[$staff_key]['probationary_expiration'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['probationary_expiration'];
					$data_object_kpi[$staff_key]['primary_effective'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['primary_effective'];
					$data_object_kpi[$staff_key]['primary_expiration'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['primary_expiration'];

					// array merge: staff information + earning list (probationary contract) + earning list (formal)
					if (isset($employees_value[$staff_value['staffid'] . '_' . $month_filter]['contract_value'])) {

						$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $employees_value[$staff_value['staffid'] . '_' . $month_filter]['contract_value']);
					} else {
						$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_employees_value['probationary'], $format_employees_value['formal']);
					}

					$data_object_kpi[$staff_key]['id'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['id'];
					$data_object_kpi[$staff_key]['bank_name'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['bank_name'];
					$data_object_kpi[$staff_key]['account_number'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['account_number'];


				} else {
					$data_object_kpi[$staff_key]['income_rebate_code'] = 'A';
					$data_object_kpi[$staff_key]['income_tax_rate'] = 'A';

					// array merge: staff information + earning list (probationary contract) + earning list (formal)
					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_employees_value['probationary'], $format_employees_value['formal']);

					$data_object_kpi[$staff_key]['id'] = 0;
					$data_object_kpi[$staff_key]['bank_name'] = '';
					$data_object_kpi[$staff_key]['account_number'] = '';

				}

				$data_object_kpi[$staff_key]['rel_type'] = $rel_type;
			}

		}

		//check is add new or update data
		if (count($employees_value) > 0) {
			$button_name = _l('hrp_update');
		} else {
			$button_name = _l('submit');
		}

		echo json_encode([
			'data_object' => $data_object_kpi,
			'button_name' => $button_name,
		]);
		die;
	}

	/**
	 * add manage employees
	 */
	public function add_manage_employees() {
		if (!has_permission('hrp_employee', '', 'create') && !has_permission('hrp_employee', '', 'edit') && !is_admin()) {
			access_denied('hrp_employee');
		}

		if ($this->input->post()) {
			$data = $this->input->post();
			if ($data['hrp_employees_rel_type'] == 'synchronization') {
				//synchronization
				$success = $this->hr_payroll_model->employees_synchronization($data);
			} elseif ($data['hrp_employees_rel_type'] == 'update') {
				// update data
				$success = $this->hr_payroll_model->employees_update($data);
			} else {
				$success = false;
			}

			if ($success) {
				set_alert('success', _l('updated_successfully'));
			} else {
				set_alert('warning', _l('hrp_updated_failed'));
			}

			redirect(admin_url('hr_payroll/manage_employees'));
		}

	}

	/**
	 * render filter query
	 * @param  [type] $data_month
	 * @param  [type] $data_staff
	 * @param  [type] $data_department
	 * @param  [type] $data_role_attendance
	 * @return [type]
	 */
	public function render_filter_query($data_month, $data_staff, $data_department, $data_role_attendance) {

		$months_filter = $data_month;
		$querystring = ' active=1';
		$department = $data_department;

		$staff = '';
		if (isset($data_staff)) {
			$staff = $data_staff;
		}
		$staff_querystring = '';
		$department_querystring = '';
		$role_querystring = '';

		if ($department != '') {
			$arrdepartment = $this->staff_model->get('', 'staffid in (select tblstaff_departments.staffid from tblstaff_departments where departmentid = ' . $department . ')');
			$temp = '';
			foreach ($arrdepartment as $value) {
				$temp = $temp . $value['staffid'] . ',';
			}
			$temp = rtrim($temp, ",");
			$department_querystring = 'FIND_IN_SET(staffid, "' . $temp . '")';
		}

		if ($staff != '') {
			$temp = '';
			$araylengh = count($staff);
			for ($i = 0; $i < $araylengh; $i++) {
				$temp = $temp . $staff[$i];
				if ($i != $araylengh - 1) {
					$temp = $temp . ',';
				}
			}
			$staff_querystring = 'FIND_IN_SET(staffid, "' . $temp . '")';
		}

		if (isset($data_role_attendance) && $data_role_attendance != '') {
			$temp = '';
			$araylengh = count($data_role_attendance);
			for ($i = 0; $i < $araylengh; $i++) {
				$temp = $temp . $data_role_attendance[$i];
				if ($i != $araylengh - 1) {
					$temp = $temp . ',';
				}
			}
			$role_querystring = 'FIND_IN_SET(role, "' . $temp . '")';
		}

		$arrQuery = array($staff_querystring, $department_querystring, $querystring, $role_querystring);

		$newquerystring = '';
		foreach ($arrQuery as $string) {
			if ($string != '') {
				$newquerystring = $newquerystring . $string . ' AND ';
			}
		}

		$newquerystring = rtrim($newquerystring, "AND ");
		if ($newquerystring == '') {
			$newquerystring = [];
		}

		return $newquerystring;
	}

	/**
	 * manage attendance
	 * @return [type]
	 */
	public function manage_attendance() {
		if (!has_permission('hrp_attendance', '', 'view') && !has_permission('hrp_attendance', '', 'view_own') && !is_admin()) {
			access_denied('hrp_attendance');
		}

		$this->load->model('staff_model');
		$this->load->model('departments_model');

		// $rel_type = hrp_get_timesheets_status();

		// //get current month
		// $current_month = date('Y-m-d', strtotime(date('Y-m') . '-01'));

		// //get day header in month
		// $days_header_in_month = $this->hr_payroll_model->get_day_header_in_month($current_month, $rel_type);

		// $attendances = $this->hr_payroll_model->get_hrp_attendance($current_month);
		// $attendances_value = [];

		// foreach ($attendances as $key => $value) {
		// 	$attendances_value[$value['staff_id'] . '_' . $value['month']] = $value;
		// }

		//load deparment by manager
		if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		// $data_object_kpi = [];

		// foreach ($staffs as $staff_key => $staff_value) {
		// 	/*check value from database*/

		// 	$staff_i = $this->hr_payroll_model->get_staff_info($staff_value['staffid']);
		// 	if ($staff_i) {

		// 		if (isset($staff_i->staff_identifi)) {
		// 			$data_object_kpi[$staff_key]['hr_code'] = $staff_i->staff_identifi;
		// 		} else {
		// 			$data_object_kpi[$staff_key]['hr_code'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_i->staffid, 5);
		// 		}

		// 		$data_object_kpi[$staff_key]['staff_name'] = $staff_i->firstname . ' ' . $staff_i->lastname;

		// 		$arr_department = $this->hr_payroll_model->get_staff_departments($staff_i->staffid, true);

		// 		$list_department = '';
		// 		if (count($arr_department) > 0) {

		// 			foreach ($arr_department as $key => $department) {
		// 				$department_value = $this->departments_model->get($department);

		// 				if ($department_value) {
		// 					if (strlen($list_department) != 0) {
		// 						$list_department .= ', ' . $department_value->name;
		// 					} else {
		// 						$list_department .= $department_value->name;
		// 					}
		// 				}

		// 			}
		// 		}

		// 		$data_object_kpi[$staff_key]['staff_departments'] = $list_department;

		// 	} else {
		// 		$data_object_kpi[$staff_key]['hr_code'] = '';
		// 		$data_object_kpi[$staff_key]['staff_name'] = '';
		// 		$data_object_kpi[$staff_key]['staff_departments'] = '';

		// 	}

		// 	if (isset($attendances_value[$staff_value['staffid'] . '_' . $current_month])) {

		// 		$data_object_kpi[$staff_key]['standard_workday'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['standard_workday'];
		// 		$data_object_kpi[$staff_key]['actual_workday'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['actual_workday'];
		// 		$data_object_kpi[$staff_key]['actual_workday_probation'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['actual_workday_probation'];
		// 		$data_object_kpi[$staff_key]['paid_leave'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['paid_leave'];
		// 		$data_object_kpi[$staff_key]['unpaid_leave'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['unpaid_leave'];
		// 		$data_object_kpi[$staff_key]['id'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['id'];

		// 		$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $attendances_value[$staff_value['staffid'] . '_' . $current_month]);

		// 	} else {
		// 		$data_object_kpi[$staff_key]['standard_workday'] = get_hr_payroll_option('standard_working_time');
		// 		$data_object_kpi[$staff_key]['actual_workday_probation'] = 0;
		// 		$data_object_kpi[$staff_key]['actual_workday'] = 0;
		// 		$data_object_kpi[$staff_key]['paid_leave'] = 0;
		// 		$data_object_kpi[$staff_key]['unpaid_leave'] = 0;
		// 		$data_object_kpi[$staff_key]['id'] = 0;
		// 		$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $days_header_in_month['days_header']);

		// 	}
		// 	$data_object_kpi[$staff_key]['rel_type'] = $rel_type;
		// 	$data_object_kpi[$staff_key]['month'] = $current_month;
		// 	$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

		// }

		// //check is add new or update data
		// if (count($attendances_value) > 0) {
		// 	$data['button_name'] = _l('hrp_update');
		// } else {
		// 	$data['button_name'] = _l('submit');
		// }

		$data['departments'] = $this->departments_model->get();
		// $data['roles'] = $this->roles_model->get();
		$data['staffs'] = $staffs;
		// $data['data_object_kpi'] = $data_object_kpi;

		// $data['body_value'] = json_encode($data_object_kpi);
		// $data['columns'] = json_encode($days_header_in_month['columns_type']);
		// $data['col_header'] = json_encode($days_header_in_month['headers']);
		$data['title'] = 'Staff Attendance';
		$this->load->view('attendances/attendance_manage2', $data);
	}

	public function month_attendance_list($date = '', $staff_id = ''){
        # customize filter
		$where = ' `is_active` = "Y" ';
		if(!is_admin()){
			$where .= ' AND staff_id = ' . get_staff_user_id() . ' ';
		}

		if(($date != '') && ($date != 'null')){
			$where .= ' AND `check_in_date` BETWEEN "'.$date.'-01" AND "'.$date.'-31" ';
		} else{
			$where .= ' AND `check_in_date` BETWEEN "'.date('Y-m').'-01" AND "'.date('Y-m').'-31" ';
		}

		if(($staff_id != 0) && ($staff_id != '') && ($staff_id != 'null')){
			$where .= ' AND staff_id = ' . $staff_id . ' ';
		}

		// Skip number of Rows count  
		$start = $_POST["start"];

		// Paging Length 10,20  
		$length = $_POST["length"];

		// Search Value from (Search box)  
		$searchValue = trim($_POST["search"]["value"]);
		$searchwhere = '';
		if(!empty($searchValue)){
			$searchwhere .= ' AND check_in_date LIKE "%'.$searchValue.'%" ';
		}

		//Paging Size (10, 20, 50,100)  
		$pageSize = $length != null ? intval($length) : 0;
		$skip = $start != null ? intval($start) : 0;

		#region order by column
		//Cr by DEEP BASAK on March 26, 2024
		if(!empty($_POST['order'][0])){
			$colArr = array('', 'tbl_staff_attendance.check_in_date');
			$columnIndex = $_POST['order'][0]['column'];
			$orderColName = $colArr[$columnIndex];
			$orderDir = $_POST['order'][0]['dir'];
			$orderQuery = ' ORDER BY '. $orderColName . ' ' . $orderDir . ' ';
		} else{
			$orderQuery = 'ORDER BY `check_in_date` DESC';
		}
		#endregion


		//Datatable view Query
		$query = 'SELECT
			MAX( id ) AS id,
			`check_in_date` 
		FROM
			`tbl_staff_attendance` 
		WHERE
			'.$where.'
			'.$searchwhere.'
		GROUP BY
			`check_in_date` '. $orderQuery . ' 
		LIMIT '.$pageSize.' OFFSET '.$skip.' ';

		//Total records query
		$query_total = 'SELECT
			MAX( id ) AS id,
			`check_in_date` 
		FROM
			`tbl_staff_attendance` 
		WHERE
			'.$where.'
		GROUP BY
			`check_in_date` ' . $orderQuery . ' ';

		$testdata = $this->Common_model->callSP($query);
		$testdata_total = $this->Common_model->callSP($query_total);
		$data = array();
		
		foreach ($testdata as $key => $fieldData){
			$data[] = array(
				$key + $skip + 1,
				'<a href="javascript:" onclick="openAttendanceModal(\'' . $fieldData['check_in_date'] . '\')">' . date("F d, Y", strtotime($fieldData['check_in_date'])) . '</a>'
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

	public function get_working_chart(){
		$staff_id = $this->input->post('staff_id');
		$date = $this->input->post('month');
		if(($date != '') && ($date != 'null')){
			$where = 'YEAR ( check_in_date ) = '.date('Y', strtotime($date)).' AND MONTH ( check_in_date ) = '.date('m', strtotime($date)).' ';
		} else{
			$where = 'YEAR ( check_in_date ) = '.date('Y').' AND MONTH ( check_in_date ) = '.date('m').' ';
		}
		
		if(($staff_id != 0) && ($staff_id != '') && ($staff_id != 'null')){
			$where .= ' AND staff_id = ' . $staff_id . ' ';
		} else{
			$where .= ' AND staff_id = ' . get_staff_user_id() . ' ';
		}

		$query = 'SELECT
			total_hour
		FROM
			tbl_staff_attendance 
		WHERE
			' . $where . ' ORDER BY id DESC ';		//CR by Deep Added Order by cause on August 16, 2024
			
		$query1 = 'SELECT
			FORMAT(SUM(today_hour), 2) AS total_work_done
		FROM
			tbl_staff_attendance 
		WHERE
			' . $where . ' ORDER BY id DESC ';		//CR by Deep Added Order by cause on August 16, 2024
		// prx($query);

		$testdata = $this->Common_model->callSP($query, 'row');
		$testdata1 = $this->Common_model->callSP($query1, 'row');

		$data = array(
			'label' => array(
				'Work Hour Done ' . floatval($testdata1['total_work_done']),
				'Work Hour Pending ' . floatval($testdata['total_hour']) - floatval($testdata1['total_work_done'])
			),
			'serise' => array(
				floatval($testdata1['total_work_done']),
				floatval($testdata['total_hour']) - floatval($testdata1['total_work_done'])
			)
		);

		# response
        $result = array('status'=> 'success', 'message'=>'Display modal', 'data' => $data);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}

	/**
	 * Get Day Wise Attendance
	 * Added by DEEP BASAK on January 22, 2024
	 */
	public function day_wise_attendance(){
		$month = $this->input->post('month');
		$staff = $this->input->post('staff_id');
		if(empty($staff)){
			$where2 = array('staff_id' => get_staff_user_id());
		} else{
			$where2 = array('staff_id' => $staff);
		}

		//CR by DEEP BASAK on January 24, 2024 for bug fixing of column chart
		if(!empty($month)){
			$where3 = array('check_in_date >=' => $month . '-01', 'check_in_date <=' => $month . '-31');
		} else {
			$where3 = array('check_in_date >=' => date('Y-m') . '-01', 'check_in_date <=' => date('Y-m') . '-31',);
		}

		$where1 = array('is_active' => 'Y', 'today_hour <>' => '');
		$where = array_merge($where1, $where2, $where3);

		$attendance_details = $this->Common_model->getAllData('tbl_staff_attendance', 'total_hour, today_hour, check_in_date', '', $where, 'check_in_date ASC');
		// prx($this->db->last_query());
		$labels = array();
		$data = array();
		if(!empty($attendance_details)){
			foreach($attendance_details as $key => $val){
				$labels[] = date("F d, Y", strtotime($val->check_in_date));
				$data[] = $val->today_hour;
			}
		}
		$mainData = array('data'=> $data, 'label'=>$labels);

		# response
        $result = array('status'=> 'success', 'message'=>'Display modal', 'chart' => $mainData, 'text'=>'Monthly Attendance in '. date('F, Y', strtotime($month)));
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}

	public function load_attendance_modal(){
		// $data['attendance_list'] = 'test';
		$join = array(
			array(
				'table'		=> 'tblstaff',
				'on'		=> 'tblstaff.staffid = tbl_staff_attendance.staff_id',
				'type'		=> 'left'
			)
		);
		if(is_admin()){
			$data['attendance_list'] = $this->Common_model->getAllData('tbl_staff_attendance', '', '', ['is_active' => 'Y', 'check_in_date' => $this->input->post('date')], 'check_in_date DESC', '', '', '', [], $join);
		} else{
			$data['attendance_list'] = $this->Common_model->getAllData('tbl_staff_attendance', '', '', ['is_active' => 'Y', 'staff_id' => get_staff_user_id(), 'check_in_date' => $this->input->post('date')], 'check_in_date DESC', '', '', '', [], $join);
		}

		$html = $this->load->view('attendances/components/attendance_modal_body', $data, true);

		# response
        $result = array('status'=> 'success', 'message'=>'Display modal', 'html' => $html);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}

	/**
	 * Manage Payslip
	 * Added by DEEP BASAK on January 19, 2024
	 */
	public function manage_payslip(){
		if (!has_permission('hrp_attendance', '', 'view') && !has_permission('hrp_attendance', '', 'view_own') && !is_admin()) {
			access_denied('hrp_attendance');
		}

		$this->load->model('staff_model');
		$this->load->model('departments_model');

		//load deparment by manager
		if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		$data['departments'] = $this->departments_model->get();
		$data['staffs'] = $staffs;

		$data['title'] = 'Staff Payslip';

		$month = date('m');
		$year = date('Y');

		$staff_details = $this->Common_model->getAllData('tblstaff', '', '', ['active' => 1]);		//CR by DEEP BASAK on March 26, 2024 //CR BY DEEP BASAK on May 07, 2024
		
		// CR by DEEP BASAK on March 26, 2024
		if(!empty($staff_details)){
			foreach($staff_details as $staff_key => $staff_val){
				$msg = $this->calculatePayslipForAll($month, $year, $staff_val);
			}
		}
		
		$this->load->view('attendances/payslip_manage', $data);
	}

	/**
	 * Monthly Payslip Calculate
	 * Added by DEEP BASAK on March 21, 2024
	 * CR by DEEP BASAK on March 26, 2024
	 */
	public function calculatePayslipForAll($month, $year, $staff_val = array()){
		$payslipDetails = $this->Common_model->getAllData('tbl_staff_payslip', '', '', ['is_active'=>'Y', 'staff_id' => $staff_val->staffid, 'month' => $month, 'year' => $year, 'is_generate' => 'Y']);

		if(empty($payslipDetails)){
			$sql = "SELECT * FROM `tbldirect_cost_training` WHERE cost_for = 'staff' AND staff_id = $staff_val->staffid AND created_at LIKE '$year-$month%'";
			$emp_cost = $this->Common_model->callSP($sql);
			$empCostVal = 0;
			if(!empty($emp_cost)){
				foreach($emp_cost as $key => $val){
					$empCostVal = $empCostVal + $val['total'] + $val['indirect_cost_total'] + $val['recruitment_cost'] + $val['onboarding_cost'] + $val['payroll_processing_cost'] + $val['hr_personnel_cost'] + $val['administrative_costs'] + $val['employee_training'] + $val['workshops'] + $val['courses'] + $val['certifications'] + $val['materials'] + $val['training_development_expenses_total'] + $val['training_program'] + $val['component_name'] + $val['wefw'] + $val['ewf_wef'] + $val['erger_fbgrnby'];
				}
			}
			$this->Common_model->UpdateDB('tbl_staff_payslip', ['is_active'=>'Y', 'staff_id' => $staff_val->staffid, 'month' => $month, 'year' => $year, 'is_generate' => 'N'], ['is_active' => 'N', 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => get_staff_user_id()]);
			
			$where = array(
				'is_active'=> 'Y', 
				'check_in_date >=' => $year . '-' . $month . '-01',
				'check_in_date <=' => $year . '-' . $month . '-31',
				'staff_id'		=> $staff_val->staffid,
				'today_hour<>'	=> ''
			);
			$totalHourByStaff = 0;
			$totalHourByMonth = 0;
			$staffMonthlyHour = 0;
			$basicSalary = 0;
			$allowance = 0;
			$da = 0;
			$hra = 0;
			$pTax = 0;
			$pf = 0;
			$grossSalary = 0;
			$netSalary = 0;
			$attendance_details = $this->Common_model->getAllData('tbl_staff_attendance', '', '', $where);
			if(!empty($attendance_details)){
				foreach($attendance_details as $att_key => $att_val){
					$totalHourByStaff = $totalHourByStaff + $att_val->today_hour;
					$totalHourByMonth = $att_val->total_hour;
				}
			}

			if($totalHourByMonth > $totalHourByStaff){
				$staffMonthlyHour = $totalHourByStaff;
			} else{
				$staffMonthlyHour = $totalHourByMonth;
			}

			$basicSalary = $staffMonthlyHour * $staff_val->hourly_rate;
			$allowance = (30/100) * $basicSalary;
			$da = (5/100) * $basicSalary;
			$hra = (20/100) * $basicSalary;
			
			$grossSalary = $basicSalary + $allowance + $da + $hra;

			if($grossSalary < 10000){
				$pTax = 0;
			} else if($grossSalary > 10000 && $grossSalary < 15000){
				$pTax = 110;
			} else if($grossSalary > 15000 && $grossSalary < 25000){
				$pTax = 130;
			} else if($grossSalary > 25000 && $grossSalary < 40000){
				$pTax = 150;
			} else{
				$pTax = 200;
			}
			$pf = (12/100) * $basicSalary;
			$netSalary = $grossSalary - ($pf - $pTax);

			$holidaySql = "SELECT * FROM tblday_off WHERE break_date LIKE '$year-$month%'";
			$holiDays = $this->Common_model->callSP($holidaySql);
			$holidayCount = 0;
			if(!empty($holiDays)){
				foreach($holiDays as $key){
					$holidayCount++;
				}
			}

			//Employee TADA
			// $staffExpSql = "SELECT * FROM tbl_staff_expenses WHERE is_active = 'Y' AND is_approve = 'Y' AND staff_id = " . $staff_val->staffid . " AND date LIKE '$year-$month%'";
			$staffExpSql = "SELECT * FROM tbl_staff_expenses WHERE is_active = 'Y' AND is_approve = 'Y' AND is_calculate = 'N' AND staff_id = " . $staff_val->staffid . " AND DATE_FORMAT(date, '%Y-%m') < DATE_FORMAT(CURRENT_DATE(), '%Y-%m')";
			$staffExp = $this->Common_model->callSP($staffExpSql);
			if(!empty($staffExp)){
				foreach($staffExp as $key => $val){
					$empCostVal = $empCostVal + $val['exp'];
				}
			}

			$tblData = array(
				'staff_id'		=> $staff_val->staffid,
				'month'			=> $month,
				'month_text'	=> date('F', strtotime("$year-$month-01")),
				'year'			=> $year,
				'days_working'	=> getWorkingDays($year, $month)-$holidayCount,
				'total_work_hour'=> $attendance_details[0]->total_hour,
				'basic_salary'	=> $basicSalary,
				'allowance'		=> $allowance,
				'da'			=> $da,
				'hra'			=> $hra,
				'p_tax'			=> $pTax,
				'pf'			=> $pf,
				'gross_salary'	=> $grossSalary,
				'net_salary'	=> $netSalary,
				'employee_exp'	=> $empCostVal,
				'is_generate'	=> 'N',
				'is_active'		=> 'Y',
				'created_at'	=> date('Y-m-d H:i:s'),
				'created_by'	=> get_staff_user_id()
			);

			$this->Common_model->add('tbl_staff_payslip', $tblData);
			return 'Payslip Calculation completed.';
		} else{
			return 'Payslip is already generated.';
		}

		
	}

	/**
	 * Monthly Payslip Calculate
	 * Added by DEEP BASAK on March 21, 2024
	 * CR by DEEP BASAK on March 26, 2024
	 */
	public function calculate_payslip(){
		$month = date('m', strtotime($_POST['month']));
		$year = date('Y', strtotime($_POST['month']));

		$staff_details = $this->Common_model->getAllData('tblstaff', '', '', ['active' => 1]);		//CR by DEEP BASAK on March 26, 2024 //Cr by DEEP BASAK on May 07, 2024

		if(!empty($staff_details)){
			foreach($staff_details as $staff_key => $staff_val){
				$msg = $this->calculatePayslipForAll($month, $year, $staff_val);
			}
		}

		$status = 'success';
		$title = 'Good Job!';
		if($msg == 'Payslip is already generated.'){
			$status = 'warning';
			$title = 'Warning';
		}

		# response
        $result = array('status'=> $status, 'message'=> $msg, 'title' => $title);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}

	/**
	 * Monthly Payslip List
	 * Added by DEEP BASAK on January 19, 2024
	 */
	public function month_payslip_list($date = ''){
		# customize filter
		$where = ' `is_active` = "Y" ';
		if(!is_admin()){
			$where .= ' AND staff_id = ' . get_staff_user_id() . ' ';
		}

		if(($date != '') && ($date != 'null')){
			$where .= ' AND `month_text` = "'.date("F", strtotime($date)).'" ';
		}

		// Skip number of Rows count  
		$start = $_POST["start"];

		// Paging Length 10,20  
		$length = $_POST["length"];

		// Search Value from (Search box)  
		$searchValue = trim($_POST["search"]["value"]);
		$searchwhere = '';
		if(!empty($searchValue)){
			$searchwhere .= ' AND month_text LIKE "%'.$searchValue.'%" 
				OR year LIKE "%'.$searchValue.'%" 
				OR basic_salary LIKE "%'.$searchValue.'%" 
				OR allowance LIKE "%'.$searchValue.'%"
				-- OR da LIKE "%'.$searchValue.'%"
				OR hra LIKE "%'.$searchValue.'%"
				OR p_tax LIKE "%'.$searchValue.'%"
				OR pf LIKE "%'.$searchValue.'%"
				OR gross_salary LIKE "%'.$searchValue.'%"
				OR net_salary LIKE "%'.$searchValue.'%"
				OR tblstaff.firstname LIKE "%'.$searchValue.'%"
				OR tblstaff.lastname LIKE "%'.$searchValue.'%"';
		}

		//Paging Size (10, 20, 50,100)  
		$pageSize = $length != null ? intval($length) : 0;
		$skip = $start != null ? intval($start) : 0;

		#region order by column
		//Cr by DEEP BASAK on March 19, 2024
		if(!empty($_POST['order'][0])){
			$colArr = array('', 'tblstaff.firstname', 'month_text', 'year', 'basic_salary', 'allowance', 'hra', 'p_tax', 'pf', 'gross_salary', 'net_salary');
			$columnIndex = $_POST['order'][0]['column'];
			$orderColName = $colArr[$columnIndex];
			$orderDir = $_POST['order'][0]['dir'];
			$orderQuery = ' ORDER BY '. $orderColName . ' ' . $orderDir . ' ';
		} else{
			$orderQuery = 'ORDER BY tblstaff.firstname DESC';
		}
		#endregion

		//Datatable view Query
		$query = 'SELECT
			tblstaff.firstname, tblstaff.lastname,
			tbl_staff_payslip.*
		FROM
			`tbl_staff_payslip` 
		LEFT JOIN tblstaff ON tblstaff.staffid = tbl_staff_payslip.staff_id
		WHERE
			'.$where.'
			'.$searchwhere.'
			' . $orderQuery . '
		LIMIT '.$pageSize.' OFFSET '.$skip.' ';

		// prx($query);

		//Total records query
		$query_total = 'SELECT
			tblstaff.firstname, tblstaff.lastname,
			tbl_staff_payslip.*
		FROM
			`tbl_staff_payslip` 
		LEFT JOIN tblstaff ON tblstaff.staffid = tbl_staff_payslip.staff_id
		WHERE
			'.$where.' 
		' . $orderQuery . ' ';

		$testdata = $this->Common_model->callSP($query);
		$testdata_total = $this->Common_model->callSP($query_total);
		$data = array();
		
		foreach ($testdata as $key => $fieldData){
			if($fieldData['is_generate'] == 'Y'){
				$paid = '<span class="badge badge-success">Paid</span>';
				$action = '<a href="javascript:" onclick="printPayslip(\'' . $fieldData['id'] . '\')"><i class="fa fa-print"></i></a>';
			} else{
				$action = '';
				$paid = '<span class="badge badge-primary">Not Paid</span>';
				if(is_admin()){
					$action = '<a href="javascript:" onclick="paidSalary(\'' . $fieldData['id'] . '\')"><i class="fa fa-check"></i></a>';
					$action .= '<a href="javascript:" onclick="printPayslip(\'' . $fieldData['id'] . '\')"><i class="fa fa-print"></i></a>';
				}
			}
			$data[] = array(
				$key + $skip + 1,
				$fieldData['firstname'] . ' ' . $fieldData['lastname'],
				$fieldData['month_text'],
				$fieldData['year'],
				$fieldData['basic_salary'],
				$fieldData['allowance'],
				// $fieldData['da'],
				$fieldData['hra'],
				$fieldData['p_tax'],
				$fieldData['pf'],
				$fieldData['gross_salary'],
				$fieldData['net_salary'],
				$fieldData['employee_exp'],
				$paid,
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
	 * Print Payslip
	 * Added by DEEP BASAK on 22 January, 2024
	 */
	public function print_payslip(){
		$data['id'] = $this->input->post('id');
		$join = array(
			array(
				'table'		=> 'tblstaff',
				'on'		=> 'tblstaff.staffid = tbl_staff_payslip.staff_id',
				'type'		=> 'left'
			)
		);
		$data['payslip_details'] = $this->Common_model->getAllData('tbl_staff_payslip', 'tbl_staff_payslip.*, tblstaff.firstname, tblstaff.lastname, tblstaff.staff_identifi', 1, ['id' => $this->input->post('id')], '', '', '', '', [], $join);
		// prx($this->db->last_query());
		$html = $this->load->view('attendances/components/payslip_print', $data, true);

		# response
        $result = array('status'=> 'success', 'message'=>'Display modal', 'html' => $html);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}

	/**
	 * Pay salary
	 * Added by DEEP BASAK on 02 Febuary, 2024
	 */
	public function pain_salary(){
		$payslip_details = $this->Common_model->getAllData('tbl_staff_payslip', 'staff_id', 1, ['id' => $this->input->post('id')]);
		
		$sql = "UPDATE `tbl_staff_expenses` SET `is_calculate` = 'Y', `updated_at` = '".date('Y-m-d H:i:s')."', `updated_by` = ".get_staff_user_id()."
		WHERE `is_active` = 'Y'
		AND `is_approve` = 'Y'
		AND `is_calculate` = 'N'
		AND `staff_id` = $payslip_details->staff_id
		AND DATE_FORMAT(date, '%Y-%m') < DATE_FORMAT(CURRENT_DATE(), '%Y-%m')";
		
		$this->db->query($sql);
		$this->Common_model->UpdateDB('tbl_staff_payslip', ['id' => $this->input->post('id')], ['is_generate' => 'Y', 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => get_staff_user_id()]);

		# response
        $result = array('status'=> 'success', 'message'=>'Salary Paid');
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}

	public function manage_expenses() {
		if (!has_permission('hrp_attendance', '', 'view') && !has_permission('hrp_attendance', '', 'view_own') && !is_admin()) {
			access_denied('hrp_attendance');
		}

		$this->load->model('staff_model');
		$this->load->model('departments_model');

		//load deparment by manager
		if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		$data['departments'] = $this->departments_model->get();
		$data['staffs'] = $staffs;
		$data['title'] = 'Staff Allowance';
		$this->load->view('attendances/expense_manage', $data);
	}

	/**
	 * Open expenses Modal
	 * Added by DEEP BASAK on 19 March, 2024
	 */
	public function open_expenses_modal(){
		$is_approve = '';
		if(post('id') != 0){
			$data['exp_details'] = $this->Common_model->getAllData('tbl_staff_expenses', '', 1, ['id' => post('id')]);
			$is_approve = $data['exp_details']->is_approve;
		}
		$data['staff_list'] = $this->Common_model->getAllData('tblstaff', '', '', []);
		$html = $this->load->view('attendances/components/add_expense_modal_body', $data, true);

		# response
        $result = array('status'=> 'success', 'message'=>'Display modal', 'html' => $html, 'is_approve' => $is_approve);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}

	/**
	 * Approv/Reject expenses
	 * Added by DEEP BASAK on 19 March, 2024
	 */
	public function approve_reject_expense(){
		// prx('t');
		if(post('type') == 'R'){
			$message = 'Expense Rejected!';
		} else{
			$message = 'Expense Approved!';
		}
		// prx('t');
		$this->Common_model->UpdateDB('tbl_staff_expenses', ['id' => post('id')], ['is_approve' => post('type'), 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => get_staff_user_id()]);

		# response
        $result = array('status'=> 'success', 'message'=>$message);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}



	/**
	 * Ecpense Rule Manage Add expense
	 * Added by DEEP BASAK on 26 March, 2024
	 */
	public function add_expense_table(){
		$data['count'] = $this->input->post('count');
		$html = $this->load->view('attendances/components/add_expense_modal_tbody', $data, true);

		# response
        $result = array('status'=> 'success', 'message'=>'Display modal', 'html' => $html);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}

	/**
	 * Ecpense Rule Manage Get rules
	 * Added by DEEP BASAK on 26 March, 2024
	 */
	public function get_expense_rate(){
		$rate = $this->Common_model->getAllData('tbl_staff_expense_rule', 'rate', 1, ['is_active' => 'Y', 'tada' => $this->input->post('tada'), 'type' => $this->input->post('type'), 'per' => $this->input->post('per')]);

		# response
        $result = array('status'=> 'success', 'message'=>'Rate display', 'rate' => $rate->rate);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}

	/**
	 * Monthly Payslip List
	 * Added by DEEP BASAK on March 19, 2024
	 */
	public function expense_manage_list($date = '', $staff = ''){
		# customize filter
		$where = ' tbl_staff_expenses.`is_active` = "Y" ';
		if(!is_admin()){
			$where .= ' AND tbl_staff_expenses.staff_id = ' . get_staff_user_id() . ' ';
		} else{
			if(($staff != '') && ($staff != 'null')){
				$where .= ' AND tbl_staff_expenses.staff_id = ' . $staff . ' ';
			}
			
		}

		if(($date != '') && ($date != 'null')){
			$where .= ' AND tbl_staff_expenses.`month` = "'.date("F", strtotime($date)).'" ';
		}

		// Skip number of Rows count  
		$start = $_POST["start"];

		// Paging Length 10,20  
		$length = $_POST["length"];

		// Search Value from (Search box)  
		$searchValue = trim($_POST["search"]["value"]);
		$searchwhere = '';
		if(!empty($searchValue)){
			$searchwhere .= ' AND tbl_staff_expenses.`month` LIKE "%'.$searchValue.'%" 
				OR tbl_staff_expenses.year LIKE "%'.$searchValue.'%" 
				OR tblstaff.firstname LIKE "%'.$searchValue.'%"
				OR tblstaff.lastname LIKE "%'.$searchValue.'%"
				OR tbl_staff_expenses.exp_type LIKE "%'.$searchValue.'%"
				OR tbl_staff_expenses.exp LIKE "%'.$searchValue.'%"
				OR tbl_staff_expenses.reason LIKE "%'.$searchValue.'%"
				OR tbl_staff_expenses.document LIKE "%'.$searchValue.'%"';
		}

		//Paging Size (10, 20, 50,100)  
		$pageSize = $length != null ? intval($length) : 0;
		$skip = $start != null ? intval($start) : 0;

		#region order by column
		//Cr by DEEP BASAK on March 19, 2024
		if(!empty($_POST['order'][0])){
			$colArr = array('', 'tblstaff.firstname', 'tbl_staff_expenses.month', 'tbl_staff_expenses.year', 'tbl_staff_expenses.exp_type', 'tbl_staff_expenses.exp', 'tbl_staff_expenses.reason', 'tbl_staff_expenses.document');
			$columnIndex = $_POST['order'][0]['column'];
			$orderColName = $colArr[$columnIndex];
			$orderDir = $_POST['order'][0]['dir'];
			$orderQuery = ' ORDER BY '. $orderColName . ' ' . $orderDir . ' ';
		} else{
			$orderQuery = 'ORDER BY tbl_staff_expenses.created_at DESC';
		}
		#endregion

		$select = 'tblstaff.firstname, tblstaff.lastname, tbl_staff_expenses.*';

		//Datatable view Query
		$query = 'SELECT
			'.$select.'
		FROM
			`tbl_staff_expenses` 
		LEFT JOIN tblstaff ON tblstaff.staffid = tbl_staff_expenses.staff_id
		WHERE
			'.$where.'
			'.$searchwhere.' 
			'. $orderQuery . ' 
		LIMIT '.$pageSize.' OFFSET '.$skip.' ';

		// prx($query);

		//Total records query
		$query_total = 'SELECT
			'.$select.'
		FROM
			`tbl_staff_expenses` 
		LEFT JOIN tblstaff ON tblstaff.staffid = tbl_staff_expenses.staff_id
		WHERE
			'.$where.'  ' . $orderQuery . ' ';

		$testdata = $this->Common_model->callSP($query);
		$testdata_total = $this->Common_model->callSP($query_total);
		$data = array();
		
		foreach ($testdata as $key => $fieldData){
			$document = "";
			if(!empty($fieldData['document'])){
				$document = '<a class="btn btn-sm btn-success" href="'.base_url($fieldData['document']).'" target="_blank" download>Download</a>';
			}
			$action = '<a href="javascript:void(0)" onclick="openExpensesModal('.$fieldData['id'].', 2)"><i class="fa fa-eye"></i></a>';

			$approveReject = '';
			if($fieldData['is_approve'] == 'Y'){
				$approveReject = '<span class="badge bg-success">Approved</span>';
			} else if($fieldData['is_approve'] == 'R'){
				$approveReject = '<span class="badge bg-danger">Rejected</span>';
			} else {
				$approveReject = '<span class="badge bg-warning">Not Approve</span>';
				if(!is_admin()){
					if($fieldData['staff_id'] == get_staff_user_id()){
						$action .= '&nbsp;<a href="javascript:void(0)" onclick="deleteExpense('.$fieldData['id'].', 2)"><i class="fa fa-trash text-danger"></i></a>';
					}
				} else{
					$action .= '&nbsp;<a href="javascript:void(0)" onclick="deleteExpense('.$fieldData['id'].', 2)"><i class="fa fa-trash text-danger"></i></a>';
				}
				// $action .= '&nbsp;<a href="javascript:void(0)" onclick="deleteExpense('.$fieldData['id'].', 2)"><i class="fa fa-trash text-danger"></i></a>';
			}

			$data[] = array(
				$key + $skip + 1,
				$fieldData['firstname'] . ' ' . $fieldData['lastname'],
				$fieldData['month'],
				$fieldData['year'],
				$fieldData['exp_type'],
				'₹'.$fieldData['exp'],
				$fieldData['reason'],
				$document,
				$approveReject,
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

	public function save_expenses(){

		//$this->load->library('form_validation');
		#validation
		$this->form_validation->set_rules('staff_id', 'Staff Name', 'trim|required');
		$this->form_validation->set_rules('exp_type', 'Expense Type', 'trim|required');
		$this->form_validation->set_rules('exp_amount', 'Expense Amount', 'trim|required');
		$this->form_validation->set_rules('date', 'Expense Date', 'trim|required');
		$this->form_validation->set_rules('exp_name', 'Expense name', 'trim|required');

		//Multiple validation
		if(!empty(post('tada'))){
			foreach(post('tada') as $key => $val){
				$this->form_validation->set_rules('tada['.$key.']', 'TADA ['.$key.']', 'trim|required');
				$this->form_validation->set_rules('type['.$key.']', 'Type ['.$key.']', 'trim|required');
				$this->form_validation->set_rules('amount['.$key.']', 'Amount ['.$key.']', 'trim|required');
				$this->form_validation->set_rules('per['.$key.']', 'Per ['.$key.']', 'trim|required');
				$this->form_validation->set_rules('distance['.$key.']', 'Distance ['.$key.']', 'trim|required');
				// $this->form_validation->set_rules('distance['.$key.']', 'Distance ['.$key.']', 'trim|required');
			}
		} else{
			$this->form_validation->set_rules('tada[0]', 'TADA [0]', 'trim|required');
			$this->form_validation->set_rules('type[0]', 'Type [0]', 'trim|required');
			$this->form_validation->set_rules('amount[0]', 'Amount [0]', 'trim|required');
			$this->form_validation->set_rules('per[0]', 'Per [0]', 'trim|required');
			$this->form_validation->set_rules('distance[0]', 'Distance [0]', 'trim|required');
		}

		if ($this->form_validation->run() == FALSE) {
            $msg = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

			//For file upload
			if($_FILES['document']['size'] > 0){
				$filetype = array('jpeg','jpg','png','PNG','JPEG','JPG', 'pdf');
				$document_url = multiUpload('document', 'uploads/staff/expenses', $filetype, 'single', '');
				$document_url = 'uploads/staff/expenses/' . $document_url;
			} else {
				$document_url = "";
			}

			//For multiple expense
			//Cr by DEEP BASAK on March 26, 2024
			$expense_details = array();
			if(!empty(post('tada'))){
				foreach(post('tada') as $key => $val){
					$expense_details[] = array(
						'tada'		=> post('tada')[$key],
						'type'		=> post('type')[$key],
						'per'		=> post('per')[$key],
						'amount'	=> post('amount')[$key],
						'distance'	=> post('distance')[$key],
						'reason'	=> post('reason')[$key]
					);
				}
			}
			

			$data = array(
				'staff_id'		=> $this->input->post('staff_id'),
				'exp_name'		=> post('exp_name'),
				'month'			=> date('F', strtotime($this->input->post('date'))),
				'year'			=> date('Y', strtotime($this->input->post('date'))),
				'exp_type'		=> $this->input->post('exp_type'),
				'exp'			=> $this->input->post('exp_amount'),
				'reason'		=> $this->input->post('reason'),
				'date'			=> $this->input->post('date'),
				'document'		=> $document_url,
				'expense_details'=> json_encode($expense_details),
				'is_active'		=> 'Y',
				'created_at'	=> date('Y-m-d H:i:s'),
				'created_by'	=> get_staff_user_id()
			);

			$save = $this->Common_model->add('tbl_staff_expenses', $data);

			if ($save) {
                $array = array('status' => 'success', 'error' => '', 'message' => 'Staff Expenses Added');
            } else {
                $array = array('status' => 'fail', 'error' => 'error_message', 'message' => '');
            }
		}

		# Response
		$array = array_merge($array,update_csrf_session());
        echo json_encode($array);
	}


	/**
	 * Ecpense Rule Manage
	 * Added by DEEP BASAK on 26 March, 2024
	 */
	public function expense_rule_manage(){
		if (!has_permission('hrp_attendance', '', 'view') && !has_permission('hrp_attendance', '', 'view_own') && !is_admin()) {
			access_denied('hrp_attendance');
		}

		$this->load->model('staff_model');
		$this->load->model('departments_model');

		//load deparment by manager
		if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		$data['departments'] = $this->departments_model->get();
		$data['staffs'] = $staffs;
		$data['title'] = 'Expense Rule';
		$this->load->view('attendances/expense_rule_manage', $data);
	}

	/**
	 * Ecpense Rule Manage List
	 * Added by DEEP BASAK on 26 March, 2024
	 */
	public function expense_rule_manage_list(){
		# customize filter
		$where = ' is_active = \'Y\' ';

		$tableName = 'tbl_staff_expense_rule';
		

		// Skip number of Rows count  
		$start = $_POST["start"];

		// Paging Length 10,20  
		$length = $_POST["length"];

		// Search Value from (Search box)  
		$searchValue = trim($_POST["search"]["value"]);
		$searchwhere = '';
		if(!empty($searchValue)){
			$searchwhere .= ' AND '.$tableName.'.tada LIKE "%'.$searchValue.'%" 
				OR '.$tableName.'.type LIKE "%'.$searchValue.'%" 
				OR '.$tableName.'.rate LIKE "%'.$searchValue.'%"
				OR '.$tableName.'.per LIKE "%'.$searchValue.'%"';
		}

		//Paging Size (10, 20, 50,100)  
		$pageSize = $length != null ? intval($length) : 0;
		$skip = $start != null ? intval($start) : 0;

		#region order by column
		//Cr by DEEP BASAK on March 19, 2024
		if(!empty($_POST['order'][0])){
			$colArr = array('', $tableName.'.tada', $tableName.'.type', $tableName.'.rate', $tableName.'.created_at', $tableName.'.updated_at', '');
			$columnIndex = $_POST['order'][0]['column'];
			$orderColName = $colArr[$columnIndex];
			$orderDir = $_POST['order'][0]['dir'];
			$orderQuery = ' ORDER BY '. $orderColName . ' ' . $orderDir . ' ';
		} else{
			$orderQuery = 'ORDER BY '.$tableName.'.created_at DESC';
		}
		#endregion

		$select = ''.$tableName.'.*';

		//Datatable view Query
		$query = 'SELECT
			'.$select.'
		FROM
			`'.$tableName.'` ';
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
			`'.$tableName.'` ';
		if($where != ' '):
			$query_total .=' WHERE
					'.$where.' ';
		endif; 
		$query_total .=' '.$orderQuery.' ';


		$testdata = $this->Common_model->callSP($query);
		$testdata_total = $this->Common_model->callSP($query_total);
		$data = array();
		
		foreach ($testdata as $key => $fieldData){
			$action = '<a href="javascript:void(0)" onclick="openExpensesRuleModal('.$fieldData['id'].', 2)"><i class="fa fa-eye"></i></a>';
			if(is_admin()){
				$action .= '&nbsp<a href="javascript:void(0)" class="text-success" onclick="openExpensesRuleModal('.$fieldData['id'].', 1)"><i class="fa fa-pencil"></i></a>';
				// $action .= '&nbsp<a href="javascript:void(0)" class="text-danger" onclick="deleteModal('.$fieldData['id'].')"><i class="fa fa-trash"></i></a>';
			}

			$data[] = array(
				$key + $skip + 1,
				$fieldData['tada'],
				$fieldData['type'],
				'₹'.$fieldData['rate'].'/'.$fieldData['per'],
				date('Y-m-d', strtotime($fieldData['created_at'])),
				!empty($fieldData['updated_at'])?date('Y-m-d', strtotime($fieldData['updated_at'])):'',
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
	 * Ecpense Rule Manage Modal
	 * Added by DEEP BASAK on 26 March, 2024
	 */
	public function open_expenses_rule_modal(){
		$id = $this->input->post('id');
		if($id != 0){
			$data['exp_rule'] = $this->Common_model->getAllData('tbl_staff_expense_rule', '', 1, ['id' => $id]);
		} else{
			$data['exp_rule'] = array();
		}
		
		$html = $this->load->view('attendances/components/add_expense_rule_modal_body', $data, true);

		# response
        $result = array('status'=> 'success', 'message'=>'Display modal', 'html' => $html);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}

	/**
	 * Ecpense Rule Manage Save
	 * Added by DEEP BASAK on 26 March, 2024
	 */
	public function save_expenses_rule(){
		//$this->load->library('form_validation');
		#validation
		$this->form_validation->set_rules('tada', 'TADA', 'trim|required');
		$this->form_validation->set_rules('type', 'Expense Type', 'trim|required');
		$this->form_validation->set_rules('exp_amount', 'Expense Amount', 'trim|required');
		$this->form_validation->set_rules('per', 'Per', 'trim|required');
		if ($this->form_validation->run() == FALSE) {
            $msg = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

			if($this->input->post('rule_id') == 0){
				//Add
				$data = array(
					'tada'			=> $this->input->post('tada'),
					'type'			=> $this->input->post('type'),
					'rate'			=> $this->input->post('exp_amount'),
					'per'			=> $this->input->post('per'),
					'is_active'		=> 'Y',
					'created_at'	=> date('Y-m-d H:i:s'),
					'created_by'	=> get_staff_user_id()
				);
	
				$save = $this->Common_model->add('tbl_staff_expense_rule', $data);
			} else{
				$data = array(
					'tada'			=> $this->input->post('tada'),
					'type'			=> $this->input->post('type'),
					'rate'			=> $this->input->post('exp_amount'),
					'per'			=> $this->input->post('per'),
					'is_active'		=> 'Y',
					'updated_at'	=> date('Y-m-d H:i:s'),
					'updated_by'	=> get_staff_user_id()
				);
	
				$save = $this->Common_model->UpdateDB('tbl_staff_expense_rule', ['id' => $this->input->post('rule_id')], $data);
			}

			if ($save) {
                $array = array('status' => 'success', 'error' => '', 'message' => 'Staff Expenses Rule Added');
            } else {
                $array = array('status' => 'fail', 'error' => 'error_message', 'message' => '');
            }
		}

		# Response
		$array = array_merge($array,update_csrf_session());
        echo json_encode($array);
	}

	/**
	 * Ecpense Rule Manage Delete
	 * Added by DEEP BASAK on 26 March, 2024
	 */
	public function delete_expense_rule(){
		$id = $this->input->post('id');
		$save = $this->Common_model->UpdateDB('tbl_staff_expense_rule', ['id' => $id], ['is_active' => 'N']);
		if ($save) {
			$array = array('status' => 'success', 'error' => '', 'message' => 'Staff Expenses Rule Deleted!');
		} else {
			$array = array('status' => 'fail', 'error' => 'error_message', 'message' => '');
		}

		# Response
		$array = array_merge($array,update_csrf_session());
        echo json_encode($array);
	}


	/**
	 * Task List View
	 * Added by DEEP BASAK on March 21, 2024
	 */
	public function manage_task(){
		if (!has_permission('hrp_attendance', '', 'view') && !has_permission('hrp_attendance', '', 'view_own') && !is_admin()) {
			access_denied('hrp_attendance');
		}

		$this->load->model('staff_model');
		$this->load->model('departments_model');

		//load deparment by manager
		if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		$data['departments'] = $this->departments_model->get();
		$data['staffs'] = $staffs;

		$data['title'] = 'Task List';
		
		$this->load->view('attendances/task_manage', $data);
	}

	/**
	 * Task List
	 * Added by DEEP BASAK on March 21, 2024
	 */
	public function task_list(){
		# customize filter
		if(is_admin()){
			$where = ' ';
		} else{
			$where = ' tbltask_assigned.staffid = '.get_staff_user_id();
		}
		

		// Skip number of Rows count  
		$start = $_POST["start"];

		// Paging Length 10,20  
		$length = $_POST["length"];

		// Search Value from (Search box)  
		$searchValue = trim($_POST["search"]["value"]);
		$searchwhere = '';
		if(!empty($searchValue)){
			$searchwhere .= ' AND tbltasks.name LIKE "%'.$searchValue.'%" 
				OR tbltasks.dateadded LIKE "%'.$searchValue.'%" 
				OR tblstaff.firstname LIKE "%'.$searchValue.'%"
				OR tblstaff.lastname LIKE "%'.$searchValue.'%"
				OR tbltasks.duedate LIKE "%'.$searchValue.'%"
				OR tbltasks.startdate LIKE "%'.$searchValue.'%"
				OR tbltasks.datefinished LIKE "%'.$searchValue.'%"
				OR tbltasks.priority LIKE "%'.$searchValue.'%" 
				OR tbltasks.rel_type LIKE "%'.$searchValue.'%"
				OR tbltasks.hourly_rate LIKE "%'.$searchValue.'%"';
		}

		//Paging Size (10, 20, 50,100)  
		$pageSize = $length != null ? intval($length) : 0;
		$skip = $start != null ? intval($start) : 0;

		#region order by column
		//Cr by DEEP BASAK on March 19, 2024
		if(!empty($_POST['order'][0])){
			$colArr = array('', 'tbltasks.name', 'tblstaff.firstname', 'tbltasks.dateadded', 'tbltasks.datefinished', 'tbltasks.startdate', 'tbltasks.duedate', 'tbltasks.priority', 'tbltasks.rel_type', 'tbltasks.hourly_rate', '');
			$columnIndex = $_POST['order'][0]['column'];
			$orderColName = $colArr[$columnIndex];
			$orderDir = $_POST['order'][0]['dir'];
			$orderQuery = ' ORDER BY '. $orderColName . ' ' . $orderDir . ' ';
		} else{
			$orderQuery = 'ORDER BY tbltasks.startdate DESC';
		}
		#endregion

		// $select = 'tblstaff.firstname, tblstaff.lastname, tbltasks.*';
		$select = 'tbltasks.*';

		//Datatable view Query
		$query = 'SELECT
			'.$select.'
		FROM
			`tbltasks` 
		LEFT JOIN tbltask_assigned ON tbltask_assigned.taskid = tbltasks.id 
		LEFT JOIN tblstaff ON tblstaff.staffid = tbltask_assigned.staffid ';
		if($where != ' '):
			$query .=' WHERE
				'.$where.' ';
		endif;
		$query .=' '.$searchwhere.' GROUP BY tbltasks.id 
			'. $orderQuery . ' 
		LIMIT '.$pageSize.' OFFSET '.$skip.' ';

		// prx($query);

		//Total records query
		$query_total = 'SELECT
			'.$select.'
		FROM
			`tbltasks` 
		LEFT JOIN tbltask_assigned ON tbltask_assigned.taskid = tbltasks.id 
		LEFT JOIN tblstaff ON tblstaff.staffid = tbltask_assigned.staffid ';
		if($where != ' '):
			$query_total .=' WHERE
					'.$where.' ';
		endif; 
		$query_total .=' GROUP BY tbltasks.id '.$orderQuery.' ';


		$testdata = $this->Common_model->callSP($query);
		$testdata_total = $this->Common_model->callSP($query_total);
		$data = array();
		
		foreach ($testdata as $key => $fieldData){
			$action = '<a href="javascript:void(0)" onclick="init_task_modal('.$fieldData['id'].')"><i class="fa fa-list-alt"></i></a>';
			// $assignName = '';
			// $join = array(
			// 	array(
			// 		'table'		=> 'tblstaff',
			// 		'on'		=> 'tblstaff.staffid = tbltask_assigned.staffid',
			// 		'type'		=> 'left'
			// 	)
			// );
			// $assignNameDetails = $this->Common_model->getAllData('tbltask_assigned', 'tblstaff.firstname,tblstaff.lastname,tblstaff.staffid', '', ['tbltask_assigned.taskid' => $fieldData['id']], '', '', '', '', [], $join);
			// $staffId = 0;
			// if(!empty($assignNameDetails)):
			// 	foreach($assignNameDetails as $k => $val):
			// 		if(is_admin()):
			// 			$assignName .= '<span class="badge bg-secondary">'.$val->firstname.' '.$val->lastname.'</span>';
			// 		else:
			// 			if(get_staff_user_id() == $val->staffid):
			// 				$staffId = $val->staffid;
			// 				$assignName = '<span class="badge bg-secondary">'.$val->firstname.' '.$val->lastname.'</span>';
			// 			endif;
			// 		endif;
			// 	endforeach;
			// endif;

			if(is_admin()):
				$data[] = array(
					$key + $skip + 1,
					$fieldData['name'],
					// $assignName,
					$fieldData['dateadded'],
					$fieldData['datefinished'],
					$fieldData['startdate'],
					$fieldData['duedate'],
					$fieldData['priority'],
					$fieldData['rel_type'],
					'₹'.$fieldData['hourly_rate'],
					$action
				);
			else:
				if(get_staff_user_id() == $staffId):
					$data[] = array(
						$key + $skip + 1,
						$fieldData['name'],
						$assignName,
						$fieldData['dateadded'],
						$fieldData['datefinished'],
						$fieldData['startdate'],
						$fieldData['duedate'],
						$fieldData['priority'],
						$fieldData['rel_type'],
						'₹'.$fieldData['hourly_rate'],
						$action
					);
				endif;
			endif;

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
	 * Resignation List View
	 * Added by DEEP BASAK on May 02, 2024
	 */
	public function manage_resignation(){
		if (!has_permission('hrp_attendance', '', 'view') && !has_permission('hrp_attendance', '', 'view_own') && !is_admin()) {
			access_denied('hrp_attendance');
		}

		$this->load->model('staff_model');
		$this->load->model('departments_model');

		//load deparment by manager
		if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		$data['departments'] = $this->departments_model->get();
		$data['staffs'] = $staffs;

		$data['title'] = 'Resignation Manage';
		
		$this->load->view('resignation/resignation_manage', $data);
	}

	/**
	 * Open Resignation Modal View
	 * Added by DEEP BASAK on May 02, 2024
	 * CR by DEEP BASAK on May 06, 2024
	 * CR by DEEP BASAK on May 07, 2024
	 */
	public function open_resignation_modal(){
		$disabled = true;		//CR by DEEP BASAK on May 07, 2024
		if(post('id') != 0){
			$select = 'tbl_staff_resignation.*, tblstaff.firstname, tblstaff.lastname, tbl_manager.firstname AS manager_firstname, tbl_manager.lastname manager_lastname';
			$where = array('tbl_staff_resignation.id' => post('id'));
			$join = array(
				array(
					'table'		=> 'tblstaff AS tbl_manager',
					'on'		=> 'tbl_staff_resignation.manager_id = tbl_manager.staffid',
					'type'		=> 'left'
				),
				array(
					'table'		=> 'tblstaff',
					'on'		=> 'tbl_staff_resignation.staff_id = tblstaff.staffid',
					'type'		=> 'left'
				)
			);
			$data['details'] = $this->Common_model->getAllData('tbl_staff_resignation', $select, 1, $where, '', '', '', '', [], $join);
			$joinStaff = array();
			$selectStaff = 'tblstaff.*';

			if($data['details']->is_approve == 'P'){		//CR by DEEP BASAK on May 07, 2024
				$disabled = false;
			}
		}else{
			$joinStaff = array(
				array(
					'table'		=> 'tbl_staff_resignation',
					'on'		=> 'tbl_staff_resignation.staff_id = tblstaff.staffid',
					'type'		=> 'left'
				)
			);
			$selectStaff = 'tbl_staff_resignation.manager_id, tbl_staff_resignation.is_approve, tblstaff.*';
		}
		
		$data['staff_list'] = $this->Common_model->getAllData('tblstaff', $selectStaff, '', ['tblstaff.active' => 1], '', '', '', '', [], $joinStaff);
		// prx($this->db->last_query());
		$html = $this->load->view('resignation/components/resignation_manage_modal_body', $data, true);

		# response
        $result = array('status'=> 'success', 'message'=>'Display modal', 'html' => $html, 'is_admin' => is_admin(), 'disabled' => $disabled);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}

	/**
	 * Calculation Resignation List View
	 * Added by DEEP BASAK on May 02, 2024
	 * Bug fixing by DEEP BASAK on May 06, 2024
	 */
	public function get_date(){
		$date = date('Y-m-d', strtotime('+'.intval(post('days')).' days'));

		if(empty(post('is_return'))){
			# response
			$result = array('status'=> 'success', 'message'=>'Display modal', 'date' => $date);
			$obj = (object) array_merge((array) $result, update_csrf_session());
			echo json_encode($obj);
		} else{
			return $date;
		}
		
	}

	/**
	 * Resignation List
	 * Added by DEEP BASAK on May 03, 2024
	 */
	public function resignation_list(){
		# customize filter
		if(is_admin()){
			$where = ' ';
		} else{
			$where = ' tbl_staff_resignation.staff_id = '.get_staff_user_id();
		}

		// Skip number of Rows count  
		$start = $_POST["start"];

		// Paging Length 10,20  
		$length = $_POST["length"];

		// Search Value from (Search box)  
		$searchValue = trim($_POST["search"]["value"]);
		$searchwhere = '';
		if(!empty($searchValue)){
			$searchwhere .= ' AND tblstaff.firstname LIKE "%'.$searchValue.'%"
				OR tblstaff.lastname LIKE "%'.$searchValue.'%"
				OR tbl_manager.firstname LIKE "%'.$searchValue.'%"
				OR tbl_manager.lastname LIKE "%'.$searchValue.'%"
				OR tbl_staff_resignation.notice_time LIKE "%'.$searchValue.'%"
				OR tbl_staff_resignation.notice_days LIKE "%'.$searchValue.'%"
				OR tbl_staff_resignation.is_approve LIKE "%'.$searchValue.'%"
				OR tbl_staff_resignation.created_at LIKE "%'.$searchValue.'%" ';
		}

		//Paging Size (10, 20, 50,100)  
		$pageSize = $length != null ? intval($length) : 0;
		$skip = $start != null ? intval($start) : 0;

		#region order by column
		//Cr by DEEP BASAK on March 19, 2024
		if(!empty($_POST['order'][0])){
			$colArr = array('', 'tblstaff.firstname', 'tbl_manager.firstname', 'tbl_staff_resignation.notice_days', 'tbl_staff_resignation.notice_time', 'tbl_staff_resignation.is_approve', 'tbl_staff_resignation.created_at', '');
			$columnIndex = $_POST['order'][0]['column'];
			$orderColName = $colArr[$columnIndex];
			$orderDir = $_POST['order'][0]['dir'];
			$orderQuery = ' ORDER BY '. $orderColName . ' ' . $orderDir . ' ';
		} else{
			$orderQuery = 'ORDER BY tbl_staff_resignation.created_at DESC';
		}
		#endregion

		// $select = 'tblstaff.firstname, tblstaff.lastname, tbltasks.*';
		$select = 'tbl_staff_resignation.*, tblstaff.firstname, tblstaff.lastname, tbl_manager.firstname AS manager_firstname, tbl_manager.lastname manager_lastname';

		//Datatable view Query
		$query = 'SELECT
			'.$select.'
		FROM
			`tbl_staff_resignation` 
		LEFT JOIN tblstaff AS tbl_manager ON tbl_staff_resignation.manager_id = tbl_manager.staffid 
		LEFT JOIN tblstaff ON tblstaff.staffid = tbl_staff_resignation.staff_id ';
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
			`tbl_staff_resignation` 
		LEFT JOIN tblstaff AS tbl_manager ON tbl_staff_resignation.manager_id = tbl_manager.staffid 
		LEFT JOIN tblstaff ON tblstaff.staffid = tbl_staff_resignation.staff_id ';
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
			if(($fieldData['is_approve'] == 'P') && ($fieldData['created_by'] == get_staff_user_id())){
				$action .= '<a href="javascript:void(0)" onclick="openModal('.$fieldData['id'].', 1)"><i class="fa fa-pencil"></i></a>';
			}

			if($fieldData['is_approve'] == 'P'){
				$status = '<span class="badge badge-secondary">Pending</span>';
			} elseif($fieldData['is_approve'] == 'A'){
				$status = '<span class="badge badge-success">Approve</span>';
			} elseif($fieldData['is_approve'] == 'R'){
				$status = '<span class="badge badge-danger">Reject</span>';
			}
			
			
			$data[] = array(
				$key + $skip + 1,
				$fieldData['firstname'] . ' ' . $fieldData['lastname'],
				$fieldData['manager_firstname'] . ' ' . $fieldData['manager_lastname'],
				intval($fieldData['notice_days']),
				date('F d, Y', strtotime($fieldData['notice_time'])),
				$status,
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
	 * Resignation Save
	 * Added by DEEP BASAK on May 03, 2024
	 */
	public function save_resignation(){
		// prx($_POST);
		//$this->load->library('form_validation');
		#validation
		$this->form_validation->set_rules('staff_id', 'Staff', 'trim|required');
		$this->form_validation->set_rules('manager_id', 'Manager', 'trim|required');
		$this->form_validation->set_rules('notice_days', 'Notice Days', 'trim|required');
		$this->form_validation->set_rules('notice_date', 'Notice Date', 'trim|required');
		$this->form_validation->set_rules('reason', 'Reason', 'trim|required');
		if ($this->form_validation->run() == FALSE) {
            $msg = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

			//Add
			if(empty(post('is_return'))){
				$data = array(
					'staff_id'		=> $this->input->post('staff_id'),
					'manager_id'	=> $this->input->post('manager_id'),
					'notice_time'	=> $this->input->post('notice_date'),
					'notice_days'	=> $this->input->post('notice_days'),
					'reason'		=> post('reason'),
					'is_approve'	=> 'P',
					'is_active'		=> 'Y',
					'created_at'	=> date('Y-m-d H:i:s'),
					'created_by'	=> get_staff_user_id()
				);
			} else{
				$data = array(
					'staff_id'		=> $this->input->post('staff_id'),
					'manager_id'	=> $this->input->post('manager_id'),
					'notice_time'	=> $this->input->post('notice_date'),
					'notice_days'	=> $this->input->post('notice_days'),
					'reason'		=> post('reason'),
					'is_approve'	=> 'A',
					'approved_at'	=> date('Y-m-d H:i:s'),
					'approved_by'	=> get_staff_user_id(),
					'is_active'		=> 'Y',
					'created_at'	=> date('Y-m-d H:i:s'),
					'created_by'	=> get_staff_user_id()
				);
			}

			$save = $this->Common_model->add('tbl_staff_resignation', $data);

			if ($save) {
                $array = array('status' => 'success', 'error' => '', 'message' => 'Staff Resignation Added');
            } else {
                $array = array('status' => 'fail', 'error' => 'error_message', 'message' => '');
            }
		}

		if(empty(post('is_return'))){
			# Response
			$array = array_merge($array,update_csrf_session());
			echo json_encode($array);
		} else{
			return '1';
		}
		
	}

	/**
	 * Resignation Approve/Reject
	 * Added by DEEP BASAK on May 07, 2024
	 */
	public function approve_reject(){
		$data = array(
			'is_approve'	=> post('type'),
			'approved_at'	=> date('Y-m-d H:i:s'),
			'approved_by'	=> get_staff_user_id(),
			'notice_time'	=> date('Y-m-d', strtotime('+'.intval(post('notice_days')).' days')),
			'notice_days'	=> post('notice_days'),
			'updated_by'	=> get_staff_user_id(),
			'updated_at'	=> date('Y-m-d H:i:s')
		);
		$this->Common_model->UpdateDB('tbl_staff_resignation', ['id' => post('id')], $data);

		if(post('type') == 'A'){
			$msg = 'Resignation Approved!';
		} else{
			$msg = 'Resignation Rejected!';
		}

		# response
        $result = array('status'=> 'success', 'message'=>$msg);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}


	//------------------- disciplinary Management Start -----------------

	/**
	 * Disciplinary Managment Show Cause
	 * Added by DEEP BASAK on June 11, 2024
	 */
	public function show_cause(){
		$_POST['days'] = '1';
		$_POST['is_return'] = true;
		$date = $this->get_date();
		$details = $this->Common_model->getAllData('tbl_staff_disciplinary', '', 1, ['id' => post('case_id')]);

		$_POST['staff_id'] = $details->staff_id;
		$_POST['manager_id'] = $details->manager;
		$_POST['notice_date'] = $date;
		$_POST['notice_days'] = $_POST['days'];
		$_POST['reason'] = 'Show Cause by orgination';
		$_POST['is_return'] = true;
		$check = $this->save_resignation();

		if($check == '1'){
			$status = 'success';
			$msg = 'Employee show cause successfully!';
		} else{
			$status = 'fail';
			$msg = 'Somthing went wrong!';
		}

		# response
        $result = array('status'=> $status, 'error'=> $msg, 'message'=>$msg);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}

	/**
	 * Disciplinary Managment
	 * Added by DEEP BASAK on May 16, 2024
	 */
	public function manage_disciplinary(){
		if (!has_permission('hrp_attendance', '', 'view') && !has_permission('hrp_attendance', '', 'view_own') && !is_admin()) {
			access_denied('hrp_attendance');
		}

		$this->load->model('staff_model');
		$this->load->model('departments_model');

		//load deparment by manager
		if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		$data['departments'] = $this->departments_model->get();
		$data['staffs'] = $staffs;

		$data['title'] = 'Disciplinary Manage';
		
		$this->load->view('disciplinary/disciplinary_manage', $data);
	}

	/**
	 * Disciplinary Managment
	 * Added by DEEP BASAK on May 16, 2024
	 */
	public function open_disciplinary_modal(){
		$manager = 0;
		if(post('id') != 0){
			$data['details'] = $this->Common_model->getAllData('tbl_staff_disciplinary', '', 1, ['id' => post('id')]);
			$manager = $data['details']->manager;
		}else{
			$data['case_id'] = getCaseId();
		}
		
		$data['staff_list'] = $this->Common_model->getAllData('tblstaff', '', '', ['tblstaff.active' => 1]);
		// prx($this->db->last_query());
		$html = $this->load->view('disciplinary/components/disciplinary_manage_model_body', $data, true);

		# response
        $result = array('status'=> 'success', 'message'=>'Display modal', 'html' => $html, 'manager' => $manager);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}

	/**
	 * Disciplinary Managment Approve Comments
	 * Added by DEEP BASAK on May 22, 2024
	 */
	public function approve_comment_complain(){
		$join = array(
			array(
				'table'		=> 'tbl_staff_disciplinary',
				'on'		=> 'tbl_staff_disciplinary_comments.complain_id = tbl_staff_disciplinary.id',
				'type'		=> 'left'
			),
			array(
				'table'		=> 'tblstaff',
				'on'		=> 'tbl_staff_disciplinary_comments.staff_id = tblstaff.staffid',
				'type'		=> 'left'
			)
		);
		$select = 'tbl_staff_disciplinary_comments.*, tblstaff.firstname, tblstaff.lastname';
		$data['details_comments'] = $this->Common_model->getAllData('tbl_staff_disciplinary_comments', $select, '', ['complain_id' => post('case_id'), 'tbl_staff_disciplinary_comments.is_active' => 'Y'], 'tbl_staff_disciplinary_comments.created_at DESC', '', '', '', [], $join);
		$data['details_complaint'] = $this->Common_model->getAllData('tbl_staff_disciplinary', '', 1, ['id' => post('case_id')]);
		$data['case_id'] = post('case_id');
		$data['staff_id'] = get_staff_user_id();
		$data['staff_list'] = $this->Common_model->getAllData('tblstaff', '', '', ['tblstaff.active' => 1]);
		$html = $this->load->view('disciplinary/components/disciplinary_manage_model_comments_body', $data, true);

		# response
        $result = array('status'=> 'success', 'message'=>'', 'html' => $html);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}

	public function complain_comment_save(){
		#validation
		$this->form_validation->set_rules('staff_id', 'Staff', 'trim|required');
		$this->form_validation->set_rules('case_id', 'Case', 'trim|required');
		$this->form_validation->set_rules('comment', 'Comment', 'trim|required');
		if ($this->form_validation->run() == FALSE) {
            $msg = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
			//Add
			$data = array(
				'parent_id'			=> 0,
				'complain_id'		=> post('case_id'),
				'staff_id'			=> post('staff_id'),
				'comments'			=> post('comment'),
				'is_active'			=> 'Y',
				'created_at'		=> date('Y-m-d H:i:s'),
				'created_by'		=> get_staff_user_id()
			);
			
			$save = $this->Common_model->add('tbl_staff_disciplinary_comments', $data);

			if ($save) {
                $array = array('status' => 'success', 'error' => '', 'message' => 'Complain Comment Added');
            } else {
                $array = array('status' => 'fail', 'error' => 'error_message', 'message' => '');
            }
		}

		# Response
		$array = array_merge($array,update_csrf_session());
        echo json_encode($array);
	}

	/**
	 * Disciplinary Managment Approve Comments
	 * Added by DEEP BASAK on May 22, 2024
	 */
	public function approve_reject_complain(){
		$message = 'Approved!';
		if(post('type') == 'R'){
			$message = 'Rejected!';
		}
		$status = 'success';
		if(!empty(post('judge'))){
			$msg = $message;
			$status = 'success';
			$error = "Please select Judge first";

			$data = array(
				'is_approved' 	=> post('type'),
				'judge'			=> post('judge'),
				'updated_at'	=> date('Y-m-d H:i:s'),
				'updated_by'	=> get_staff_user_id()
			);
			$this->Common_model->UpdateDB('tbl_staff_disciplinary', ['id' => post('case_id')], $data);
		} else{
			$msg = '';
			$status = 'fail';
			$error = "Please select Judge first";
		}
		

		# response
        $result = array('status'=> $status, 'message'=> $msg, 'error' => $error);
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}


	/**
	 * Disciplinary Managment List
	 * Added by DEEP BASAK on May 21, 2024
	 */
	public function complain_list(){
		# customize filter
		if(is_admin()){
			$where = ' ';
		} else{
			$where = ' tbl_staff_disciplinary.created_by = '.get_staff_user_id().' OR tbl_staff_disciplinary.staff_id = '.get_staff_user_id();
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
			$searchwhere .= $f.' tblstaff.firstname LIKE "%'.$searchValue.'%"
				OR tblstaff.lastname LIKE "%'.$searchValue.'%"
				OR tbl_manager.firstname LIKE "%'.$searchValue.'%"
				OR tbl_manager.lastname LIKE "%'.$searchValue.'%"
				OR tbl_staff_disciplinary.case_no LIKE "%'.$searchValue.'%"
				OR tbl_complain.lastname LIKE "%'.$searchValue.'%"
				OR tbl_complain.firstname LIKE "%'.$searchValue.'%" ';
		}

		//Paging Size (10, 20, 50,100)  
		$pageSize = $length != null ? intval($length) : 0;
		$skip = $start != null ? intval($start) : 0;

		#region order by column
		//Cr by DEEP BASAK on March 19, 2024
		if(!empty($_POST['order'][0])){
			$colArr = array(
				'', 
				'tbl_staff_disciplinary.case_no', 
				'tblstaff.firstname', 
				'complain_firstname', 
				'manager_firstname', 
				'tbl_staff_disciplinary.priority',
				'tbl_staff_disciplinary.is_approved', 
				'tbl_staff_disciplinary.created_at', 
				''
			);

			$columnIndex = $_POST['order'][0]['column'];
			$orderColName = $colArr[$columnIndex];
			$orderDir = $_POST['order'][0]['dir'];
			if($orderColName != ''){
				$orderQuery = ' ORDER BY '. $orderColName . ' ' . $orderDir . ' ';
			} else{
				$orderQuery = ' ORDER BY tbl_staff_disciplinary.created_at DESC ';
			}
			
		} else{
			$orderQuery = ' ORDER BY tbl_staff_disciplinary.created_at DESC ';
		}
		#endregion
		
		$select = ' tbl_staff_disciplinary.*, 
					tblstaff.firstname, 
					tblstaff.lastname, 
					tbl_manager.firstname AS manager_firstname, 
					tbl_manager.lastname AS manager_lastname,
					tbl_complain.firstname AS complain_firstname,
					tbl_complain.lastname AS complain_lastname ';

		//Datatable view Query
		$query = 'SELECT
			'.$select.'
		FROM
			`tbl_staff_disciplinary` 
		LEFT JOIN tblstaff AS tbl_manager ON tbl_staff_disciplinary.manager = tbl_manager.staffid 
		LEFT JOIN tblstaff AS tbl_complain ON tbl_staff_disciplinary.complain_by = tbl_complain.staffid 
		LEFT JOIN tblstaff ON tblstaff.staffid = tbl_staff_disciplinary.staff_id ';
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
			`tbl_staff_disciplinary` 
		LEFT JOIN tblstaff AS tbl_manager ON tbl_staff_disciplinary.manager = tbl_manager.staffid 
		LEFT JOIN tblstaff AS tbl_complain ON tbl_staff_disciplinary.complain_by = tbl_complain.staffid 
		LEFT JOIN tblstaff ON tblstaff.staffid = tbl_staff_disciplinary.staff_id ';
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
			if(($fieldData['is_approved'] == 'P') && ($fieldData['created_by'] == get_staff_user_id())){
				$action .= '<a href="javascript:void(0)" onclick="openModal('.$fieldData['id'].', 1)"><i class="fa fa-pencil"></i></a>';
			}

			if($fieldData['is_approved'] == 'P'){
				$status = '<span class="badge bg-secondary">Pending</span>';
			} elseif($fieldData['is_approved'] == 'A'){
				$status = '<span class="badge bg-success">Approve</span>';
			} elseif($fieldData['is_approved'] == 'R'){
				$status = '<span class="badge bg-danger">Reject</span>';
			} elseif($fieldData['is_approved'] == 'C'){
				$status = '<span class="badge bg-primary">Case Closed</span>';
			}

			if($fieldData['priority'] == 'L'){
				$priority = '<span class="badge bg-secondary">Low</span>';
			} elseif($fieldData['priority'] == 'M'){
				$priority = '<span class="badge bg-warning">Medium</span>';
			} elseif($fieldData['priority'] == 'H'){
				$priority = '<span class="badge bg-danger">High</span>';
			}
			
			
			
			$data[] = array(
				$key + $skip + 1,
				$fieldData['case_no'],
				$fieldData['firstname'] . ' ' . $fieldData['lastname'],
				$fieldData['complain_firstname'] . ' ' . $fieldData['complain_lastname'],
				$fieldData['manager_firstname'] . ' ' . $fieldData['manager_lastname'],
				$priority,
				$status,
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
	public function save_complain(){

		#validation
		$this->form_validation->set_rules('staff_id', 'Staff', 'trim|required');
		$this->form_validation->set_rules('manager_id', 'Manager', 'trim|required');
		$this->form_validation->set_rules('case_no', 'Case Number', 'trim|required');
		$this->form_validation->set_rules('priority', 'Priority', 'trim|required');
		$this->form_validation->set_rules('reason', 'Reason', 'trim|required');
		if ($this->form_validation->run() == FALSE) {
            $msg = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
			
			if(post('complain_id') == 0){
				
				//Add
				$data = array(
					'case_no'			=> getCaseId(),
					'staff_id'			=> post('staff_id'),
					'manager'			=> post('manager_id'),
					'complain_by'		=> get_staff_user_id(),
					'priority'			=> post('priority'),
					'complain_reason'	=> post('reason'),
					'is_approved'		=> 'P',
					'is_active'			=> 'Y',
					'created_at'		=> date('Y-m-d H:i:s'),
					'created_by'		=> get_staff_user_id()
				);
				
				$save = $this->Common_model->add('tbl_staff_disciplinary', $data);
				// prx($save);
			} else{
				//Edit
				$data = array(
					'case_no'			=> post('case_no'),
					'staff_id'			=> post('staff_id'),
					'manager'			=> post('manager_id'),
					'complain_by'		=> get_staff_user_id(),
					'priority'			=> post('priority'),
					'complain_reason'	=> post('reason'),
					'is_approved'		=> 'P',
					'is_active'			=> 'Y',
					'updated_at'		=> date('Y-m-d H:i:s'),
					'updated_by'		=> get_staff_user_id()
				);
				$save = $this->Common_model->UpdateDB('tbl_staff_disciplinary', ['id' => post('complain_id')], $data);
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

	/**
	 * Disciplinary Managment Final Judgment
	 * Added by DEEP BASAK on May 27, 2024
	 */
	public function final_judgement(){

		$data = array(
			'is_approved' 	=> 'C',
			'updated_at'	=> date('Y-m-d H:i:s'),
			'updated_by'	=> get_staff_user_id()
		);
		$this->Common_model->UpdateDB('tbl_staff_disciplinary', ['id' => post('case_id')], $data);

		# response
        $result = array('status'=> 'success', 'message'=> 'Final Judgment Complete', 'error' => '');
        $obj = (object) array_merge((array) $result, update_csrf_session());
        echo json_encode($obj);
	}

	//------------------- disciplinary Management End -------------------


	/**
	 * Project List View
	 * Added by DEEP BASAK on April 09, 2024
	 */
	public function manage_project(){
		if (!has_permission('hrp_attendance', '', 'view') && !has_permission('hrp_attendance', '', 'view_own') && !is_admin()) {
			access_denied('hrp_attendance');
		}

		$this->load->model('staff_model');
		$this->load->model('departments_model');

		//load deparment by manager
		if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		$data['departments'] = $this->departments_model->get();
		$data['staffs'] = $staffs;

		$data['title'] = 'Project List';
		
		$this->load->view('attendances/project_manage', $data);
	}

	/**
	 * Task List
	 * Added by DEEP BASAK on April 09, 2024
	 */
	public function project_list(){
		# customize filter
		$table_name = 'tblprojects';
		if(is_admin()){
			$where = ' ';
		} else{
			$where = ' tblproject_members.staff_id = '.get_staff_user_id();
		}
		

		// Skip number of Rows count  
		$start = $_POST["start"];

		// Paging Length 10,20  
		$length = $_POST["length"];

		// Search Value from (Search box)  
		$searchValue = trim($_POST["search"]["value"]);
		$searchwhere = '';
		if(!empty($searchValue)){
			$searchwhere .= ' AND '.$table_name.'.name LIKE "%'.$searchValue.'%" 
				OR tblclients.company LIKE "%'.$searchValue.'%" 
				OR '.$table_name.'.start_date LIKE "%'.$searchValue.'%"
				OR '.$table_name.'.deadline LIKE "%'.$searchValue.'%"
				OR '.$table_name.'.project_cost LIKE "%'.$searchValue.'%"
				OR '.$table_name.'.project_rate_per_hour LIKE "%'.$searchValue.'%"';
		}

		//Paging Size (10, 20, 50,100)  
		$pageSize = $length != null ? intval($length) : 0;
		$skip = $start != null ? intval($start) : 0;

		#region order by column
		//Cr by DEEP BASAK on March 19, 2024
		if(!empty($_POST['order'][0])){
			$colArr = array('', $table_name.'.name', 'tblclients.company', $table_name.'.start_date', $table_name.'.deadline', $table_name.'.project_cost', $table_name.'.project_rate_per_hour', '');
			$columnIndex = $_POST['order'][0]['column'];
			$orderColName = $colArr[$columnIndex];
			$orderDir = $_POST['order'][0]['dir'];
			$orderQuery = ' ORDER BY '. $orderColName . ' ' . $orderDir . ' ';
		} else{
			$orderQuery = 'ORDER BY '.$table_name.'.project_created DESC';
		}
		#endregion

		// $select = 'tblstaff.firstname, tblstaff.lastname, tbltasks.*';
		$select = $table_name.'.*, tblclients.company ';

		//Datatable view Query
		$query = 'SELECT
			'.$select.'
		FROM
			`'.$table_name.'` 
		LEFT JOIN tblclients ON tblclients.userid = '.$table_name.'.clientid 
		LEFT JOIN tblproject_members ON tblproject_members.project_id = '.$table_name.'.id ';
		if($where != ' '):
			$query .=' WHERE
				'.$where.' ';
		endif;
		$query .=' '.$searchwhere.' GROUP BY '.$table_name.'.id 
			'. $orderQuery . ' 
		LIMIT '.$pageSize.' OFFSET '.$skip.' ';

		// prx($query);

		//Total records query
		$query_total = 'SELECT
			'.$select.'
		FROM
			`'.$table_name.'` 
		LEFT JOIN tblclients ON tblclients.userid = '.$table_name.'.clientid 
		LEFT JOIN tblproject_members ON tblproject_members.project_id = '.$table_name.'.id ';
		if($where != ' '):
			$query_total .=' WHERE
					'.$where.' ';
		endif; 
		$query_total .=' GROUP BY '.$table_name.'.id '.$orderQuery.' ';


		$testdata = $this->Common_model->callSP($query);
		$testdata_total = $this->Common_model->callSP($query_total);
		$data = array();
		
		foreach ($testdata as $key => $fieldData){
			$project_url = base_url('admin/projects/view/').$fieldData['id'];
			$action = '<a href="'.$project_url.'"><i class="fa fa-eye"></i></a>';

			// if(is_admin()):
				$data[] = array(
					$key + $skip + 1,
					$fieldData['name'],
					$fieldData['company'],
					$fieldData['start_date'],
					$fieldData['deadline'],
					'₹'.$fieldData['project_cost'],
					'₹'.$fieldData['project_rate_per_hour'],
					$action
				);
			// else:
			// 	if(get_staff_user_id() == $staffId):
			// 		$data[] = array(
			// 			$key + $skip + 1,
			// 			$fieldData['name'],
			// 			$assignName,
			// 			$fieldData['dateadded'],
			// 			$fieldData['datefinished'],
			// 			$fieldData['startdate'],
			// 			$fieldData['duedate'],
			// 			$fieldData['priority'],
			// 			$fieldData['rel_type'],
			// 			'₹'.$fieldData['hourly_rate'],
			// 			$action
			// 		);
			// 	endif;
			// endif;

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
	 * add attendance
	 */
	public function add_attendance() {
		if (!has_permission('hrp_attendance', '', 'create') && !has_permission('hrp_attendance', '', 'edit') && !is_admin()) {
			access_denied('hrp_attendance');
		}

		if ($this->input->post()) {
			$data = $this->input->post();
			if (isset($data)) {

				if ($data['hrp_attendance_rel_type'] == 'update') {
					$success = $this->hr_payroll_model->add_update_attendance($data);
				} elseif ($data['hrp_attendance_rel_type'] == 'synchronization') {
					$success = $this->hr_payroll_model->synchronization_attendance($data);
				} else {
					$success = false;
				}

				if ($success) {
					set_alert('success', _l('hrp_updated_successfully'));
				} else {
					set_alert('warning', _l('hrp_updated_failed'));
				}
				redirect(admin_url('hr_payroll/manage_attendance'));
			}

		}
	}

	/**
	 * import xlsx employees
	 * @return [type]
	 */
	public function import_xlsx_employees() {
		if (!has_permission('hrp_employee', '', 'create') && !has_permission('hrp_employee', '', 'edit') && !is_admin()) {
			access_denied('hrp_employee');
		}

		$this->load->model('staff_model');
		$data_staff = $this->staff_model->get(get_staff_user_id());
		/*get language active*/
		if ($data_staff) {
			if ($data_staff->default_language != '') {
				$data['active_language'] = $data_staff->default_language;
			} else {
				$data['active_language'] = get_option('active_language');
			}

		} else {
			$data['active_language'] = get_option('active_language');
		}

		$this->load->view('hr_payroll/employees/import_employees', $data);
	}

	/**
	 * create employees sample file
	 * @return [type]
	 */
	public function create_employees_sample_file() {
		if (!has_permission('hrp_employee', '', 'create') && !has_permission('hrp_employee', '', 'edit') && !is_admin()) {
			access_denied('hrp_employee');
		}

		$this->load->model('staff_model');
		$this->load->model('departments_model');

		$month_employees = $this->input->post('month_employees');

		if (!class_exists('XLSXReader_fin')) {
			require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php';
		}
		require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php';

		$this->delete_error_file_day_before('1', HR_PAYROLL_CREATE_EMPLOYEES_SAMPLE);

		//get current month
		$rel_type = hrp_get_hr_profile_status();
		$month_filter = date('Y-m-d', strtotime($month_employees . '-01'));

		$employees_data = $this->hr_payroll_model->get_employees_data($month_filter, $rel_type);
		$employees_value = [];
		foreach ($employees_data as $key => $value) {
			$employees_value[$value['staff_id'] . '_' . $value['month']] = $value;
		}

		//get employee data for the first
		$format_employees_value = $this->hr_payroll_model->get_format_employees_data($rel_type);
		$staff_information_key = $format_employees_value['staff_information'];
		$probationary_key = $format_employees_value['probationary_key'];
		$primary_key = $format_employees_value['primary_key'];
		$staff_probationary_key = array_keys($format_employees_value['probationary']);
		$staff_formal_key = array_keys($format_employees_value['formal']);

		$header_key = array_merge($staff_information_key, $staff_probationary_key, $probationary_key, $staff_formal_key, $primary_key);

		//Writer file
		//create header value
		$writer_header = [];
		$widths = [];

		$writer_header[_l('month')] = 'string';
		$widths[] = 30;

		foreach ($format_employees_value['header'] as $header_value) {
			$writer_header[$header_value] = 'string';
			$widths[] = 30;
		}

		$writer = new XLSXWriter();

		$col_style1 = [0, 1, 2, 3, 4, 5, 7];
		$style1 = ['widths' => $widths, 'fill' => '#ff9800', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13];

		$writer->writeSheetHeader_v2('Sheet1', $writer_header, $col_options = ['widths' => $widths, 'fill' => '#03a9f46b', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13],
			$col_style1, $style1);

		//load deparment by manager
		if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		foreach ($staffs as $staff_key => $staff_value) {
			$data_object_kpi = [];

			/*check value from database*/
			$data_object_kpi['staff_id'] = $staff_value['staffid'];

			if ($rel_type == 'hr_records') {
				$data_object_kpi['employee_number'] = $staff_value['staff_identifi'];
			} else {
				$data_object_kpi['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_value['staffid'], 5);
			}

			$data_object_kpi['employee_name'] = $staff_value['firstname'] . ' ' . $staff_value['lastname'];

			$arr_department = $this->hr_payroll_model->get_staff_departments($staff_value['staffid'], true);

			$list_department = '';
			if (count($arr_department) > 0) {

				foreach ($arr_department as $key => $department) {
					$department_value = $this->departments_model->get($department);

					if ($department_value) {
						if (strlen($list_department) != 0) {
							$list_department .= ', ' . $department_value->name;
						} else {
							$list_department .= $department_value->name;
						}
					}
				}
			}

			$data_object_kpi['department_name'] = $list_department;

			if ($rel_type == 'hr_records') {
				$data_object_kpi['job_title'] = $staff_value['position_name'];
				$data_object_kpi['income_tax_number'] = $staff_value['Personal_tax_code'];
				$data_object_kpi['residential_address'] = $staff_value['resident'];
			} else {
				if (isset($employees_value[$staff_value['staffid'] . '_' . $month_filter])) {
					$data_object_kpi['job_title'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['job_title'];
					$data_object_kpi['income_tax_number'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['income_tax_number'];
					$data_object_kpi['residential_address'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['residential_address'];

				} else {
					$data_object_kpi['job_title'] = '';
					$data_object_kpi['income_tax_number'] = '';
					$data_object_kpi['residential_address'] = '';
				}
			}

			if (isset($employees_value[$staff_value['staffid'] . '_' . $month_filter])) {

				$data_object_kpi['income_rebate_code'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['income_rebate_code'];
				$data_object_kpi['income_tax_rate'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['income_tax_rate'];
				$data_object_kpi['bank_name'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['bank_name'];
				$data_object_kpi['account_number'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['account_number'];

				// array merge: staff information + earning list (probationary contract) + earning list (formal)
				if (isset($employees_value[$staff_value['staffid'] . '_' . $month_filter]['contract_value'])) {
					$data_object_kpi = array_merge($data_object_kpi, $employees_value[$staff_value['staffid'] . '_' . $month_filter]['contract_value']);
				}

				$data_object_kpi['probationary_effective'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['probationary_effective'];
				$data_object_kpi['probationary_expiration'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['probationary_expiration'];
				$data_object_kpi['primary_effective'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['primary_effective'];
				$data_object_kpi['primary_expiration'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['primary_expiration'];

				$data_object_kpi['id'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['id'];

			} else {
				$data_object_kpi['income_rebate_code'] = 'A';
				$data_object_kpi['income_tax_rate'] = 'A';
				$data_object_kpi['bank_name'] = '';
				$data_object_kpi['account_number'] = '';

				// array merge: staff information + earning list (probationary contract) + earning list (formal)
				$data_object_kpi = array_merge($data_object_kpi, $format_employees_value['probationary'], $format_employees_value['formal']);

				$data_object_kpi['probationary_effective'] = '';
				$data_object_kpi['probationary_expiration'] = '';
				$data_object_kpi['primary_effective'] = '';
				$data_object_kpi['primary_expiration'] = '';

				$data_object_kpi['id'] = 0;

			}

			$data_object_kpi['rel_type'] = $rel_type;

			$data_object = array_values($data_object_kpi);
			$temp = [];
			$temp['month'] = $month_filter;
			foreach ($header_key as $_key) {
				$temp[] = isset($data_object_kpi[$_key]) ? $data_object_kpi[$_key] : '';
			}

			if ($staff_key == 0) {
				$writer->writeSheetRow('Sheet1', array_merge([0 => 'month'], $header_key));
			}
			$writer->writeSheetRow('Sheet1', $temp);

		}

		$filename = 'employees_sample_file' . get_staff_user_id() . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
		$writer->writeToFile(str_replace($filename, HR_PAYROLL_CREATE_EMPLOYEES_SAMPLE . $filename, $filename));

		echo json_encode([
			'success' => true,
			'site_url' => site_url(),
			'staff_id' => get_staff_user_id(),
			'filename' => HR_PAYROLL_CREATE_EMPLOYEES_SAMPLE . $filename,
		]);

	}

	/**
	 * import employees excel
	 * @return [type]
	 */
	public function import_employees_excel() {
		if (!has_permission('hrp_employee', '', 'create') && !has_permission('hrp_employee', '', 'edit') && !is_admin()) {
			access_denied('hrp_employee');
		}

		if (!class_exists('XLSXReader_fin')) {
			require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php';
		}
		require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php';

		$filename = '';
		if ($this->input->post()) {
			if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {

				$this->delete_error_file_day_before();
				$rel_type = hrp_get_hr_profile_status();

				// Get the temp file path
				$tmpFilePath = $_FILES['file_csv']['tmp_name'];
				// Make sure we have a filepath
				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					$rows = [];
					$arr_insert = [];

					$tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

					if (!file_exists(TEMP_FOLDER)) {
						mkdir(TEMP_FOLDER, 0755);
					}

					if (!file_exists($tmpDir)) {
						mkdir($tmpDir, 0755);
					}

					// Setup our new file path
					$newFilePath = $tmpDir . $_FILES['file_csv']['name'];

					if (move_uploaded_file($tmpFilePath, $newFilePath)) {
						//Writer file
						$writer_header = array(
							_l('staffid') => 'string',
							_l('id') => 'string',
							_l('hr_code') => 'string',
							_l('staff_name') => 'string',
							_l('department') => 'string',
							_l('integration_actual_workday') => 'string',
							_l('integration_paid_leave') => 'string',
							_l('integration_unpaid_leave') => 'string',
							_l('standard_working_time_of_month') => 'string',
							_l('month') => 'string',
							_l('error') => 'string',
						);

						$writer = new XLSXWriter();
						$writer->writeSheetHeader('Sheet1', $writer_header, $col_options = ['widths' => [40, 40, 40, 50, 40, 40, 40, 40, 50, 50]]);

						//Reader file
						$xlsx = new XLSXReader_fin($newFilePath);
						$sheetNames = $xlsx->getSheetNames();
						$data = $xlsx->getSheetData($sheetNames[1]);
						$arr_header = [];

						$arr_header['staff_id'] = 0;
						$arr_header['id'] = 1;
						$arr_header['hr_code'] = 2;
						$arr_header['staff_name'] = 3;
						$arr_header['staff_departments'] = 4;
						$arr_header['actual_workday'] = 5;
						$arr_header['paid_leave'] = 6;
						$arr_header['unpaid_leave'] = 7;
						$arr_header['standard_workday'] = 8;
						$arr_header['month'] = 9;

						$total_rows = 0;
						$total_row_false = 0;

						$column_key = $data[1];

						for ($row = 2; $row < count($data); $row++) {

							$total_rows++;

							$rd = array();
							$flag = 0;
							$flag2 = 0;

							$string_error = '';

							$flag_staff_id = 0;

							if (($flag == 1) || $flag2 == 1) {
								//write error file
								$writer->writeSheetRow('Sheet1', [

								]);

								$total_row_false++;
							}

							if ($flag == 0 && $flag2 == 0) {

								$rd = array_combine($column_key, $data[$row]);
								unset($rd['employee_number']);
								unset($rd['employee_name']);
								unset($rd['department_name']);

								array_push($arr_insert, $rd);

							}

						}

						//insert batch
						if (count($arr_insert) > 0) {
							$this->hr_payroll_model->import_employees_data($arr_insert);
						}

						$total_rows = $total_rows;
						$total_row_success = isset($rows) ? count($rows) : 0;
						$dataerror = '';
						$message = 'Not enought rows for importing';

						if ($total_row_false != 0) {
							$filename = 'Import_attendance_error_' . get_staff_user_id() . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
							$writer->writeToFile(str_replace($filename, HR_PAYROLL_ERROR . $filename, $filename));
						}

					}
				}
			}
		}

		if (file_exists($newFilePath)) {
			@unlink($newFilePath);
		}

		echo json_encode([
			'message' => $message,
			'total_row_success' => $total_row_success,
			'total_row_false' => $total_row_false,
			'total_rows' => $total_rows,
			'site_url' => site_url(),
			'staff_id' => get_staff_user_id(),
			'filename' => HR_PAYROLL_ERROR . $filename,
		]);
	}

	/**
	 * attendance filter
	 * @return [type]
	 */
	public function attendance_filter() {
		$this->load->model('departments_model');
		$data = $this->input->post();

		$rel_type = hrp_get_timesheets_status();

		$months_filter = $data['month'];

		$querystring = ' active=1';
		$department = $data['department'];

		$staff = '';
		if (isset($data['staff'])) {
			$staff = $data['staff'];
		}
		$staff_querystring = '';
		$department_querystring = '';
		$role_querystring = '';

		if ($department != '') {
			$arrdepartment = $this->staff_model->get('', 'staffid in (select tblstaff_departments.staffid from tblstaff_departments where departmentid = ' . $department . ')');
			$temp = '';
			foreach ($arrdepartment as $value) {
				$temp = $temp . $value['staffid'] . ',';
			}
			$temp = rtrim($temp, ",");
			$department_querystring = 'FIND_IN_SET(staffid, "' . $temp . '")';
		}

		if ($staff != '') {
			$temp = '';
			$araylengh = count($staff);
			for ($i = 0; $i < $araylengh; $i++) {
				$temp = $temp . $staff[$i];
				if ($i != $araylengh - 1) {
					$temp = $temp . ',';
				}
			}
			$staff_querystring = 'FIND_IN_SET(staffid, "' . $temp . '")';
		}

		if (isset($data['role_attendance'])) {
			$temp = '';
			$araylengh = count($data['role_attendance']);
			for ($i = 0; $i < $araylengh; $i++) {
				$temp = $temp . $data['role_attendance'][$i];
				if ($i != $araylengh - 1) {
					$temp = $temp . ',';
				}
			}
			$role_querystring = 'FIND_IN_SET(role, "' . $temp . '")';
		}

		$arrQuery = array($staff_querystring, $department_querystring, $querystring, $role_querystring);

		$newquerystring = '';
		foreach ($arrQuery as $string) {
			if ($string != '') {
				$newquerystring = $newquerystring . $string . ' AND ';
			}
		}

		$newquerystring = rtrim($newquerystring, "AND ");
		if ($newquerystring == '') {
			$newquerystring = [];
		}

		$month_filter = date('Y-m-d', strtotime($data['month'] . '-01'));
		//get day header in month
		$days_header_in_month = $this->hr_payroll_model->get_day_header_in_month($month_filter, $rel_type);

		$attendances = $this->hr_payroll_model->get_hrp_attendance($month_filter);
		$attendances_value = [];
		foreach ($attendances as $key => $value) {
			$attendances_value[$value['staff_id'] . '_' . $value['month']] = $value;
		}

		// data return
		$data_object_kpi = [];
		$index_data_object = 0;
		if ($newquerystring != '') {

			//load staff
			if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
				//View own
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission($newquerystring));
			} else {
				//admin or view global
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object($newquerystring);
			}

			foreach ($staffs as $staff_key => $staff_value) {

				/*check value from database*/
				$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

				$staff_i = $this->hr_payroll_model->get_staff_info($staff_value['staffid']);
				if ($staff_i) {

					if (isset($staff_i->staff_identifi)) {
						$data_object_kpi[$staff_key]['hr_code'] = $staff_i->staff_identifi;
					} else {
						$data_object_kpi[$staff_key]['hr_code'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_i->staffid, 5);
					}

					$data_object_kpi[$staff_key]['staff_name'] = $staff_i->firstname . ' ' . $staff_i->lastname;

					$arr_department = $this->hr_payroll_model->get_staff_departments($staff_i->staffid, true);

					$list_department = '';
					if (count($arr_department) > 0) {

						foreach ($arr_department as $key => $department) {
							$department_value = $this->departments_model->get($department);

							if ($department_value) {
								if (strlen($list_department) != 0) {
									$list_department .= ', ' . $department_value->name;
								} else {
									$list_department .= $department_value->name;
								}
							}

						}
					}

					$data_object_kpi[$staff_key]['staff_departments'] = $list_department;

				} else {
					$data_object_kpi[$staff_key]['hr_code'] = '';
					$data_object_kpi[$staff_key]['staff_name'] = '';
					$data_object_kpi[$staff_key]['staff_departments'] = '';

				}

				if (isset($attendances_value[$staff_value['staffid'] . '_' . $month_filter])) {

					$data_object_kpi[$staff_key]['standard_workday'] = $attendances_value[$staff_value['staffid'] . '_' . $month_filter]['standard_workday'];
					$data_object_kpi[$staff_key]['actual_workday_probation'] = $attendances_value[$staff_value['staffid'] . '_' . $month_filter]['actual_workday_probation'];
					$data_object_kpi[$staff_key]['actual_workday'] = $attendances_value[$staff_value['staffid'] . '_' . $month_filter]['actual_workday'];
					$data_object_kpi[$staff_key]['paid_leave'] = $attendances_value[$staff_value['staffid'] . '_' . $month_filter]['paid_leave'];
					$data_object_kpi[$staff_key]['unpaid_leave'] = $attendances_value[$staff_value['staffid'] . '_' . $month_filter]['unpaid_leave'];
					$data_object_kpi[$staff_key]['id'] = $attendances_value[$staff_value['staffid'] . '_' . $month_filter]['id'];
					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $attendances_value[$staff_value['staffid'] . '_' . $month_filter]);

				} else {
					$data_object_kpi[$staff_key]['standard_workday'] = get_hr_payroll_option('standard_working_time');
					$data_object_kpi[$staff_key]['actual_workday_probation'] = 0;
					$data_object_kpi[$staff_key]['actual_workday'] = 0;
					$data_object_kpi[$staff_key]['paid_leave'] = 0;
					$data_object_kpi[$staff_key]['unpaid_leave'] = 0;
					$data_object_kpi[$staff_key]['id'] = 0;
					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $days_header_in_month['days_header']);

				}

				$data_object_kpi[$staff_key]['rel_type'] = $rel_type;
				$data_object_kpi[$staff_key]['month'] = $month_filter;

			}

		}

		//check is add new or update data
		if (count($attendances_value) > 0) {
			$button_name = _l('hrp_update');
		} else {
			$button_name = _l('submit');
		}

		echo json_encode([
			'data_object' => $data_object_kpi,
			'columns' => $days_header_in_month['columns_type'],
			'col_header' => $days_header_in_month['headers'],
			'button_name' => $button_name,
		]);
		die;
	}

	/**
	 * import xlsx attendance
	 * @return [type]
	 */
	public function import_xlsx_attendance() {
		$this->load->model('staff_model');
		$data_staff = $this->staff_model->get(get_staff_user_id());
		/*get language active*/
		if ($data_staff) {
			if ($data_staff->default_language != '') {
				$data['active_language'] = $data_staff->default_language;
			} else {
				$data['active_language'] = get_option('active_language');
			}

		} else {
			$data['active_language'] = get_option('active_language');
		}

		$this->load->view('hr_payroll/attendances/import_attendance', $data);
	}

	/**
	 * create attendance sample file
	 * @return [type]
	 */
	public function create_attendance_sample_file() {
		$this->load->model('staff_model');
		$this->load->model('departments_model');

		$month_attendance = $this->input->post('month_attendance');

		if (!class_exists('XLSXReader_fin')) {
			require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php';
		}
		require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php';

		$this->delete_error_file_day_before('1', HR_PAYROLL_CREATE_ATTENDANCE_SAMPLE);

		$rel_type = hrp_get_timesheets_status();
		//get attendance data
		$current_month = date('Y-m-d', strtotime($month_attendance . '-01'));
		//get day header in month
		$days_header_in_month = $this->hr_payroll_model->get_day_header_in_month($current_month, $rel_type);
		$header_key = array_merge($days_header_in_month['staff_key'], $days_header_in_month['days_key'], $days_header_in_month['attendance_key']);

		$attendances = $this->hr_payroll_model->get_hrp_attendance($current_month);
		$attendances_value = [];
		foreach ($attendances as $key => $value) {
			$attendances_value[$value['staff_id'] . '_' . $value['month']] = $value;
		}

		//load staff
		if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		//Writer file
		$writer_header = [];
		$widths = [];
		foreach ($days_header_in_month['headers'] as $value) {
			$writer_header[$value] = 'string';
			$widths[] = 30;
		}

		$writer = new XLSXWriter();

		$col_style1 = [0, 1, 2, 3, 4, 5, 6];
		$style1 = ['widths' => $widths, 'fill' => '#ff9800', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13];

		$writer->writeSheetHeader_v2('Sheet1', $writer_header, $col_options = ['widths' => $widths, 'fill' => '#03a9f46b', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13],
			$col_style1, $style1);

		$data_object_kpi = [];
		foreach ($staffs as $staff_key => $staff_value) {
			$data_object_kpi = [];
			$staffid = 0;
			$hr_code = '';
			$id = 0;
			$staff_name = '';
			$staff_departments = '';
			$actual_workday_probation = 0;
			$actual_workday = 0;
			$paid_leave = 0;
			$unpaid_leave = 0;
			$standard_workday = 0;

			/*check value from database*/
			$staffid = $staff_value['staffid'];

			/*check value from database*/
			$staff_i = $this->hr_payroll_model->get_staff_info($staff_value['staffid']);
			if ($staff_i) {

				if (isset($staff_i->staff_identifi)) {
					$data_object_kpi['hr_code'] = $staff_i->staff_identifi;
				} else {
					$data_object_kpi['hr_code'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_i->staffid, 5);
				}

				$data_object_kpi['staff_name'] = $staff_i->firstname . ' ' . $staff_i->lastname;

				$arr_department = $this->hr_payroll_model->get_staff_departments($staff_i->staffid, true);

				$list_department = '';
				if (count($arr_department) > 0) {

					foreach ($arr_department as $key => $department) {
						$department_value = $this->departments_model->get($department);

						if ($department_value) {
							if (strlen($list_department) != 0) {
								$list_department .= ', ' . $department_value->name;
							} else {
								$list_department .= $department_value->name;
							}
						}

					}
				}

				$data_object_kpi['staff_departments'] = $list_department;

			} else {
				$data_object_kpi['hr_code'] = '';
				$data_object_kpi['staff_name'] = '';
				$data_object_kpi['staff_departments'] = '';

			}

			if (isset($attendances_value[$staff_value['staffid'] . '_' . $current_month])) {

				$data_object_kpi['standard_workday'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['standard_workday'];
				$data_object_kpi['actual_workday_probation'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['actual_workday_probation'];
				$data_object_kpi['actual_workday'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['actual_workday'];
				$data_object_kpi['paid_leave'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['paid_leave'];
				$data_object_kpi['unpaid_leave'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['unpaid_leave'];
				$data_object_kpi['id'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['id'];

				$data_object_kpi = array_merge($data_object_kpi, $attendances_value[$staff_value['staffid'] . '_' . $current_month]);

			} else {
				$data_object_kpi['standard_workday'] = get_hr_payroll_option('standard_working_time');
				$data_object_kpi['actual_workday_probation'] = 0;
				$data_object_kpi['actual_workday'] = 0;
				$data_object_kpi['paid_leave'] = 0;
				$data_object_kpi['unpaid_leave'] = 0;
				$data_object_kpi['id'] = 0;
				$data_object_kpi = array_merge($data_object_kpi, $days_header_in_month['days_header']);

			}
			$data_object_kpi['rel_type'] = $rel_type;
			$data_object_kpi['month'] = $current_month;
			$data_object_kpi['staff_id'] = $staff_value['staffid'];

			if ($staff_key == 0) {
				$writer->writeSheetRow('Sheet1', $header_key);
			}

			$get_values_for_keys = $this->get_values_for_keys($data_object_kpi, $header_key);
			$writer->writeSheetRow('Sheet1', $get_values_for_keys);

		}

		$filename = 'attendance_sample_file' . get_staff_user_id() . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
		$writer->writeToFile(str_replace($filename, HR_PAYROLL_CREATE_ATTENDANCE_SAMPLE . $filename, $filename));

		echo json_encode([
			'success' => true,
			'site_url' => site_url(),
			'staff_id' => get_staff_user_id(),
			'filename' => HR_PAYROLL_CREATE_ATTENDANCE_SAMPLE . $filename,
		]);

	}

	/**
	 * get values for keys
	 * @param  [type] $mapping
	 * @param  [type] $keys
	 * @return [type]
	 */
	function get_values_for_keys($mapping, $keys) {
		foreach ($keys as $key) {
			$output_arr[] = $mapping[$key];
		}
		return $output_arr;
	}

	/**
	 * import attendance excel
	 * @return [type]
	 */
	public function import_attendance_excel() {
		if (!has_permission('hrp_employee', '', 'create') && !has_permission('hrp_employee', '', 'edit') && !is_admin()) {
			access_denied('hrp_employee');
		}

		if (!class_exists('XLSXReader_fin')) {
			require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php';
		}
		require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php';

		$filename = '';
		if ($this->input->post()) {
			if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {

				$this->delete_error_file_day_before();
				$rel_type = hrp_get_timesheets_status();

				// Get the temp file path
				$tmpFilePath = $_FILES['file_csv']['tmp_name'];
				// Make sure we have a filepath
				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					$rows = [];
					$arr_insert = [];

					$tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

					if (!file_exists(TEMP_FOLDER)) {
						mkdir(TEMP_FOLDER, 0755);
					}

					if (!file_exists($tmpDir)) {
						mkdir($tmpDir, 0755);
					}

					// Setup our new file path
					$newFilePath = $tmpDir . $_FILES['file_csv']['name'];

					if (move_uploaded_file($tmpFilePath, $newFilePath)) {
						//Writer file
						$writer_header = array(
							_l('staffid') => 'string',
							_l('id') => 'string',
							_l('hr_code') => 'string',
							_l('staff_name') => 'string',
							_l('department') => 'string',
							_l('integration_actual_workday') => 'string',
							_l('integration_paid_leave') => 'string',
							_l('integration_unpaid_leave') => 'string',
							_l('standard_working_time_of_month') => 'string',
							_l('month') => 'string',
							_l('error') => 'string',
						);

						$writer = new XLSXWriter();
						$writer->writeSheetHeader('Sheet1', $writer_header, $col_options = ['widths' => [40, 40, 40, 50, 40, 40, 40, 40, 50, 50]]);

						//Reader file
						$xlsx = new XLSXReader_fin($newFilePath);
						$sheetNames = $xlsx->getSheetNames();
						$data = $xlsx->getSheetData($sheetNames[1]);

						$arr_header = [];

						$arr_header['staff_id'] = 0;
						$arr_header['id'] = 1;
						$arr_header['hr_code'] = 2;
						$arr_header['staff_name'] = 3;
						$arr_header['staff_departments'] = 4;
						$arr_header['actual_workday'] = 5;
						$arr_header['paid_leave'] = 6;
						$arr_header['unpaid_leave'] = 7;
						$arr_header['standard_workday'] = 8;
						$arr_header['month'] = 9;

						$total_rows = 0;
						$total_row_false = 0;

						$column_key = $data[1];
						for ($row = 1; $row < count($data); $row++) {

							$total_rows++;

							$rd = array();
							$flag = 0;
							$flag2 = 0;

							$string_error = '';
							$flag_position_group;
							$flag_department = null;

							$flag_staff_id = 0;

							if (($flag == 1) || $flag2 == 1) {
								//write error file
								$writer->writeSheetRow('Sheet1', [
									$value_staffid,
									$value_dependent_name,
									$value_relationship,
									$value_dependent_bir,
									$value_dependent_iden,
									$value_reason,
									$value_start_month,
									$value_end_month,
									$value_status,
									$string_error,
								]);

								$total_row_false++;
							}

							if ($flag == 0 && $flag2 == 0) {

								$rd = array_combine($column_key, $data[$row]);
								unset($rd['employee_number']);
								unset($rd['employee_name']);
								unset($rd['department_name']);
								unset($rd['hr_code']);
								unset($rd['staff_name']);
								unset($rd['staff_departments']);

								$rows[] = $rd;
								array_push($arr_insert, $rd);

							}

						}

						//insert batch
						if (count($arr_insert) > 0) {
							$this->hr_payroll_model->import_attendance_data($arr_insert);
						}

						$total_rows = $total_rows;
						$total_row_success = isset($rows) ? count($rows) : 0;
						$dataerror = '';
						$message = 'Not enought rows for importing';

						if ($total_row_false != 0) {
							$filename = 'Import_attendance_error_' . get_staff_user_id() . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
							$writer->writeToFile(str_replace($filename, HR_PAYROLL_ERROR . $filename, $filename));
						}

					}
				}
			}
		}

		if (file_exists($newFilePath)) {
			@unlink($newFilePath);
		}

		echo json_encode([
			'message' => $message,
			'total_row_success' => $total_row_success,
			'total_row_false' => $total_row_false,
			'total_rows' => $total_rows,
			'site_url' => site_url(),
			'staff_id' => get_staff_user_id(),
			'filename' => HR_PAYROLL_ERROR . $filename,
		]);
	}

	/**
	 * attendance calculation
	 * @return [type]
	 */
	public function attendance_calculation() {
		if (!has_permission('hrp_employee', '', 'edit') && !is_admin()) {
			access_denied('hrp_employee');
		}

		$data = $this->input->post();
		$this->hr_payroll_model->attendance_calculation($data);
		$message = _l('updated_successfully');
		echo json_encode([
			'message' => $message,
		]);
	}

	/**
	 * manage deductions
	 * @return [type]
	 */
	public function manage_deductions() {
		if (!has_permission('hrp_deduction', '', 'view') && !has_permission('hrp_deduction', '', 'view_own') && !is_admin()) {
			access_denied('hrp_deduction');
		}

		$this->load->model('staff_model');
		$this->load->model('departments_model');

		$rel_type = hrp_get_hr_profile_status();

		//get current month
		$current_month = date('Y-m-d', strtotime(date('Y-m') . '-01'));
		$deductions_data = $this->hr_payroll_model->get_deductions_data($current_month);
		$deductions_value = [];
		if (count($deductions_data) > 0) {
			foreach ($deductions_data as $key => $value) {
				$deductions_value[$value['staff_id'] . '_' . $value['month']] = $value;
			}
		}

		//get deduction data for the first
		$format_deduction_value = $this->hr_payroll_model->get_format_deduction_data();

		//load staff
		if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		$data_object_kpi = [];
		foreach ($staffs as $staff_key => $staff_value) {
			/*check value from database*/
			$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

			if ($rel_type == 'hr_records') {
				$data_object_kpi[$staff_key]['employee_number'] = $staff_value['staff_identifi'];
			} else {
				$data_object_kpi[$staff_key]['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_value['staffid'], 5);
			}

			$data_object_kpi[$staff_key]['employee_name'] = $staff_value['firstname'] . ' ' . $staff_value['lastname'];

			$arr_department = $this->hr_payroll_model->get_staff_departments($staff_value['staffid'], true);

			$list_department = '';
			if (count($arr_department) > 0) {

				foreach ($arr_department as $key => $department) {
					$department_value = $this->departments_model->get($department);

					if ($department_value) {
						if (strlen($list_department) != 0) {
							$list_department .= ', ' . $department_value->name;
						} else {
							$list_department .= $department_value->name;
						}
					}
				}
			}

			$data_object_kpi[$staff_key]['department_name'] = $list_department;

			if (isset($deductions_value[$staff_value['staffid'] . '_' . $current_month])) {

				// array merge: staff information + earning list (probationary contract) + earning list (formal)
				if (isset($deductions_value[$staff_value['staffid'] . '_' . $current_month]['deduction_value'])) {

					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $deductions_value[$staff_value['staffid'] . '_' . $current_month]['deduction_value']);
				} else {
					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_deduction_value['array_deduction']);
				}

				$data_object_kpi[$staff_key]['id'] = $deductions_value[$staff_value['staffid'] . '_' . $current_month]['id'];

			} else {

				// array merge: staff information + earning list (probationary contract) + earning list (formal)
				$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_deduction_value['array_deduction']);

				$data_object_kpi[$staff_key]['id'] = 0;

			}
			$data_object_kpi[$staff_key]['month'] = $current_month;

		}

		//check is add new or update data
		if (count($deductions_value) > 0) {
			$data['button_name'] = _l('hrp_update');
		} else {
			$data['button_name'] = _l('submit');
		}

		$data['departments'] = $this->departments_model->get();
		$data['roles'] = $this->roles_model->get();
		$data['staffs'] = $staffs;

		$data['body_value'] = json_encode($data_object_kpi);
		$data['columns'] = json_encode($format_deduction_value['column_format']);
		$data['col_header'] = json_encode($format_deduction_value['header']);

		$this->load->view('deductions/deductions_manage', $data);
	}

	/**
	 * add manage deductions
	 */
	public function add_manage_deductions() {
		if (!has_permission('hrp_deduction', '', 'create') && !has_permission('hrp_deduction', '', 'edit') && !is_admin()) {
			access_denied('hrp_deduction');
		}

		if ($this->input->post()) {
			$data = $this->input->post();

			if ($data['hrp_deductions_rel_type'] == 'update') {
				// update data
				$success = $this->hr_payroll_model->deductions_update($data);
			} else {
				$success = false;
			}

			if ($success) {
				set_alert('success', _l('updated_successfully'));
			} else {
				set_alert('warning', _l('hrp_updated_failed'));
			}

			redirect(admin_url('hr_payroll/manage_deductions'));
		}

	}

	/**
	 * deductions filter
	 * @return [type]
	 */
	public function deductions_filter() {
		$this->load->model('departments_model');
		$data = $this->input->post();

		$rel_type = hrp_get_hr_profile_status();

		$months_filter = $data['month'];
		$department = $data['department'];
		$staff = '';
		if (isset($data['staff'])) {
			$staff = $data['staff'];
		}
		$role_attendance = '';
		if (isset($data['role_attendance'])) {
			$role_attendance = $data['role_attendance'];
		}

		$newquerystring = $this->render_filter_query($months_filter, $staff, $department, $role_attendance);
		//get current month
		$month_filter = date('Y-m-d', strtotime($data['month'] . '-01'));
		$deductions_data = $this->hr_payroll_model->get_deductions_data($month_filter);
		$deductions_value = [];
		foreach ($deductions_data as $key => $value) {
			$deductions_value[$value['staff_id'] . '_' . $value['month']] = $value;
		}

		//get employee data for the first
		$format_deduction_value = $this->hr_payroll_model->get_format_deduction_data();

		// data return
		$data_object_kpi = [];
		$index_data_object = 0;
		if ($newquerystring != '') {

			//load staff
			if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
				//View own
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission($newquerystring));
			} else {
				//admin or view global
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object($newquerystring);
			}

			$data_object_kpi = [];

			foreach ($staffs as $staff_key => $staff_value) {
				/*check value from database*/
				$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

				if ($rel_type == 'hr_records') {
					$data_object_kpi[$staff_key]['employee_number'] = $staff_value['staff_identifi'];
				} else {
					$data_object_kpi[$staff_key]['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_value['staffid'], 5);
				}

				$data_object_kpi[$staff_key]['employee_name'] = $staff_value['firstname'] . ' ' . $staff_value['lastname'];

				$arr_department = $this->hr_payroll_model->get_staff_departments($staff_value['staffid'], true);

				$list_department = '';
				if (count($arr_department) > 0) {

					foreach ($arr_department as $key => $department) {
						$department_value = $this->departments_model->get($department);

						if ($department_value) {
							if (strlen($list_department) != 0) {
								$list_department .= ', ' . $department_value->name;
							} else {
								$list_department .= $department_value->name;
							}
						}
					}
				}

				$data_object_kpi[$staff_key]['department_name'] = $list_department;

				if (isset($deductions_value[$staff_value['staffid'] . '_' . $month_filter])) {

					// array merge: staff information + earning list (probationary contract) + earning list (formal)
					if (isset($deductions_value[$staff_value['staffid'] . '_' . $month_filter]['deduction_value'])) {

						$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $deductions_value[$staff_value['staffid'] . '_' . $month_filter]['deduction_value']);
					} else {
						$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_deduction_value['array_deduction']);
					}

					$data_object_kpi[$staff_key]['id'] = $deductions_value[$staff_value['staffid'] . '_' . $month_filter]['id'];

				} else {

					// array merge: staff information + earning list (probationary contract) + earning list (formal)
					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_deduction_value['array_deduction']);

					$data_object_kpi[$staff_key]['id'] = 0;

				}
				$data_object_kpi[$staff_key]['month'] = $month_filter;
			}

		}

		//check is add new or update data
		if (count($deductions_value) > 0) {
			$button_name = _l('hrp_update');
		} else {
			$button_name = _l('submit');
		}

		echo json_encode([
			'data_object' => $data_object_kpi,
			'button_name' => $button_name,
		]);
		die;
	}

	/**
	 * manage commissions
	 * @return [type]
	 */
	public function manage_commissions() {
		if (!has_permission('hrp_commission', '', 'view') && !has_permission('hrp_commission', '', 'view_own') && !is_admin()) {
			access_denied('hrp_commission');
		}

		$this->load->model('staff_model');
		$this->load->model('departments_model');

		$rel_type = hrp_get_hr_profile_status();
		$commission_type = hrp_get_commission_status();

		//get current month
		$current_month = date('Y-m-d', strtotime(date('Y-m') . '-01'));
		$commissions_data = $this->hr_payroll_model->get_commissions_data($current_month);
		$commissions_value = [];
		if (count($commissions_data) > 0) {
			foreach ($commissions_data as $key => $value) {
				$commissions_value[$value['staff_id'] . '_' . $value['month']] = $value;
			}
		}

		//get deduction data for the first
		$format_commission_value = $this->hr_payroll_model->get_format_commission_data();

		//load staff
		if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		$data_object_kpi = [];
		foreach ($staffs as $staff_key => $staff_value) {
			/*check value from database*/
			$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

			if ($rel_type == 'hr_records') {
				$data_object_kpi[$staff_key]['employee_number'] = $staff_value['staff_identifi'];
			} else {
				$data_object_kpi[$staff_key]['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_value['staffid'], 5);
			}

			$data_object_kpi[$staff_key]['employee_name'] = $staff_value['firstname'] . ' ' . $staff_value['lastname'];

			$arr_department = $this->hr_payroll_model->get_staff_departments($staff_value['staffid'], true);

			$list_department = '';
			if (count($arr_department) > 0) {

				foreach ($arr_department as $key => $department) {
					$department_value = $this->departments_model->get($department);

					if ($department_value) {
						if (strlen($list_department) != 0) {
							$list_department .= ', ' . $department_value->name;
						} else {
							$list_department .= $department_value->name;
						}
					}
				}
			}

			$data_object_kpi[$staff_key]['department_name'] = $list_department;

			if (isset($commissions_value[$staff_value['staffid'] . '_' . $current_month])) {

				$data_object_kpi[$staff_key]['commission_amount'] = $commissions_value[$staff_value['staffid'] . '_' . $current_month]['commission_amount'];
				$data_object_kpi[$staff_key]['id'] = $commissions_value[$staff_value['staffid'] . '_' . $current_month]['id'];

			} else {

				$data_object_kpi[$staff_key]['commission_amount'] = 0;
				$data_object_kpi[$staff_key]['id'] = 0;

			}
			$data_object_kpi[$staff_key]['month'] = $current_month;
			$data_object_kpi[$staff_key]['rel_type'] = $commission_type;

		}

		//check is add new or update data
		if (count($commissions_value) > 0) {
			$data['button_name'] = _l('hrp_update');
		} else {
			$data['button_name'] = _l('submit');
		}

		$data['departments'] = $this->departments_model->get();
		$data['roles'] = $this->roles_model->get();
		$data['staffs'] = $staffs;

		$data['body_value'] = json_encode($data_object_kpi);
		$data['columns'] = json_encode($format_commission_value['column_format']);
		$data['col_header'] = json_encode($format_commission_value['headers']);

		$this->load->view('commissions/commissions_manage', $data);
	}

	/**
	 * add manage commissions
	 */
	public function add_manage_commissions() {
		if (!has_permission('hrp_commission', '', 'create') && !has_permission('hrp_commission', '', 'edit') && !is_admin()) {
			access_denied('hrp_commission');
		}

		if ($this->input->post()) {
			$data = $this->input->post();

			if ($data['hrp_commissions_rel_type'] == 'update') {
				// update data
				$success = $this->hr_payroll_model->commissions_update($data);
			} elseif ($data['hrp_commissions_rel_type'] == 'synchronization') {
				//synchronization
				$success = $this->hr_payroll_model->commissions_synchronization($data);

			} else {
				$success = false;
			}

			if ($success) {
				set_alert('success', _l('updated_successfully'));
			} else {
				set_alert('warning', _l('hrp_updated_failed'));
			}

			redirect(admin_url('hr_payroll/manage_commissions'));
		}

	}

	/**
	 * commissions filter
	 * @return [type]
	 */
	public function commissions_filter() {
		$this->load->model('departments_model');
		$data = $this->input->post();

		$rel_type = hrp_get_hr_profile_status();
		$commission_type = hrp_get_commission_status();

		$months_filter = $data['month'];
		$department = $data['department'];
		$staff = '';
		if (isset($data['staff'])) {
			$staff = $data['staff'];
		}
		$role_attendance = '';
		if (isset($data['role_attendance'])) {
			$role_attendance = $data['role_attendance'];
		}

		$newquerystring = $this->render_filter_query($months_filter, $staff, $department, $role_attendance);

		//get current month
		$month_filter = date('Y-m-d', strtotime($data['month'] . '-01'));
		$commissions_data = $this->hr_payroll_model->get_commissions_data($month_filter);
		$commissions_value = [];
		if (count($commissions_data) > 0) {
			foreach ($commissions_data as $key => $value) {
				$commissions_value[$value['staff_id'] . '_' . $value['month']] = $value;
			}
		}

		// data return
		$data_object_kpi = [];
		$index_data_object = 0;
		if ($newquerystring != '') {

			//load staff
			if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
				//View own
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission($newquerystring));
			} else {
				//admin or view global
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object($newquerystring);
			}

			$data_object_kpi = [];

			foreach ($staffs as $staff_key => $staff_value) {
				/*check value from database*/
				$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

				if ($rel_type == 'hr_records') {
					$data_object_kpi[$staff_key]['employee_number'] = $staff_value['staff_identifi'];
				} else {
					$data_object_kpi[$staff_key]['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_value['staffid'], 5);
				}

				$data_object_kpi[$staff_key]['employee_name'] = $staff_value['firstname'] . ' ' . $staff_value['lastname'];

				$arr_department = $this->hr_payroll_model->get_staff_departments($staff_value['staffid'], true);

				$list_department = '';
				if (count($arr_department) > 0) {

					foreach ($arr_department as $key => $department) {
						$department_value = $this->departments_model->get($department);

						if ($department_value) {
							if (strlen($list_department) != 0) {
								$list_department .= ', ' . $department_value->name;
							} else {
								$list_department .= $department_value->name;
							}
						}
					}
				}

				$data_object_kpi[$staff_key]['department_name'] = $list_department;

				if (isset($commissions_value[$staff_value['staffid'] . '_' . $month_filter])) {

					$data_object_kpi[$staff_key]['commission_amount'] = $commissions_value[$staff_value['staffid'] . '_' . $month_filter]['commission_amount'];
					$data_object_kpi[$staff_key]['id'] = $commissions_value[$staff_value['staffid'] . '_' . $month_filter]['id'];

				} else {

					$data_object_kpi[$staff_key]['commission_amount'] = 0;
					$data_object_kpi[$staff_key]['id'] = 0;

				}
				$data_object_kpi[$staff_key]['month'] = $month_filter;
				$data_object_kpi[$staff_key]['rel_type'] = $commission_type;
			}

		}

		//check is add new or update data
		if (count($commissions_value) > 0) {
			$button_name = _l('hrp_update');
		} else {
			$button_name = _l('submit');
		}

		echo json_encode([
			'data_object' => $data_object_kpi,
			'button_name' => $button_name,
		]);
		die;
	}

	/**
	 * [import_xlsx_commissions
	 * @return [type]
	 */
	public function import_xlsx_commissions() {
		$this->load->model('staff_model');
		$data_staff = $this->staff_model->get(get_staff_user_id());
		/*get language active*/
		if ($data_staff) {
			if ($data_staff->default_language != '') {
				$data['active_language'] = $data_staff->default_language;
			} else {
				$data['active_language'] = get_option('active_language');
			}

		} else {
			$data['active_language'] = get_option('active_language');
		}

		$this->load->view('hr_payroll/commissions/import_commissions', $data);
	}

	/**
	 * create commissions sample file
	 * @return [type]
	 */
	public function create_commissions_sample_file() {
		if (!has_permission('hrp_commission', '', 'create') && !has_permission('hrp_commission', '', 'edit') && !is_admin()) {
			access_denied('hrp_commission');

		}
		$this->load->model('staff_model');
		$this->load->model('departments_model');

		$month_commission = $this->input->post('month_commissions');

		if (!class_exists('XLSXReader_fin')) {
			require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php';
		}
		require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php';

		$this->delete_error_file_day_before('1', HR_PAYROLL_CREATE_COMMISSIONS_SAMPLE);

		$rel_type = hrp_get_commission_status();
		//get commission data
		$current_month = date('Y-m-d', strtotime($month_commission . '-01'));
		//get day header in month
		$format_commission_data = $this->hr_payroll_model->get_format_commission_data($current_month, $rel_type);
		$header_key = $format_commission_data['staff_information'];

		$commissions = $this->hr_payroll_model->get_commissions_data($current_month);
		$commissions_value = [];
		foreach ($commissions as $key => $value) {
			$commissions_value[$value['staff_id'] . '_' . $value['month']] = $value;
		}

		//load staff
		if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		//Writer file
		$writer_header = [];
		$widths = [];
		foreach ($format_commission_data['headers'] as $value) {
			$writer_header[$value] = 'string';
			$widths[] = 30;
		}

		$writer = new XLSXWriter();

		$col_style1 = [0, 1, 2, 3, 4, 5, 6];
		$style1 = ['widths' => $widths, 'fill' => '#ff9800', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13];

		$writer->writeSheetHeader_v2('Sheet1', $writer_header, $col_options = ['widths' => $widths, 'fill' => '#03a9f46b', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13],
			$col_style1, $style1);

		$data_object_kpi = [];
		foreach ($staffs as $staff_key => $staff_value) {
			$staffid = 0;
			$id = 0;
			$staff_name = '';
			$staff_departments = '';
			$commissions_amount = 0;

			/*check value from database*/
			$staffid = $staff_value['staffid'];

			/*check value from database*/
			$staff_i = $this->hr_payroll_model->get_staff_info($staff_value['staffid']);
			if ($staff_i) {

				if (isset($staff_i->staff_identifi)) {
					$data_object_kpi['employee_number'] = $staff_i->staff_identifi;
				} else {
					$data_object_kpi['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_i->staffid, 5);
				}

				$data_object_kpi['employee_name'] = $staff_i->firstname . ' ' . $staff_i->lastname;

				$arr_department = $this->hr_payroll_model->get_staff_departments($staff_i->staffid, true);

				$list_department = '';
				if (count($arr_department) > 0) {

					foreach ($arr_department as $key => $department) {
						$department_value = $this->departments_model->get($department);

						if ($department_value) {
							if (strlen($list_department) != 0) {
								$list_department .= ', ' . $department_value->name;
							} else {
								$list_department .= $department_value->name;
							}
						}

					}
				}

				$data_object_kpi['department_name'] = $list_department;

			} else {
				$data_object_kpi['employee_number'] = '';
				$data_object_kpi['employee_name'] = '';
				$data_object_kpi['department_name'] = '';

			}

			if (isset($commissions_value[$staff_value['staffid'] . '_' . $current_month])) {

				$data_object_kpi['commission_amount'] = $commissions_value[$staff_value['staffid'] . '_' . $current_month]['commission_amount'];
				$data_object_kpi['id'] = $commissions_value[$staff_value['staffid'] . '_' . $current_month]['id'];

			} else {
				$data_object_kpi['commission_amount'] = 0;
				$data_object_kpi['id'] = 0;

			}
			$data_object_kpi['rel_type'] = $rel_type;
			$data_object_kpi['month'] = $current_month;
			$data_object_kpi['staff_id'] = $staff_value['staffid'];

			if ($staff_key == 0) {
				$writer->writeSheetRow('Sheet1', $header_key);
			}
			$get_values_for_keys = $this->get_values_for_keys($data_object_kpi, $header_key);

			$writer->writeSheetRow('Sheet1', $get_values_for_keys);

		}

		$filename = 'commission_sample_file' . get_staff_user_id() . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
		$writer->writeToFile(str_replace($filename, HR_PAYROLL_CREATE_COMMISSIONS_SAMPLE . $filename, $filename));

		echo json_encode([
			'success' => true,
			'site_url' => site_url(),
			'staff_id' => get_staff_user_id(),
			'filename' => HR_PAYROLL_CREATE_COMMISSIONS_SAMPLE . $filename,
		]);

	}

	/**
	 * import commissions excel
	 * @return [type]
	 */
	public function import_commissions_excel() {
		if (!has_permission('hrp_commission', '', 'create') && !has_permission('hrp_commission', '', 'edit') && !is_admin()) {
			access_denied('hrp_commission');
		}

		if (!class_exists('XLSXReader_fin')) {
			require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php';
		}
		require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php';

		$filename = '';
		if ($this->input->post()) {
			if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {

				$this->delete_error_file_day_before();
				$rel_type = hrp_get_timesheets_status();

				// Get the temp file path
				$tmpFilePath = $_FILES['file_csv']['tmp_name'];
				// Make sure we have a filepath
				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					$rows = [];
					$arr_insert = [];

					$tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

					if (!file_exists(TEMP_FOLDER)) {
						mkdir(TEMP_FOLDER, 0755);
					}

					if (!file_exists($tmpDir)) {
						mkdir($tmpDir, 0755);
					}

					// Setup our new file path
					$newFilePath = $tmpDir . $_FILES['file_csv']['name'];

					if (move_uploaded_file($tmpFilePath, $newFilePath)) {
						//Writer file
						$writer_header = array(
							_l('staffid') => 'string',
							_l('id') => 'string',
							_l('hr_code') => 'string',
							_l('staff_name') => 'string',
							_l('department') => 'string',
							_l('integration_actual_workday') => 'string',
							_l('integration_paid_leave') => 'string',
							_l('integration_unpaid_leave') => 'string',
							_l('standard_working_time_of_month') => 'string',
							_l('month') => 'string',
							_l('error') => 'string',
						);

						$writer = new XLSXWriter();
						$writer->writeSheetHeader('Sheet1', $writer_header, $col_options = ['widths' => [40, 40, 40, 50, 40, 40, 40, 40, 50, 50]]);

						//Reader file
						$xlsx = new XLSXReader_fin($newFilePath);
						$sheetNames = $xlsx->getSheetNames();
						$data = $xlsx->getSheetData($sheetNames[1]);

						$arr_header = [];

						$arr_header['staff_id'] = 0;
						$arr_header['id'] = 1;
						$arr_header['hr_code'] = 2;
						$arr_header['staff_name'] = 3;
						$arr_header['staff_departments'] = 4;
						$arr_header['actual_workday'] = 5;
						$arr_header['paid_leave'] = 6;
						$arr_header['unpaid_leave'] = 7;
						$arr_header['standard_workday'] = 8;
						$arr_header['month'] = 9;

						$total_rows = 0;
						$total_row_false = 0;

						$column_key = $data[1];
						for ($row = 2; $row < count($data); $row++) {

							$total_rows++;

							$rd = array();
							$flag = 0;
							$flag2 = 0;

							$string_error = '';
							$flag_position_group;
							$flag_department = null;

							$flag_staff_id = 0;

							if (($flag == 1) || $flag2 == 1) {
								//write error file
								$writer->writeSheetRow('Sheet1', [
									$value_staffid,
									$value_dependent_name,
									$value_relationship,
									$value_dependent_bir,
									$value_dependent_iden,
									$value_reason,
									$value_start_month,
									$value_end_month,
									$value_status,
									$string_error,
								]);

								$total_row_false++;
							}

							if ($flag == 0 && $flag2 == 0) {

								$rd = array_combine($column_key, $data[$row]);
								unset($rd['employee_number']);
								unset($rd['employee_name']);
								unset($rd['department_name']);
								unset($rd['hr_code']);
								unset($rd['staff_name']);
								unset($rd['staff_departments']);

								$rows[] = $rd;
								array_push($arr_insert, $rd);

							}

						}

						//insert batch
						if (count($arr_insert) > 0) {
							$this->hr_payroll_model->import_commissions_data($arr_insert);
						}

						$total_rows = $total_rows;
						$total_row_success = isset($rows) ? count($rows) : 0;
						$dataerror = '';
						$message = 'Not enought rows for importing';

						if ($total_row_false != 0) {
							$filename = 'Import_commissions_error_' . get_staff_user_id() . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
							$writer->writeToFile(str_replace($filename, HR_PAYROLL_ERROR . $filename, $filename));
						}

					}
				}
			}
		}

		if (file_exists($newFilePath)) {
			@unlink($newFilePath);
		}

		echo json_encode([
			'message' => $message,
			'total_row_success' => $total_row_success,
			'total_row_false' => $total_row_false,
			'total_rows' => $total_rows,
			'site_url' => site_url(),
			'staff_id' => get_staff_user_id(),
			'filename' => HR_PAYROLL_ERROR . $filename,
		]);
	}

	/**
	 * manage income taxs
	 * @return [type]
	 */
	public function income_taxs_manage() {
		if (!has_permission('hrp_income_tax', '', 'view') && !has_permission('hrp_income_tax', '', 'view_own') && !is_admin()) {
			access_denied('hrp_income_tax');
		}
		$this->load->model('staff_model');
		$this->load->model('departments_model');

		$rel_type = hrp_get_hr_profile_status();

		//get current month
		$current_month = date('Y-m-d', strtotime(date('Y-m') . '-01'));
		$income_taxs_data = $this->hr_payroll_model->get_income_tax_data($current_month);
		$income_taxs_value = [];
		if (count($income_taxs_data) > 0) {
			foreach ($income_taxs_data as $key => $value) {
				$income_taxs_value[$value['staff_id'] . '_' . $value['month']] = $value;
			}
		}

		//get tax for year
		$total_income_tax_in_year = $this->hr_payroll_model->get_total_income_tax_in_year($current_month);
		$tax_in_year = [];
		foreach ($total_income_tax_in_year as $t_key => $t_value) {
			$tax_in_year[$t_value['staff_id']] = $t_value;
		}

		//get deduction data for the first
		$format_income_tax_value = $this->hr_payroll_model->get_format_income_tax_data();

		//load staff
		if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		$data_object_kpi = [];
		foreach ($staffs as $staff_key => $staff_value) {
			/*check value from database*/
			$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

			if ($rel_type == 'hr_records') {
				$data_object_kpi[$staff_key]['employee_number'] = $staff_value['staff_identifi'];
			} else {
				$data_object_kpi[$staff_key]['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_value['staffid'], 5);
			}

			$data_object_kpi[$staff_key]['employee_name'] = $staff_value['firstname'] . ' ' . $staff_value['lastname'];

			$arr_department = $this->hr_payroll_model->get_staff_departments($staff_value['staffid'], true);

			$list_department = '';
			if (count($arr_department) > 0) {

				foreach ($arr_department as $key => $department) {
					$department_value = $this->departments_model->get($department);

					if ($department_value) {
						if (strlen($list_department) != 0) {
							$list_department .= ', ' . $department_value->name;
						} else {
							$list_department .= $department_value->name;
						}
					}
				}
			}

			$data_object_kpi[$staff_key]['department_name'] = $list_department;

			if (isset($income_taxs_value[$staff_value['staffid'] . '_' . $current_month])) {

				$data_object_kpi[$staff_key]['income_tax'] = $income_taxs_value[$staff_value['staffid'] . '_' . $current_month]['income_tax'];
				$data_object_kpi[$staff_key]['id'] = $income_taxs_value[$staff_value['staffid'] . '_' . $current_month]['id'];

			} else {

				$data_object_kpi[$staff_key]['income_tax'] = 0;
				$data_object_kpi[$staff_key]['id'] = 0;

			}
			$data_object_kpi[$staff_key]['month'] = $current_month;

			if (isset($tax_in_year[$staff_value['staffid']])) {
				$data_object_kpi[$staff_key]['tax_for_year'] = $tax_in_year[$staff_value['staffid']]['tax_for_year'];
			} else {
				$data_object_kpi[$staff_key]['tax_for_year'] = 0;
			}
		}

		$data['departments'] = $this->departments_model->get();
		$data['roles'] = $this->roles_model->get();
		$data['staffs'] = $staffs;

		$data['body_value'] = json_encode($data_object_kpi);
		$data['columns'] = json_encode($format_income_tax_value['column_format']);
		$data['col_header'] = json_encode($format_income_tax_value['headers']);

		$this->load->view('income_tax/income_tax_manage', $data);
	}

	/**
	 * income taxs filter
	 * @return [type]
	 */
	public function income_taxs_filter() {
		$this->load->model('departments_model');
		$data = $this->input->post();

		$rel_type = hrp_get_hr_profile_status();
		$commission_type = hrp_get_commission_status();

		$months_filter = $data['month'];
		$department = $data['department'];
		$staff = '';
		if (isset($data['staff'])) {
			$staff = $data['staff'];
		}
		$role_attendance = '';
		if (isset($data['role_attendance'])) {
			$role_attendance = $data['role_attendance'];
		}

		$newquerystring = $this->render_filter_query($months_filter, $staff, $department, $role_attendance);

		//get current month
		$current_month = date('Y-m-d', strtotime($data['month'] . '-01'));
		$income_taxs_data = $this->hr_payroll_model->get_income_tax_data($current_month);
		$income_taxs_value = [];
		if (count($income_taxs_data) > 0) {
			foreach ($income_taxs_data as $key => $value) {
				$income_taxs_value[$value['staff_id'] . '_' . $value['month']] = $value;
			}
		}

		//get tax for year
		$total_income_tax_in_year = $this->hr_payroll_model->get_total_income_tax_in_year($current_month);
		$tax_in_year = [];
		foreach ($total_income_tax_in_year as $t_key => $t_value) {
			$tax_in_year[$t_value['staff_id']] = $t_value;
		}

		// data return
		$data_object_kpi = [];
		$index_data_object = 0;
		if ($newquerystring != '') {

			//load staff
			if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
				//View own
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission($newquerystring));
			} else {
				//admin or view global
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object($newquerystring);
			}

			$data_object_kpi = [];

			foreach ($staffs as $staff_key => $staff_value) {
				/*check value from database*/
				$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

				if ($rel_type == 'hr_records') {
					$data_object_kpi[$staff_key]['employee_number'] = $staff_value['staff_identifi'];
				} else {
					$data_object_kpi[$staff_key]['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_value['staffid'], 5);
				}

				$data_object_kpi[$staff_key]['employee_name'] = $staff_value['firstname'] . ' ' . $staff_value['lastname'];

				$arr_department = $this->hr_payroll_model->get_staff_departments($staff_value['staffid'], true);

				$list_department = '';
				if (count($arr_department) > 0) {

					foreach ($arr_department as $key => $department) {
						$department_value = $this->departments_model->get($department);

						if ($department_value) {
							if (strlen($list_department) != 0) {
								$list_department .= ', ' . $department_value->name;
							} else {
								$list_department .= $department_value->name;
							}
						}
					}
				}

				$data_object_kpi[$staff_key]['department_name'] = $list_department;

				if (isset($income_taxs_value[$staff_value['staffid'] . '_' . $current_month])) {

					$data_object_kpi[$staff_key]['income_tax'] = $income_taxs_value[$staff_value['staffid'] . '_' . $current_month]['income_tax'];
					$data_object_kpi[$staff_key]['id'] = $income_taxs_value[$staff_value['staffid'] . '_' . $current_month]['id'];

				} else {

					$data_object_kpi[$staff_key]['income_tax'] = 0;
					$data_object_kpi[$staff_key]['id'] = 0;

				}
				$data_object_kpi[$staff_key]['month'] = $current_month;
				if (isset($tax_in_year[$staff_value['staffid']])) {
					$data_object_kpi[$staff_key]['tax_for_year'] = $tax_in_year[$staff_value['staffid']]['tax_for_year'];
				} else {
					$data_object_kpi[$staff_key]['tax_for_year'] = 0;
				}
			}

		}

		echo json_encode([
			'data_object' => $data_object_kpi,
		]);
		die;
	}

	/**
	 * manage insurances
	 * @return [type]
	 */
	public function manage_insurances() {
		if (!has_permission('hrp_insurrance', '', 'view') && !has_permission('hrp_insurrance', '', 'view_own') && !is_admin()) {
			access_denied('hrp_insurrance');
		}

		$this->load->model('staff_model');
		$this->load->model('departments_model');

		$rel_type = hrp_get_hr_profile_status();

		//get current month
		$current_month = date('Y-m-d', strtotime(date('Y-m') . '-01'));
		$insurances_data = $this->hr_payroll_model->get_insurances_data($current_month);
		$insurances_value = [];
		if (count($insurances_data) > 0) {
			foreach ($insurances_data as $key => $value) {
				$insurances_value[$value['staff_id'] . '_' . $value['month']] = $value;
			}
		}

		//get insurance data for the first
		$format_insurance_value = $this->hr_payroll_model->get_format_insurance_data();

		//load staff
		if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		$data_object_kpi = [];
		foreach ($staffs as $staff_key => $staff_value) {
			/*check value from database*/
			$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

			if ($rel_type == 'hr_records') {
				$data_object_kpi[$staff_key]['employee_number'] = $staff_value['staff_identifi'];
			} else {
				$data_object_kpi[$staff_key]['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_value['staffid'], 5);
			}

			$data_object_kpi[$staff_key]['employee_name'] = $staff_value['firstname'] . ' ' . $staff_value['lastname'];

			$arr_department = $this->hr_payroll_model->get_staff_departments($staff_value['staffid'], true);

			$list_department = '';
			if (count($arr_department) > 0) {

				foreach ($arr_department as $key => $department) {
					$department_value = $this->departments_model->get($department);

					if ($department_value) {
						if (strlen($list_department) != 0) {
							$list_department .= ', ' . $department_value->name;
						} else {
							$list_department .= $department_value->name;
						}
					}
				}
			}

			$data_object_kpi[$staff_key]['department_name'] = $list_department;

			if (isset($insurances_value[$staff_value['staffid'] . '_' . $current_month])) {

				// array merge: staff information + earning list (probationary contract) + earning list (formal)
				if (isset($insurances_value[$staff_value['staffid'] . '_' . $current_month]['insurance_value'])) {
					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $insurances_value[$staff_value['staffid'] . '_' . $current_month]['insurance_value']);
				} else {
					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_insurance_value['array_insurance']);
				}

				$data_object_kpi[$staff_key]['id'] = $insurances_value[$staff_value['staffid'] . '_' . $current_month]['id'];

			} else {

				// array merge: staff information + earning list (probationary contract) + earning list (formal)
				$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_insurance_value['array_insurance']);

				$data_object_kpi[$staff_key]['id'] = 0;

			}
			$data_object_kpi[$staff_key]['month'] = $current_month;

		}

		//check is add new or update data
		if (count($insurances_value) > 0) {
			$data['button_name'] = _l('hrp_update');
		} else {
			$data['button_name'] = _l('submit');
		}

		$data['departments'] = $this->departments_model->get();
		$data['roles'] = $this->roles_model->get();
		$data['staffs'] = $staffs;

		$data['body_value'] = json_encode($data_object_kpi);
		$data['columns'] = json_encode($format_insurance_value['column_format']);
		$data['col_header'] = json_encode($format_insurance_value['header']);

		$this->load->view('insurances/insurances_manage', $data);
	}

	/**
	 * add manage insurances
	 */
	public function add_manage_insurances() {
		if (!has_permission('hrp_insurrance', '', 'create') && !has_permission('hrp_insurrance', '', 'edit') && !is_admin()) {
			access_denied('hrp_insurrance');
		}

		if ($this->input->post()) {
			$data = $this->input->post();

			if ($data['hrp_insurances_rel_type'] == 'update') {
				// update data
				$success = $this->hr_payroll_model->insurances_update($data);
			} else {
				$success = false;
			}

			if ($success) {
				set_alert('success', _l('updated_successfully'));
			} else {
				set_alert('warning', _l('hrp_updated_failed'));
			}

			redirect(admin_url('hr_payroll/manage_insurances'));
		}

	}

	/**
	 * insurances filter
	 * @return [type]
	 */
	public function insurances_filter() {
		$this->load->model('departments_model');
		$data = $this->input->post();

		$rel_type = hrp_get_hr_profile_status();

		$months_filter = $data['month'];
		$department = $data['department'];
		$staff = '';
		if (isset($data['staff'])) {
			$staff = $data['staff'];
		}
		$role_attendance = '';
		if (isset($data['role_attendance'])) {
			$role_attendance = $data['role_attendance'];
		}

		$newquerystring = $this->render_filter_query($months_filter, $staff, $department, $role_attendance);

		//get current month
		$month_filter = date('Y-m-d', strtotime($data['month'] . '-01'));
		$insurances_data = $this->hr_payroll_model->get_insurances_data($month_filter);
		$insurances_value = [];
		foreach ($insurances_data as $key => $value) {
			$insurances_value[$value['staff_id'] . '_' . $value['month']] = $value;
		}

		//get employee data for the first
		$format_insurance_value = $this->hr_payroll_model->get_format_insurance_data();

		// data return
		$data_object_kpi = [];
		$index_data_object = 0;
		if ($newquerystring != '') {

			//load staff
			if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
				//View own
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission($newquerystring));
			} else {
				//admin or view global
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object($newquerystring);
			}

			$data_object_kpi = [];

			foreach ($staffs as $staff_key => $staff_value) {
				/*check value from database*/
				$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

				if ($rel_type == 'hr_records') {
					$data_object_kpi[$staff_key]['employee_number'] = $staff_value['staff_identifi'];
				} else {
					$data_object_kpi[$staff_key]['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_value['staffid'], 5);
				}

				$data_object_kpi[$staff_key]['employee_name'] = $staff_value['firstname'] . ' ' . $staff_value['lastname'];

				$arr_department = $this->hr_payroll_model->get_staff_departments($staff_value['staffid'], true);

				$list_department = '';
				if (count($arr_department) > 0) {

					foreach ($arr_department as $key => $department) {
						$department_value = $this->departments_model->get($department);

						if ($department_value) {
							if (strlen($list_department) != 0) {
								$list_department .= ', ' . $department_value->name;
							} else {
								$list_department .= $department_value->name;
							}
						}
					}
				}

				$data_object_kpi[$staff_key]['department_name'] = $list_department;

				if (isset($insurances_value[$staff_value['staffid'] . '_' . $month_filter])) {

					// array merge: staff information + earning list (probationary contract) + earning list (formal)
					if (isset($insurances_value[$staff_value['staffid'] . '_' . $month_filter]['insurance_value'])) {
						$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $insurances_value[$staff_value['staffid'] . '_' . $month_filter]['insurance_value']);
					} else {
						$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_insurance_value['array_insurance']);
					}

					$data_object_kpi[$staff_key]['id'] = $insurances_value[$staff_value['staffid'] . '_' . $month_filter]['id'];

				} else {

					// array merge: staff information + earning list (probationary contract) + earning list (formal)
					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_insurance_value['array_insurance']);

					$data_object_kpi[$staff_key]['id'] = 0;

				}
				$data_object_kpi[$staff_key]['month'] = $month_filter;
			}

		}

		//check is add new or update data
		if (count($insurances_value) > 0) {
			$button_name = _l('hrp_update');
		} else {
			$button_name = _l('submit');
		}

		echo json_encode([
			'data_object' => $data_object_kpi,
			'button_name' => $button_name,
		]);
		die;
	}

	/**
	 * delete_error file day before
	 * @return [type]
	 */
	public function delete_error_file_day_before($before_day = '', $folder_name = '') {
		if ($before_day != '') {
			$day = $before_day;
		} else {
			$day = '7';
		}

		if ($folder_name != '') {
			$folder = $folder_name;
		} else {
			$folder = HR_PAYROLL_ERROR;
		}

		//Delete old file before 7 day
		$date = date_create(date('Y-m-d H:i:s'));
		date_sub($date, date_interval_create_from_date_string($day . " days"));
		$before_7_day = strtotime(date_format($date, "Y-m-d H:i:s"));

		foreach (glob($folder . '*') as $file) {

			$file_arr = explode("/", $file);
			$filename = array_pop($file_arr);

			if (file_exists($file)) {
				//don't delete index.html file
				if ($filename != 'index.html') {
					$file_name_arr = explode("_", $filename);
					$date_create_file = array_pop($file_name_arr);
					$date_create_file = str_replace('.xlsx', '', $date_create_file);

					if ((float) $date_create_file <= (float) $before_7_day) {
						unlink($folder . $filename);
					}
				}
			}
		}
		return true;
	}

	/**
	 * payslip manage
	 * @param  string $id
	 * @return [type]
	 */
	public function payslip_manage($id = '') {
		if (!has_permission('hrp_payslip', '', 'view') && !has_permission('hrp_payslip', '', 'view_own') && !is_admin()) {
			access_denied('hrp_payslip');
		}
		$data['internal_id'] = $id;
		$data['title'] = _l('hr_pay_slips');
		$data['staffs'] = $this->staff_model->get();
		$this->load->view('payslips/payslip_manage', $data);
	}

	/**
	 * payslip table
	 * @return table
	 */
	public function payslip_table() {
		$this->app->get_table_data(module_views_path('hr_payroll', 'payslips/payslip_table'));
	}

	/**
	 * delete payslip
	 * @param  [type] $id
	 * @return [type]
	 */
	public function delete_payslip($id) {
		if (!is_admin() && !has_permission('hrp_payslip', '', 'delete')) {
			access_denied('hrp_payslip');
		}
		if (!$id) {
			redirect(admin_url('hr_payroll/payslip_manage'));
		}

		$response = $this->hr_payroll_model->delete_payslip($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('is_referenced', _l('payslip_template')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('payslip_template')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('payslip_template')));
		}
		redirect(admin_url('hr_payroll/payslip_manage'));
	}

	/**
	 * payslip manage
	 * @param  string $id
	 * @return [type]
	 */
	public function payslip_templates_manage($id = '') {
		if (!has_permission('hrp_payslip_template', '', 'view') && !has_permission('hrp_payslip_template', '', 'view_own') && !is_admin()) {
			access_denied('hrp_payslip_template');
		}

		$this->load->model('staff_model');
		$this->load->model('departments_model');

		$data['staffs'] = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		$data['internal_id'] = $id;

		$data['departments'] = $this->departments_model->get();
		$data['roles'] = $this->roles_model->get();

		$data['title'] = _l('payslip_template');
		$this->load->view('payslip_templates/payslip_template_manage', $data);
	}

	/**
	 * payslip table
	 * @return table
	 */
	public function payslip_template_table() {
		$this->app->get_table_data(module_views_path('hr_payroll', 'payslip_templates/payslip_template_table'));
	}

	/**
	 * get column key html add
	 * @return [type]
	 */
	public function get_payroll_column_method_html_add() {
		$method_option = $this->hr_payroll_model->get_list_payroll_column_method(['id' => '']);
		$order_display = $this->hr_payroll_model->count_payroll_column();

		echo json_encode([
			'method_option' => $method_option['method_option'],
			'order_display' => $order_display,

		]);
	}

	/**
	 * get payroll column function name html
	 * @return [type]
	 */
	public function get_payroll_column_function_name_html() {
		$method_option = $this->hr_payroll_model->get_list_payroll_column_function_name(['function_name' => '']);

		echo json_encode([
			'method_option' => $method_option['method_option'],

		]);
	}

	/**
	 * payroll column
	 * @return [type]
	 */
	public function payroll_column() {
		if ($this->input->post()) {
			$data = $this->input->post();
			if (!$this->input->post('id')) {

				if (!is_admin() && !has_permission('hrp_setting', '', 'create')) {
					access_denied('hr_payroll');
				}

				$add = $this->hr_payroll_model->add_payroll_column($data);
				if ($add) {
					$message = _l('added_successfully', _l('payroll_column'));
					set_alert('success', $message);
				}
				redirect(admin_url('hr_payroll/setting?group=payroll_columns'));
			} else {

				if (!is_admin() && !has_permission('hrp_setting', '', 'edit')) {
					access_denied('hr_payroll');
				}

				$id = $data['id'];
				unset($data['id']);
				$success = $this->hr_payroll_model->update_payroll_column($data, $id);
				if ($success == true) {
					$message = _l('updated_successfully', _l('payroll_column'));
					set_alert('success', $message);
				}
				redirect(admin_url('hr_payroll/setting?group=payroll_columns'));
			}

		}
	}

	/**
	 * get payroll column
	 * @param  [type] $id
	 * @return [type]
	 */
	public function get_payroll_column($id) {
		//get data
		$payroll_column = $this->hr_payroll_model->get_hrp_payroll_columns($id);
		//get taking method html
		if ($payroll_column) {
			$method_option = $this->hr_payroll_model->get_list_payroll_column_method(['taking_method' => $payroll_column->taking_method]);
		} else {
			$method_option = $this->hr_payroll_model->get_list_payroll_column_method(['taking_method' => '']);
		}
		//get function name html
		if ($payroll_column) {
			$function_name = $this->hr_payroll_model->get_list_payroll_column_function_name(['function_name' => $payroll_column->function_name]);
		} else {
			$function_name = $this->hr_payroll_model->get_list_payroll_column_function_name(['function_name' => '']);
		}

		echo json_encode([
			'payroll_column' => $payroll_column,
			'method_option' => $method_option,
			'function_name' => $function_name,
		]);
		die;

	}

	/**
	 * delete payroll column setting
	 * @param  string $id
	 * @return [type]
	 */
	public function delete_payroll_column_setting($id = '') {
		if (!is_admin() && !has_permission('hrp_setting', '', 'delete')) {
			access_denied('hr_payroll');
		}
		if (!$id) {
			redirect(admin_url('hr_payroll/setting?group=payroll_columns'));
		}

		$response = $this->hr_payroll_model->delete_payroll_column($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('is_referenced', _l('payslip_template')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('payslip_template')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('payslip_template')));
		}
		redirect(admin_url('hr_payroll/setting?group=payroll_columns'));
	}

	/**
	 * get payslip template
	 * @param  string $id
	 * @return [type]
	 */
	public function get_payslip_template($id = '') {
		$payslip_template_data = '';
		if (isset($id) && $id != '') {
			$payslip_template = $this->hr_payroll_model->get_hrp_payslip_templates($id);
			// update
			$payslip_template_selected = $this->hr_payroll_model->get_payslip_template_selected_html($payslip_template->payslip_id_copy);
			$payslip_column_selected = $this->hr_payroll_model->get_payslip_column_html($payslip_template->payslip_columns);
			$payslip_template_data = $payslip_template;

		} else {
			// create
			$payslip_template_selected = $this->hr_payroll_model->get_payslip_template_selected_html('');
			$payslip_column_selected = $this->hr_payroll_model->get_payslip_column_html('');
		}

		echo json_encode([
			'payslip_template_selected' => $payslip_template_selected,
			'payslip_column_selected' => $payslip_column_selected,
			'payslip_template_data' => $payslip_template_data,
		]);
		die;

	}

	/**
	 * payslip template
	 * @return [type]
	 */
	public function payslip_template() {
		if (!has_permission('hrp_payslip_template', '', 'create') && !has_permission('hrp_payslip_template', '', 'edit') && !is_admin()) {
			access_denied('hrp_payslip_template');
		}

		if ($this->input->post()) {
			$data = $this->input->post();

			if (!$this->input->post('id')) {

				if (!is_admin() && !has_permission('hrp_payslip_template', '', 'create')) {
					access_denied('hrp_payslip_template');
				}

				$insert_id = $this->hr_payroll_model->add_payslip_template($data);
				if ($insert_id) {
					$this->hr_payroll_model->add_payslip_templates_detail_first($insert_id);

					$message = _l('added_successfully', _l('payroll_column'));
					set_alert('success', $message);
				}
				redirect(admin_url('hr_payroll/view_payslip_templates_detail/' . $insert_id));
			} else {

				if (!is_admin() && !has_permission('hrp_payslip_template', '', 'edit')) {
					access_denied('hrp_payslip_template');
				}

				$id = $data['id'];
				unset($data['id']);

				$edit_payslip_column = false;
				if (isset($data['edit_payslip_column']) && $data['edit_payslip_column'] == 'true') {
					$edit_payslip_column = true;
					unset($data['edit_payslip_column']);
				}

				$check_update_detail = false;
				$check_update_detail = $this->hr_payroll_model->check_update_payslip_template_detail($data, $id);
				$success = $this->hr_payroll_model->update_payslip_template($data, $id);

				if ($success == true) {
					if ($check_update_detail['status']) {
						$this->hr_payroll_model->update_payslip_templates_detail_first($check_update_detail['old_column_formular'], $id);
					}

					$message = _l('updated_successfully', _l('payroll_column'));
					set_alert('success', $message);
				}
				redirect(admin_url('hr_payroll/view_payslip_templates_detail/' . $id));
			}

		}
	}

	/**
	 * delete payslip template
	 * @param  [type] $id
	 * @return [type]
	 */
	public function delete_payslip_template($id) {
		if (!is_admin() && !has_permission('hrp_payslip_template', '', 'delete')) {
			access_denied('hr_payroll');
		}
		if (!$id) {
			redirect(admin_url('hr_payroll/payslip_templates_manage'));
		}

		$response = $this->hr_payroll_model->delete_payslip_template($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('is_referenced', _l('payslip_template')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('payslip_template')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('payslip_template')));
		}
		redirect(admin_url('hr_payroll/payslip_templates_manage'));
	}

	/**
	 * view payslip templates detail, add or edit
	 * @param [type] $parent_id
	 * @param string $id
	 */
	public function view_payslip_templates_detail($id = "") {

		$data_form = $this->input->post();
		if ($this->input->post()) {
			$data = $this->input->post();

			if (!is_admin() && !has_permission('hrp_payslip_template', '', 'edit') && !has_permission('hrp_payslip_template', '', 'create')) {
				$message = _l('access_denied');
				echo json_encode(['danger' => false, 'message' => $message]);
				die;
			}

			$id = $data['id'];
			unset($data['id']);
			$success = $this->hr_payroll_model->update_payslip_templates_detail($data, $id);

			if ($success == true) {
				$message = _l('payslip_template') . ' ' . _l('updated_successfully');
				$file_excel = $this->hr_payroll_model->get_hrp_payslip_templates($id);
				echo json_encode(['success' => true, 'message' => $message, 'name_excel' => $file_excel->templates_name]);
				die;
			} else {
				$message = _l('payslip_template') . ' ' . _l('updated_failed');
				echo json_encode(['success' => false, 'message' => $message]);
				die;
			}

		}

		if ($id != '') {
			$data['id'] = $id;
			$data['file_excel'] = $this->hr_payroll_model->get_hrp_payslip_templates($data['id']);
			$data['data_form'] = $data['file_excel']->payslip_template_data;

		}
		if (has_permission('hrp_payslip_template', '', 'create') || has_permission('hrp_payslip_template', '', 'edit')) {

			$permission_actions = '<button id="luckysheet_info_detail_save" class="BTNSS btn btn-info luckysheet_info_detail_save pull-right">Save</button><a id="luckysheet_info_detail_export" class="btn btn-info luckysheet_info_detail_export pull-right"> Download</a><a href="' . admin_url() . 'hr_payroll/payslip_templates_manage' . '" class="btn mright5 btn-default pull-right" >Back</a>';
		} else {
			$permission_actions = '<a id="luckysheet_info_detail_export" class="btn btn-info luckysheet_info_detail_export pull-right"> Download</a><a href="' . admin_url() . 'hr_payroll/payslip_templates_manage' . '" class="btn mright5 btn-default pull-right" >Back</a>';
		}

		$data['permission_actions'] = $permission_actions;

		$data['title'] = _l('view_payslip_templates_detail');

		$this->load->view('payslip_templates/add_payslip_template', $data);

	}

	/**
	 * view payslip
	 * @param  string $id
	 * @return [type]
	 */
	public function view_payslip_detail($id = "") {

		if (!is_admin() && !has_permission('hrp_payslip', '', 'view')) {
			access_denied('view_payslip');
		}

		$data_form = $this->input->post();

		if ($this->input->post()) {
			$data = $this->input->post();

			if (!is_admin() && !has_permission('hrp_payslip', '', 'edit') && !has_permission('hrp_payslip', '', 'create')) {
				$message = _l('access_denied');
				echo json_encode(['danger' => false, 'message' => $message]);
				die;
			}
			$id = $data['id'];
			unset($data['id']);
			$success = $this->hr_payroll_model->update_payslip($data, $id);
			if ($success == true) {
				$message = _l('payslip_template') . ' ' . _l('updated_successfully');
				echo json_encode(['success' => true, 'message' => $message]);
				die;
			} else {
				$message = _l('payslip_template') . ' ' . _l('updated_failed');
				echo json_encode(['success' => false, 'message' => $message]);
				die;
			}

		}

		if ($id != '') {
			$data['id'] = $id;
			$payslip = $this->hr_payroll_model->get_hrp_payslip($data['id']);

			$data['payslip'] = $payslip;

			$path = HR_PAYROLL_PAYSLIP_FILE . $payslip->file_name;
			$mystring = file_get_contents($path, true);

			//$data['data_form'] = replace_spreadsheet_value($mystring);
			$data['data_form'] = $mystring;

		}

		if (has_permission('hrp_payslip', '', 'create') || has_permission('hrp_payslip', '', 'edit')) {
			$permission_actions = '<button id="save_data" class="btn mright5 btn-primary pull-right luckysheet_info_detail_save" >Save</button><a href="#" class="btn mright5 btn-success pull-right payslip_download hide" >Download</a><button  class="btn mright5 btn-info pull-right luckysheet_info_detail_exports ">Create file</button><button id="payslip_close" class="btn mright5 btn-warning pull-right luckysheet_info_detail_payslip_close" >Payslip closing</button><a href="' . admin_url() . 'hr_payroll/payslip_manage' . '" class="btn mright5 btn-default pull-right" >Back</a>';
		} else {
			$permission_actions = '<a href="#" class="btn mright5 btn-success pull-right payslip_download hide" >Download</a><button  class="btn mright5 btn-info pull-right luckysheet_info_detail_exports ">Create file</button><a href="' . admin_url() . 'hr_payroll/payslip_manage' . '" class="btn mright5 btn-default pull-right" >Back</a>';
		}
		$data['permission_actions'] = $permission_actions;

		$data['title'] = _l('payslip_detail');

		$this->load->view('payslips/payslip', $data);

	}

	/**
	 * view payslip detail v2
	 * @param  string $id
	 * @return [type]
	 */
	public function view_payslip_detail_v2($id = "") {
		if (!is_admin() && !has_permission('hrp_payslip', '', 'view_own')) {
			access_denied('view_payslip');
		}

		$data_form = $this->input->post();

		if ($this->input->post()) {
			$data = $this->input->post();

			if (!is_admin() && !has_permission('hrp_payslip', '', 'edit') && !has_permission('hrp_payslip', '', 'create')) {
				$message = _l('access_denied');
				echo json_encode(['danger' => false, 'message' => $message]);
				die;
			}
			$id = $data['id'];
			unset($data['id']);
			$success = $this->hr_payroll_model->update_payslip($data, $id);
			if ($success == true) {
				$message = _l('payslip_template') . ' ' . _l('updated_successfully');
				echo json_encode(['success' => true, 'message' => $message]);
				die;
			} else {
				$message = _l('payslip_template') . ' ' . _l('updated_failed');
				echo json_encode(['success' => false, 'message' => $message]);
				die;
			}

		}

		if ($id != '') {

			$data['id'] = $id;
			$payslip = $this->hr_payroll_model->get_hrp_payslip($data['id']);

			$data['payslip'] = $payslip;

			$path = HR_PAYROLL_PAYSLIP_FILE . $payslip->file_name;
			$mystring = file_get_contents($path, true);

			//remove employees not under management
			$mystring = $this->hr_payroll_model->remove_employees_not_under_management_on_payslip($mystring);

			//$data['data_form'] = replace_spreadsheet_value($mystring);
			$data['data_form'] = $mystring;

		}

		$permission_actions = '<a href="#" class="btn mright5 btn-success pull-right payslip_download hide" >Download</a><button  class="btn mright5 btn-info pull-right luckysheet_info_detail_exports ">Create file</button><a href="' . admin_url() . 'hr_payroll/payslip_manage' . '" class="btn mright5 btn-default pull-right" >Back</a>';
		$data['permission_actions'] = $permission_actions;

		$data['title'] = _l('view_payslip');

		$this->load->view('payslips/payslip_view_own', $data);

	}

	/**
	 * manage bonus
	 * @return [type]
	 */
	public function manage_bonus() {
		if (!has_permission('hrp_bonus_kpi', '', 'view') && !has_permission('hrp_bonus_kpi', '', 'view_own') && !is_admin()) {
			access_denied('hrp_bonus_kpi');
		}

		$this->load->model('staff_model');
		$this->load->model('departments_model');

		/*bonus commodity fill*/
		//get current month
		$current_month = date('Y-m');

		/*bonus commodity fill*/

		/*bonus Kpi*/
		//get current month

		//load staff
		if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		$data_object_kpi = [];
		$bonus_status = true;

		foreach ($staffs as $staff_key => $staff_value) {
			/*check value from database*/
			$data_object_kpi[$staff_key]['staffid'] = $staff_value['staffid'];

			$staff_i = $this->hr_payroll_model->get_staff_info($staff_value['staffid']);
			if ($staff_i) {

				if (isset($staff_i->staff_identifi)) {
					$data_object_kpi[$staff_key]['hr_code'] = $staff_i->staff_identifi;
				} else {
					$data_object_kpi[$staff_key]['hr_code'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_i->staffid, 5);
				}

				$data_object_kpi[$staff_key]['staff_name'] = $staff_i->firstname . ' ' . $staff_i->lastname;

				$data_object_kpi[$staff_key]['job_position'] = '';

				$arr_department = $this->hr_payroll_model->get_staff_departments($staff_i->staffid, true);

				$list_department = '';
				if (count($arr_department) > 0) {

					foreach ($arr_department as $key => $department) {
						$department_value = $this->departments_model->get($department);

						if ($department_value) {
							if (strlen($list_department) != 0) {
								$list_department .= ', ' . $department_value->name;
							} else {
								$list_department .= $department_value->name;
							}
						}

					}
				}

				$data_object_kpi[$staff_key]['staff_departments'] = $list_department;

			} else {
				$data_object_kpi[$staff_key]['hr_code'] = '';
				$data_object_kpi[$staff_key]['staff_name'] = '';
				$data_object_kpi[$staff_key]['job_position'] = $staff_value['staffid'];
				$data_object_kpi[$staff_key]['staff_departments'] = '';

			}

			//get_data from hrm_allowance_commodity_fill
			$bonus_kpi = $this->hr_payroll_model->get_bonus_by_month($staff_value['staffid'], $current_month);
			if ($bonus_kpi) {

				$data_object_kpi[$staff_key]['bonus_kpi'] = $bonus_kpi->bonus_kpi;

			} else {
				$data_object_kpi[$staff_key]['bonus_kpi'] = 0;
				$bonus_status = false;
			}

		}

		/*bonus Kpi*/
		//check is add new or update data
		if ($bonus_status == true) {
			$data['button_name'] = _l('hrp_update');
		} else {
			$data['button_name'] = _l('submit');
		}

		$data['departments'] = $this->departments_model->get();
		$data['staffs_li'] = $this->staff_model->get();
		$data['roles'] = $this->roles_model->get();
		$data['staffs'] = $staffs;
		$data['data_object_kpi'] = $data_object_kpi;

		$this->load->view('bonus/bonus_kpi', $data);
	}

	/**
	 * add bonus kpi
	 * @return redirect
	 */
	public function add_bonus_kpi() {
		if (!has_permission('hrp_bonus_kpi', '', 'view') && !has_permission('hrp_bonus_kpi', '', 'edit') && !is_admin()) {
			access_denied('hrp_bonus_kpi');
		}
		if ($this->input->post()) {
			$data = $this->input->post();

			if (isset($data)) {

				$success = $this->hr_payroll_model->add_bonus_kpi($data);

				if ($success) {
					set_alert('success', _l('hrp_updated_successfully'));
				} else {
					set_alert('warning', _l('hrp_updated_failed'));
				}
				redirect(admin_url('hr_payroll/manage_bonus'));
			}

		}
	}

	/**
	 * bonus kpi filter
	 * @return array
	 */
	public function bonus_kpi_filter() {
		$this->load->model('departments_model');
		$data = $this->input->post();

		$months_filter = $data['month'];
		$year = date('Y', strtotime(($data['month'] . '-01')));
		$g_month = date('m', strtotime(($data['month'] . '-01')));

		$querystring = ' active=1';

		$department = $data['department'];

		$staff = '';
		if (isset($data['staff'])) {
			$staff = $data['staff'];
		}
		$staff_querystring = '';
		$department_querystring = '';
		$month_year_querystring = '';
		$month = date('m');
		$month_year = date('Y');
		$cmonth = date('m');
		$cyear = date('Y');

		if ($year != '') {
			$month_new = (string) $g_month;
			if (strlen($month_new) == 1) {
				$month_new = '0' . $month_new;
			}
			$month = $month_new;
			$month_year = (int) $year;

		}

		if ($department != '') {
			$arrdepartment = $this->staff_model->get('', 'staffid in (select tblstaff_departments.staffid from tblstaff_departments where departmentid = ' . $department . ')');
			$temp = '';
			foreach ($arrdepartment as $value) {
				$temp = $temp . $value['staffid'] . ',';
			}
			$temp = rtrim($temp, ",");
			$department_querystring = 'FIND_IN_SET(staffid, "' . $temp . '")';
		}

		if ($staff != '') {
			$temp = '';
			$araylengh = count($staff);
			for ($i = 0; $i < $araylengh; $i++) {
				$temp = $temp . $staff[$i];
				if ($i != $araylengh - 1) {
					$temp = $temp . ',';
				}
			}
			$staff_querystring = 'FIND_IN_SET(staffid, "' . $temp . '")';
		}

		$arrQuery = array($staff_querystring, $department_querystring, $month_year_querystring, $querystring);

		$newquerystring = '';
		foreach ($arrQuery as $string) {
			if ($string != '') {
				$newquerystring = $newquerystring . $string . ' AND ';
			}
		}

		$newquerystring = rtrim($newquerystring, "AND ");
		if ($newquerystring == '') {
			$newquerystring = [];
		}

		// data return
		$data_object = [];
		$index_data_object = 0;
		$bonus_status = true;

		if ($newquerystring != '') {

			//load staff
			if (!is_admin() && !has_permission('hrp_employee', '', 'view')) {
				//View own
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission($newquerystring));
			} else {
				//admin or view global
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object($newquerystring);
			}

			foreach ($staffs as $staffs_key => $staff_value) {

				$bonus_value = $this->hr_payroll_model->get_bonus_by_month($staff_value['staffid'], $months_filter);

				if ($bonus_value) {

					$data_object[$index_data_object]['staffid'] = $staff_value['staffid'];

					$data_object[$index_data_object]['hr_code'] = $staff_value['staff_identifi'];
					$data_object[$index_data_object]['staff_name'] = $staff_value['full_name'];

					$data_object[$index_data_object]['job_position'] = '';

					$data_object[$index_data_object]['bonus_kpi'] = $bonus_value->bonus_kpi;

				} else {
					$data_object[$index_data_object]['staffid'] = $staff_value['staffid'];

					$data_object[$index_data_object]['hr_code'] = $staff_value['staff_identifi'];
					$data_object[$index_data_object]['staff_name'] = $staff_value['full_name'];

					$data_object[$index_data_object]['job_position'] = '';

					$data_object[$index_data_object]['bonus_kpi'] = 0;

					$bonus_status = false;

				}

				$arr_department = $this->hr_payroll_model->get_staff_departments($staff_value['staffid'], true);

				$list_department = '';
				if (count($arr_department) > 0) {

					foreach ($arr_department as $key => $department) {
						$department_value = $this->departments_model->get($department);

						if ($department_value) {
							if (strlen($list_department) != 0) {
								$list_department .= ', ' . $department_value->name;
							} else {
								$list_department .= $department_value->name;
							}
						}

					}
				}

				$data_object[$index_data_object]['staff_departments'] = $list_department;

				$index_data_object++;

			}

		}

		//check is add new or update data
		if ($bonus_status == true) {
			$button_name = _l('hrp_update');
		} else {
			$button_name = _l('submit');
		}

		echo json_encode([
			'data_object' => $data_object,
			'button_name' => $button_name,
		]);
		die;
	}

	/**
	 * payslip
	 * @param  string $value
	 * @return [type]
	 */
	public function payslip($value = '') {
		if ($this->input->post()) {
			$data = $this->input->post();

			if (!$this->input->post('id')) {

				if (!is_admin() && !has_permission('hrp_payslip', '', 'create')) {
					access_denied('hrp_payslip');
				}

				$insert_id = $this->hr_payroll_model->add_payslip($data);
				if ($insert_id) {
					$message = _l('added_successfully', _l('hrp_payslip'));
					set_alert('success', $message);
				}
				redirect(admin_url('hr_payroll/payslip_manage'));

			}

		}
	}

	/**
	 * payslip closing
	 * @return [type]
	 */
	public function payslip_closing() {
		if (!has_permission('hrp_payslip', '', 'edit') && !is_admin()) {
			$message = _l('access_denied');
			echo json_encode(['danger' => false, 'message' => $message]);
			die;
		}
		if ($this->input->post()) {
			$data = $this->input->post();

			$hrp_payslip = $this->hr_payroll_model->get_hrp_payslip($data['id']);

			if ($hrp_payslip) {
				$payslip_checked = $this->hr_payroll_model->payslip_checked($hrp_payslip->payslip_month, $hrp_payslip->payslip_template_id, true);
				if ($payslip_checked) {

					$result = $this->hr_payroll_model->payslip_close($data);
					if ($result == true) {
						$message = _l('hrp_updated_successfully');
						$status = true;
					} else {
						$message = _l('hrp_updated_failed');
						$status = false;
					}
				} else {
					$status = false;
					$message = _l('payslip_for_the_month_of');
				}

			} else {
				$message = _l('hrp_updated_failed');
				$status = false;
			}

			echo json_encode([
				'message' => $message,
				'status' => $status,
			]);
		}
	}

	/**
	 * payslip update status
	 * @param  [type] $id
	 * @return [type]
	 */
	public function payslip_update_status($id) {
		if (!is_admin() && !has_permission('hrp_payslip', '', 'udpate')) {
			access_denied('hrp_payslip');
		}

		$result = $this->hr_payroll_model->update_payslip_status($id, 'payslip_opening');
		if ($result) {
			set_alert('success', _l('hrp_updated_successfully'));
		} else {
			set_alert('warning', _l('hrp_updated_failed'));
		}
		redirect(admin_url('hr_payroll/payslip_manage'));
	}

	/**
	 * table staff payslip
	 * @return [type]
	 */
	public function table_staff_payslip() {
		$this->app->get_table_data(module_views_path('hr_payroll', 'employee_payslip/table_staff_payslip'));
	}

	/**
	 * view staff payslip modal
	 * @return [type]
	 */
	public function view_staff_payslip_modal() {
		if (!$this->input->is_ajax_request()) {
			show_404();
		}

		$this->load->model('departments_model');

		if ($this->input->post('slug') === 'view') {
			$payslip_detail_id = $this->input->post('payslip_detail_id');

			$data['payslip_detail'] = $this->hr_payroll_model->get_payslip_detail($payslip_detail_id);

			$arr_department = $this->hr_payroll_model->get_staff_departments($data['payslip_detail']->staff_id, true);
			$list_department = '';
			if (count($arr_department) > 0) {

				foreach ($arr_department as $key => $department) {
					$department_value = $this->departments_model->get($department);

					if ($department_value) {
						if (strlen($list_department) != 0) {
							$list_department .= ', ' . $department_value->name;
						} else {
							$list_department .= $department_value->name;
						}
					}
				}
			}

			$employee = $this->hr_payroll_model->get_employees_data($data['payslip_detail']->month, '', ' staff_id = ' . $data['payslip_detail']->staff_id);

			$data['employee'] = count($employee) > 0 ? $employee[0] : [];
			$data['list_department'] = $list_department;

			$this->load->view('employee_payslip/staff_payslip_modal_view', $data);
		}
	}

	/**
	 * reports
	 * @return [type]
	 */
	public function reports() {
		if (!has_permission('hrp_report', '', 'view') && !is_admin()) {
			access_denied('reports');
		}

		$this->load->model('staff_model');
		$this->load->model('departments_model');

		$data['mysqlVersion'] = $this->db->query('SELECT VERSION() as version')->row();
		$data['sqlMode'] = $this->db->query('SELECT @@sql_mode as mode')->row();
		// $data['position']     = $this->hr_profile_model->get_job_position();
		$data['staff'] = $this->staff_model->get();
		$data['department'] = $this->departments_model->get();
		$data['title'] = _l('hr_reports');

		$this->load->view('reports/manage_reports', $data);
	}

	/**
	 * payslip report
	 * @return [type]
	 */
	public function payslip_report() {
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {

				$months_report = $this->input->post('months_filter');
				$position_filter = $this->input->post('position_filter');
				$department_filter = $this->input->post('department_filter');
				$staff_filter = $this->input->post('staff_filter');

				if ($months_report == 'this_month') {
					$from_date = date('Y-m-01');
					$to_date = date('Y-m-t');
				}
				if ($months_report == '1') {
					$from_date = date('Y-m-01', strtotime('first day of last month'));
					$to_date = date('Y-m-t', strtotime('last day of last month'));
				}
				if ($months_report == 'this_year') {
					$from_date = date('Y-m-d', strtotime(date('Y-01-01')));
					$to_date = date('Y-m-d', strtotime(date('Y-12-31')));
				}
				if ($months_report == 'last_year') {
					$from_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
					$to_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
				}

				if ($months_report == '3') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');
				}
				if ($months_report == '6') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');

				}
				if ($months_report == '12') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');

				}
				if ($months_report == 'custom') {
					$from_date = to_sql_date($this->input->post('report_from'));
					$to_date = to_sql_date($this->input->post('report_to'));
				}

				$select = [
					'month',
					'pay_slip_number',
					'employee_name',
					'gross_pay',
					'total_deductions',
					'income_tax_paye',
					'it_rebate_value',
					'commission_amount',
					'bonus_kpi',
					'total_insurance',
					'net_pay',
					'total_cost',
				];
				$query = '';

				if (isset($from_date) && isset($to_date)) {

					$query = ' month >= \'' . $from_date . '\' and month <= \'' . $to_date . '\' and ';
				} else {
					$query = '';
				}

				if (isset($staff_filter)) {
					$staffid_list = implode(',', $staff_filter);
					$query .= db_prefix() . 'hrp_payslip_details.staff_id in (' . $staffid_list . ') and ';
				}
				if (isset($department_filter)) {
					$department_list = implode(',', $department_filter);
					$query .= db_prefix() . 'hrp_payslip_details.staff_id in (SELECT staffid FROM ' . db_prefix() . 'staff_departments where departmentid in (' . $department_list . ')) and ';
				}

				$query .= db_prefix() . 'hrp_payslips.payslip_status = "payslip_closing" and ';

				$total_query = '';
				if (($query) && ($query != '')) {
					$total_query = rtrim($query, ' and');
					$total_query = ' where ' . $total_query;
				}

				$where = [$total_query];

				$aColumns = $select;
				$sIndexColumn = 'id';
				$sTable = db_prefix() . 'hrp_payslip_details';
				$join = [
					'LEFT JOIN ' . db_prefix() . 'hrp_payslips ON ' . db_prefix() . 'hrp_payslip_details.payslip_id = ' . db_prefix() . 'hrp_payslips.id',
				];

				$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
					db_prefix() . 'hrp_payslip_details.id',
					db_prefix() . 'hrp_payslip_details.month',
				]);

				$output = $result['output'];
				$rResult = $result['rResult'];
				foreach ($rResult as $aRow) {
					$row = [];

					$row[] = $aRow['id'];
					$row[] = $aRow['month'];
					$row[] = $aRow['pay_slip_number'];
					$row[] = $aRow['employee_name'];
					$row[] = app_format_money($aRow['gross_pay'], '');
					$row[] = app_format_money($aRow['total_deductions'], '');
					$row[] = app_format_money($aRow['income_tax_paye'], '');
					$row[] = app_format_money($aRow['it_rebate_value'], '');
					$row[] = app_format_money($aRow['commission_amount'], '');
					$row[] = app_format_money($aRow['bonus_kpi'], '');
					$row[] = app_format_money($aRow['total_insurance'], '');
					$row[] = app_format_money($aRow['net_pay'], '');
					$row[] = app_format_money($aRow['total_cost'], '');

					$output['aaData'][] = $row;
				}

				echo json_encode($output);
				die();
			}
		}
	}

	/**
	 * income summary report
	 * @return [type]
	 */
	public function income_summary_report() {
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$this->load->model('departments_model');

				$months_report = $this->input->post('months_filter');
				$position_filter = $this->input->post('position_filter');
				$department_filter = $this->input->post('department_filter');
				$staff_filter = $this->input->post('staff_filter');

				if ($months_report == 'this_month') {
					$from_date = date('Y-m-01');
					$to_date = date('Y-m-t');
				}
				if ($months_report == '1') {
					$from_date = date('Y-m-01', strtotime('first day of last month'));
					$to_date = date('Y-m-t', strtotime('last day of last month'));
				}
				if ($months_report == 'this_year') {
					$from_date = date('Y-m-d', strtotime(date('Y-01-01')));
					$to_date = date('Y-m-d', strtotime(date('Y-12-31')));
				}
				if ($months_report == 'last_year') {
					$from_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
					$to_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
				}

				if ($months_report == '3') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');
				}
				if ($months_report == '6') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');

				}
				if ($months_report == '12') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');

				}
				if ($months_report == 'custom') {
					$from_date = to_sql_date($this->input->post('report_from'));
					$to_date = to_sql_date($this->input->post('report_to'));
				}

				$select = [
					'staffid',

				];
				$query = '';
				$staff_query = '';

				if (isset($from_date) && isset($to_date)) {

					$staff_query = ' month >= \'' . $from_date . '\' and month <= \'' . $to_date . '\' and ';
				} else {
					$staff_query = '';
				}

				if (isset($staff_filter)) {
					$staffid_list = implode(',', $staff_filter);
					$query .= db_prefix() . 'staff.staffid in (' . $staffid_list . ') and ';

					$staff_query .= db_prefix() . 'hrp_payslip_details.staff_id in (' . $staffid_list . ') and ';
				}

				if (isset($department_filter)) {
					$department_list = implode(',', $department_filter);
					$query .= db_prefix() . 'staff.staffid in (SELECT staffid FROM ' . db_prefix() . 'staff_departments where departmentid in (' . $department_list . ')) and ';

					$staff_query .= db_prefix() . 'hrp_payslip_details.staff_id in (SELECT staffid FROM ' . db_prefix() . 'staff_departments where departmentid in (' . $department_list . ')) and ';
				}

				$query .= db_prefix() . 'staff.active = "1" and ';

				$total_query = '';
				$staff_query_trim = '';
				if (($query) && ($query != '')) {
					$total_query = rtrim($query, ' and');
					$total_query = ' where ' . $total_query;
				}

				if (($staff_query) && ($staff_query != '')) {
					$staff_query_trim = rtrim($staff_query, ' and');

				}
				$where = [$total_query];

				$aColumns = $select;
				$sIndexColumn = 'staffid';
				$sTable = db_prefix() . 'staff';
				$join = [];

				$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['firstname', 'lastname']);

				$output = $result['output'];
				$rResult = $result['rResult'];
				$rel_type = hrp_get_hr_profile_status();
				$staff_income = $this->hr_payroll_model->get_income_summary_report($staff_query_trim);

				$staffs_data = [];
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
				foreach ($staffs as $value) {
					$staffs_data[$value['staffid']] = $value;
				}

				$temp = 0;
				foreach ($rResult as $staff_key => $aRow) {
					$row = [];

					$arr_department = $this->hr_payroll_model->get_staff_departments($aRow['staffid'], true);

					$list_department = '';
					if (count($arr_department) > 0) {

						foreach ($arr_department as $key => $department) {
							$department_value = $this->departments_model->get($department);

							if ($department_value) {
								if (strlen($list_department) != 0) {
									$list_department .= ', ' . $department_value->name;
								} else {
									$list_department .= $department_value->name;
								}
							}
						}
					}

					$data_object_kpi[$staff_key]['department_name'] = $list_department;

					if ($rel_type == 'hr_records') {
						if (isset($staffs_data[$aRow['staffid']])) {
							$row[] = $staffs_data[$aRow['staffid']]['staff_identifi'];
						} else {
							$row[] = '';
						}
					} else {
						$row[] = $this->hr_payroll_model->hrp_format_code('EXS', $aRow['staffid'], 5);
					}

					$row[] = $aRow['firstname'] . ' ' . $aRow['lastname'];

					$row[] = $list_department;

					if (isset($staff_income[$aRow['staffid']]['01'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['01'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if (isset($staff_income[$aRow['staffid']]['02'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['02'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if (isset($staff_income[$aRow['staffid']]['03'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['03'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if (isset($staff_income[$aRow['staffid']]['04'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['04'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if (isset($staff_income[$aRow['staffid']]['05'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['05'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if (isset($staff_income[$aRow['staffid']]['06'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['06'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if (isset($staff_income[$aRow['staffid']]['07'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['07'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if (isset($staff_income[$aRow['staffid']]['08'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['08'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if (isset($staff_income[$aRow['staffid']]['09'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['09'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if (isset($staff_income[$aRow['staffid']]['10'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['10'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if (isset($staff_income[$aRow['staffid']]['11'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['11'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if (isset($staff_income[$aRow['staffid']]['12'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['12'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if ($temp != 0) {
						if (isset($staff_income[$aRow['staffid']]['average_income'])) {

							$row[] = app_format_money($staff_income[$aRow['staffid']]['average_income'] / $temp, '');
						} else {
							$row[] = 0;
						}
					} else {
						$row[] = 0;
					}

					$temp = 0;
					$output['aaData'][] = $row;
				}

				echo json_encode($output);
				die();

			}
		}
	}

	/**
	 * insurance cost summary report
	 * @return [type]
	 */
	public function insurance_cost_summary_report() {
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$this->load->model('departments_model');

				$months_report = $this->input->post('months_filter');
				$position_filter = $this->input->post('position_filter');
				$department_filter = $this->input->post('department_filter');
				$staff_filter = $this->input->post('staff_filter');

				if ($months_report == 'this_month') {
					$from_date = date('Y-m-01');
					$to_date = date('Y-m-t');
				}
				if ($months_report == '1') {
					$from_date = date('Y-m-01', strtotime('first day of last month'));
					$to_date = date('Y-m-t', strtotime('last day of last month'));
				}
				if ($months_report == 'this_year') {
					$from_date = date('Y-m-d', strtotime(date('Y-01-01')));
					$to_date = date('Y-m-d', strtotime(date('Y-12-31')));
				}
				if ($months_report == 'last_year') {
					$from_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
					$to_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
				}

				if ($months_report == '3') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');
				}
				if ($months_report == '6') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');

				}
				if ($months_report == '12') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');

				}
				if ($months_report == 'custom') {
					$from_date = to_sql_date($this->input->post('report_from'));
					$to_date = to_sql_date($this->input->post('report_to'));
				}

				$select = [
					'departmentid',

				];
				$query = '';
				$staff_query = '';

				if (isset($from_date) && isset($to_date)) {

					$staff_query = ' month >= \'' . $from_date . '\' and month <= \'' . $to_date . '\' and ';
				} else {
					$staff_query = '';
				}

				if (isset($staff_filter)) {
					$staffid_list = implode(',', $staff_filter);
					$query .= db_prefix() . 'staff.staffid in (' . $staffid_list . ') and ';

					$staff_query .= db_prefix() . 'hrp_payslip_details.staff_id in (' . $staffid_list . ') and ';
				}

				if (isset($department_filter)) {
					$department_list = implode(',', $department_filter);
					$query .= db_prefix() . 'departments.departmentid in  (' . $department_list . ') and ';

					$staff_query .= db_prefix() . 'hrp_payslip_details.staff_id in (SELECT staffid FROM ' . db_prefix() . 'staff_departments where departmentid in (' . $department_list . ')) and ';
				}

				$total_query = '';
				$staff_query_trim = '';
				if (($query) && ($query != '')) {
					$total_query = rtrim($query, ' and');
					$total_query = ' where ' . $total_query;
				}

				if (($staff_query) && ($staff_query != '')) {
					$staff_query_trim = rtrim($staff_query, ' and');

				}

				$where = [$total_query];

				$aColumns = $select;
				$sIndexColumn = 'departmentid';
				$sTable = db_prefix() . 'departments';
				$join = [];

				$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['name']);

				$output = $result['output'];
				$rResult = $result['rResult'];
				$rel_type = hrp_get_hr_profile_status();

				$staff_insurance = $this->hr_payroll_model->get_insurance_summary_report($staff_query_trim);

				$temp_insurance = 0;
				foreach ($rResult as $der_key => $aRow) {
					$row = [];

					$row[] = $aRow['name'];

					$staff_ids = $this->hr_payroll_model->get_staff_in_deparment($aRow['departmentid']);

					foreach ($staff_ids as $key => $value) {
						if (isset($staff_insurance[$value])) {
							$temp_insurance += $staff_insurance[$value];
						}
					}

					$row[] = $temp_insurance;
					$temp_insurance = 0;

					$output['aaData'][] = $row;
				}

				echo json_encode($output);
				die();

			}
		}
	}

	/**
	 * payslip chart
	 * @return [type]
	 */
	public function payslip_chart() {
		if ($this->input->is_ajax_request()) {

			$months_report = $this->input->post('months_filter');
			$staff_id = $this->input->post('staff_id');
			$filter_by_year = '';

			$filter_by_year .= 'date_format(month, "%Y") = ' . $months_report;

			echo json_encode($this->hr_payroll_model->payslip_chart($filter_by_year, $staff_id));
		}
	}

	/**
	 * department payslip chart
	 * @return [type]
	 */
	public function department_payslip_chart() {
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$months_report = $this->input->post('months_filter');
				$department_filter = $this->input->post('department_filter');

				$from_date = date('Y-m-d', strtotime('1997-01-01'));
				$to_date = date('Y-m-d', strtotime(date('Y-12-31')));
				if ($months_report == 'this_month') {

					$from_date = date('Y-m-01');
					$to_date = date('Y-m-t');
				}
				if ($months_report == '1') {
					$from_date = date('Y-m-01', strtotime('first day of last month'));
					$to_date = date('Y-m-t', strtotime('last day of last month'));

				}
				if ($months_report == 'this_year') {
					$from_date = date('Y-m-d', strtotime(date('Y-01-01')));
					$to_date = date('Y-m-d', strtotime(date('Y-12-31')));
				}
				if ($months_report == 'last_year') {
					$from_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
					$to_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
				}

				if ($months_report == '3') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');

				}
				if ($months_report == '6') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');

				}
				if ($months_report == '12') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');

				}
				if ($months_report == 'custom') {
					$from_date = to_sql_date($this->input->post('report_from'));
					$to_date = to_sql_date($this->input->post('report_to'));
				}

				$id_department = '';
				if (isset($department_filter)) {
					$id_department = implode(',', $department_filter);
				}
				$circle_mode = false;
				$list_diploma = array(
					"ps_total_insurance",
					"ps_income_tax_paye",
					"ps_total_deductions",
					"ps_net_pay",
				);
				$list_result = array();
				$list_data_department = [];

				$staff_payslip = $this->hr_payroll_model->get_department_payslip_chart($from_date, $to_date);
				$base_currency = get_base_currency();

				$current_name = '';
				if ($base_currency) {
					$current_name .= $base_currency->name;
				}

				echo json_encode([
					'department' => $staff_payslip['department_name'],
					'data_result' => $staff_payslip['list_result'],
					'circle_mode' => $circle_mode,
					'current_name' => $current_name,
				]);
				die;
			}
		}
	}

	/**
	 * payslip template checked
	 * @return [type]
	 */
	public function payslip_template_checked() {
		$data = $this->input->post();
		if ($this->input->is_ajax_request()) {
			$payslip_template_checked = $this->hr_payroll_model->payslip_template_checked($data);

			if ($payslip_template_checked === true) {
				$status = true;
			} else {
				$status = false;
			}

			echo json_encode([
				'status' => $status,
				'staff_name' => $payslip_template_checked,
			]);
		}
	}

	/**
	 * payslip checked
	 * @return [type]
	 */
	public function payslip_checked() {
		$data = $this->input->post();
		if ($this->input->is_ajax_request()) {
			$payslip_checked = $this->hr_payroll_model->payslip_checked($data['payslip_month'], $data['payslip_template_id']);

			if ($payslip_checked) {
				$status = true;
				$message = '';
			} else {
				$status = false;
				$message = _l('payslip_for_the_month_of');
			}

			echo json_encode([
				'status' => $status,
				'message' => $message,
			]);
		}
	}

	/**
	 * create payslip file
	 * @return [type]
	 */
	public function create_payslip_file() {

		$data = $this->input->post();
		$get_data = $this->hr_payroll_model->payslip_download($data);
		if ($get_data) {

			if (!class_exists('XLSXReader_fin')) {
				require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php';
			}
			require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php';

			$this->delete_error_file_day_before('1', HR_PAYROLL_CREATE_PAYSLIP_EXCEL);

			$payroll_system_columns_dont_format = payroll_system_columns_dont_format();

			//Writer file
			$writer_header = [];
			$widths = [];
			$col_style1 = [];

			$payroll_column_key = $get_data['payroll_column_key'];
			foreach ($get_data['payroll_header'] as $key => $value) {
				if (!in_array($payroll_column_key[$key], $payroll_system_columns_dont_format)) {

					$writer_header[$value] = '#,##0.00';
				} else {
					$writer_header[$value] = 'string';

				}
				$widths[] = 30;
				$col_style1[] = $key;
			}

			$writer = new XLSXWriter();

			$style1 = ['widths' => $widths, 'fill' => '#ff9800', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13];

			$writer->writeSheetHeader_v2('Sheet1', $writer_header, $col_options = ['widths' => $widths, 'fill' => '#03a9f46b', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13],
				$col_style1, $style1);

			$data_object_kpi = [];
			$writer->writeSheetRow('Sheet1', $get_data['payroll_header']);

			foreach ($get_data['payslip_detail'] as $data_key => $payslip_detail) {

				$writer->writeSheetRow('Sheet1', array_values($payslip_detail));

			}

			$filename = 'Payslip_' . date('Y-m', strtotime($get_data['month'])) . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
			$writer->writeToFile(str_replace($filename, HR_PAYROLL_CREATE_PAYSLIP_EXCEL . $filename, $filename));

			echo json_encode([
				'success' => true,
				'message' => _l('create_a_payslip_for_successful_download'),
				'site_url' => site_url(),
				'staff_id' => get_staff_user_id(),
				'filename' => HR_PAYROLL_CREATE_PAYSLIP_EXCEL . $filename,
			]);
			die;
		}

		echo json_encode([
			'success' => false,
			'message' => _l('an_error_occurred_while_creating_a_payslip_to_download'),
			'site_url' => site_url(),
			'staff_id' => get_staff_user_id(),
			'filename' => HR_PAYROLL_CREATE_PAYSLIP_EXCEL,
		]);
		die;

	}

	/**
	 *employees copy
	 * @return [type]
	 */
	public function employees_copy() {
		if (!has_permission('hrp_employee', '', 'create') && !has_permission('hrp_employee', '', 'edit') && !is_admin()) {
			access_denied('hrp_employee');
		}

		if ($this->input->post()) {
			$data = $this->input->post();
			$results = $this->hr_payroll_model->employees_copy($data);

			if ($results) {
				$message = _l('updated_successfully');
			} else {
				$message = _l('hrp_updated_failed');
			}

			echo json_encode([
				'message' => $results['message'],
				'status' => $results['status'],
			]);
		}

	}

	/**
	 * reset data
	 * @return [type]
	 */
	public function reset_data() {

		if (!is_admin()) {
			access_denied('hr_payroll');
		}
		//delete hrp_employees_value
		$this->db->truncate(db_prefix() . 'hrp_employees_value');

		//delete hrp_employees_timesheets
		$this->db->truncate(db_prefix() . 'hrp_employees_timesheets');

		//delete hrp_commissions
		$this->db->truncate(db_prefix() . 'hrp_commissions');

		//delete hrp_salary_deductions
		$this->db->truncate(db_prefix() . 'hrp_salary_deductions');

		//delete hrp_bonus_kpi
		$this->db->truncate(db_prefix() . 'hrp_bonus_kpi');

		//delete hrp_staff_insurances
		$this->db->truncate(db_prefix() . 'hrp_staff_insurances');

		//delete hrp_payslips
		$this->db->truncate(db_prefix() . 'hrp_payslips');

		//delete hrp_payslip_details
		$this->db->truncate(db_prefix() . 'hrp_payslip_details');

		//delete attendance_sample_file
		foreach (glob('modules/hr_payroll/uploads/attendance_sample_file/' . '*') as $file) {
			$file_arr = explode("/", $file);
			$filename = array_pop($file_arr);

			if (file_exists($file)) {
				//don't delete index.html file
				if ($filename != 'index.html') {
					unlink('modules/hr_payroll/uploads/attendance_sample_file/' . $filename);
				}
			}

		}

		foreach (glob('modules/hr_payroll/uploads/commissions_sample_file/' . '*') as $file) {
			$file_arr = explode("/", $file);
			$filename = array_pop($file_arr);

			if (file_exists($file)) {
				//don't delete index.html file
				if ($filename != 'index.html') {
					unlink('modules/hr_payroll/uploads/commissions_sample_file/' . $filename);
				}
			}

		}

		foreach (glob('modules/hr_payroll/uploads/employees_sample_file/' . '*') as $file) {
			$file_arr = explode("/", $file);
			$filename = array_pop($file_arr);

			if (file_exists($file)) {
				//don't delete index.html file
				if ($filename != 'index.html') {
					unlink('modules/hr_payroll/uploads/employees_sample_file/' . $filename);
				}
			}

		}

		foreach (glob('modules/hr_payroll/uploads/file_error_response/' . '*') as $file) {
			$file_arr = explode("/", $file);
			$filename = array_pop($file_arr);

			if (file_exists($file)) {
				//don't delete index.html file
				if ($filename != 'index.html') {
					unlink('modules/hr_payroll/uploads/file_error_response/' . $filename);
				}
			}

		}

		foreach (glob('modules/hr_payroll/uploads/payslip/' . '*') as $file) {
			$file_arr = explode("/", $file);
			$filename = array_pop($file_arr);

			if (file_exists($file)) {
				//don't delete index.html file
				if ($filename != 'index.html') {
					unlink('modules/hr_payroll/uploads/payslip/' . $filename);
				}
			}

		}

		foreach (glob('modules/hr_payroll/uploads/payslip_excel_file/' . '*') as $file) {
			$file_arr = explode("/", $file);
			$filename = array_pop($file_arr);

			if (file_exists($file)) {
				//don't delete index.html file
				if ($filename != 'index.html') {
					unlink('modules/hr_payroll/uploads/payslip_excel_file/' . $filename);
				}
			}

		}

		set_alert('success', _l('reset_data_successful'));
		redirect(admin_url('hr_payroll/setting?group=reset_data'));

	}

	/**
	 * employee export pdf
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function employee_export_pdf($id) {
		if (!$id) {
			show_404();
		}

		$this->db->where('id', $id);
		$hrp_payslip_details = $this->db->get(db_prefix() . 'hrp_payslip_details')->result_array();

		$data = [];
		$data['payslip_detail'] = $hrp_payslip_details[0];

		$arr_department = $this->hr_payroll_model->get_staff_departments($data['payslip_detail']['staff_id'], true);
		$list_department = '';
		if (count($arr_department) > 0) {

			foreach ($arr_department as $key => $department) {
				$this->load->model('departments_model');

				$department_value = $this->departments_model->get($department);

				if ($department_value) {
					if (strlen($list_department) != 0) {
						$list_department .= ', ' . $department_value->name;
					} else {
						$list_department .= $department_value->name;
					}
				}
			}
		}

		$employee = $this->hr_payroll_model->get_employees_data($data['payslip_detail']['month'], '', ' staff_id = ' . $data['payslip_detail']['staff_id']);
		$data['employee'] = count($employee) > 0 ? $employee[0] : [];
		$data['list_department'] = $list_department;


		$html = $this->load->view('hr_payroll/employee_payslip/export_employee_payslip', $data, true);
		$html .= '<link href="' . module_dir_url(HR_PAYROLL_MODULE_NAME, 'assets/css/export_employee_pdf.css') . '"  rel="stylesheet" type="text/css" />';


		try {
			$pdf = $this->hr_payroll_model->employee_export_pdf($html);

		} catch (Exception $e) {
			echo html_entity_decode($e->getMessage());
			die;
		}

		$type = 'D';

		if ($this->input->get('output_type')) {
			$type = $this->input->get('output_type');
		}

		if ($this->input->get('print')) {
			$type = 'I';
		}

		$pdf->Output($data['payslip_detail']['employee_number'].'_'.date('m-Y', strtotime($data['payslip_detail']['month'])).'_'.strtotime(date('Y-m-d H:i:s')).'.pdf', $type);
	}

	/**
	 * payslip manage export pdf
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function payslip_manage_export_pdf($id)
	{
		if (!$id) {
			show_404();
		}

		$data = $this->input->post();

		//delete sub folder STOCK_EXPORT
		foreach(glob(HR_PAYROLL_EXPORT_EMPLOYEE_PAYSLIP . '*') as $file) { 
			$file_arr = explode("/",$file);
			$filename = array_pop($file_arr);

			if(file_exists($file)) {
				if ($filename != 'index.html') {
					unlink(HR_PAYROLL_EXPORT_EMPLOYEE_PAYSLIP.$filename);
				}
			}
		}

		$payslip = $this->hr_payroll_model->get_hrp_payslip($id);
		$payslip_details = $this->hr_payroll_model->get_payslip_detail_by_payslip_id($id);

		foreach ($payslip_details as $payslip_detail) {

			$data = [];
			$data['payslip_detail'] = $payslip_detail;

			$arr_department = $this->hr_payroll_model->get_staff_departments($payslip_detail['staff_id'], true);
			$list_department = '';
			if (count($arr_department) > 0) {

				foreach ($arr_department as $key => $department) {
					$this->load->model('departments_model');

					$department_value = $this->departments_model->get($department);

					if ($department_value) {
						if (strlen($list_department) != 0) {
							$list_department .= ', ' . $department_value->name;
						} else {
							$list_department .= $department_value->name;
						}
					}
				}
			}

			$employee = $this->hr_payroll_model->get_employees_data($payslip_detail['month'], '', ' staff_id = ' . $payslip_detail['staff_id']);
			$data['employee'] = count($employee) > 0 ? $employee[0] : [];
			$data['list_department'] = $list_department;

			$html = $this->load->view('hr_payroll/employee_payslip/export_employee_payslip', $data, true);
			$html .= '<link href="' . module_dir_url(HR_PAYROLL_MODULE_NAME, 'assets/css/export_employee_pdf.css') . '"  rel="stylesheet" type="text/css" />';


			try {
				$pdf = $this->hr_payroll_model->employee_export_pdf($html);
				
			} catch (Exception $e) {
				echo html_entity_decode($e->getMessage());
				die;
			}

			$this->re_save_to_dir($pdf, $payslip_detail['employee_number'].'_'.date('m-Y', strtotime($payslip_detail['month'])) . '.pdf');
		}

		$this->load->library('zip');

        //get list file
		foreach(glob(HR_PAYROLL_EXPORT_EMPLOYEE_PAYSLIP . '*') as $file) { 
			$file_arr = explode("/",$file);
			$filename = array_pop($file_arr);

			$this->zip->read_file(HR_PAYROLL_EXPORT_EMPLOYEE_PAYSLIP. $filename);
		}

		$this->zip->download($payslip->payslip_name .'_'. date('m-Y', strtotime($payslip->payslip_month)). '.zip');
		$this->zip->clear_data();
	}

	/**
	 * re save to dir
	 * @param  [type] $pdf       
	 * @param  [type] $file_name 
	 * @return [type]            
	 */
	private function re_save_to_dir($pdf, $file_name)
	{
		$dir = HR_PAYROLL_EXPORT_EMPLOYEE_PAYSLIP;

		$dir .= $file_name;

		$pdf->Output($dir, 'F');
	}

//End file
}