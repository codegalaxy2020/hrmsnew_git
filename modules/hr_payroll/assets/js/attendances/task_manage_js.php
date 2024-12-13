<script>
	$(document).ready(function () {
		var fromDate = $(document).find('#from_date').val();
		var toDate = $(document).find('#to_date').val();
		serverSideDataTable('table-task_list', baseUrl + 'hr_payroll/task_list/' + fromDate + '/' + toDate, 10);
	});

	function filterData(fromDate, toDate){
		serverSideDataTable('table-task_list', baseUrl + 'hr_payroll/task_list/' + fromDate + '/' + toDate, 10);
	}
</script>