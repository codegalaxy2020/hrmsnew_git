<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php if($this->session->flashdata('debug')){ ?>
				<div class="col-lg-12">
					<div class="alert alert-warning">
						<?php echo html_entity_decode($this->session->flashdata('debug')); ?>
					</div>
				</div>
			<?php } ?>

			<div class="col-md-3">
				<ul class="nav navbar-pills navbar-pills-flat nav-tabs nav-stacked">
				    <!-- <li<?php if($group_item == $group){echo " class='active'"; } ?>>
							<a href="<?php echo admin_url('hr_profile/manage_faculty'); ?>" data-group="<?php echo html_entity_decode('faculty'); ?>">
                            Faculty / Coordinator
								
							</a>
						</li> -->
				    <li<?php if($group_item == $group){echo " class='active'"; } ?>>
							<a href="<?php echo admin_url('utilities/calendartraining'); ?>">
                            Training Add calendar
								
							</a>
						</li>
					<?php
					$i = 0;
					foreach($tab as $group_item){
						?>
						<li<?php if($group_item == $group){echo " class='active'"; } ?>>
							<a href="<?php echo admin_url('hr_profile/training?group='.$group_item); ?>" data-group="<?php echo html_entity_decode($group_item); ?>">

								<?php
								if($group_item == 'training_library'){
									echo _l('hr__training_library');
								}elseif($group_item == 'training_program'){
								    if (!is_admin()) {
										echo 'My Training Program';
								    } else {
								    	echo _l('hr__training_program');
								    }
								}elseif($group_item == 'training_result'){
									echo _l('hr_training_result');
								}elseif($group_item == 'others_training_program' && !is_admin()){
									echo 'Others Training Programs';
								}elseif($group_item == 'waitlist' && is_admin()){
									echo 'Waitlist';
								}elseif($group_item == 'training_attendance' && is_admin()){
									echo 'Training Attendance';
								}
								elseif($group_item == 'training_attendance_staff' && !is_admin()){
									echo 'Training Attendance';
								}

								//Added by DEEP BASAK on January 08, 2024
								//For added training feed back module
								elseif($group_item == 'training_feedback'){
									echo 'Training Feedback';
								}

								//Added by DEEP BASAK on January 15, 2024
								//For added training Chart module
								elseif($group_item == 'training_chart'){
									echo 'Training Chart';
								}
								// elseif($group_item == 'training_attendance_staff' && !is_admin()){
								// 	echo 'Training Attendance';
								// }
								// elseif($group_item == 'training_calender_staff' && !is_admin()){
								// 	echo 'Personal Calender';
								// }
								
								?>
							</a>
						</li>
					<?php } ?>
					<li <?php if($group_item == $group){echo " class='active'"; } ?>>
							<a href="<?php echo admin_url('utilities/calendar'); ?>">
                            	Personal Calender
							</a>
						</li>
				</ul>
			</div>
			<div class="col-md-9">
				<div class="panel_s">
					<div class="panel-body">

						<?php $this->load->view($tabs['view']); ?>

					</div>
				</div>
			</div>

		<div class="clearfix"></div>
	</div>
	<?php echo form_close(); ?>
</div>
</div>
<?php init_tail(); ?>

<?php hooks()->do_action('settings_tab_footer', $tab); ?>

<?php 
$viewuri = $_SERVER['REQUEST_URI'];
if(!(strpos($viewuri,'admin/hr_profile/training?group=training_program') === false) ){
	require('modules/hr_profile/assets/js/training/training_program_js.php');
}elseif(!(strpos($viewuri,'admin/hr_profile/training?group=training_result') === false)){
	require('modules/hr_profile/assets/js/training/training_result_js.php');
}

//Added by DEEP BASAK on January 09, 2024
elseif(!(strpos($viewuri,'admin/hr_profile/training?group=training_feedback') === false)){
	require('modules/hr_profile/assets/js/training/training_feedback_js.php');
}

//Added by DEEP BASAK on January 15, 2024
elseif(!(strpos($viewuri,'admin/hr_profile/training?group=training_chart') === false)){
	require('modules/hr_profile/assets/js/training/training_chart_js.php');
}

//Added by DEEP BASAK on January 16, 2024
elseif(!(strpos($viewuri,'admin/hr_profile/training?group=training_attendance') === false)){
	require('modules/hr_profile/assets/js/training/training_attendance_js.php');
}

require('modules/hr_profile/assets/js/training/training_program_js.php');

?>
</body>
</html>
