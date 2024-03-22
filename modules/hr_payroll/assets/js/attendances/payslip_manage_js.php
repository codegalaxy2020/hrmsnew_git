<script>
	$(document).ready(function () {
		var month, staff;
		if ($('#month_attendance').val() != undefined) {
			month = $('#month_attendance').val();
		} else {
			month = null;
		}

		if ($('#staff_attendance').val() != undefined) {
			staff = $('#staff_attendance').val();
		} else {
			staff = null;
		}

		serverSideDataTable('table-staff_payslip', baseUrl + 'hr_payroll/month_payslip_list/' + month, 10);
		// generateApexChart('donut', staff, month);
	});

	function filterData(month){
		serverSideDataTable('table-staff_payslip', baseUrl + 'hr_payroll/month_payslip_list/' + month, 10);
	}

	function printPayslip(id){
		ajaxPostRequest('hr_payroll/print_payslip', {'id': id}, function (data) {
			popup(data.html);
		});
	}

	//Added by DEEP BASAK on Febuary 02, 2024
	function paidSalary(id){
		warnMsg2("Are you sure want to Pay this amount?", false, true, "Pay It!", "", function (){
			ajaxPostRequest('hr_payroll/pain_salary', {'id': id}, function (data) {
				SwalSuccess2("Good Job!", data.message, data.status);
				var month;
				if ($('#month_attendance').val() != undefined) {
					month = $('#month_attendance').val();
				} else {
					month = null;
				}
				serverSideDataTable('table-staff_payslip', baseUrl + 'hr_payroll/month_payslip_list/' + month, 10);
			})
		});
	}

	//Added by DEEP BASAK on March 20, 2024
	function calculatePayslip(){
		if ($('#month_attendance').val() != undefined) {
			month = $('#month_attendance').val();
		} else {
			month = null;
		}

		ajaxPostRequest('hr_payroll/calculate_payslip', {'month': month}, function(data) {
			serverSideDataTable('table-staff_payslip', baseUrl + 'hr_payroll/month_payslip_list/' + month, 10);
			SwalSuccess2(data.title, data.message, data.status);
		});
	}

</script>