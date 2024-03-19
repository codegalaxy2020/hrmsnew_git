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
	function openExpensesModal(){
		ajaxPostRequest('hr_payroll/open_expenses_modal', {}, function (data){
			$('#attendance_modal').find('#attendance_modal_body').html(data.html);
			$('#attendance_modal').find('#attendance_modal_title').text('Add expenses');
			dynamicModalSize('attendance_modal', 'modal-xl', 'modal-md');
			holdModal('attendance_modal');
		});
	}

	//Added by DEEP BASAK on March 19, 2024
	function dynamicModalSize(modalId, modalRemoveClass, modalSize){
		$('#' + modalId).find('.modal-dialog').removeClass(modalRemoveClass);
		$('#' + modalId).find('.modal-dialog').addClass(modalSize);
	}
</script>