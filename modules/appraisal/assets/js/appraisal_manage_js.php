<script type="text/javascript">
    var modelId = 'appraisal_modal';
    var appraisalModalId = 'appraisal_approve_modal';
    $(document).ready(function () {

        var year;
		if ($('#year_filter').val() != undefined) {
			year = $('#year_filter').val();
		} else {
			year = null;
		}
        $('#year_filter').select2();
        serverSideDataTable('table-staff_appraisal', baseUrl + 'appraisal/apprisal_list/' + year, 10);

        $('#modalForm').on('submit', function (e){
            e.preventDefault();
            ajaxFromSubmit('appraisal/save_appraisal', this, function(data){
                closeModal(modelId);
                serverSideDataTable('table-staff_appraisal', baseUrl + 'appraisal/apprisal_list/'+year, 10);
                SwalSuccess2(data.message, '', data.status);
            });
        });

        //Added by DEEP BASAK on July 05, 2024
        $('#modalFormAppraisalApprove').on('submit', function (e){
            e.preventDefault();
            ajaxFromSubmit('appraisal/appraisal_approve_reject', this, function(data){
                closeModal(appraisalModalId);
                serverSideDataTable('table-staff_appraisal', baseUrl + 'appraisal/apprisal_list/'+year, 10);
                SwalSuccess2(data.message, '', data.status);
            });
        });
    });

    function filterData(year){
        serverSideDataTable('table-staff_appraisal', baseUrl + 'appraisal/apprisal_list/' + year, 10);
    }

    function openAppraisalModal(id = 0, type = 0){
        ajaxPostRequest('appraisal/open_appraisal_modal', {'id': id}, function (data){
            $('#'+modelId).find('#'+modelId+'_body').html(data.html);
			$('#'+modelId).find('#'+modelId+'_title').text('Add Appraisal');
            $('#'+modelId).find('.save').show();
            holdModal(modelId);

            if(type == 2){
				$('#'+modelId).find('#'+modelId+'_body input, #'+modelId+'_body select, #'+modelId+'_body textarea, #'+modelId+'_body .delBtn').attr('disabled', true);
                $('#'+modelId).find('#'+modelId+'_body .delBtn').attr('onclick', '');
                $('#'+modelId).find('#'+modelId+'_body .addFieldsBtn').attr('onclick', '');
				$('#'+modelId).find('.save').hide();
                $('#'+modelId).find('#'+modelId+'_title').text('View Requirement');
                
			} else if(type == 1){
				$('#'+modelId).find('#'+modelId+'_body input, #'+modelId+'_body select, #'+modelId+'_body textarea, #'+modelId+'_body .delBtn').attr('disabled', false);
                $('#'+modelId).find('#'+modelId+'_body .delBtn').attr('onclick', 'this.parentNode.remove()');
                $('#'+modelId).find('#'+modelId+'_body .addFieldsBtn').attr('onclick', 'addFields()');
                $('#'+modelId).find('#'+modelId+'_title').text('Edit Requirement');
			}

            
        });
    }

    function getAppraisalDetails(type, elem){
        var staffId = $('#'+modelId).find('#staff_id').val();
        if((staffId != undefined) && (staffId != '') && (staffId != null)){
            var salary, designation;
            if($('#salary').is(':checked')){
                salary = 'S';
            } else{
                salary = '';
            }

            if($('#designation').is(':checked')){
                designation = 'S';
            } else{
                designation = '';
            }
            ajaxPostRequest('appraisal/get_appraisal_details', {'salary': salary, 'designation': designation, 'staff_id': staffId}, function (data){
                $('#'+modelId).find('#appraisalDetailsDiv').html(data.html);
                $('#'+modelId).find('#staff_id').attr('disabled', data.type);
                $('#'+modelId).find('#hdn_staff_id').val(staffId);
                if(salary != ''){
                    $('#'+modelId).find('#salary').val('Y');
                } else{
                    $('#'+modelId).find('#salary').val('N');
                }

                if(designation != ''){
                    $('#'+modelId).find('#designation').val('Y');
                } else{
                    $('#'+modelId).find('#designation').val('N');
                }
                
            });
        } else{
            SwalSuccess2('Please select staff first!', '', 'warning');
            $(".swal2-container").css("z-index", "11000");
            $(elem).prop('checked', false);
        }
    }

    function approveOrRejectAppraisal(apprisalId){
        
        warnMsg2('Are you sure want to approve or reject the appraisal?', true, true, 'Approve', 'Reject', function (){
            ajaxPostRequest('appraisal/appraisal_approve_reject_open_modal', {'appraisal_id': apprisalId}, function (data){
                $('#'+appraisalModalId).find('#'+appraisalModalId+'_body').html(data.html);
                $('#'+appraisalModalId).find('#'+appraisalModalId+'_title').text('Add Appraisal Starting Date');
                $('#'+appraisalModalId).find('.save').show();
                holdModal(appraisalModalId);
            });
        }, '', function (){
            ajaxPostRequest('appraisal/appraisal_approve_reject', {'appraisal_id': apprisalId, 'type': 'R'}, function (data){
                serverSideDataTable('table-staff_appraisal', baseUrl + 'appraisal/apprisal_list/' + year, 10);
                SwalSuccess2(data.message, '', data.status);
            });
        });
    }
</script>