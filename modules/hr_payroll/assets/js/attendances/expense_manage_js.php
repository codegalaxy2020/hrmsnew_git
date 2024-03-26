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

		serverSideDataTable('table-staff_expense', baseUrl + 'hr_payroll/expense_manage_list/' + month + '/' + staff, 10);

        $('#modalForm').on('submit', function (e){
            e.preventDefault();
            ajaxFromSubmit('hr_payroll/save_expenses', this, function(data){
                closeModal('attendance_modal');
                serverSideDataTable('table-staff_expense', baseUrl + 'hr_payroll/expense_manage_list/' + month + '/' + staff, 10);
                SwalSuccess2(data.message, '', data.status);
            });
        });
	});

    function filterData(month, staff){
		serverSideDataTable('table-staff_expense', baseUrl + 'hr_payroll/expense_manage_list/' + month + '/' + staff, 10);
	}

    //Added by DEEP BASAK on March 19, 2024
	function openExpensesModal(id = 0, type = 0){
		ajaxPostRequest('hr_payroll/open_expenses_modal', {'id': id}, function (data){
			$('#attendance_modal').find('#attendance_modal_body').html(data.html);
			$('#attendance_modal').find('#attendance_modal_title').text('Add expenses');
			dynamicModalSize('attendance_modal', 'modal-xl', 'modal-fullscreen');
			addExpenseTable();
			holdModal('attendance_modal');
		});
	}

	//Added by DEEP BASAK on March 26, 2024
	function addExpenseTable(){
		var count = $('#staff_expense_list_table').find('.expenseList').length;
		ajaxPostRequest('hr_payroll/add_expense_table', {'count': count}, function (data){
			$('#staff_expense_list_table').find('tbody').append(data.html);
		});
	}

	//Added by DEEP BASAK on March 26, 2024
	function dynamicTADAOption(count = 0){
		var selectedOption = $('#tada_'+count).val();
		$('#type_'+count).val('');
		$('#per_'+count).val('');
		$('#exp_amount_'+count).val('');

		if (selectedOption === 'TA') {
			$('#type_'+count).find('.da_option').attr('disabled', true);
			$('#type_'+count).find('.ta_option').attr('disabled', false);

			$('#per_'+count).find('.da_option').attr('disabled', true);
			$('#per_'+count).find('.ta_option').attr('disabled', false);
		} else if (selectedOption === 'DA') {
			$('#type_'+count).find('.da_option').attr('disabled', false);
			$('#type_'+count).find('.ta_option').attr('disabled', true);

			$('#per_'+count).find('.da_option').attr('disabled', false);
			$('#per_'+count).find('.ta_option').attr('disabled', true);
		}
	}

	function getExpenseRate(count = 0){
		var tada = $('#tada_'+count).val();
		var type = $('#type_'+count).val();
		var per = $('#per_'+count).val();
		var total = $('#exp_amount').val();
		ajaxPostRequest('hr_payroll/get_expense_rate', {'tada': tada, 'type': type, 'per': per}, function (data){
			$('#amount_' + count).val(data.rate);
			total = parseFloat(total) + parseFloat(data.rate);
			$('#exp_amount').val(total);
		});
	}
</script>