<!--
	Author: Deep Basak
	ON: January 09, 2024
	IDE: VS Code
-->
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
	<?php if(!is_admin()): ?>
	<div role="tabpanel" class="tab-pane d-flex justify-content-end" id="training_feedback_add">
		<button class="btn btn-primary btn-sm" onclick="openModal(1)">Add Feedback</button>
	</div><br>
	<?php endif; ?>
    <div role="tabpanel" class="tab-pane" id="training_feedback">

        <div class="row">
			<div class="col-md-6">
				<div id="chart"></div>
			</div>

			<div class="col-md-6">
				<div class="mb-3">
                    <label for="" class="form-label">Select Training<span class="text-danger">*</span></label>
                    <select class="form-control form-control-sm" name="training_id" id="training_id" onchange="generateApexChart(this.value)">
                        <option value="" selected disabled>Select Content</option>
                        <?php
						if(!empty($training_list)):
							foreach($training_list as $key => $val):
						?>
						<option value="<?= $val->training_process_id ?>"><?= $val->training_name ?></option>
						<?php
							endforeach;
						endif;
						?>
                    </select>
                </div>
			</div>
		</div>

    </div>
</div>

<!-- Defining CSRF Token in JS Format -->
<input type="hidden" name="token_name" id="token_name" value="<?= $this->security->get_csrf_token_name() ?>">
<input type="hidden" name="token_hash" id="token_hash" value="<?= $this->security->get_csrf_hash() ?>">

<?php $this->load->view('training/components/training_feedback_modal') ?>
<script type="text/javascript">
	var baseUrl = '<?= base_url() ?>';
	var pageURL = 'admin/hr_profile/';
	var trainingId = '<?= $training_list[0]->training_process_id ?>';
</script>