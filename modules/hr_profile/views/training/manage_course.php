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
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addCourseModal">Add Course</button>
						<?php }?>
                        <div class="col-md-12">
							<table class="table" id="staffTable">
								<thead>
									<tr>
										<th>ID</th>
										<th>Course Name</th>
										<th>Course Duration</th>
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
    <div class="modal fade" id="addCourseModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Course</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Course form goes here -->
                <form id="courseForm" enctype="multipart/form-data">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label for="CourseName">Course Name</label>
                        <input type="text" class="form-control" id="CourseName" name="CourseName" required>
                    </div>
                    <div class="form-group">
                        <label for="CourseDuration">Course Duration (hr.)</label>
                        <input type="number" class="form-control" id="CourseDuration" name="CourseDuration" required>
                    </div>
                    <div class="form-group">
                        <label for="CourseDescription">Course Description</label>
                        <textarea class="form-control" id="CourseDescription" name="CourseDescription" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="CourseFiles">Course Files</label>
                        <input type="file" class="form-control-file" id="CourseFiles" name="CourseFiles" accept=".pdf, .doc, .docx">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Edit Course Modal -->
<div class="modal fade" id="editCourseModal" tabindex="-1" role="dialog" aria-labelledby="editCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCourseModalLabel">Edit Course</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editCourseForm" enctype="multipart/form-data">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="courseID" id="courseID">  
                <div class="form-group">
                        <label for="editCourseName">Course Name</label>
                        <input type="text" class="form-control" id="editCourseName" name="editCourseName">
                    </div>
                    <div class="form-group">
                        <label for="editCourseDuration">Course Duration</label>
                        <input type="text" class="form-control" id="editCourseDuration" name="editCourseDuration">
                    </div>
                    <div class="form-group">
                        <label for="editCourseDescription">Course Description</label>
                        <textarea class="form-control" id="editCourseDescription" name="editCourseDescription"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editCourseFiles">Course Files</label>
                        <input type="file" class="form-control-file" id="editCourseFiles" name="editCourseFiles" accept=".pdf, .doc, .docx">
                        <small id="editCourseFilesLabel" class="form-text text-muted"></small>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="feedbackModal" tabindex="-1" role="dialog" aria-labelledby="feedbackModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="feedbackModalLabel">Give Feedback</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" class="form-control" id="courseId" name="courseId">
        <label for="feedbackTextarea">Feedback:</label>
        <textarea class="form-control" id="feedbackTextarea" rows="4"></textarea>
        
        <label for="ratingInput">Rating:</label>
        <input type="number" class="form-control" id="ratingInput" min="1" max="5">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-info" id="feedbackSubmitBtn">submit</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal Structure -->
<div class="modal fade" id="viewfeedbackModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Feedback Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table" id="feedbackTable">
          <thead>
            <tr>
              <th>Feedback</th>
              <th>Rating</th>
            </tr>
          </thead>
          <tbody>
            <!-- Feedback details will be appended here dynamically -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
    $(document).ready(function () {
        fetchData();
    });
    function fetchData() {
        $.ajax({
            url: '<?php echo base_url('hr_profile/get_course'); ?>',
            method: 'POST',
            dataType: 'json',
            success: function (data) {
                $('#staffTable tbody').empty();

                $.each(data, function (index, task) {
                    var rowIndex = index + 1;
                    var newRow = '<tr>' +
                        '<td>' + rowIndex + '</td>' +
                        '<td>' + task.CourseName + '</td>' +
                        '<td>' + task.CourseDuration + '</td>' +
                        '<td>' + task.CourseStatus + '</td>' +
                        '<td>';

                    // Check if the user is an admin
                    <?php if(is_admin()){ ?>
                        newRow += '<button style="margin-right: 5px;" class="btn btn-primary btn-sm" onclick="downloadCourseFile(\'' + task.CourseFiles + '\')">Download Course File</button>' +
                                '<button style="margin-right: 5px;" class="btn btn-info btn-sm ml-1" onclick="editCourse(' + task.CourseID + ', \'' + task.CourseName + '\', \'' + task.CourseDuration + '\', \'' + task.CourseDescription + '\', \'' + task.CourseFiles + '\')">Edit</button>' +
                                '<button style="margin-right: 5px;" class="btn btn-danger btn-sm ml-1" onclick="confirmDelete(' + task.CourseID + ')">Delete</button>'+
                                '<button class="btn btn-success btn-sm ml-1 btn-view-feedback" data-toggle="modal" data-target="#viewfeedbackModal" data-course-id="' + task.CourseID + '">View Feedbacks</button>';
                    <?php } else { ?>
                        newRow += '<button style="margin-right: 5px;" class="btn btn-primary btn-sm" onclick="downloadCourseFile(\'' + task.CourseFiles + '\')">Download Course File</button>' +
                                  '<button class="btn btn-success btn-sm ml-1" data-toggle="modal" data-target="#feedbackModal" data-course-id="' + task.CourseID + '">Give Feedback</button>';

                    <?php } ?>

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

    $('#feedbackModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); 
    var courseId = button.data('course-id');
    $('#courseId').val(courseId);
    $('#feedbackTextarea').val('');
    $('#ratingInput').val('');
    });

    $('#feedbackSubmitBtn').on('click', function () {
        // var feedbackData = [
            
        // ];

        // Send AJAX request to the CodeIgniter controller
        $.ajax({
            url: '<?php echo base_url('hr_profile/addcoursefeedback'); ?>',
            type: 'POST',
            data: {
            course_id: $('#courseId').val(),
            feedback: $('#feedbackTextarea').val(),
            rating: $('#ratingInput').val()
            }
            ,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Display success SweetAlert
                    Swal.fire({
                        title: 'Feedback Submitted!',
                        icon: 'success'
                    });

                    // Close the modal
                    $('#feedbackModal').modal('hide');
                } else {
                    // Display error SweetAlert if needed
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to submit feedback. Please try again.',
                        icon: 'error'
                    });
                }
            },
            error: function () {
                // Display error SweetAlert
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to submit feedback. Please try again.',
                    icon: 'error'
                });
            }
        });
    });


    function updateStatus(taskId, newStatus) {
    $.ajax({
        url: '<?php echo base_url('hr_profile/update_status'); ?>',
        method: 'POST',
        data: {
            task_id: taskId,
            new_status: newStatus
        },
        success: function (response) {
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

    function downloadCourseFile(fileUrl) {
    window.open('<?php echo base_url('uploads/course_files/'); ?>' + fileUrl, '_blank');
    }

    function editCourse(courseID, courseName, courseDuration, courseDescription, courseFiles) {
    $('#courseID').val(courseID);    
    $('#editCourseName').val(courseName);
    $('#editCourseDuration').val(courseDuration);
    $('#editCourseDescription').val(courseDescription);
    $('#editCourseFiles').val('');  
    $('#editCourseFilesLabel').text(courseFiles); 
    $('#editCourseModal').modal('show');
}
    $(document).ready(function() {
    $('#addCourseModal').on('show.bs.modal', function (e) {
        $('#courseForm')[0].reset();
    });

    $('#courseForm').submit(function(e) {
        e.preventDefault();

        
        var formData = new FormData($('#courseForm')[0]);
        console.log(formData);
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url('hr_profile/add_course'); ?>', 
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
                text: 'Course added successfully.',
            });
            },
            error: function(error) {
                // Handle error
                console.error('Error:', error);
            }
        });
    });

    // Function to handle editing a course
function editCourse(courseID, courseName, courseDuration, courseDescription, courseFiles) {
    // Populate the modal with the course data
    $('#editCourseID').val(courseID);
    $('#editCourseName').val(courseName);
    $('#editCourseDuration').val(courseDuration);
    $('#editCourseDescription').val(courseDescription);
    
    // Clear the file input field and set the filename in a label or another element
    $('#editCourseFiles').val('');  // Clear the file input
    $('#editCourseFilesLabel').text(courseFiles);  // Set the filename in a label or another element

    // Open the edit modal
    $('#editCourseModal').modal('show');
}

// Function to handle form submission for editing a course
$('#editCourseForm').submit(function(e) {
    e.preventDefault();

    // Get form data
    var formData = new FormData($('#editCourseForm')[0]);

    // Use Ajax to submit the form data
    $.ajax({
        type: 'POST',
        url: '<?php echo base_url('hr_profile/update_course'); ?>', // Replace with the actual backend script URL for updating
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            // Handle success
            $('#editCourseModal').modal('hide'); // Close the modal
            fetchData();
                Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Course update successfully.',
            });
        },
        error: function(error) {
            // Handle error
            console.error('Error:', error);
        }
    });
});

    
});
function confirmDelete(courseID) {
    var willDelete = confirm("Are you sure? Once deleted, you will not be able to recover this course!");

    if (willDelete) {
        // Perform the delete action here, for example, an AJAX request
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url('hr_profile/delete_course'); ?>',
            data: { courseID: courseID },
            success: function (response) {
                // Check the response from the server
                if (response.success) {
                    // If the deletion was successful, remove the row from the table
                    $('#staffTable tbody').find('tr[data-course-id="' + courseID + '"]').remove();
                    alert("Course deleted successfully!");
                    fetchData();
                } else {
                    // If deletion failed, show an error message
                    alert("Error deleting course!");
                }
            },
            error: function (xhr, status, error) {
                // Handle AJAX error
                console.error(xhr.responseText);
                alert("Error deleting course!");
            }
        });
    }
}
// JavaScript code
$(document).ready(function() {
  $(document).on('click', '.btn-view-feedback', function() {
    var courseId = $(this).data('course-id');
    // alert(courseId);
    $.ajax({
      type: 'GET',
      url: '<?php echo base_url('hr_profile/get_course_details'); ?>', // Replace with your server endpoint
      data: { courseId: courseId },
      success: function(data) {
        updateModalContent(data);
        $('#viewfeedbackModal').modal('show');
      },
      error: function(err) {
        console.error('Error fetching feedback details:', err);
      }
    });
  });

  // Function to update modal content with feedback details
  function updateModalContent(feedbackData) {
    var tableBody = $('#feedbackTable tbody');
    tableBody.empty(); // Clear existing content

    // Iterate through feedbackData and append rows to the table
    for (var i = 0; i < feedbackData.length; i++) {
      var feedback = feedbackData[i].feedback;
      var rating = feedbackData[i].rating;

      // Append a new row to the table
      tableBody.append('<tr><td>' + feedback + '</td><td>' + rating + '</td></tr>');
    }
  }
});

</script>
</body>
</html>
