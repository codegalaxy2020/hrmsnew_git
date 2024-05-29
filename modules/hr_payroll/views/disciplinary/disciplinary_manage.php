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

                                    <div class="col-md-2 leads-filter-column pull-right">
										<a class="btn btn-primary btn-block" href="javascript:void(0)" onclick="openModal()"><i class="fa fa-plus"></i>&nbsp;Add Disciplinary</a>
									</div>

								</div>
								<!-- filter -->
							</div>
							<div class="col-md-12">
								<hr class="hr-color">
								<div class="table-responsive">
									<table class="table table-bordered table-sm" id="table-staff_disciplinary">
										<thead>
											<tr>
												<th>#</th>
												<th>Case Number</th>
												<th>Staff</th>
												<th>Complain By</th>
                                                <th>Manager</th>
												<th>Priority</th>
												<th>Approve / Reject</th>
												<th>Complain On</th>
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

<div class="modal fade" id="disciplinary_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="modalForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="disciplinary_modal_title">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="disciplinary_modal_body">
                </div>
                <div class="modal-footer">
                    <button type="subnit" class="btn btn-primary save">Submit Complain</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php init_tail(); ?>
<?php require 'modules/hr_payroll/assets/js/disciplinary/disciplinary_manage_js.php'; ?>

</body>
</html>
