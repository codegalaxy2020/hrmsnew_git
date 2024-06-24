<script type="text/javascript">
    var modelId = 'requirement_modal';
    $(document).ready(function () {
        getData();
        //serverSideDataTable('table-staff_disciplinary', baseUrl + 'requirement/requirement_list', 10);

        $('#modalForm').on('submit', function (e){
            e.preventDefault();
            ajaxFromSubmit('requirement/save_interview_schedule', this, function(data){
                closeModal(modelId);
                getData();
                SwalSuccess2(data.message, '', data.status);
            });
        });
    });

    function getData(){
        ajaxPostRequest('requirement/requirement_data_list', {'form_id': formId}, function (data) {
            $('#table-staff_requirement_data tbody').html(data.html);
            staticDataTable('table-staff_requirement_data');
        });
    }

    //Added by DEEP BASAK on June 19, 2024
    function scheduleInterview(canId){
        ajaxPostRequest('requirement/schedule_interview', {'can_id': canId}, function (data){
            $('#'+modelId).find('#'+modelId+'_body').html(data.html);
			$('#'+modelId).find('#'+modelId+'_title').text('Schedule Interview');
            $('#'+modelId).find('.save').show();
            $('#'+modelId).find('.modal-dialog').removeClass('modal-xl');
            $('#'+modelId).find('.modal-dialog').addClass('modal-sm');
            holdModal(modelId);
        });
    }

    //Added by DEEP BASAK on June 19, 2024
    function shortlisted(canId){
        warnMsg2("Are you sure want to Shortlist this candidate?", true, true, "Shortlisted!", "Rejected!", function (){
            ajaxPostRequest('requirement/shortlisted', {'can_id': canId, 'type': 'Y'}, function (data) {
                getData();
                SwalSuccess2(data.message, '', 'success');
            });
        }, '', function (){
            ajaxPostRequest('requirement/shortlisted', {'can_id': canId, 'type': 'N'}, function (data) {
                getData();
                SwalSuccess2(data.message, '', 'warning');
            });
        });
    }

    //added by DEEP BASAK on June 21, 2024
    function interviewDetails(canID){
        ajaxPostRequest('requirement/interview_details', {'can_id': canID}, function (data) {
            $('#'+modelId).find('#'+modelId+'_body').html(data.html);
			$('#'+modelId).find('#'+modelId+'_title').text('Interview Details of ' + data.title);
            $('#'+modelId).find('.save').hide();
            $('#'+modelId).find('.modal-dialog').removeClass('modal-sm');
            $('#'+modelId).find('.modal-dialog').addClass('modal-xl');
            holdModal(modelId);
            getInterviewDetailsList(canID);
        });
    }

    //added by DEEP BASAK June 21, 2024
    function getInterviewDetailsList(canID){
        ajaxPostRequest('requirement/get_interview_details_list', {'can_id': canID}, function (data) {
            $('#'+modelId).find('#table-interview_details tbody').html(data.html);
            staticDataTable('table-interview_details');
        });
    }

    //Added by DEEP BASAK June 21, 2024
    function submitInterviewComments(){
        ajaxPostRequest(
            'requirement/submit_interview_comments', 
            {
                'can_id': $('#'+modelId).find('#can_id').val(), 
                'interview_datetime': $('#'+modelId).find('#interview_datetime').val(),
                'comments': $('#'+modelId).find('#comments').val()
            },
            function (data){
                getInterviewDetailsList($('#'+modelId).find('#can_id').val());
                $('#'+modelId).find('#interview_datetime').val('');
                $('#'+modelId).find('#comments').val('')
            }
        );
    }

    //Added by DEEP BASAK June 24, 2024
    function selectAsEmployee(){
        var canId = $('#'+modelId).find('#can_id').val();
        warnMsg2("Are you sure want to select this candidate as employee?", true, true, "Selected!", "Rejected!", function (){
            ajaxPostRequest('requirement/select_as_employee', {'can_id': canId, 'type': 'C'}, function (data) {
                closeModal(modelId);
                getData();
                SwalSuccess2(data.message, '', 'success');
            });
        }, '', function (){
            ajaxPostRequest('requirement/select_as_employee', {'can_id': canId, 'type': 'N'}, function (data) {
                closeModal(modelId);
                getData();
                SwalSuccess2(data.message, '', 'warning');
            });
        });
    }
</script>