<script>
    var tableId = 'table-staff_expense_rule';
    var modalId = 'attendance_modal';
	$(document).ready(function () {

		serverSideDataTable(tableId, baseUrl + 'hr_payroll/expense_rule_manage_list', 10);

        $('#modalForm').on('submit', function (e){
            e.preventDefault();
            ajaxFromSubmit('hr_payroll/save_expenses_rule', this, function(data){
                closeModal(modalId);
                serverSideDataTable(tableId, baseUrl + 'hr_payroll/expense_rule_manage_list', 10);
                SwalSuccess2(data.message, '', data.status);
            });
        });
	});

    function openExpensesRuleModal(id = 0, type = 0){
		ajaxPostRequest('hr_payroll/open_expenses_rule_modal', {'id': id}, function (data){
			$('#'+modalId).find('#'+modalId+'_body').html(data.html);
			$('#'+modalId).find('#'+modalId+'_title').text('Add expenses rule');
			dynamicModalSize(modalId, 'modal-xl', 'modal-md');
			holdModal(modalId);

            if(type == 2){
                $('#'+modalId).find('#modalForm input, #modalForm select').attr('disabled', true);
                $('#'+modalId).find('.save').hide();
            } else{
                $('#'+modalId).find('#modalForm input, #modalForm select').attr('disabled', false);
                $('#'+modalId).find('.save').show();
            }
		});
	}

    function deleteModal(feedbackId = 0){
        warnMsg2("Are you sure want to delete this?", false, true, "Delete It!", "", function (){
            ajaxPostRequest('hr_payroll/delete_expense_rule', {'id': feedbackId}, function(data){
                SwalSuccess2("Good Job!", data.message, data.status);
                serverSideDataTable(tableId, baseUrl + 'hr_payroll/expense_rule_manage_list', 10);
            });
        }, function(){
            SwalSuccess2("Good Job!", "Your Item is safe", "success");
        });
    }

</script>