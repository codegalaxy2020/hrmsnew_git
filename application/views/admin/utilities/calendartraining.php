<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body" style="overflow-x: auto;">
						<div class="dt-loader hide"></div>
						<?php $this->load->view('admin/utilities/calendar_filters'); ?>
						<div id="calendar"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('admin/utilities/calendar_templatetraining'); ?>
<?php hooks()->do_action('after_calendar_loaded');?>
<script>
	app.calendarIDs = '<?php echo json_encode($google_ids_calendars); ?>';
</script>
<?php init_tail(); ?>
<script>
var position_training_id = {};
(function(){
	'use strict';
	window.addEventListener('load',function(){
		appValidateForm($("body").find('.job_position_training_add_edit'), {
			training_type: 'required',
			'position_training_id[]': 'required',
			mint_point: 'required',
			training_name: 'required',
		});

	});  
})(jQuery);
	$(function(){
		if(get_url_param('eventid')) {
			view_event(get_url_param('eventid'));
		}
	});
	function training_type_change(invoker){
	'use strict';
    // alert(invoker);
	if(invoker.value){
		$.post(admin_url + 'hr_profile/get_training_type_child/'+invoker.value).done(function(response) {
			response = JSON.parse(response);
			$('select[name="position_training_id[]"]').html('');
			$('select[name="position_training_id[]"]').append(response.html);

			$('select[name="position_training_id[]"]').selectpicker('refresh');

			$('#job_position_training select[name="position_training_id[]"]').val(position_training_id).change();
		}); 
	}

}
function department_change(invoker){
	'use strict';

	var data_select = {};
	data_select.department_id = $('select[name="department_id[]"]').val();
	data_select.status = 'true';
	if((data_select.department_id).length == 0){
		data_select.status = 'false';
	}
	$.post(admin_url + 'hr_profile/get_jobposition_fill_data',data_select).done(function(response){
	 response = JSON.parse(response);
	 $("select[name='job_position_id[]']").html('');
	 $("select[name='job_position_id[]']").append(response.job_position);
	 $("select[name='job_position_id[]']").selectpicker('refresh');
 });
}
    $(document).ready(function() {
      // Add a click event listener to a button with the id "refreshButton"
      $("#kolk").click(function() {
        // Reload the current page
        setTimeout(function() {
            location.reload();
        }, 3000);
      });
    });
    function new_training_process(){
	'use strict';

	$('#job_position_training').modal('show');
	$('.add-title-training').addClass('hide');
	$('.edit-title-training').removeClass('hide');

	$('#additional_form_training').empty();

	$('#job_position_training input[name="training_name"]').val('');
	$('#job_position_training input[name="mint_point"]').val('');

	$('#job_position_training select[name="training_type"]').val('');
	$('#job_position_training select[name="training_type"]').change();


	 $('#job_position_training select[name="position_training_id[]"]').val('').change();
	 
	 $('#job_position_training select[name="staff_id[]"]').val('').change();

 	$('#job_position_training input[id="additional_training"]').prop("checked", false);
 	$('.additional_training_hide').addClass('hide');
 	$('.onboading_hide').removeClass('hide');
 	$('select[id="job_position_id"]').prop('required',true);

 $('input[name="time_to_start"]').val('');
 $('input[name="time_to_end"]').val('');

	
	position_training_id = ('').split(',');
	tinyMCE.activeEditor.setContent("");

	$("select[name='job_position_id[]']").val('');
	$("select[name='job_position_id[]']").change();
	$('.selectpicker').selectpicker({
		});
}
 var staff_id_str = $(invoker).data('staff_id');
	if(typeof(staff_id_str) == "string"){
		$('#job_position_training select[name="staff_id[]"]').val( ($(invoker).data('staff_id')).split(',')).change();
	}else{
	 $('#job_position_training select[name="staff_id[]"]').val($(invoker).data('staff_id')).change();
 }

 if($(invoker).data('additional_training') == 'additional_training'){
 	$('#job_position_training input[id="additional_training"]').prop('checked', true);

 	$('.additional_training_hide').removeClass('hide');
 	$('.onboading_hide').addClass('hide');

 	$('select[id="job_position_id"]').removeAttr( "required" );

 }else{
 	$('#job_position_training input[id="additional_training"]').prop("checked", false);

 	$('.additional_training_hide').addClass('hide');
 	$('.onboading_hide').removeClass('hide');

 	$('select[id="job_position_id"]').prop('required',true);
 }

	 $('input[name="additional_training"]').on('click', function() {
		'use strict';

		var additional_training = $('input[id="additional_training"]').is(":checked");

		if(additional_training == true){
			$('.additional_training_hide').removeClass('hide');
			$('.onboading_hide').addClass('hide');

			$('select[id="job_position_id"]').removeAttr( "required" );

		}else {
			$('.additional_training_hide').addClass('hide');
			$('.onboading_hide').removeClass('hide');

			$('select[id="job_position_id"]').prop('required',true);
		}

	});
</script>
</body>
</html>
