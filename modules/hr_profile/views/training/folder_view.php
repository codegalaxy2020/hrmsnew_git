<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php  init_head(); ?>


<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">						
						<div class="row">
						<div class="col-md-12">
                  <h2>Folder: <?= $folder_name ?></h2>
                  <ul style="display: flex;" id="file_list">
      <?php foreach ($files as $file): ?>
         <li data-file="<?= $file ?>">
            <?php if (pathinfo($file, PATHINFO_EXTENSION) === 'png'): ?>
               <img src="<?= base_url('staffuploads/' . $folder_name . '/' . $file) ?>" alt="<?= $file ?>" style="max-width: 100px; max-height: 100px;">
            <?php elseif (pathinfo($file, PATHINFO_EXTENSION) === 'pdf'): ?>
               <img src="<?= base_url('staffuploads/' . $folder_name . '/' . $file) ?>" alt="PDF" style="max-width: 100px; max-height: 100px;">
            <?php else: ?>
               <span><?= $file ?></span>
            <?php endif; ?>
            <div>
               <button class="download_button btn btn-success" data-file="<?=$file?>">Download</button>
               <button class="delete_button btn btn-danger">Delete</button>
            </div>
         </li>
      <?php endforeach; ?>
   </ul>

   <h2>Upload File</h2>
   <form id="upload_form" enctype="multipart/form-data">
      <input type="file" name="userfile" id="userfile">
      <input type="hidden" name="folder_name" value="<?= $folder_name ?>">
      <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
      <button type="button" class="btn btn-info" id="upload_button">Upload File</button>
   </form>
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
         $('#upload_button').click(function() {
            var formData = new FormData($('#upload_form')[0]);

            $.ajax({
               url: '<?= base_url('hr_profile/upload_file1') ?>',
               type: 'POST',
               data: formData,
               dataType: 'json',
               contentType: false,
               processData: false,
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

         // Download Button Click Event
         $('#file_list').on('click', '.download_button', function() {
            var fileName = $(this).data('file');
            var filePath = '<?= $folder_name ?>/' + fileName;
            window.location.href = '<?= base_url('hr_profile/download_file/') ?>' + filePath + '/' + encodeURIComponent(fileName);
         });

         // Delete Button Click Event
         $('#file_list').on('click', '.delete_button', function() {
            var fileName = $(this).closest('li').data('file');
            $.ajax({
               url: '<?= base_url('hr_profile/delete_file') ?>',
               type: 'POST',
               data: { folder_name: '<?= $folder_name ?>', file_name: fileName },
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

      // Function to check if the file is an image
      function is_image(file) {
         var image_extensions = ['jpg', 'jpeg', 'png', 'gif'];
         var ext = file.split('.').pop().toLowerCase();
         return image_extensions.includes(ext);
      }

      // Function to check if the file is a PDF
      function is_pdf(file) {
         var pdf_extensions = ['pdf'];
         var ext = file.split('.').pop().toLowerCase();
         return pdf_extensions.includes(ext);
      }
   </script>
    </body>
    </html>
