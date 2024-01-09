"use-strict"
$(function(){
    validate_sms_form();
})
var id = $('#lead-modals').find('input[name="leadid"]').val();

var table_lead_manager = $('table.table-lead-managerd');

function lead_manager_mark_as(status_id, lead_id) {
    /*console.log(status_id); return false;*/
    if(status_id == '1'){
        requestGet('lead_manager/get_convert_data/' + lead_id).done(function (response) {
            $('#lead_convert_to_customer').html(response);
            $('#convert_lead_to_client_modal').modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            });
        }).fail(function (data) {
            alert_float('danger', data.responseText);
        }).always(function () {
            console.log('always'); return false;
        })
    }else{
        var data = {};
        data.status = status_id;
        data.leadid = lead_id;
        $.post(admin_url + 'lead_manager/update_lead_status', data).done(function(response) {
            table_lead_manager.DataTable().ajax.reload(null, false);
        });
    }
}

var LeadsManagerServerParams = {
    "assigned": "[name='view_assigned']",
    "status": "[name='view_status[]']",
};

if (table_lead_manager.length) {
    var tableLeadsConsentHeading = table_lead_manager.find('#th-consent');
    var manageLeadsTableNotSortable = [0];
    var manageLeadsTableNotSearchable = [0, table_lead_manager.find('#th-assigned').index()];

    if (tableLeadsConsentHeading.length > 0) {
        manageLeadsTableNotSortable.push(tableLeadsConsentHeading.index());
        manageLeadsTableNotSearchable.push(tableLeadsConsentHeading.index());
    }

    var lead_manager_table_api = initDataTable(table_lead_manager, admin_url + 'lead_manager/table', manageLeadsTableNotSearchable, manageLeadsTableNotSortable, LeadsManagerServerParams, [table_lead_manager.find('th.date-created').index(), 'desc']);

    if (lead_manager_table_api && tableLeadsConsentHeading.length > 0) {
        lead_manager_table_api.on('draw', function() {
            var tableData = table_lead_manager.find('tbody tr');
            $.each(tableData, function() {
                $(this).find('td:eq(3)').addClass('bg-light-gray');
            });
        });
    }

    $.each(LeadsManagerServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {

            $("[name='view_status[]']")
            .prop('disabled', ($(this).val() == 'lost' || $(this).val() == 'junk'))
            .selectpicker('refresh');

            table_lead_manager.DataTable().ajax.reload()
            .columns.adjust()
            .responsive.recalc();
        });
    });
}
$("body").on('click', 'table.dataTable tbody td:first-child', function() {
    var tr = $(this).parents('tr');
    if ($(this).parents('table').DataTable().row(tr).child.isShown()) {
        var switchBox = $(tr).next().find('input.onoffswitch-checkbox');
        if (switchBox.length > 0) {
            var switchBoxId = Math.random().toString(16).slice(2);
            switchBox.attr('id', switchBoxId).next().attr('for', switchBoxId);
        }
    }
});

function leadManagerActivity(lead_id){
    let data= {'id':lead_id};
    let url= admin_url+'lead_manager/activity_log';
    let modalContentSpace = $("#lead-manager-activity-modal").find(".modal-content")
    $.get(url,data,function(resp){
        modalContentSpace.html(resp);
        $("#lead-manager-activity-modal").modal("show");
    })
}
function zoomMeetingDetails(meeting_id){
    let data= {'id':meeting_id};
    let url= admin_url+'lead_manager/zoom_meeting/zoomMeetingDetails';
    let modalContentSpace = $("#lead-manager-meeting-details").find(".modal-content")
    $.get(url,data,function(resp){
      modalContentSpace.html(resp);
      $("#lead-manager-meeting-details").modal("show");
  })
}
function validate_sms_form(lead_id) {
    var messages = {};
    var validationObject = {message: 'required'};
    var form = $('#sms-form-'+lead_id);
    appValidateForm(form, validationObject, sms_form_handler, messages);
}
function sms_form_handler(form) {
    console.log($(form).closest('.modal-footer.send_sms_btn_lm'))
    form = $(form);
    var data = form.serialize();
    var leadid = $('#lead-modals').find('input[name="lm_leadid"]').val();
    $.post(form.attr('action'), data).done(function(response) {
        response = JSON.parse(response);
        if(response.error){
            alert_float('danger', response.error);
        }if(response.success){
            alert_float('success', 'Message Sent!');
            if ($.fn.DataTable.isDataTable('.table-lead-managerd')) {
                form.trigger("reset")
                $("#lead-manager-sms-modal").modal('hide');
                $('.table-lead-managerd').DataTable().ajax.reload(null, false);
            }

        }
    }).fail(function(data) {
        alert_float('danger', data.responseText);
        return false;
    });
    return false;
}
$('body').on('click','button.send_sms_btn_lm', function() {
    let lead_id = $(this).attr('data-lead');
    console.log(lead_id)
    $('form#sms-form-'+lead_id).submit();
});

$("body").on('submit', '#lead-manager-meeting-remark #meeting_remark_form', function() {
    var form = $(this);
    var $leadModal = $('#lead-manager-meeting-remark');
    var data = $(form).serialize();
    $.post(form.attr('action'), data).done(function(response) {
        response = JSON.parse(response);
        $leadModal.modal('hide');
        alert_float('success', "Remark  Save successfully.");
        table_lead_manager.DataTable().ajax.reload(null, false);
    }).fail(function(data) {
        alert_float('danger', "something Wrong");
    });
    return false;
});

function leadManagerMessage(lead_id){
    let data= {'id':lead_id};
    let url= admin_url+'lead_manager/send_sms_modal';
    let modalContentSpace = $("#lead-manager-sms-modal").find(".modal-content")
    $.get(url,data,function(resp){
        modalContentSpace.html(resp);
        $("#lead-manager-sms-modal").modal("show");
    })
}  

function saveMeetingRemark(id,rel_type){
    let data= {'id':id,'rel_type':rel_type};
    let url= admin_url+'lead_manager/zoom_meeting/show_remark_modal';
    let modalContentSpace = $("#lead-manager-meeting-remark").find(".modal-content")
    $.get(url,data,function(resp){
        modalContentSpace.html(resp);
        $("#lead-manager-meeting-remark").modal("show");
    })
}  
function showMeetingRemark(id,rel_type){
    let data= {'id':id,'rel_type':rel_type};
    let url= admin_url+'lead_manager/zoom_meeting/showMeetingRemark';
    let modalContentSpace = $("#lead-manager-meeting-show_remark").find(".modal-content")
    $.get(url,data,function(resp){
        modalContentSpace.html(resp);
        $("#lead-manager-meeting-show_remark").modal("show");
    })
}  
function leadManagerZoom(lead_id){
    let data= {'id':lead_id};
    let url= admin_url+'lead_manager/send_zoom_link_modal';
    let modalContentSpace = $("#lead-manager-zoom-modal").find(".modal-content")
    $.get(url,data,function(resp){
        if(resp == 'email not found!'){
            alert('Plz add email id to lead!')
        }else{
            modalContentSpace.html(resp);
            $("#lead-manager-zoom-modal").modal("show");
        }
    })
}
function lead_manager_bulk_sms_actions(event) {
    if (confirm_delete()) { 
        var ids = [];
        var data = {};
        var rows = $('.table-lead-managerd').find('tbody tr');
        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') === true) {
                ids.push(checkbox.val());
            }
        });
        if(ids.length > 0){
            data.ids = ids;
            data.message = $('#bulk_message_content').val();
            $(event).addClass('disabled');
            setTimeout(function() {
                $.post(admin_url + 'lead_manager/bulk_action', data).done(function(data) {
                    alert_float('success', 'Message Sent!');
                    if ($.fn.DataTable.isDataTable('.table-lead-managerd')) {
                        $("#lead_manager_bulk_actions").modal('hide');
                        $('.table-lead-managerd').DataTable().ajax.reload(null, false);
                    }
                }).fail(function(data) {
                    $('#lead_manager_bulk_actions').modal('hide');
                    alert_float('danger', data.responseText);
                });
            }, 2000);
        }else{
            alert_float('danger', 'No lead selected!');
        }
    }
}
$("body").on('submit', '#lead-manager-zoom-modal #zoom_meeting_form', function() {
    var form = $(this);
    var $leadModal = $('#lead-manager-zoom-modal');
    var data = $(form).serialize();
    $.post(form.attr('action'), data).done(function(response) {
        response = JSON.parse(response);
        $leadModal.modal('hide');
        alert_float('success', "Meeting  scheduled successfully. Please check email id for more details.");
    }).fail(function(data) {
        alert_float('danger', "something Wrong");
    });
    return false;
});