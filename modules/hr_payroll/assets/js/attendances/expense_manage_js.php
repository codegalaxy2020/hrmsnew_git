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
			
			holdModal('attendance_modal');

			if(type == 2){
				$('#attendance_modal').find('#attendance_modal_body input, #attendance_modal_body select, #attendance_modal_body textarea').attr('disabled', true);
				$('#attendance_modal').find('.save').hide();
			} else{
				$('#attendance_modal').find('#attendance_modal_body input, #attendance_modal_body select, #attendance_modal_body textarea').attr('disabled', false);
				$('#attendance_modal').find('.save').show();
				addExpenseTable(type);
			}
		});
	}

	//Added by DEEP BASAK on March 26, 2024
	function addExpenseTable(type = 0){
		var count = $('#staff_expense_list_table').find('.expenseList').length;
		ajaxPostRequest('hr_payroll/add_expense_table', {'count': count}, function (data){
			$('#staff_expense_list_table').find('tbody').append(data.html);

			if(type == 2){
				$('#attendance_modal').find('#attendance_modal_body input, #attendance_modal_body select, #attendance_modal_body textarea').attr('disabled', true);
				$('#attendance_modal').find('.save').hide();
			} else{
				$('#attendance_modal').find('#attendance_modal_body input, #attendance_modal_body select, #attendance_modal_body textarea').attr('disabled', false);
				$('#attendance_modal').find('.save').show();
			}
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
		var total = 0;
		if(count != -1){
			var tada = $('#tada_'+count).val();
			var type = $('#type_'+count).val();
			var per = $('#per_'+count).val();
			// var total = $('#exp_amount').val();
			var totalCal = $('#distance_'+count).val();
			
			
			ajaxPostRequest('hr_payroll/get_expense_rate', {'tada': tada, 'type': type, 'per': per}, function (data){
				if(totalCal != undefined || totalCal != NaN || totalCal != 0){
					// totalCal = totalCal;
					totalCal = totalCal * data.rate;
				} else{
					totalCal = data.rate;
				}
				$('#amount_' + count).val(totalCal);

				for(var index = 0; index < $('.amount').length; index++){
					total = parseFloat(total) + parseFloat($('#amount_' + index).val());
				}
				// total = parseFloat(total) + parseFloat(totalCal);
				$('#exp_amount').val(total);
			});
		} else{
			// debugger;
			for(var index = 0; index < $('.amount').length; index++){
				total = parseFloat(total) + parseFloat($('#amount_' + index).val());
			}
			$('#exp_amount').val(total);
		}
		
	}

	function removeElm(elm, count = 0){
		elm.parentNode.remove();
		getExpenseRate(-1);
	}
</script>