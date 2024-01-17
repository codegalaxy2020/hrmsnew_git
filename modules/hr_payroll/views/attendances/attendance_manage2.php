<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
	.modal-fullscreen {
		width: 100vw;
		max-width: none;
		height: 100%;
		margin: 0;
	}
</style>
<div id="wrapper">
	<div class="content">

		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">

						<div class="row mb-5">
							<div class="col-md-12">
								<h4 class="no-margin"><?php echo _l('hr_manage_attendance') ?> </h4>
							</div>
							<div class="col-md-12">
								<hr class="hr">
							</div>
						</div>

						<div class="row mb-4">   
							<div class="col-md-12">
								<!-- filter -->
								<div class="row filter_by">

									<div class="col-md-2">
										<?php echo render_input('month_attendance','month',date('Y-m'), 'month'); ?>   
									</div>

									<div class="col-md-3 leads-filter-column pull-left">
										<?php echo render_select('department_attendance',$departments,array('departmentid', 'name'),'department',''); ?>
									</div>

									<div class="col-md-3 leads-filter-column pull-left">
										<div class="form-group">
											<label for="role_attendance" class="control-label"><?php echo _l('role'); ?></label>
											<select name="role_attendance[]" class="form-control selectpicker" multiple="true" id="role_attendance" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true"> 
												<?php foreach ($roles as $key => $role) { ?>
													<option value="<?php echo html_entity_decode($role['roleid']); ?>" ><?php  echo html_entity_decode($role['name']); ?></option>
												<?php } ?>
											</select>
										</div>
									</div>

									<div class="col-md-3 leads-filter-column pull-left">

										<div class="form-group">
											<label for="staff_attendance" class="control-label"><?php echo _l('staff'); ?></label>
											<select name="staff_attendance[]" class="form-control selectpicker" multiple="true" id="staff_attendance" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true"> 
												<?php foreach ($staffs as $key => $staff) { ?>

													<option value="<?php echo html_entity_decode($staff['staffid']); ?>" ><?php  echo html_entity_decode($staff['firstname'].' '.$staff['lastname']); ?></option>
												<?php } ?>
											</select>
										</div>

									</div>
								

								</div>
								<!-- filter -->
							</div>
							<div class="col-md-12">
								<hr class="hr-color">
								<table class="table table-bordered table-sm">
									<thead>
										<tr>
											<th>#</th>
											<th>Date</th>
										</tr>
									</thead>
									<tbody>
										<?php
										if(!empty($attendance)):
											foreach($attendance as $key => $val):
										?>
										<tr>
											<td><?= $key + 1 ?></td>
											<td><a href="javascript:" onclick="openAttendanceModal('<?= $val->check_in_date ?>')"><?= date("F d, Y", strtotime($val->check_in_date)) ?></a></td>
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
				</div>
			</div>

			<?php echo form_close(); ?>

		</div>

	</div>
</div>

</div>
</div>
</div>


<div class="modal fade" id="attendance_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="modalFeedbackForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="attendance_modal_title">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="attendance_modal_body">
                </div>
                <div class="modal-footer">
                    <!-- <button type="subnit" class="btn btn-primary save">Save changes</button> -->
                </div>
            </form>
        </div>
    </div>
</div>


<?php init_tail(); ?>
<?php require 'modules/hr_payroll/assets/js/attendances/attendance_manage_js2.php'; ?>

</body>
</html>
