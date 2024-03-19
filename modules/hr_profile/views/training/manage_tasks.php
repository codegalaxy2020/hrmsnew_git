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
						<div class="row">
						<div class="col-md-12">
							<table class="table" id="staffTable">
								<thead>
									<tr>
										<th>ID</th>
										<th>Task Name</th>
										<th>Task Start Date</th>
                                        <th>Task Due Date</th>
                                        <th>Task Priority</th>
                                        <th>Status</th>
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
	
<div id="modal_wrapper"></div>
	<?php init_tail(); ?>
	<?php 
	require('modules/hr_profile/assets/js/hr_record/hr_record_js.php');
	?>
	<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
	<script>
        // Call the function when the page is ready
    $(document).ready(function () {
        fetchData();
    });
    // Function to fetch data and populate the table
    function fetchData() {
        // Make AJAX request
        $.ajax({
            url: '<?php echo base_url('hr_profile/get_tasks'); ?>',
            method: 'POST',
            dataType: 'json',
            success: function (data) {
                // Clear existing table rows
                $('#staffTable tbody').empty();

                $.each(data, function (index, task) {
                    var rowIndex = index + 1;

                    // Determine priority color
                    var priorityColor = '';
                    var priorityTextColor = '#fff';
                    switch (task.priority) {
                        case 'high':
                            priorityColor = '#ff3b3b';
                            priorityTextColor = '#fff';
                            break;
                        case 'medium':
                            priorityColor = '#ffdb9a';
                            priorityTextColor = '#000';
                            break;
                        case 'low':
                            priorityColor = '#81ff81';
                            priorityTextColor = '#000';
                            break;
                        default:
                            priorityColor = 'black'; // Default color for other cases
                            break;
                    }

                    var statusOptions = '<select class="form-control" onchange="updateStatus(' + task.task_id + ', this.value)">' +
                            '<option value="pending"' + (task.status === 'pending' ? ' selected' : '') + '>Pending</option>' +
                            '<option value="work_in_progress"' + (task.status === 'work_in_progress' ? ' selected' : '') + '>Work in Progress</option>' +
                            '<option value="done"' + (task.status === 'done' ? ' selected' : '') + '>Done</option>' +
                            '<option value="not_possible"' + (task.status === 'not_possible' ? ' selected' : '') + '>Not Possible</option>' +
                         '</select>';

                    var newRow = '<tr>' +
                        '<td>' + rowIndex + '</td>' +
                        '<td>' + task.task_name + '</td>' +
                        '<td>' + task.start_date + '</td>' +
                        '<td>' + task.due_date + '</td>' +                        
                        '<td style="background-color: ' + priorityColor + '; color: ' + priorityTextColor + ';">' + task.priority + '</td>' +
                        '<td>' + statusOptions + '</td>' +
                        '<td><button onclick="viewDescription(\'' + task.description + '\')" class="btn btn-primary">View Description</button></td>' +
                        '</tr>';

                    $('#staffTable tbody').append(newRow);
                });


            },
            error: function (xhr, status, error) {
                console.error('Error fetching data:', error);
            }
        });
    }

    function updateStatus(taskId, newStatus) {
    // Make an AJAX request to update the status in the database
    $.ajax({
        url: '<?php echo base_url('hr_profile/update_status'); ?>',
        method: 'POST',
        data: {
            task_id: taskId,
            new_status: newStatus
        },
        success: function (response) {
            // Display a SweetAlert modal indicating the success
            Swal.fire({
                title: 'Status Updated',
                text: 'Task status has been successfully updated.',
                icon: 'success'
            });
        },
        error: function (xhr, status, error) {
            console.error('Error updating status:', error);
        }
    });
}

    function viewDescription(description) {
        Swal.fire({
            title: 'Task Description',
            text: description,
            icon: 'info'
        });
    }
</script>
</body>
</html>
