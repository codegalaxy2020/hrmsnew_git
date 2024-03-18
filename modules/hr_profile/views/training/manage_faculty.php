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
        .table>tbody>tr>td, .table>tfoot>tr>td {
    color: #000000;
}
    </style>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">						
						<div class="row">
						<div class="col-md-12">
							<table class="table" id="staffTable">
								<thead>
									<tr>
										<th>ID</th>
										<th>Name</th>
										<th>Role</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<!-- Table rows will be dynamically added here -->
								</tbody>
							</table>
						</div>
					</div>


						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="assignTaskModal" tabindex="-1" role="dialog" aria-labelledby="assignTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="padding: 18px;">
            <!-- Task Assignment Form -->
            <form id="taskAssignmentForm">
                <div class="form-group">
                    <label for="taskName">Task Name</label>
                    <input type="text" class="form-control" id="taskName" name="taskName" required>
                </div>
                <div class="form-group">
                    <label for="startDate">Task Start Date</label>
                    <input type="date" class="form-control" id="startDate" name="startDate" required>
                </div>
                <div class="form-group">
                    <label for="dueDate">Task Due Date</label>
                    <input type="date" class="form-control" id="dueDate" name="dueDate" required>
                </div>
                <div class="form-group">
                    <label for="taskDescription">Task Description</label>
                    <textarea class="form-control" id="taskDescription" name="taskDescription" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="taskPriority">Task Priority</label>
                    <select class="form-control" id="taskPriority" name="taskPriority" required>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Assign Task</button>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="assignCourseModal" tabindex="-1" role="dialog" aria-labelledby="assignCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="padding: 18px;">
        <form id="assignCourseForm">
        <div class="form-group">
            <label for="courseDropdown">Select Course(s):</label>
            <select class="form-control" name="courseDropdown" id="courseDropdown">
                <?php
                $query = $this->db->get('tblcourse');
                $options = "";
                
                $options .="<option value=''>Select Course</option>";
                foreach ($query->result() as $row) {
                    $options .= "<option value='{$row->CourseID}'>{$row->CourseName}</option>";
                }
                
                // Return the options
                echo $options;
                ?>
            </select>
            </div>
            <button type="submit" class="btn btn-primary">Assign</button>
        </form>
        </div>
    </div>
</div>

<div id="modal_wrapper"></div>
	<?php init_tail(); ?>
	<?php 
	require('modules/hr_profile/assets/js/hr_record/hr_record_js.php');
	?>
	<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
	<script>
    function openAssignTaskModal(userId) {
        $('#assignTaskModal').modal('show');
        $('#assignTaskModal').data('userId', userId);
    }

    function openAssignCourseModal(userId) {
        $('#assignCourseModal').modal('show');
        $('#assignCourseModal').data('userId', userId);
    }

    

	function openViewTasksModal(userId) {
    // Perform Ajax request to fetch tasks for the user
    $.ajax({
        url: "<?php echo base_url('hr_profile/get_user_tasks'); ?>",
        type: "GET",
        data: { userId: userId },
        dataType: "json",
        success: function (data) {
            // Create a table to display tasks
            var modalContent = '<div class="modal fade" id="viewTasksModal" tabindex="-1" role="dialog" aria-labelledby="viewTasksModalLabel" aria-hidden="true">';
            modalContent += '<div class="modal-dialog" role="document">';
            modalContent += '<div class="modal-content">';
            modalContent += '<div class="modal-header">';
            modalContent += '<h5 class="modal-title" id="viewTasksModalLabel">Tasks for User ID: ' + userId + '</h5>';
            modalContent += '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
            modalContent += '<span aria-hidden="true">&times;</span>';
            modalContent += '</button>';
            modalContent += '</div>';
            modalContent += '<div class="modal-body">';
            modalContent += '<table class="table">';
            modalContent += '<thead><tr><th>Task Name</th><th>Start Date</th><th>Due Date</th><th>Description</th><th>Priority</th><th>Status</th></tr></thead>';
            modalContent += '<tbody>';

            // Populate the table with fetched task data
            $.each(data.tasks, function (index, task) {

                //.....colors....
                var backgroundColor;

                switch (task.status) {
                case 'work_in_progress':
                    backgroundColor = 'lightblue';
                    break;
                case 'pending':
                    backgroundColor = '#ffff75';
                    break;
                case 'not_possible':
                    backgroundColor = 'lightcoral';
                    break;
                case 'done':
                    backgroundColor = 'lightgreen';
                    break;
                default:
                    backgroundColor = 'white'; // Set a default color or handle other cases as needed
                }
                modalContent += '<tr style="background-color: ' + backgroundColor + ';">';
                modalContent += '<td>' + task.task_name + '</td>';
                modalContent += '<td>' + task.start_date + '</td>';
                modalContent += '<td>' + task.due_date + '</td>';
                modalContent += '<td>' + task.description + '</td>';
                modalContent += '<td>' + task.priority + '</td>';
                modalContent += '<td>' + task.status.replace(/_/g, ' ').toUpperCase() + '</td>';
                modalContent += '</tr>';
            });

            modalContent += '</tbody></table>';
            modalContent += '</div>';
            modalContent += '<div class="modal-footer">';
            modalContent += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
            modalContent += '</div>';
            modalContent += '</div>';
            modalContent += '</div>';
            modalContent += '</div>';

            // Append the modal to the body and show it
            $('body').append(modalContent);
            $('#viewTasksModal').modal('show');
        },
        error: function (error) {
            console.log(error);
            // Handle error if needed
        }
    });
}


    $(document).ready(function () {
        // Fetch staff data using Ajax
        $.ajax({
            url: "<?php echo base_url('hr_profile/get_staff_data'); ?>",
            type: "GET",
            dataType: "json",
            success: function (data) {
                // Clear existing rows
                $('#staffTable tbody').empty();

                // Populate the table with fetched data
                $.each(data.staff_data, function (index, staff) {
                    var assignTaskButton = '<button onclick="openAssignTaskModal(' + staff.staffid + ')" class="btn btn-primary">Assign Task</button>';
                    var assignCourseButton = '<button onclick="openAssignCourseModal(' + staff.staffid + ')" class="btn btn-success">Assign Course</button>';
                    var viewTasksButton = '<button onclick="openViewTasksModal(' + staff.staffid + ')" class="btn btn-info">View Tasks</button>';
					$('#staffTable tbody').append('<tr><td>' + (index + 1) + '</td><td>' + staff.firstname + ' ' + staff.lastname + '</td><td>' + staff.name + '</td><td>' + assignTaskButton + ' ' + assignCourseButton + ' ' + viewTasksButton + '</td></tr>');
                });
            },
            error: function (error) {
                console.log(error);
            }
        });

        // Submit task assignment form
        $('#taskAssignmentForm').submit(function (e) {
            e.preventDefault();

            // Get user ID from the modal data
            var userId = $('#assignTaskModal').data('userId');

			// Include CSRF token in the data
			var formData = $(this).serialize() + "&userId=" + userId + "&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>";

            // Perform Ajax submit (replace this with your actual Ajax call)
            $.ajax({
                url: "<?php echo base_url('hr_profile/assign_task'); ?>",
                type: "POST",
                data: formData,
                success: function (response) {
                    // Close the modal
                    $('#assignTaskModal').modal('hide');

							Swal.fire({
						icon: 'success',
						title: 'Success',
						text: 'Task Assigned Successfully',
					});
                },
                error: function (error) {
                    console.log(error);
                    // Handle error if needed
                }
            });
        });

        $('#assignCourseForm').submit(function (e) {
            e.preventDefault();

            // Get user ID from the modal data
            var userId = $('#assignCourseModal').data('userId');

			// Include CSRF token in the data
			var formData = $(this).serialize() + "&userId=" + userId + "&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>";

            // Perform Ajax submit (replace this with your actual Ajax call)
            $.ajax({
                url: "<?php echo base_url('hr_profile/assign_course'); ?>",
                type: "POST",
                data: formData,
                success: function (response) {
                    // Close the modal
                    $('#assignCourseModal').modal('hide');

							Swal.fire({
						icon: 'success',
						title: 'Success',
						text: 'Course Assigned Successfully',
					});
                },
                error: function (error) {
                    console.log(error);
                    // Handle error if needed
                }
            });
        });
    });
</script>
</body>
</html>
