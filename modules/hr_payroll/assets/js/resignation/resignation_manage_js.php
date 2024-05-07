<script>
    var modelId = 'resignation_modal';
	$(document).ready(function () {

		serverSideDataTable('table-staff_resignation', baseUrl + 'hr_payroll/resignation_list/', 10);

        $('#modalForm').on('submit', function (e){
            e.preventDefault();
            ajaxFromSubmit('hr_payroll/save_resignation', this, function(data){
                closeModal(modelId);
                serverSideDataTable('table-staff_resignation', baseUrl + 'hr_payroll/resignation_list/', 10);
                SwalSuccess2(data.message, '', data.status);
            });
        });
	});

    function openModal(id = 0, type = 0){
        ajaxPostRequest('hr_payroll/open_resignation_modal', {'id': id}, function (data){
            $('#'+modelId).find('#'+modelId+'_body').html(data.html);
			$('#'+modelId).find('#'+modelId+'_title').text('Add resignation');
            holdModal(modelId);

            if(type == 2){
				$('#'+modelId).find('#'+modelId+'_body input, #'+modelId+'_body select, #'+modelId+'_body textarea').attr('disabled', true);
				$('#'+modelId).find('.save').hide();
                if(data.is_admin = true){
                    $('#'+modelId).find('#notice_days').attr('disabled', data.disabled);
                }
                
			} else{
				$('#'+modelId).find('#'+modelId+'_body input, #'+modelId+'_body select, #'+modelId+'_body textarea').attr('disabled', false);
				$('#'+modelId).find('.save').show();
                $('#'+modelId).find('#approve_btn, #reject_btn').hide();
			}
        });
    }

    function getDate(elmn){
        if(($(elmn).val() != '') || ($(elmn).val() != undefined) || ($(elmn).val() != "")){
            ajaxPostRequest('hr_payroll/get_date', {'days': $(elmn).val()}, function (data) {
                $('#notice_date').val(data.date);
            });
        } else{
            swalErrMsg('Please enter notice days');
        }
    }

    function approveReject(id, type){
        var msg = '';
        if(type == 'A'){
            msg = "Are you sure want to approved this Regination?";
        } else{
            msg = "Are you sure want to reject this Resignation?"
        }
        var noticeDays = $('#'+modelId).find('#notice_days').val();
        warnMsg2(msg, true, true, "Approve It!", "Reject It!", function (){
            ajaxPostRequest('hr_payroll/approve_reject', {'id': id, 'type': type, 'notice_days': noticeDays}, function (data){
                SwalSuccess2(data.message);
                closeModal(modelId);
                serverSideDataTable('table-staff_resignation', baseUrl + 'hr_payroll/resignation_list/', 10);
            });
        }, function(){
            SwalSuccess2("Good Job!", "Your Resignation is safe", "success");
            closeModal(modelId);
        }, function (){
            ajaxPostRequest('hr_payroll/approve_reject', {'id': id, 'type': type, 'notice_days': noticeDays}, function (data){
                SwalSuccess2(data.message);
                closeModal(modelId);
                serverSideDataTable('table-staff_resignation', baseUrl + 'hr_payroll/resignation_list/', 10);
            });
        });
    }
</script>