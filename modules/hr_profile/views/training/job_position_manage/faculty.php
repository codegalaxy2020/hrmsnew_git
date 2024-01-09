
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12" id="training-add-edit-wrapper">
				<div class="row">
					<div class="col-md-12">
						<div class="panel_s">
							<!--<form action="https://skilltest.live/hrmsnew/admin/hr_profile/faculty" method="post" accept-charset="utf-8">-->
							<?php echo form_open($this->uri->uri_string(), array('id'=>'faculty')); ?>
                            <!--<input type="hidden" name="csrf_token_name" value="4c0aaaedc32aa6fe86d61f61eecc0a35">     -->
							<div class="panel-body">
								<h4 class="no-margin">
								<?php echo html_entity_decode($title); ?>
								</h4>
								<hr class="hr-panel-heading" />
								

								<label for="training_program" class="control-label">Training Program</label>
								<select name="training_program" class="selectpicker" id="training_program" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
									<option value=""></option> 
									
									<?php 
								// 	print_r($type_of_trainings);
									foreach ($type_of_trainings as $key => $value) {
									
									?>
										<option value="<?php echo $value['training_process_id'] ?>" <?php if($faculty_data_id['training_program'] == $value['training_process_id '] ){echo 'selected';}; ?> ><?php echo $value['training_name'] ?></option>
									<?php } ?>
								</select>

								<div class="clearfix"></div>
								<br>
								<div class="clearfix"></div>
								<?php $value = (isset($faculty_data_id) ? $faculty_data_id['faculty'] : ''); ?>
								<?php $attrs = (isset($faculty_data_id) ? array() : array('autofocus'=>true)); ?>
								<?php echo render_input('subject','name',$value,'text',$attrs); ?>
								
								<p class="bold"><?php echo _l('hr_hr_description'); ?></p>

								<?php $value = (isset($faculty_data_id) ? $faculty_data_id['description'] : ''); ?>
								<?php echo render_textarea('viewdescription','',$value,array(),array(),'','tinymce-view-description'); ?>                     
								<hr />
								<button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
								<a href="<?php echo admin_url('hr_profile/training?group=training_library'); ?>"  class="btn btn-default pull-right mright5 "><?php echo _l('hr_close'); ?></a>
							</div>
							</form>
						</div>
					</div>

				</div>
			</div>
		
				</div>
			</div>
			<?php init_tail(); ?>
			<?php 
			require('modules/hr_profile/assets/js/training/position_training_js.php');
			?>
		</body>
	</body>
	</html>
