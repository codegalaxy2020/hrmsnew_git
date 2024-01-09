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

								<a href="#" id="addCostTrackingBtn" class="btn mright5 btn-info pull-left display-block">Add Components</a>
								
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
            <th>Cost Type</th>
            <th>Action</th>
        </tr>
        <?php foreach ($faculty_data as $index => $faculty): ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td><?php echo $faculty['name']; ?></td>
                <td><?php echo $faculty['costtype']; ?></td>
                <td class="action-buttons">
                    <a href="#" class="edit-button btn btn-info"
    onclick="openEditModal('<?php echo $faculty['name']; ?>', '<?php echo $faculty['costtype']; ?>', <?php echo $faculty['id']; ?>)">
    View
</a>
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
	<div class="modal" id="allocationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Components</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Cost Type Dropdown -->
                <label for="costTypeDropdown">Select Cost Type:</label>
                <select id="costTypeDropdown" class="form-control">
                    <option value="" selected>Select Cost Type</option>
                    <option value="direct">Direct Cost</option>
                    <option value="indirect">Indirect Cost</option>
                    
                </select>
                <div class="form-group">
                    <label for="nameInput">Component Name:</label>
                    <input type="text" id="nameInput" class="form-control" placeholder="Enter Name">
                </div>
                <input type="hidden" id="rowIdInput" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="allocateCost()">Submit</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Components</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Edit Form -->
                <form id="editForm">
                    <label for="editName">Name:</label>
                    <input type="text" id="editName" class="form-control" disabled>

                    <label for="editCostType">Cost Type:</label>
                    <input type="text" id="editCostType" class="form-control" disabled>

                    <!-- Add other fields as needed -->

                    <input type="hidden" id="editFacultyId">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveEdit()">Save Changes</button>
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
    $(document).ready(function () {
        // When Add Cost Tracking button is clicked, open the modal
        $("#addCostTrackingBtn").click(function () {
            $("#allocationModal").modal("show");
        });
    });
</script>
<script>
    $(document).ready(function () {
        // When the Allocate button is clicked, handle the form submission via Ajax
        window.allocateCost = function () {
            event.preventDefault(); // Prevent the default form submission

            // Get form data
            var formData = {
                costType: $("#costTypeDropdown").val(),
                name: $("#nameInput").val()
                // Add other form fields if needed
            };

            // Ajax request
            $.ajax({
                type: "POST",
                url: "<?php echo admin_url('hr_profile/addelementscost'); ?>",
                data: formData,
                success: function (response) {
                    // Reload the page upon successful completion
                    location.reload();
                },
                error: function (error) {
                    // Handle the error if needed
                    console.error("Ajax request failed:", error);
                }
            });
        };
    });
</script>
<script>
    $(document).ready(function () {
        // Function to handle opening the edit modal
        window.openEditModal = function (name, costType, id) {
            $("#editName").val(name);
            $("#editCostType").val(costType);
            $("#editFacultyId").val(id);

            // Show the edit modal
            $("#editModal").modal("show");
        };

        // Function to handle saving edits (you can implement this function)
        window.saveEdit = function () {
            // Your logic to save edits here

            // Close the modal after saving
            $("#editModal").modal("hide");
        };

        // Function to handle deleting faculty (you can implement this function)
        window.deleteFaculty = function (id) {
            // Your logic to delete faculty here
        };
    });
</script>
</body>
</html>
