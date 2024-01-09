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

								<a href="<?php echo admin_url('hr_profile/add_cost_tracking'); ?>" class="btn mright5 btn-info pull-left display-block ">Add Cost Tracking</a>
								
							<?php } ?>

							
						</div>
						<br>
						

						<div class="row">
							<div class="col-md-12">
							<?php if (!empty($faculty_data)): ?>
                                <table border="1">
                                    <tr>
            <th>ID</th>
            <th>Training Program</th>
            <th>Salary</th>
            <th>Bonuses</th>
            <th>Commissions</th>
            <th>Allowances</th>
            <th>Benefits</th>
            <th>Healthcare</th>
            <th>Retirement Plans</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
        <?php foreach ($faculty_data as $index => $faculty): ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td><?php echo $faculty['training_name']; ?></td>
                <td><?php echo $faculty['salary']; ?></td>
                <td><?php echo $faculty['bonuses']; ?></td>
                <td><?php echo $faculty['commissions']; ?></td>
                <td><?php echo $faculty['allowances']; ?></td>
                <td><?php echo $faculty['benefits']; ?></td>
                <td><?php echo $faculty['healthcare']; ?></td>
                <td><?php echo $faculty['retirement_plans']; ?></td>
                <td><?php echo $faculty['total']; ?></td>
                <td class="action-buttons">
                    <a href="<?php echo base_url('hr_profile/add_cost_tracking/' . $faculty['id']); ?>" class="edit-button btn btn-info">Edit</a>
                    <button class="delete-button btn btn-danger" onclick="deleteFaculty(<?php echo $faculty['id']; ?>)">Delete</button>
                    <button class="allocate-button btn btn-primary" onclick="openAllocationModal(<?php echo $faculty['id']; ?>)">Allocate</button>
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
	<div class="modal" id="allocationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Allocate Cost</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- User Dropdown -->
                <label for="userDropdown">Select User:</label>
                <select class="form-control" id="userDropdown" name="user_id">
                            <?php
                            // Fetch data from tblstaff where admin not equal to 1
                            $users = $this->db->where('admin !=', 1)->get('tblstaff')->result_array();
                    
                            // Loop through the users and populate the dropdown
                            foreach ($users as $user) {
                                echo '<option value="' . $user['staffid'] . '">' . $user['firstname'] . ' ' . $user['lastname'] . '</option>';
                            }
                            ?>
                        </select>

                <!-- Cost Type Dropdown -->
                <label for="costTypeDropdown">Select Cost Type:</label>
                <select id="costTypeDropdown" class="form-control">
                    <option value="direct">Direct Cost</option>
                    <option value="indirect">Indirect Cost</option>
                    <option value="all">All</option>
                </select>
                <input type="hidden" id="rowIdInput" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="allocateCost()">Allocate</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                url: admin_url + 'hr_profile/', // Replace with the actual server-side script
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
<script>
    function openAllocationModal(rowId) {
        // Code to open the modal and display user dropdown and cost selection
        // You can use Bootstrap or any other modal library

        // Example: Bootstrap Modal
        $('#allocationModal').modal('show');

        // Set the rowId in a hidden input for later use
        $('#rowIdInput').val(rowId);
    }

    function allocateCost() {
        // Code to handle the allocation and send AJAX request

        // Example: Get user id, row id, and cost value
        var userId = $('#userDropdown').val();
        var rowId = $('#rowIdInput').val();
        var costType = $('#costTypeDropdown').val();

        // AJAX request
        $.ajax({
            url: '<?php echo base_url('hr_profile/allocate_cost'); ?>',
            type: 'POST',
            data: {
                userId: userId,
                rowId: rowId,
                costType: costType
                // Add more data if needed
            },
            success: function(response) {
                // Handle success response
                console.log(response);
                location.reload();
            },
            error: function(error) {
                // Handle error response
                console.error(error);
            }
        });

        // Close the modal
        // $('#allocationModal').modal('hide');
    }
</script>
</body>
</html>
