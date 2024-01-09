<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
<div>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
	<div role="tabpanel" class="tab-pane <?php if(isset($tab) && $tab='others_training_program'){echo 'active';} ?>" id="training_program" >
	

		<div class="clearfix"></div>
		<br>

		<div class="modal bulk_actions" id="table_training_program_bulk_actions" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title"><?php echo _l('hr_bulk_actions'); ?></h4>
					</div>
					<div class="modal-body">
						<?php if(has_permission('staffmanage_training','','delete') || is_admin()){ ?>
							<div class="checkbox checkbox-danger">
								<input type="checkbox" name="mass_delete" id="mass_delete">
								<label for="mass_delete"><?php echo _l('hr_mass_delete'); ?></label>
							</div>
						<?php } ?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>

						<?php if(has_permission('staffmanage_training','','delete') || is_admin()){ ?>
							<a href="#" class="btn btn-info" onclick="training_program_delete_bulk_action(this); return false;"><?php echo _l('hr_confirm'); ?></a>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
            
        <div id="calendar"></div>

<!-- Add Event Modal -->
<div id="addEventModal" style="display: none;">
    <form id="addEventForm">
        <label for="title">Event Title:</label>
        <input type="text" name="title" required>
        <br>
        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" required>
        <br>
        <label for="end_date">End Date:</label>
        <input type="date" name="end_date" required>
        <br>
        <input type="submit" value="Add Event">
    </form>
</div>

		</div>
		<!-- training_program end -->
	</div>
	
</body>
</html>

<script>
    $(document).ready(function() {
        // Initialize FullCalendar
        $('#calendar').fullCalendar({
            events: <?php echo json_encode($events); ?>,
            selectable: true,
            select: function(start, end, jsEvent, view) {
                // Open the add event modal
                $('#addEventModal').css('display', 'block');
            }
        });

        // Handle the add event form submission
        $('#addEventForm').submit(function(event) {
            event.preventDefault();
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url("calendar/add_event"); ?>',
                data: $(this).serialize(),
                success: function(response) {
                    // Refresh the calendar
                    $('#calendar').fullCalendar('refetchEvents');
                    // Close the modal
                    $('#addEventModal').css('display', 'none');
                }
            });
        });
    });
</script>