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
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="table-table_training_attendence">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Staff Name</th>
                            <th>Name</th>
                            <th>Time to Start</th>
                            <th>Time to End</th>
                            <th>Location</th>
                            <th>Venues</th>
                            <th>Attendence</th>
                            <th>Training Progress</th>
                            <!-- <th>Action</th> -->
                        </tr>
                    </thead>
                    <tbody>
                <?php

                $staff_ids = $this->db->select('staff_id,training_process_id')->where('staff_id IS NOT NULL AND staff_id != ""')->order_by('training_process_id', 'DESC')->get('tblhr_jp_interview_training')->result_array();

                $count = 0;

                foreach ($staff_ids as $index => $staff) :
                    $wait_staff_ids = explode(',', $staff['staff_id']);
                    foreach ($wait_staff_ids as $key => $wait_staff_id) :
                        $count++;

                        //Added by DEEP BASAK on January 16, 2024
                        $total_present = 0;
                        $attendance_details = $this->Common_model->getAllData('tbltraining_attendance', '', '', ['is_active' => 'Y', 'training_id' => $staff['training_process_id'], 'staff_id' => $wait_staff_id]);
                        foreach($attendance_details as $key => $val){
                            $total_present = $total_present + $val->count;
                        }
                        $training_details = $this->Common_model->getAllData('tblhr_jp_interview_training', '', 1, ['training_process_id' => $staff['training_process_id']]);
                        $total_present = 100 * ($total_present / $training_details->mint_point);
                        $class = 'bg-danger';
                        if($total_present <= 30){
                            $class = 'bg-danger';
                        } else if(($total_present > 30) && ($total_present <= 60)){
                            $class = 'bg-warning';
                        } else if(($total_present > 60)){
                            $class = 'bg-success';
                        }
                        
                        $wait_staff = $this->db->get_where('tblstaff', array('staffid' => $wait_staff_id))->row_array();
                        $staff_name = $wait_staff['firstname'] . ' ' . $wait_staff['lastname'];
                
                        $interview_training = $this->db->select('training_name,time_to_start,time_to_end,training_location,training_venues,training_process_id')
                            ->where_in('training_process_id', $staff['training_process_id'])
                            ->get('tblhr_jp_interview_training')
                            ->result_array();
                ?>
                    <tr>
                        <td><?php echo $count; ?></td>
                        <td><a href="<?php echo base_url('hr_profile/view_attendance/' . $wait_staff_id . '/' . $interview_training[0]['training_process_id']); ?>"><?php echo $staff_name; ?></a></td>
                        <td><?php echo $interview_training[0]['training_name']; ?></td>
                        <td><?php echo $interview_training[0]['time_to_start']; ?></td>
                        <td><?php echo $interview_training[0]['time_to_end']; ?></td>
                        <td><?php echo $interview_training[0]['training_location']; ?></td>
                        <td><?php echo $interview_training[0]['training_venues']; ?></td>
                        <td class="action-buttons">
                            <select class="attendance-dropdown form-control form-control-sm" data-staff-id="<?php echo $wait_staff_id; ?>" data-lead-id="<?php echo $interview_training[0]['training_process_id']; ?>">
                                <option value="">Attendance</option>
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                                <option value="half_day">Half Day</option>
                            </select>
                        </td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar-striped <?= $class ?>" role="progressbar" style="width: <?= $total_present ?>%" aria-valuenow="<?= $total_present ?>" aria-valuemin="0" aria-valuemax="100"><?= $total_present ?>%</div>
                            </div>
                        </td>
                        <!-- <td><a href="<?php echo base_url('hr_profile/view_attendance/' . $wait_staff_id . '/' . $interview_training[0]['training_process_id']); ?>" class="btn btn-primary">View Attendance</a></td> -->

                    </tr>
                <?php 
                // $counter++;
                endforeach;
                endforeach; ?>
                </tbody>
            </table>
            </div>


		</div>
		<!-- training_program end -->
	</div>