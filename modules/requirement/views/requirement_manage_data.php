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
							<!-- <div class="col-md-12">
								<hr class="hr">
							</div> -->
						</div>

						<div class="row mb-4">   
							<div class="col-md-12">
								<!-- filter -->
								<div class="row filter_by">

                                    <!-- <div class="col-md-2 leads-filter-column pull-right">
										<a class="btn btn-primary btn-block" href="javascript:void(0)" onclick="openModal()"><i class="fa fa-plus"></i>&nbsp;Add Requirement</a>
									</div> -->

								</div>
								<!-- filter -->
							</div>
							<div class="col-md-12">
								<hr class="hr-color">
                                <?php 
                                if(!empty($form_details->form_fields)): 
                                    $form_fields = json_decode($form_details->form_fields);
                                ?>
								<div class="table-responsive">
									<table class="table table-bordered table-sm" id="table-staff_requirement_data">
										<thead>
											<tr>
												<th>#</th>
                                                <?php foreach($form_fields as $key => $val): ?>
                                                <th><?= $val->field_name ?></th>
                                                <?php endforeach; ?>
												<th>Interview Time</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>

										</tbody>
									</table>
								</div>
                                <?php endif; ?>
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

<div class="modal fade" id="requirement_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form id="modalForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="requirement_modal_title">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="requirement_modal_body">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary save">Submit Interview Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php init_tail(); ?>
<script type="text/javascript">
	var formId = '<?= $form_id ?>';
</script>
<?php require 'modules/requirement/assets/js/requirement_manage_data_js.php'; ?>

</body>
</html>
