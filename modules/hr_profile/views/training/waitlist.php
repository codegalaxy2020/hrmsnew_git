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
                    <th>Staff Name</th>
                    <th>Name</th>
                    <th>Time to Start</th>
                    <th>Time to End</th>
                    <th>Location</th>
                    <th>Venues</th>
                    <th>Action</th>
                    
                </tr>
                <?php
                $staff_ids = $this->db->select('wait_staff_id,training_process_id')->where('wait_staff_id IS NOT NULL AND wait_staff_id != ""')->get('tblhr_jp_interview_training')->result_array();

                foreach ($staff_ids as $index => $staff) :
                    $wait_staff_ids = explode(',', $staff['wait_staff_id']);
                    foreach ($wait_staff_ids as $wait_staff_id) :
                        $wait_staff = $this->db->get_where('tblstaff', array('staffid' => $wait_staff_id))->row_array();
                        $staff_name = $wait_staff['firstname'] . ' ' . $wait_staff['lastname'];
                
                        $interview_training = $this->db->select('training_name,time_to_start,time_to_end,training_location,training_venues,training_process_id')
                            ->where_in('training_process_id', $staff['training_process_id'])
                            ->get('tblhr_jp_interview_training')
                            ->result_array();
                ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                    <td><?php echo $staff_name; ?></td>
                    <td><?php echo $interview_training[0]['training_name']; ?></td>
                    <td><?php echo $interview_training[0]['time_to_start']; ?></td>
                    <td><?php echo $interview_training[0]['time_to_end']; ?></td>
                    <td><?php echo $interview_training[0]['training_location']; ?></td>
                    <td><?php echo $interview_training[0]['training_venues']; ?></td>
                    <td class="action-buttons">
                        <button class="join-button btn btn-success" onclick="askToJoinw(<?php echo $interview_training[0]['training_process_id']; ?>, <?php echo $wait_staff_id; ?>, '<?php echo $interview_training[0]['training_name']; ?>')">Join</button>
                    <button class="cancel-button btn btn-danger" onclick="askToCancel(<?php echo $interview_training[0]['training_process_id']; ?>, <?php echo $wait_staff_id; ?>, '<?php echo $interview_training[0]['training_name']; ?>')">Cancel</button>
                    </td>
                    </tr>
                <?php endforeach;
    endforeach; ?>
            </table>

		</div>
		<!-- training_program end -->
	</div>

</body>
</html>
<script>
    function askToJoinw(trainingId, staffId, training_name) {
        // Perform AJAX request
        $.ajax({
            url: '<?php echo base_url();?>admin/hr_profile/check_allotted_slots_join',
            type: 'POST',
            dataType: 'json',
            data: { training_id: trainingId, staffId: staffId, training_name:training_name },
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
    
        function askToCancel(trainingId, staffId, training_name) {
        // Perform AJAX request
        $.ajax({
            url: '<?php echo base_url();?>admin/hr_profile/wait_staff_id_cancel',
            type: 'POST',
            dataType: 'json',
            data: { training_id: trainingId, staffId: staffId, training_name:training_name },
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