$(document).ready(function (){
    // staticDataTable("table-table_training_feedback");

    serverSideDataTable('table-table_training_feedback', baseUrl + pageURL + 'feedback_list', 10);
    // swalWarnMsg("test", "message", "error", "ok");

    //Added by DEEP BASAK on January 10, 2023
    $("#modalFeedbackForm").on("submit", function (e) {
        e.preventDefault();
        ajaxFromSubmit(pageURL + 'save_feedback', this, function(data) {
            holdModal('training_feedback_modal');
            // Reload the current page
            closeModal('training_feedback_modal');
            SwalSuccess2("Good Job!", data.message, data.status);
            serverSideDataTable('table-table_training_feedback', baseUrl + pageURL + 'feedback_list', 10);
        });
    });
});


//Added by DEEP BASAK on January 10, 2023
function openModal(type = 0){
    ajaxPostRequest(pageURL + 'load_modal', {}, function (data){
        $("#training_feedback_modal_body").html(data.html);
        $("#training_feedback_modal_title").text("Add Feedback");
        $("#training_feedback_modal").find(".save").text("Send Feedback");
        holdModal('training_feedback_modal');
    });
}

function deleteFeedback(feedbackId = 0){
    warnMsg2("Are you sure want to delete this Feedback?", false, true, "Delete It!", "", function (){
        ajaxPostRequest(pageURL + 'delete_feedback', {'id': feedbackId}, function(data){
            SwalSuccess2("Good Job!", data.message, data.status);
            serverSideDataTable('table-table_training_feedback', baseUrl + pageURL + 'feedback_list', 10);
        });
    }, function(){
        SwalSuccess2("Good Job!", "Your Feedback is safe", "success");
    });
}