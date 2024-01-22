<script>
	var Gchart;
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

</script>