<div class="row">
    <div class="col-md-3">
        <div class="mb-3">
            <label for="staff_id" class="form-label">Select Staff<span class="text-danger">*</span></label>
            <select class="form-control" id="staff_id" name="staff_id">
                <option value="" selected disabled>Select Staff</option>
                <?php
                if(!empty($staff_list)):
                    foreach($staff_list as $key => $staff):
                        if(!is_admin()):
                            if($staff->staffid == get_staff_user_id()):
                ?>
                <option value="<?= $staff->staffid ?>" selected><?= $staff->firstname . ' ' . $staff->lastname . ' (' . $staff->staff_identifi . ')'; ?></option>
                <?php
                            endif;
                        else:
                            $selected = "";
                            if(!empty($exp_details)):
                                if($staff->staffid == $exp_details->staff_id):
                                    $selected = "selected";
                                else:
                                    $selected = "";
                                endif;
                            endif;
                ?>
                <option value="<?= $staff->staffid ?>" <?= $selected ?>><?= $staff->firstname . ' ' . $staff->lastname . ' (' . $staff->staff_identifi . ')'; ?></option>
                <?php
                        endif;
                    endforeach;
                endif;
                ?>
            </select>
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3 mt-3">
            <label for="exp_type" class="form-label">Select Type<span class="text-danger">*</span></label>
            <select class="form-control" id="exp_type" name="exp_type">
                <option value="" selected disabled>Select Type</option>
                <option value="TA" <?php if(!empty($exp_details)){ if($exp_details->exp_type == 'TA') echo "selected"; } ?>>Travel Allowance</option>
                <option value="DA" <?php if(!empty($exp_details)){ if($exp_details->exp_type == 'DA') echo "selected"; } ?>>Dearness Allowance</option>
            </select>
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3 mt-3">
            <label for="date" class="form-label">Date<span class="text-danger">*</span></label>
            <input type="date" name="date" id="date" class="form-control" value="<?php if(!empty($exp_details)){ echo $exp_details->date; } ?>">
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="mb-3 mt-3">
            <label for="exp_name" class="form-label">Expense Name<span class="text-danger">*</span></label>
            <input type="name" name="exp_name" id="exp_name" class="form-control" value="<?php if(!empty($exp_details)){ echo $exp_details->exp_name; } ?>">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-sm table-bordered" id="staff_expense_list_table">
                <thead>
                    <tr>
                        <!-- <th>Sl No</th> -->
                        <th>TADA<span class="text-danger">*</span></th>
                        <th>Type<span class="text-danger">*</span></th>
                        <th>Total (KM/Days)<span class="text-danger">*</span></th>
                        <th>Total (KM/Days)<span class="text-danger">*</span></th>
                        <th>Remarks</th>
                        <th>Rate (₹)<span class="text-danger">*</span></th>
                        <th onclick="<?php if(empty($exp_details)): ?>addExpenseTable()<?php endif; ?>">
                            <i class="fa fa-plus"></i>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if(!empty($exp_details)):
                        $details = json_decode($exp_details->expense_details);
                        foreach($details as $key => $val):
                            // pr($val);
                            $data['exp_rule'] = $val;
                            echo $this->load->view('attendances/components/add_expense_modal_tbody', $data, true);
                        endforeach;
                    endif;
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5"><span class="text-end">Total Expense Amount (₹)<span class="text-danger">*</span></span></th>
                        <td><input type="float" name="exp_amount" id="exp_amount" class="form-control" value="<?php if(!empty($exp_details)){ echo $exp_details->exp; }else{ echo "0"; } ?>" readonly></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <label for="reason" class="form-label">Reason</label>
            <textarea class="form-control" id="reason" name="reason" rows="3"><?php if(!empty($exp_details)){ echo $exp_details->reason; } ?></textarea>
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label for="document" class="form-label">Document</label>
            <input class="form-control" type="file" name="document" id="document">
        </div>
    </div>
</div>

<?php
if(!empty($exp_details) && ($exp_details->is_approve == 'N') && is_admin()): ?>
<div class="row">
    <div class="col-md-10">&nbsp;</div>
    <div class="col-md-2">
        <button type="button" onclick="approveRejectExpense(<?= !empty($exp_details)?$exp_details->id:0 ?>, 'Y')" class="btn btn-success btn-sm approveBtn">Approve</button>
        <button type="button" onclick="approveRejectExpense(<?= !empty($exp_details)?$exp_details->id:0 ?>, 'R')" class="btn btn-danger btn-sm rejectBtn">Reject</button>
    </div>
</div>
<?php endif; ?>