<script type="text/javascript">
    var modelId = 'krakpi_modal';
    $(document).ready(function () {
        var month;
		if ($('#month_attendance').val() != undefined) {
			month = $('#month_attendance').val();
		} else {
			month = null;
		}
        serverSideDataTable('table-staff_krakpi', baseUrl + 'appraisal/krakpi_list/' + month, 10);

        $(document).on('submit', '#modalForm', function (e){
            e.preventDefault();
            ajaxFromSubmit('appraisal/save_krakpi', this, function(data){
                closeModal(modelId);
                SwalSuccess2(data.message, '', data.status);
                serverSideDataTable('table-staff_krakpi', baseUrl + 'appraisal/krakpi_list/' + month, 10);
            });
        });

        $(document).on('submit', '#appraisalTimeModalForm', function (e){
            e.preventDefault();
            ajaxFromSubmit('appraisal/save_appraisal_time', this, function(data){
                closeModal(modelId);
                //getData();
                SwalSuccess2(data.message, '', data.status);
            });
        });
    });

    function filterData(month){
        serverSideDataTable('table-staff_krakpi', baseUrl + 'appraisal/krakpi_list/' + month, 10);
    }

    function openAppraisalTimeModal(){
        ajaxPostRequest('appraisal/open_appraisal_time_modal', {}, function (data) {
            $('#'+modelId).find('#'+modelId+'_body').html(data.html);
			$('#'+modelId).find('#'+modelId+'_title').text('Appraisal Time');
            $('#'+modelId).find('.save').show();
            $('#'+modelId).find('.save').text('Update Appraisal Time');
            holdModal(modelId);
            $('#'+modelId).find('.modal-dialog').removeClass('modal-xl');
            $('#'+modelId).find('.modal-dialog').addClass('modal-sm');
            $('#'+modelId).find('form').attr('id', 'appraisalTimeModalForm');
        });
    }

    function openModal(id = 0, type = 0){
        ajaxPostRequest('appraisal/open_modal', {'id': id}, function (data) {
            $('#'+modelId).find('#'+modelId+'_body').html(data.html);
			$('#'+modelId).find('#'+modelId+'_title').text('Add KRA & KPI');
            $('#'+modelId).find('.save').show();
            $('#'+modelId).find('.save').text('Submit KRA & KPI');
            holdModal(modelId);
            if($('#'+modelId).find('.modal-dialog').hasClass('modal-sm')){
                $('#'+modelId).find('.modal-dialog').removeClass('modal-sm');
            }
            $('#'+modelId).find('.modal-dialog').addClass('modal-md');
            $('#'+modelId).find('form').attr('id', 'modalForm');

            if(type == 2){
				$('#'+modelId).find('#'+modelId+'_body input, #'+modelId+'_body select, #'+modelId+'_body textarea').attr('disabled', true);
                $('#'+modelId).find('.save').hide();
                $('#'+modelId).find('#'+modelId+'_title').text('View KRA & KPI');
            } else if(type == 1){
				$('#'+modelId).find('#'+modelId+'_body input, #'+modelId+'_body select, #'+modelId+'_body textarea').attr('disabled', false);
                $('#'+modelId).find('#'+modelId+'_title').text('Edit KRA & KPI');
            }
        });
    }

    function openRatingModal(staffId = 0){
        ajaxPostRequest('appraisal/open_rating_modal', {'staff_id': staffId}, function (data) {
            $('#'+modelId).find('#'+modelId+'_body').html(data.html);
			$('#'+modelId).find('#'+modelId+'_title').text('View Rating Details');
            $('#'+modelId).find('.save').hide();
            holdModal(modelId);
            if($('#'+modelId).find('.modal-dialog').hasClass('modal-sm')){
                $('#'+modelId).find('.modal-dialog').removeClass('modal-sm');
            }
            $('#'+modelId).find('.modal-dialog').addClass('modal-md');
        });
    }
</script>