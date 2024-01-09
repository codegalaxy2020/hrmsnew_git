
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .control-label, label {
    margin-top: 10px;
}
</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12" id="training-add-edit-wrapper">
				<div class="row">
					<div class="col-md-12">
						<div class="panel_s">
							<!--<form action="https://skilltest.live/hrmsnew/admin/hr_profile/faculty" method="post" accept-charset="utf-8">-->
							
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
								<?php if($cost == "direct" || $cost == "all"){?>
								<h4 style="padding-top: 10px;" class="no-margin">
								DIRECT COST
								</h4>
								<hr class="hr-panel-heading" />
                                <?php
                                $query = $this->db->get_where('tblcomponents', array('costtype' => 'direct'));
                                if ($query->num_rows() > 0) {
                                    foreach ($query->result() as $row) {
                                       ?>
                                
                                        
                                        <label for="<?=$row->slug?>" class="control-label"><?=$row->name?></label>
                                        <input type="number" name="<?=$row->slug?>" value="<?=$get_cost->{$row->slug}?>" value="" class="form-control direct" readonly />
                                  <?php  }
                                }
                                ?>
                        
                                <!-- Readonly field to display the total -->
                                <label for="total" class="control-label">Total</label>
                                <input type="text" name="total" id="total" value="<?=$get_cost->total?>" class="form-control" readonly />
                                
                                <h4 style="padding-top: 10px;" class="no-margin">
								TRAINING AND DEVELOPMENT EXPENSES
								</h4>
								<hr class="hr-panel-heading" />
								<label for="employee_training" class="control-label">Employee Training</label>
                                <input type="number" name="employee_training" value="<?=$get_cost->employee_training?>" class="form-control" readonly />
                        
                                <label for="workshops" class="control-label">Workshops</label>
                                <input type="number" name="workshops" value="<?=$get_cost->workshops?>" class="form-control" readonly />
                        
                                <label for="courses" class="control-label">Courses</label>
                                <input type="number" name="courses" value="<?=$get_cost->courses?>" class="form-control" readonly />
                        
                                <label for="certifications" class="control-label">Certifications</label>
                                <input type="number" name="certifications" value="<?=$get_cost->certifications?>" class="form-control" readonly />
                                
                                <label for="materials" class="control-label">Materials</label>
                                <input type="number" name="materials" value="<?=$get_cost->materials?>" class="form-control" readonly />
                                
                                <label for="training_development_expenses_total" class="control-label">Training and Development Expenses Total</label>
                                <input type="text" name="training_development_expenses_total" id="training_development_expenses_total" value="<?=$get_cost->training_development_expenses_total?>" class="form-control" readonly />
                                
                                <?php } if($cost == "indirect" || $cost == "all"){?>
                                <h4 style="padding-top: 10px;" class="no-margin">
								INDIRECT COST
								</h4>
								<hr class="hr-panel-heading" />
								<?php
                                $query = $this->db->get_where('tblcomponents', array('costtype' => 'indirect'));
                                if ($query->num_rows() > 0) {
                                    foreach ($query->result() as $row) {
                                       ?>
                                
                                        
                                        <label for="<?=$row->slug?>" class="control-label"><?=$row->name?></label>
                                        <input type="number" name="<?=$row->slug?>" value="<?=$get_cost->{$row->slug}?>" value="" class="form-control indirect" readonly/>
                                  <?php  }
                                }
                                ?>
                                
                                <label for="indirect_cost_total" class="control-label">Indirect Cost Total</label>
                                <input type="text" name="indirect_cost_total" id="indirect_cost_total" value="<?=$get_cost->indirect_cost_total?>" class="form-control" readonly />
                                <h4 style="padding-top: 10px;" class="no-margin">
								ADMINISTRATIVE COST
								</h4>
								<hr class="hr-panel-heading" />
								
								<label for="recruitment_cost" class="control-label">Recruitment Cost</label>
                                <input type="number" name="recruitment_cost" value="<?=$get_cost->recruitment_cost?>" class="form-control" readonly />
                        
                                <label for="onboarding_cost" class="control-label">Onboarding Cost</label>
                                <input type="number" name="onboarding_cost" value="<?=$get_cost->onboarding_cost?>" class="form-control" readonly />
                        
                                <label for="payroll_processing_cost" class="control-label">Payroll Processing Cost</label>
                                <input type="number" name="payroll_processing_cost" value="<?=$get_cost->payroll_processing_cost?>" class="form-control" readonly />
                        
                                <label for="hr_personnel_cost" class="control-label">HR Personnel Cost</label>
                                <input type="number" name="hr_personnel_cost" value="<?=$get_cost->hr_personnel_cost?>" class="form-control" readonly />
                                
                                <label for="administrative_costs" class="control-label">Administrative Costs</label>
                                <input type="text" name="administrative_costs" id="administrative_costs" value="<?=$get_cost->administrative_costs?>" class="form-control" readonly />
								<hr />
								<?php }?>
								<!--<button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>-->
								<!--<a href="<?php echo admin_url('hr_profile/training?group=training_library'); ?>"  class="btn btn-default pull-right mright5 "><?php echo _l('hr_close'); ?></a>-->
							</div>
							
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
	<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function(){
        // Function to update total field
        function updateTotal() {
            var salary = parseFloat($('[name="salary"]').val()) || 0;
            var bonuses = parseFloat($('[name="bonuses"]').val()) || 0;
            var commissions = parseFloat($('[name="commissions"]').val()) || 0;
            var allowances = parseFloat($('[name="allowances"]').val()) || 0;
            var benefits = parseFloat($('[name="benefits"]').val()) || 0;
            var healthcare = parseFloat($('[name="healthcare"]').val()) || 0;
            var retirement_plans = parseFloat($('[name="retirement_plans"]').val()) || 0;

            var total = salary + bonuses + commissions + allowances + benefits + healthcare + retirement_plans;

            $('#total').val(total.toFixed(2));
        }

        // Attach the updateTotal function to input change events
        $('[name="salary"], [name="bonuses"], [name="commissions"], [name="allowances"], [name="benefits"], [name="healthcare"], [name="retirement_plans"]').on('input', updateTotal);
    });
        $(document).ready(function(){
        // Function to update total field
        function updateTotal1() {
            var office_space = parseFloat($('[name="office_space"]').val()) || 0;
            var utilities = parseFloat($('[name="utilities"]').val()) || 0;
            var equipment = parseFloat($('[name="equipment"]').val()) || 0;
            var supplies = parseFloat($('[name="supplies"]').val()) || 0;
            var other_resources = parseFloat($('[name="other_resources"]').val()) || 0;

            var total = office_space + utilities + equipment + supplies + other_resources;

            $('#indirect_cost_total').val(total.toFixed(2));
        }

        // Attach the updateTotal function to input change events
        $('[name="office_space"], [name="utilities"], [name="equipment"], [name="supplies"], [name="other_resources"]').on('input', updateTotal1);
    });
    $(document).ready(function(){
        // Function to update total field
        function updateTotal2() {
            var recruitment_cost = parseFloat($('[name="recruitment_cost"]').val()) || 0;
            var onboarding_cost = parseFloat($('[name="onboarding_cost"]').val()) || 0;
            var payroll_processing_cost = parseFloat($('[name="payroll_processing_cost"]').val()) || 0;
            var hr_personnel_cost = parseFloat($('[name="hr_personnel_cost"]').val()) || 0;

            var total = recruitment_cost + onboarding_cost + payroll_processing_cost + hr_personnel_cost;

            $('#administrative_costs').val(total.toFixed(2));
        }

        // Attach the updateTotal function to input change events
        $('[name="recruitment_cost"], [name="onboarding_cost"], [name="payroll_processing_cost"], [name="hr_personnel_cost"]').on('input', updateTotal2);
    });
    $(document).ready(function(){
        // Function to update total field
        function updateTotal3() {
            var employee_training = parseFloat($('[name="employee_training"]').val()) || 0;
            var workshops = parseFloat($('[name="workshops"]').val()) || 0;
            var courses = parseFloat($('[name="courses"]').val()) || 0;
            var certifications = parseFloat($('[name="certifications"]').val()) || 0;
            var materials = parseFloat($('[name="materials"]').val()) || 0;

            var total = employee_training + workshops + courses + certifications + materials;

            $('#training_development_expenses_total').val(total.toFixed(2));
        }

        // Attach the updateTotal function to input change events
        $('[name="employee_training"], [name="workshops"], [name="courses"], [name="certifications"], [name="materials"]').on('input', updateTotal3);
    });
</script>