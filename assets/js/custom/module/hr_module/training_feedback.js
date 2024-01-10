$(document).ready(function (){
    staticDataTable("table-table_training_feedback");

    //Added by DEEP BASAK on January 10, 2023
    $("#modalFeedbackForm").on("submit", function (e) {
        e.preventDefault();
        ajaxFromSubmit(pageURL + 'save_feedback', this, function(data) {
            holdModal('training_feedback_modal');
            // Reload the current page
            window.location.reload();
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