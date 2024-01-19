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

		serverSideDataTable('table-month_attendance', baseUrl + 'hr_payroll/month_attendance_list/' + month + '/' + staff, 4);
		$("#table-month_attendance_info").hide();
		$("#table-month_attendance_paginate").hide();
		$("#table-month_attendance_filter").hide();
		generateApexChart('donut', staff, month);
	});

	function openAttendanceModal(date = '') {
		ajaxPostRequest('hr_payroll/load_attendance_modal', { 'date': date }, function (data) {
			holdModal('attendance_modal');
			$('#attendance_modal_title').text("Attendance of " + date);
			$('#attendance_modal_body').html(data.html);
			staticDataTable('table_attendance_details');
			$("#table_attendance_details_info").hide();
			$("#table_attendance_details_paginate").hide();
			$("#table_attendance_details_filter").hide();
		});
	}

	function filterData(month, staff){
		generateApexChart('donut', staff, month);
		serverSideDataTable('table-month_attendance', baseUrl + 'hr_payroll/month_attendance_list/'+month+'/'+staff, 4);
		$("#table-month_attendance_info").hide();
		$("#table-month_attendance_paginate").hide();
		$("#table-month_attendance_filter").hide();
	}

	function generateApexChart(type = 'pie', staffId, month) {
		ajaxPostRequest('hr_payroll/get_working_chart', {'staff_id': staffId, 'month': month}, function (data){
			var options = {
				series: data.data.serise,
				chart: {
					width: 380,
					type: type,
				},
				labels: data.data.label,
				responsive: [{
					breakpoint: 480,
					options: {
						chart: {
							width: 200
						},
						legend: {
							position: 'bottom'
						}
					}
				}]
			};
			// console.log(Gchart);
			if(Gchart != undefined){
				Gchart.destroy();
			}
			Gchart = new ApexCharts(document.querySelector("#chart"), options);
			Gchart.render();
		});
	}
</script>