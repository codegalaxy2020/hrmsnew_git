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

		serverSideDataTable('table-month_attendance', baseUrl + 'hr_payroll/month_attendance_list/' + month + '/' + staff, 10);
		generateApexChart('donut', staff, month);
		generateDayWiseAttendance();
	});

	function openAttendanceModal(date = '') {
		ajaxPostRequest('hr_payroll/load_attendance_modal', { 'date': date }, function (data) {
			holdModal('attendance_modal');
			$('#attendance_modal_title').text("Attendance of " + date);
			$('#attendance_modal_body').html(data.html);
			staticDataTable('table_attendance_details');
		});
	}

	function filterData(month, staff) {
		generateApexChart('donut', staff, month);
		generateDayWiseAttendance();
		serverSideDataTable('table-month_attendance', baseUrl + 'hr_payroll/month_attendance_list/' + month + '/' + staff, 10);
	}

	function generateApexChart(type = 'pie', staffId, month) {
		ajaxPostRequest('hr_payroll/get_working_chart', { 'staff_id': staffId, 'month': month }, function (data) {
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
			if (Gchart != undefined) {
				Gchart.destroy();
			}
			Gchart = new ApexCharts(document.querySelector("#chart"), options);
			Gchart.render();
		});
	}

	function generateDayWiseAttendance() {
		var options = {
			series: [{
				name: 'Inflation',
				data: [2.3, 3.1, 4.0, 10.1, 4.0, 3.6, 3.2, 2.3, 1.4, 0.8, 0.5, 0.2]
			}],
			chart: {
				height: 350,
				type: 'bar',
			},
			plotOptions: {
				bar: {
					borderRadius: 10,
					dataLabels: {
						position: 'top', // top, center, bottom
					},
				}
			},
			dataLabels: {
				enabled: true,
				formatter: function (val) {
					return val + "%";
				},
				offsetY: -20,
				style: {
					fontSize: '12px',
					colors: ["#304758"]
				}
			},

			xaxis: {
				categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
				position: 'top',
				axisBorder: {
					show: false
				},
				axisTicks: {
					show: false
				},
				crosshairs: {
					fill: {
						type: 'gradient',
						gradient: {
							colorFrom: '#D8E3F0',
							colorTo: '#BED1E6',
							stops: [0, 100],
							opacityFrom: 0.4,
							opacityTo: 0.5,
						}
					}
				},
				tooltip: {
					enabled: true,
				}
			},
			yaxis: {
				axisBorder: {
					show: false
				},
				axisTicks: {
					show: false,
				},
				labels: {
					show: false,
					formatter: function (val) {
						return val + "%";
					}
				}

			},
			title: {
				text: 'Monthly Inflation in Argentina, 2002',
				floating: true,
				offsetY: 330,
				align: 'center',
				style: {
					color: '#444'
				}
			}
		};

		var chart = new ApexCharts(document.querySelector("#day_wise_attendance"), options);
		chart.render();
	}
</script>