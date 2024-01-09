<!--
	Author: Deep Basak
	ON: January 09, 2024
	IDE: VS Code
-->
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
    <div role="tabpanel" class="tab-pane" id="training_program">
        <div class="table-responsive">
			<table class="table table-bordered table-sm" id="table-table_training_feedback">
				<thead>
					<tr>
						<th>#</th>
						<th><?= _l('traning_feedback_staff_name') ?></th>
						<th><?= _l('traning_feedback_traning_name') ?></th>
						<th><?= _l('hr_training_type') ?></th>
						<th><?= _l('traning_feedback') ?></th>
						<th><?= _l('hr_datecreator') ?></th>
						<th><?= _l('traning_feedback_action') ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					if(!empty($training_feedback)):
						foreach($training_feedback as $key => $value):
					?>
					<tr>
						<td><?= $key+1 ?></td>
						<td><?= $value->firstname.' '.$value->lastname ?></td>
						<td><?= $value->training_name ?></td>
						<td><?= $value->name ?></td>
						<td><?= $value->feedback ?></td>
						<td><?= date("F d, Y", strtotime($value->created_at)) ?></td>
						<td></td>
					</tr>
					<?php
						endforeach;
					endif;
					?>
				</tbody>
			</table>
		</div>
    </div>
</div>