<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
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
	<div role="tabpanel" class="tab-pane <?php if(isset($tab) && $tab='others_training_program'){echo 'active';} ?>" id="training_program" >
		<div class="_buttons">
			<?php  if(is_admin() || has_permission('staffmanage_training','','create')) { ?>
				<a href="#" onclick="new_training_process(); return false;" class="btn btn-info pull-left display-block">
					<?php echo _l('hr_hr_add'); ?>
				</a>
			<?php } ?>
		</div>

		<div class="clearfix"></div>
		<br>

		<div class="modal bulk_actions" id="table_training_program_bulk_actions" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title"><?php echo _l('hr_bulk_actions'); ?></h4>
					</div>
					<div class="modal-body">
						<?php if(has_permission('staffmanage_training','','delete') || is_admin()){ ?>
							<div class="checkbox checkbox-danger">
								<input type="checkbox" name="mass_delete" id="mass_delete">
								<label for="mass_delete"><?php echo _l('hr_mass_delete'); ?></label>
							</div>
						<?php } ?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>

						<?php if(has_permission('staffmanage_training','','delete') || is_admin()){ ?>
							<a href="#" class="btn btn-info" onclick="training_program_delete_bulk_action(this); return false;"><?php echo _l('hr_confirm'); ?></a>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>

		<?php if (has_permission('staffmanage_training','','delete')) { ?>
			<a href="#"  onclick="training_program_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_training_program" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('hr_bulk_actions'); ?></a>
		<?php } ?>

        	<table border="1">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Time to Start</th>
                    <th>Time to End</th>
                    <th>Location</th>
                    <th>Venues</th>
                    <th>Action</th>
                    
                </tr>
                <?php
                $staffUserId = get_staff_user_id();
                $this->db->where("NOT FIND_IN_SET($staffUserId, staff_id)");
                $interview_training_data = $this->db->get('tblhr_jp_interview_training')->result_array();
                foreach ($interview_training_data as $index => $interview_training):
                ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                    <td><?php echo $interview_training['training_name']; ?></td>
                    <td><?php echo $interview_training['time_to_start']; ?></td>
                    <td><?php echo $interview_training['time_to_end']; ?></td>
                    <td><?php echo $interview_training['training_location']; ?></td>
                    <td><?php echo $interview_training['training_venues']; ?></td>
                    <td class="action-buttons">
                        <button class="join-button btn btn-success" onclick="askToJoin(<?php echo $interview_training['training_process_id']; ?>)">Ask to Join</button>
                    </td>
                    </tr>
                <?php endforeach; ?>
            </table>

		</div>
		<!-- training_program end -->
	</div>

	<div class="modal fade" id="job_position_training" tabindex="-1" role="dialog">
		<div class="modal-dialog new_job_positions_dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">
						<span class="add-title-training"><?php echo _l('hr_edit_training_process'); ?></span>
						<span class="edit-title-training"><?php echo _l('hr_new_training_process'); ?></span>
					</h4>
				</div>
				<?php echo form_open_multipart(admin_url('hr_profile/job_position_training_add_edit'),array('class'=>'job_position_training_add_edit','autocomplete'=>'off')); ?>
				<div class="modal-body">
					<div id="additional_form_training"></div>
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="interview_infor">

							<div class="row">
								<div class="col-md-6">
									<?php echo render_input('training_name', 'hr_training_name'); ?>
								</div>
								<div class="col-md-6">
									<label for="training_type" class="control-label"><?php echo _l('hr_training_type'); ?></label>
									<select onchange="training_type_change(this)" name="training_type" class="selectpicker" id="training_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
										<option value=""></option> 

										<?php foreach ($type_of_trainings as $key => $value) { ?>
											<option value="<?php echo $value['id'] ?>" <?php if(isset($position_training) && $position_training->training_type ==  $value['id'] ){echo 'selected';} ?> ><?php echo $value['name']  ?></option>

										<?php } ?>
										
									</select>

								</div>
							</div>

							<div class="row ">
								<div class="col-md-6">
									<label for="position_training_id" class="control-label get_id_row" value ="0" ><span class="text-danger">* </span><?php echo _l('hr_training_item'); ?></label>

									<select name="position_training_id[]" class="selectpicker mb-5" id="position_training_id[]" data-width="100%" data-live-search="true" multiple="true" data-actions-box="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-sl-id="e_criteria[0]" > 
									</select>
								</div>
								<div class="col-md-6">
									<?php $mint_point_f="1";
									$min_p =[];
									$min_p['min']='0';
									// $min_p['required']='true';

									?>
									<?php echo render_input('mint_point','hr_mint_point',$mint_point_f,'number', $min_p) ?>
								</div>
							</div>

							

							<div class="row ">
								<div class="col-md-12">
									<div class="form-group">
										<label for="staff_id" class="control-label"><?php echo _l('hr_hr_staff_name'); ?></label>
										<select name="staff_id[]" data-live-search="true" class="selectpicker" id="staff_id" data-width="100%" multiple="true"data-actions-box="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" > 
											<?php foreach ($staffs as $staff){ ?>
												<option value="<?php echo html_entity_decode($staff['staffid']) ?>"><?php echo html_entity_decode($staff['firstname'].' '.$staff['lastname']); ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								
								<div class="col-md-6">
									<?php
									echo render_date_input('time_to_start','hr_time_to_start'); ?>
								</div>
								<div class="col-md-6">
									<?php
									echo render_date_input('time_to_end','hr_time_to_end'); ?>
								</div>
							</div>

							<div class="row mb-4 onboading_hide">
								<div class="col-md-6">

									<label for="department_id" class="control-label get_id_row" value ="0" ><?php echo _l('hr_department'); ?></label>
									<select onchange="department_change(this)" name="department_id[]" class="selectpicker" id="department_id" data-width="100%" data-live-search="true" multiple="true" data-actions-box="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
										<?php foreach($hr_profile_get_department_name as $dp){ ?> 
											<option value="<?php echo html_entity_decode($dp['departmentid']); ?>"><?php echo html_entity_decode($dp['name']); ?></option>
										<?php } ?>

									</select>

								</div>

								<div class="col-md-6">

									<label for="job_position_id" class="control-label get_id_row" value ="0" ><span class="text-danger">* </span><?php echo _l('hr__position_apply'); ?></label>

									<select name="job_position_id[]" class="selectpicker" id="job_position_id" data-width="100%" data-live-search="true" multiple="true" data-actions-box="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" required> 
										<?php foreach($get_job_position as $p){ ?> 
											<option value="<?php echo html_entity_decode($p['position_id']); ?>" <?php if(isset($member) && $member->job_position == $p['position_id']){echo 'selected';} ?>><?php echo html_entity_decode($p['position_name']); ?></option>
										<?php } ?>
									</select>
									<div class="clearfix"></div>
									<br>
								</div>
							</div>
							<div class="row">
							<div class="col-md-6">
								<div class="form-group" app-field-wrapper="training_location">
								    <label for="training_location" class="control-label"> 
								    <small class="req text-danger">* </small>Training Location</label>
								    <input type="text" id="training_location" name="training_location" class="form-control" value="">
								    </div>
							</div>
							<div class="col-md-6">
								<div class="form-group" app-field-wrapper="training_venues">
								    <label for="training_venues" class="control-label"> 
								    <small class="req text-danger">* </small>Training Venues</label>
								    <input type="text" id="training_venues" name="training_venues" class="form-control" value="">
								    </div>
							</div>
							</div>
							<div class="row">
							<div class="col-md-6">
								<div class="form-group" app-field-wrapper="direct_cost">
								    <label for="direct_cost" class="control-label"> 
								    <small class="req text-danger">* </small>Direct Cost</label>
								    <input type="text" id="direct_cost" name="direct_cost" class="form-control" value="">
								    </div>
							</div>
							<div class="col-md-6">
								<div class="form-group" app-field-wrapper="indirect_cost">
								    <label for="indirect_cost" class="control-label"> 
								    <small class="req text-danger">* </small>Indirect cost</label>
								    <input type="text" id="indirect_cost" name="indirect_cost" class="form-control" value="">
								    </div>
							</div>
							</div>
                            <div class="row">
								<div class="col-md-12">

									<p class="bold">Special Needs</p>
									<?php $contents = ''; if(isset($project)){$contents = $project->special_needs ;} ?>
									<?php echo render_textarea('special_needs','',$contents,array(),array(),'','tinymce'); ?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">

									<p class="bold">Expectation From Training</p>
									<?php $contents = ''; if(isset($project)){$contents = $project->expectation_from_training ;} ?>
									<?php echo render_textarea('expectation_from_training','',$contents,array(),array(),'','tinymce'); ?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">

									<p class="bold"><?php echo _l('hr_hr_description'); ?></p>
									<?php $contents = ''; if(isset($project)){$contents = $project->description;} ?>
									<?php echo render_textarea('description','',$contents,array(),array(),'','tinymce'); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>
					<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
				</div>
				<?php echo form_close(); ?>                 
			</div>
		</div>
	</div>
</body>
</html>
<script>
    function askToJoin(trainingId) {
        // Perform AJAX request
        $.ajax({
            url: '<?php echo base_url();?>admin/hr_profile/check_allotted_slots',
            type: 'POST',
            dataType: 'json',
            data: { training_id: trainingId },
            success: function(response) {
                console.log(response);
                if (response.success) {
                    // Handle success, e.g., enable the join button
                    alert(response.message);
                    location.reload(true);
                } else {
                    // Handle failure, e.g., show an error message
                    alert(response.message);
                }
            },
            error: function() {
                // Handle AJAX error
                alert('Error making the AJAX request.');
            }
        });
    }
</script>