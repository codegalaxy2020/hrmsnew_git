<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
	.modal-fullscreen {
		width: 100vw;
		max-width: none;
		height: 100%;
		margin: 0;
	}
	.select2-container{
		z-index: 10000;
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
								<h4 class="no-margin"><?= $title ?> </h4>
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
										<label>Year</label>
										<!-- <input type="number" onblur="filterData(this.value)" class="form-control" id="year_filter" name="year_filter" value="<?= date('Y') ?>"> -->
										<select name="year_filter" id="year_filter" class="form-control" onchange="filterData(this.value)">
											<?php
											$startYear = 2000;
											$endYear = date("Y"); // Current year
											for ($year = $startYear; $year <= $endYear; $year++) {
												$selected = '';
												if($year == $endYear){
													$selected = 'selected';
												}
												echo "<option value=\"$year\" ".$selected.">$year</option>";
											}
											?>
										</select>
									</div>

                                    <div class="col-md-2 leads-filter-column pull-right">
										<a class="btn btn-primary btn-block" href="javascript:void(0)" onclick="openAppraisalModal()"><i class="fa fa-plus"></i>&nbsp;Add Appraisal</a>
									</div>

								</div>
								<!-- filter -->
							</div>
							<div class="col-md-12">
								<hr class="hr-color">
								<div class="table-responsive">
									<table class="table table-bordered table-sm" id="table-staff_appraisal">
										<thead>
											<tr>
												<th>#</th>
												<th>Staff Name</th>
												<th>Appraisal Type</th>
                                                <th>Appraisal Year</th>
                                                <th>Old Salary</th>
												<th>New Salary</th>
                                                <th>Old Designation</th>
                                                <th>New Designation</th>
                                                <th>KRA & KPI Avg. Rating</th>
                                                <th>KRA & KPI Last Rating</th>
                                                <th>Status</th>
                                                <th>Status Change At</th>
												<th>Action</th>
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
			</div>

			<?php echo form_close(); ?>

		</div>

	</div>
</div>

</div>
</div>
</div>

<div class="modal fade" id="appraisal_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="modalForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="appraisal_modal_title">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="appraisal_modal_body">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary save">Submit Appraisal</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php init_tail(); ?>
<?php require 'modules/appraisal/assets/js/appraisal_manage_js.php'; ?>

</body>
</html>
