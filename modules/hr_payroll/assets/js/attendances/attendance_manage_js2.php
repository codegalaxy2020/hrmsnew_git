<script>
	$(document).ready(function (){
		serverSideDataTable('table-month_attendance', baseUrl + 'hr_payroll/month_attendance_list/'+$('#month_attendance').val()+'/'+$('#staff_attendance').val(), 4);
	});
	function openAttendanceModal(date = ''){
		ajaxPostRequest('hr_payroll/load_attendance_modal', {'date': date}, function (data) {
			holdModal('attendance_modal');
			$('#attendance_modal_title').text("Attendance of "+date);
			$('#attendance_modal_body').html(data.html);
			staticDataTable('table_attendance_details');
		});
	}
</script>