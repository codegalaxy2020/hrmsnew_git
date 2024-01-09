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
							<?php if (is_admin() || has_permission('hrm_hr_records','','create') || has_permission('hrm_hr_records','','edit')) { ?>

								<a href="<?php echo admin_url('hr_profile/faculty'); ?>" class="btn mright5 btn-info pull-left display-block ">Add Faculty</a>
								
							<?php } ?>

							
						</div>
						<br>
						

						<div class="row">
							<div class="col-md-12">
							<?php if (!empty($faculty_data)): ?>
                                <table border="1">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Action</th>
                                        
                                    </tr>
                                    <?php foreach ($faculty_data as $index => $faculty): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo $faculty['faculty']; ?></td>
                                           <td class="action-buttons">
                                            <a href="<?php echo base_url('hr_profile/faculty/' . $faculty['id']); ?>" class="edit-button btn btn-info">Edit</a>
                                            <button class="delete-button btn btn-danger" onclick="deleteFaculty(<?php echo $faculty['id']; ?>)">Delete</button>
                                        </td>
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
