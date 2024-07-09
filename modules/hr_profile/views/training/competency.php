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
                        <?php if(is_admin()){?>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addCourseModal">Add Competency</button>
						<?php }?>
                        <div class="col-md-12">
							<table class="table" id="staffTable">
								<thead>
									<tr>
										<th>ID</th>
										<th>Name</th>
										<th>Search By</th>
                                        <th>Search With</th>
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
    <div class="modal fade" id="addCourseModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Competency</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Course form goes here -->
                <form id="courseForm" enctype="multipart/form-data">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="form-group">
                <label for="searchCriteria">Competency Name</label>
                <input type="text" id="competency_name" name="competency_name" class="form-control" placeholder="Competency Name">
                </div>
                <div class="form-group">
                    <label for="searchCriteria">Search By</label>
                    <select class="form-control" id="searchCriteria" name="searchCriteria" required>
                        <option value="">Select a criteria</option>
                        <option value="Location">Location</option>
                        <option value="Department">Department</option>
                        <option value="Designation">Designation</option>
                        <option value="TotalService">Total Service</option>
                        <option value="DeptService">Dept. Service</option>
                        <option value="Age">Age</option>
                        <option value="Qualification">Qualification</option>
                        <option value="Experience">Current or Previous Experience</option>
                    </select>
                </div>

                <div class="form-group" id="searchEmployeeDiv" style="display:none;">
                    <label for="CourseName">Search Employee</label>
                    <input type="text" class="form-control" id="CourseName" name="CourseName" required>
                </div>

                <div id="searchResults" class="mt-3"></div>                  
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="assignJobModal" tabindex="-1" role="dialog" aria-labelledby="assignJobModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="assignJobModalLabel">Assign to Job</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form id="assignJobForm" method="post">
      <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                    <div class="form-group">
                        <label for="jobDropdown">Select Job</label>
                        <select class="form-control" id="jobDropdown" name="jobDropdown">
                            <option value="">Select a job</option>
                            <?php
                            $jobs = $this->db->select('training_process_id, training_name')
                                             ->get('tblhr_jp_interview_training')
                                             ->result_array();
                            foreach ($jobs as $job): ?>
                                <option value="<?php echo $job['training_process_id']; ?>"><?php echo $job['training_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input type="hidden" id="competencyId" name="competencyId" value="">
                    <input type="hidden" id="staffid" name="staffid" value="">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
      </div>
    </div>
  </div>
</div>


<div id="modal_wrapper"></div>
	<?php init_tail(); ?>
	
	<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>    
    <script>
        var modelId = 'requirement_modal';
    $(document).ready(function () {

        serverSideDataTable('staffTable', baseUrl + 'hr_profile/get_competency', 10);
    });
    </script>
	<script>
    $(document).ready(function () {
        // fetchData();
    });
    function fetchData() {
        $.ajax({
            url: '<?php echo base_url('hr_profile/get_competency'); ?>',
            method: 'POST',
            dataType: 'json',
            success: function (data) {
                $('#staffTable tbody').empty();

                $.each(data, function (index, task) {
                    var rowIndex = index + 1;
                    var newRow = '<tr>' +
                        '<td>' + rowIndex + '</td>' +
                        '<td>' + task.name + '</td>' +
                        '<td>' + task.search_by + '</td>' +
                        '<td>' + task.search_with + '</td>' +
                        '<td>';                    
                        newRow += '<button style="margin-right: 5px;" class="btn btn-info btn-sm ml-1" onclick="editCourse(' + task.id + ')">View</button>' +
                                '<button style="margin-right: 5px;" class="btn btn-danger btn-sm ml-1" onclick="confirmDelete(' + task.id + ')">Delete</button>';
                    

                    newRow += '</td>' +
                        '</tr>';

                    $('#staffTable tbody').append(newRow);
                });


            },
            error: function (xhr, status, error) {
                console.error('Error fetching data:', error);
            }
        });
    }
    var courseId;

    $(document).ready(function() {
    $('#addCourseModal').on('show.bs.modal', function (e) {
        $('#courseForm')[0].reset();
    });

    $('#courseForm').submit(function(e) {
        e.preventDefault();

        var formData = new FormData($('#courseForm')[0]);
        console.log(...formData);  // Log form data to verify

        $.ajax({
            type: 'POST',
            url: '<?php echo base_url('hr_profile/add_competency'); ?>',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                // Handle success
                $('#addCourseModal').modal('hide'); 
                fetchData();
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Competency added successfully.',
                });
                serverSideDataTable('staffTable', baseUrl + 'hr_profile/get_competency', 10);
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    });
});

</script>
<script>
        $(document).ready(function(){
            $('#searchCriteria').change(function(){
                var selectedValue = $(this).val();
                if(selectedValue) {
                    $('#searchEmployeeDiv').show();
                } else {
                    $('#searchEmployeeDiv').hide();
                }
            });

            $('#CourseName').blur(function(){
                var searchCriteria = $('#searchCriteria').val();
                var searchValue = $(this).val();

                if (searchCriteria && searchValue) {
                    $.ajax({
                        url: '<?php echo base_url('hr_profile/search'); ?>', // Adjust the URL to your CI controller method
                        method: 'POST',
                        data: {
                            criteria: searchCriteria,
                            value: searchValue
                        },
                        success: function(response) {
                            $('#searchResults').html(response);
                        }
                    });
                }
            });
        });
    </script>
    <script>
        function editCourse(id) {
            window.location.href = '<?= base_url('hr_profile/details/') ?>' + id;
        }

        function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        // imageUrl: 'path_to_your_smile_image', 
        imageWidth: 100,
        imageHeight: 100,
        imageAlt: 'Smile Image'
    }).then((result) => {
        if (result.isConfirmed) {
            
            $.ajax({
                url: '<?= base_url('hr_profile/delete_com') ?>',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            'Deleted!',
                            'The competency has been deleted.',
                            'success'
                        );
                        
                    } else {
                        Swal.fire(
                            'Error!',
                            'There was an error deleting the competency.',
                            'error'
                        );
                    }
                    fetchData();
                }
            });
        }
    });
}


    </script>
<script>
    function openAssignJobModal(id, staffid) {
        console.log("Assigning ID:", id);
        document.getElementById('competencyId').value = id;
        document.getElementById('staffid').value = staffid;
    }

    $(document).ready(function(){
    $('#assignJobForm').on('submit', function(e){
        e.preventDefault();

        $.ajax({
            url: "<?php echo site_url('admin/hr_profile/assign_job'); ?>",
            method: "POST",
            data: $(this).serialize(),
            success: function(response) {
                var res = JSON.parse(response);
                if (res.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: res.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: res.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Error!',
                    text: 'Error assigning job.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
});

</script>

</body>
</html>
