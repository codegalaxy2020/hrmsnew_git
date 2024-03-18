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
        .folder {
        margin-right: 10px;
        background: #ffa200;
        border-radius: 6px;
        padding: 5px;
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
                        <ul style="display: flex;">
      <?php foreach ($folders as $folder): ?>
         <li class="folder"><a style="color: #fff!important;" href="<?= base_url('hr_profile/show_folder/' . $folder->folder_name) ?>"><?= $folder->folder_name ?></a></li>
      <?php endforeach; ?>
   </ul>

   <h2>Create Folder</h2>
   <input type="text" id="folder_name" placeholder="Folder Name">
   <button id="add_button" class="btn btn-info">Add Folder</button>
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
      $(document).ready(function() {
         $('#add_button').click(function() {
            var folderName = $('#folder_name').val();

            $.ajax({
               url: '<?= base_url('hr_profile/create_folder') ?>',
               type: 'POST',
               data: { folder_name: folderName },
               dataType: 'json',
               success: function(response) {
                  if (response.status === 'success') {
                     alert(response.message);
                     location.reload();
                  } else {
                     alert('Error: ' + response.message);
                  }
               },
               error: function() {
                  alert('Error: Unable to communicate with the server.');
               }
            });
         });
      });
   </script>
    </body>
    </html>
