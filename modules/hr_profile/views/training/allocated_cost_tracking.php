<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php  init_head(); ?>
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
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="_buttons">
							<?php if (is_admin() || has_permission('hrm_hr_records','','create') || has_permission('hrm_hr_records','','edit')) { ?>

								<a href="<?php echo admin_url('hr_profile/add_cost_tracking'); ?>" class="btn mright5 btn-info pull-left display-block ">Add Cost Tracking</a>
								
							<?php } ?>

							
						</div>
						<br>
						

						<div class="row">
							<div class="col-md-12">
							<?php if (!empty($faculty_data)): ?>
                                <table border="1">
                                    <tr>
            <th>ID</th>
            <th>Training Program</th>
            <!--<th>Salary</th>-->
            <!--<th>Bonuses</th>-->
            <!--<th>Commissions</th>-->
            <!--<th>Allowances</th>-->
            <!--<th>Benefits</th>-->
            <!--<th>Healthcare</th>-->
            <!--<th>Retirement Plans</th>-->
            <!--<th>Total</th>-->
            <th>Action</th>
        </tr>
        <?php 
        // echo get_staff_user_id();die();
        foreach ($faculty_data as $index => $faculty): 
        $costid = $faculty['training_program_id'];
        $costType = $this->hr_profile_model->getCostType($costid, get_staff_user_id());
        $faculty_data[$index]['costType'] = $costType;
        // print_r($faculty_data[$index]['costType']);die();
        ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td><?php echo $faculty['training_name']; ?></td>
                <!--<td><?php echo $faculty['salary']; ?></td>-->
                <!--<td><?php echo $faculty['bonuses']; ?></td>-->
                <!--<td><?php echo $faculty['commissions']; ?></td>-->
                <!--<td><?php echo $faculty['allowances']; ?></td>-->
                <!--<td><?php echo $faculty['benefits']; ?></td>-->
                <!--<td><?php echo $faculty['healthcare']; ?></td>-->
                <!--<td><?php echo $faculty['retirement_plans']; ?></td>-->
                <!--<td><?php echo $faculty['total']; ?></td>-->
                <td class="action-buttons">
                    <a href="<?php echo base_url('hr_profile/view_cost_tracking/' . $faculty['id'] . '/' . $faculty_data[$index]['costType']); ?>" class="edit-button btn btn-info">View</a>
                   
                </td>
            </tr>
        <?php endforeach; ?>
                                </table>
                            <?php else: ?>
                                <p>No faculty data available.</p>
                            <?php endif; ?>
                                    
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	<div id="modal_wrapper"></div>
	<?php init_tail(); ?>
	<?php 
	require('modules/hr_profile/assets/js/hr_record/hr_record_js.php');
	?>
	<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

</body>
</html>
