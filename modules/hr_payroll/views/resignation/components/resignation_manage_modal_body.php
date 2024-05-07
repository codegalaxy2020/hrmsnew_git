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
                            if(($staff->staffid == get_staff_user_id()) && ($staff->admin != 1) && empty($staff->manager_id) || ($staff->is_approve == 'R')):
                ?>
                <option value="<?= $staff->staffid ?>" selected><?= $staff->firstname . ' ' . $staff->lastname . ' (' . $staff->staff_identifi . ')'; ?></option>
                <?php
                            endif;
                        else:
                            if($staff->admin != 1 && empty($staff->manager_id) || ($staff->is_approve == 'R')):
                                $selected = "";
                                if(!empty($details)):
                                    if($staff->staffid == $details->staff_id):
                                        $selected = "selected";
                                    else:
                                        $selected = "";
                                    endif;
                                endif;
                ?>
                <option value="<?= $staff->staffid ?>" <?= $selected ?>><?= $staff->firstname . ' ' . $staff->lastname . ' (' . $staff->staff_identifi . ')'; ?></option>
                <?php
                            endif;
                        endif;
                    endforeach;
                endif;
                ?>
            </select>
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label for="manager_id" class="form-label">Select Manager<span class="text-danger">*</span></label>
            <select class="form-control" id="manager_id" name="manager_id">
                <option value="" selected disabled>Select Staff</option>
                <?php
                if(!empty($staff_list)):
                    foreach($staff_list as $key => $staff):
                        if($staff->admin == 1):
                            $selected = "";
                            if(!empty($details)):
                                if($staff->staffid == $details->manager_id):
                                    $selected = "selected";
                                else:
                                    $selected = "";
                                endif;
                            endif;
                ?>
                <option value="<?= $staff->staffid ?>" <?= $selected ?>><?= $staff->firstname . ' ' . $staff->lastname . ' (' . $staff->staff_identifi . ')'; ?></option>
                <?php endif; ?>
                
                <?php
                    endforeach;
                endif;
                ?>
            </select>
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label for="notice_days" class="form-label">Notice Days<span class="text-danger">*</span></label>
            <input type="float" name="notice_days" id="notice_days" class="form-control" onblur="getDate(this)" value="<?php if(!empty($details)){ echo $details->notice_days; } ?>">
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label for="notice_date" class="form-label">Notice Date<span class="text-danger">*</span></label>
            <input type="date" name="notice_date" id="notice_date" class="form-control" readonly value="<?php if(!empty($details)){ echo $details->notice_time; } ?>">
        </div>
    </div>

    <div class="col-md-12">
        <div class="mb-3">
            <label for="reason" class="form-label">Reason<span class="text-danger">*</span></label>
            <textarea class="form-control" rows="6" placeholder="Reason" name="reason" id="reason"><?php if(!empty($details)){ echo $details->reason; } ?></textarea>
        </div>
    </div>

    <?php if(is_admin() && $details->is_approve == 'P'): ?>
    <div class="col-md-12 d-flex justify-content-end mt-2">
        <button type="button" class="btn btn-success" onclick="approveReject(<?= $details->id ?>, 'A')" id="approve_btn">Approve</button>
        <button type="button" class="btn btn-danger" onclick="approveReject(<?= $details->id ?>, 'R')" id="reject_btn">Reject</button>
    </div>
    <?php endif; ?>
    
</div>