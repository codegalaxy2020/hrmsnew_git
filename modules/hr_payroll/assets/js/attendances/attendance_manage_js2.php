<script>
	function openAttendanceModal(date = ''){
		ajaxPostRequest('hr_payroll/load_attendance_modal', {'date': date}, function (data) {
			holdModal('attendance_modal');
			$('#attendance_modal_title').text("Attendance of "+date);
			$('#attendance_modal_body').html(data.html);
			staticDataTable('table_attendance_details');
		});
	}
</script>