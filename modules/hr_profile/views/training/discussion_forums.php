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
                        <button class="btn btn-primary" data-toggle="modal" data-target="#adddiscussion_forums">Add Forums</button>
						<?php }?>
                        <div class="col-md-12">
							<table class="table" id="discussion_forums">
								<thead>
									<tr>
										<th>ID</th>
										<th>Forum Subject</th>
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

    <div class="modal fade" id="adddiscussion_forums" tabindex="-1" role="dialog" aria-labelledby="addDiscussionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDiscussionModalLabel">Add Course</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    
                    <form id="addDiscussionForm">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="form-group">
                            <label for="subject">Subject:</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="attached_file">Attached File:</label>
                            <input type="file" class="form-control-file" id="attached_file" name="attached_file">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewDetailsModal" tabindex="-1" role="dialog" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailsModalLabel">Forum Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="forumDetailsSection">
                </div>

                <hr style="border-top: 5px solid #b1cc57;">
                <div id="discussionsSection">
                </div>
                <hr style="border-top: 5px solid #b1cc57;">

                <div id="giveDiscussionSection">
                    <h5>Give Discussion/Comment</h5>
                    <form id="giveDiscussionForm">
                        <input type="hidden" name="fid" id="fid">
                        <div class="form-group">
                            <label for="userComment">Your Comment:</label>
                            <textarea class="form-control" id="userComment" name="userComment" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
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
        // Ajax request to add discussion forums
        $(document).ready(function () {
            $('#addDiscussionForm').submit(function (e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url("hr_profile/add_discussion_forum"); ?>', // Adjust the URL to your controller method
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        loadDiscussionForums();
                    },
                    error: function (error) {
                        // Handle errors
                        console.log(error.responseText);
                    }
                });

                // Close the modal
                $('#adddiscussion_forums').modal('hide');
            });

            // Open the modal when the button is clicked
            $('#addDiscussionButton').click(function () {
                $('#adddiscussion_forums').modal('show');
            });
        });
    </script>

<script>
    // Function to load discussion forums using Ajax
    function loadDiscussionForums() {
        $.ajax({
            type: 'GET',
            url: '<?php echo base_url("hr_profile/get_discussion_forums"); ?>',
            dataType: 'json',
            success: function (data) {
                // Clear existing rows
                $('#discussion_forums tbody').empty();

                // Populate the table with new data
                $.each(data, function (index, forum) {
                    var fileUrl = '<?php echo base_url()?>uploads/' + forum.attached_file;
                    var row = '<tr>' +
                        '<td>' + forum.forum_id + '</td>' +
                        '<td>' + forum.subject + '</td>' +
                        '<td>' +
                        '<button class="btn btn-info btn-sm" onclick="viewDetails(' + forum.forum_id + ')">Details</button> ' +
                        '<button style="margin-right: 4px;" class="btn btn-danger btn-sm" onclick="deleteForum(' + forum.forum_id + ')">Delete</button>' +
                        '<button class="btn btn-success btn-sm" onclick="downloadFile(\'' + fileUrl + '\')">Download</button>' +
                        '</td>' +
                        '</tr>';
                    $('#discussion_forums tbody').append(row);
                });
            },
            error: function (error) {
                console.log(error.responseText);
            }
        });
    }

    function viewDetails(forumId) {
        // alert(forumId);
        $('#fid').val(forumId);
        $('#forumDetailsSection').empty();
        $('#discussionsSection').empty();

        // Load forum details
        $.ajax({
            type: 'GET',
            url: '<?php echo base_url("hr_profile/get_forum_details"); ?>',
            data: { forum_id: forumId },
            dataType: 'json',
            success: function (forumDetails) {
                // Populate the first section with forum details
                var detailsHtml = '<h4>' + forumDetails.subject + '</h4>' +
                    '<p>Description: ' + forumDetails.description + '</p>' +
                    '<p>Attached File: ' + forumDetails.attached_file + '</p>';
                $('#forumDetailsSection').html(detailsHtml);
            },
            error: function (error) {
                console.log(error.responseText);
            }
        });

        // Load discussions for the forum
        $.ajax({
            type: 'GET',
            url: '<?php echo base_url("hr_profile/get_forum_discussions"); ?>',
            data: { forum_id: forumId },
            dataType: 'json',
            success: function (discussions) {
                // Populate the second section with discussions
                $.each(discussions, function (index, discussion) {
                    var discussionHtml = '<div>' +
                    '<p>User: ' + discussion.firstname + ' ' + discussion.lastname + '</p>'+
                        '<p>Message: ' + discussion.message + '</p>' +
                        '</div>';
                    $('#discussionsSection').append(discussionHtml);
                });
            },
            error: function (error) {
                console.log(error.responseText);
            }
        });

        // Show the modal
        $('#viewDetailsModal').modal('show');
    }

    function deleteForum(forumId) {
        if (confirm('Are you sure you want to delete this forum?')) {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url("hr_profile/delete_forum"); ?>',
                data: { forum_id: forumId },
                success: function (response) {
                    console.log(response);
                    // Reload the table after deletion
                    loadDiscussionForums();
                },
                error: function (error) {
                    console.log(error.responseText);
                }
            });
        }
    }

    $('#giveDiscussionForm').submit(function (e) {
        e.preventDefault();

        var forumId = $('#fid').val();
        var userComment = $('#userComment').val();

        $.ajax({
            type: 'POST',
            url: '<?php echo base_url("hr_profile/add_discussion"); ?>',
            data: { forum_id: forumId, message: userComment },
            success: function (response) {
                console.log(response);
                // Reload discussions after submission
                loadForumDiscussions(forumId);
                // Clear the comment textarea
                $('#userComment').val('');
            },
            error: function (error) {
                console.log(error.responseText);
            }
        });
    });


    // Load discussion forums on page load
    $(document).ready(function () {
        loadDiscussionForums();
    });

    function downloadFile(fileUrl) {
    window.open(fileUrl, '_blank');
}
</script>
	
</body>
</html>
