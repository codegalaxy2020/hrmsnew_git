<script type="text/javascript">
    var modelId = 'disciplinary_modal';
    $(document).ready(function () {

        serverSideDataTable('table-staff_disciplinary', baseUrl + 'hr_payroll/complain_list', 10);

        $('#modalForm').on('submit', function (e){
            e.preventDefault();
            ajaxFromSubmit('hr_payroll/save_complain', this, function(data){
                closeModal(modelId);
                serverSideDataTable('table-staff_disciplinary', baseUrl + 'hr_payroll/complain_list', 10);
                SwalSuccess2(data.message, '', data.status);
            });
        });
    });

    function openModal(id = 0, type = 0){
        ajaxPostRequest('hr_payroll/open_disciplinary_modal', {'id': id}, function (data){
            $('#'+modelId).find('#'+modelId+'_body').html(data.html);
			$('#'+modelId).find('#'+modelId+'_title').text('Add Complain');
            $('#'+modelId).find('.save').show();
            holdModal(modelId);

            if(type == 2){
				$('#'+modelId).find('#'+modelId+'_body input, #'+modelId+'_body select, #'+modelId+'_body textarea').attr('disabled', true);
				$('#'+modelId).find('.save').hide();
                $('#'+modelId).find('#'+modelId+'_title').text('View Complain');

                if(data.manager != 0){
                    ajaxPostRequest('hr_payroll/approve_comment_complain', {'case_id': id}, function (data){
                        $('#'+modelId).find('#approve_comments').html(data.html);
                    });
                }
                
			} else if(type == 1){
				$('#'+modelId).find('#'+modelId+'_body input, #'+modelId+'_body select, #'+modelId+'_body textarea').attr('disabled', false);
                $('#'+modelId).find('#'+modelId+'_title').text('Edit Complain');
			}

            
        });
    }

    function approveReject(case_id, type){
        var msg = "", btn = "";
		if(type == 'R'){
			msg = "Are you sure? Want to reject this complain?";
			btn = "Reject it!";
		}else{
			msg = "Are you sure? Want to approve this complain?";
			btn = "Approve it!";
		}

        warnMsg2(msg, false, true, btn, "", function (){
            ajaxPostRequest('hr_payroll/approve_reject_complain', {'case_id': case_id, 'type': type, 'judge': $('#judge').val()}, function (data){
                SwalSuccess2("Good Job!", data.message, data.status);
                serverSideDataTable('table-staff_disciplinary', baseUrl + 'hr_payroll/complain_list', 10);
                closeModal(modelId);
            });
        });
    }

    function saveComplainComment(staffId, caseId){
        ajaxPostRequest(
            'hr_payroll/complain_comment_save', 
            {
                'case_id': caseId, 
                'staff_id': staffId, 
                'comment': $('#'+modelId).find('#comments_box').val()
            }, 
            function (data) {
                if(data.status == 'success'){
                    ajaxPostRequest('hr_payroll/approve_comment_complain', {'case_id': caseId}, function (data){
                        $('#'+modelId).find('#approve_comments').html(data.html);
                    });
                }
        });
        
    }

    function finalJudgement(caseId, isAuto = false){
        if(isAuto == false){
            warnMsg2("Are you sure? Want to final judgement this complain?", false, true, "Yes", "", function (){
                ajaxPostRequest('hr_payroll/final_judgement', {'case_id': caseId}, function (data){
                    SwalSuccess2("Good Job!", data.message, data.status);
                    serverSideDataTable('table-staff_disciplinary', baseUrl + 'hr_payroll/complain_list', 10);
                    closeModal(modelId);
                });
            });
        } else{
            ajaxPostRequest('hr_payroll/final_judgement', {'case_id': caseId}, function (data){
                SwalSuccess2("Good Job!", data.message, data.status);
                serverSideDataTable('table-staff_disciplinary', baseUrl + 'hr_payroll/complain_list', 10);
                closeModal(modelId);
            });
        }
        
    }

    //added by DEEP BASAK on June 11, 2024
    function showCause(caseId){
        warnMsg2("Are you sure? Want to show cause this employee?", false, true, "Yes", "", function (){
            finalJudgement(caseId, true);
            ajaxPostRequest('hr_payroll/show_cause', {'case_id': caseId}, function (data) {
                
            });
        });
        
    }
</script>