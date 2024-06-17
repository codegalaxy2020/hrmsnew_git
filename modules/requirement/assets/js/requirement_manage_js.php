<script type="text/javascript">
    var modelId = 'requirement_modal';
    $(document).ready(function () {

        serverSideDataTable('table-staff_disciplinary', baseUrl + 'requirement/requirement_list', 10);

        $('#modalForm').on('submit', function (e){
            e.preventDefault();
            ajaxFromSubmit('requirement/save_requirement', this, function(data){
                closeModal(modelId);
                serverSideDataTable('table-staff_disciplinary', baseUrl + 'requirement/requirement_list', 10);
                SwalSuccess2(data.message, '', data.status);
            });
        });
    });

    function openModal(id = 0, type = 0){
        ajaxPostRequest('requirement/open_requirement_modal', {'id': id}, function (data){
            $('#'+modelId).find('#'+modelId+'_body').html(data.html);
			$('#'+modelId).find('#'+modelId+'_title').text('Add Requirement');
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

    function addFields(){
        var count = $('#form_field_tbl tbody tr').length;
        ajaxPostRequest('requirement/add_fields', {'count': count}, function (data) {
            $('#form_field_tbl tbody').append(data.html);
        });
    }

    function setFieldNameSlug(elem, count){
        var fieldName = $('#form_field_tbl').find('#field_name_'+count).val();
        if(fieldName != undefined && fieldName != ''){
            var fieldNameSlug = fieldName.toLowerCase().replace(/ /g, '_');
            $('#form_field_tbl').find('#field_name_slug_'+count).val(fieldNameSlug);
        }
        
    }
</script>