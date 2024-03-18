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
        <div class="table-responsive">
			<table class="table table-bordered table-sm" id="table-table_training_feedback">
				<thead>
					<tr>
						<th>#</th>
						<th><?= _l('traning_feedback_staff_name') ?></th>
						<th><?= _l('traning_feedback_traning_name') ?></th>
						<th><?= _l('traning_content') ?></th>
						<th><?= _l('traning_effectiveness') ?></th>
						<th><?= _l('traning_experience') ?></th>
						<th><?= _l('traning_feedback') ?></th>
						<th><?= _l('hr_datecreator') ?></th>
						<?php if(is_admin()): ?>
						<th><?= _l('traning_feedback_action') ?></th>
						<?php endif; ?>
					</tr>
				</thead>
				<tbody>
					
				</tbody>
			</table>
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
</script>