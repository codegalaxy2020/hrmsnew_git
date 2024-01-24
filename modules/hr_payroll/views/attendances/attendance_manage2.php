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

									<?php if(is_admin()): ?>
									<div class="col-md-2">
										<label>Month</label>
										<input type="month" onchange="filterData(this.value, $('#staff_attendance').val())" class="form-control" id="month_attendance" name="month_attendance" value="<?= date('Y-m') ?>">
									</div>

									<div class="col-md-2 leads-filter-column pull-left">

										<div class="form-group">
											<label for="staff_attendance" class="control-label"><?php echo _l('staff'); ?></label>
											<select onchange="filterData($('#month_attendance').val(), this.value)" name="staff_attendance" class="form-control selectpicker" id="staff_attendance" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true"> 
												<option value="0" selected disabled>Select Staff</option>
												<?php foreach ($staffs as $key => $staff) { ?>
													<option value="<?php echo html_entity_decode($staff['staffid']); ?>" ><?php  echo html_entity_decode($staff['firstname'].' '.$staff['lastname']); ?></option>
												<?php } ?>
											</select>
										</div>

									</div>
									<?php endif; ?>

									<div class="col-md-4">
										<div id="chart"></div>
									</div>

									<?php if(is_admin()): ?>
									<div class="col-md-2">
										<a href="<?= base_url('admin/hr_payroll/manage_payslip') ?>" class="btn btn-primary btn-block">Payslip</a>
									</div>
									<?php endif; ?>
									
									<div class="col-md-12">
										<div id="day_wise_attendance"></div>
									</div>

								</div>
								<!-- filter -->
							</div>
							<div class="col-md-12">
								<hr class="hr-color">
								<table class="table table-bordered table-sm" id="table-month_attendance">
									<thead>
										<tr>
											<th>#</th>
											<th>Date</th>
										</tr>
									</thead>
									<tbody>
										
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
