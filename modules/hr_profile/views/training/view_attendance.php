<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php  init_head(); ?>
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
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="_buttons">
							<h3>Attendance</h3>
						</div>
						<br>
						<div class="row">
							<div class="col-md-4">
								<h4><strong>Staff Name:</strong> <?= $staff_details->firstname . ' ' . $staff_details->lastname ?></h4>
							</div>
							<div class="col-md-4">
								<h4><strong>Training Name:</strong> <?= $training_details->training_name ?></h4>
							</div>
							<div class="col-md-4">
								<h4><strong>Training Process: </strong></h4>
								<?php
								$class = 'bg-danger';
								if($total_present <= 30){
									$class = 'bg-danger';
								} else if(($total_present > 30) && ($total_present <= 60)){
									$class = 'bg-warning';
								} else if(($total_present > 60)){
									$class = 'bg-success';
								}
								?>
								<div class="progress">
									<div class=" progress-bar-striped <?= $class ?>" role="progressbar" style="width: <?= $total_present ?>%" aria-valuenow="<?= $total_present ?>" aria-valuemin="0" aria-valuemax="100"><?= $total_present ?>%</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
							<?php if (!empty($faculty_data)): ?>
                                <table class="table table-sm table-bordered">
                                    <tr>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Attendance</th>
                                    </tr>
                                    <?php foreach ($faculty_data as $index => $faculty): ?>
									<tr>
										<td><?php echo $index + 1; ?></td>
										<td><?php echo date("F d, Y", strtotime($faculty['attendance_date'])); ?></td>
										<td><?php echo $faculty['attendance']; ?></td>
									</tr>
                                    <?php endforeach; ?>
                                </table>
								<?php else: ?>
								<p>No faculty data available.</p>
								<?php endif; ?>
                                    
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="modal" id="delete_staff" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<?php echo form_open(admin_url('hr_profile/delete_staff',array('delete_staff_form'))); ?>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"><?php echo _l('delete_staff'); ?></h4>
				</div>
				<div class="modal-body">
					<div class="delete_id">
						<?php echo form_hidden('id'); ?>
					</div>
					<p><?php echo _l('delete_staff_info'); ?></p>
					<?php
					echo render_select('transfer_data_to',$staff_members,array('staffid',array('firstname','lastname')),'staff_member',get_staff_user_id(),array(),array(),'','',false);
					?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>
					<button type="submit" class="btn btn-danger _delete"><?php echo _l('hr_confirm'); ?></button>
				</div>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>

	<div class="modal fade" id="staff_chart_view" tabindex="-1" role="dialog">
		<div class="modal-dialog w-100 h-100">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">
						<span class="edit-title"><?php echo _l('hr_staff_chart'); ?></span>
					</h4>
				</div>
				<div class="modal-body">
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12" id="st_chart">
								<div id="staff_chart"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="modal_wrapper"></div>
	<?php init_tail(); ?>
	<?php 
	require('modules/hr_profile/assets/js/hr_record/hr_record_js.php');
	?>
	<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
	<script>

    function deleteFaculty(id) {
        var confirmDelete = confirm('Are you sure you want to delete faculty with ID ' + id + '?');
        
        if (confirmDelete) {
            // Perform delete operation using AJAX
            $.ajax({
                type: 'POST',
                url: admin_url + 'hr_profile/Uncaught SyntaxError: Unexpected identifier 'hr_profile'', // Replace with the actual server-side script
                data: { id: id },
                success: function(response) {
                    // Handle the response from the server
                    // alert(response);
                    // You may choose to reload the page or update the UI as needed
                    location.reload();
                },
                error: function(error) {
                    console.error('Error deleting faculty:', error);
                    alert('Error deleting faculty. Please try again.');
                }
            });
        }
    }
</script>
</body>
</html>
