<div class="row">

    <div class="col-md-3">
        <div class="mb-3">
            <label for="case_no" class="form-label">case Number<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="case_no" id="case_no" readonly value="<?= !empty($details)? $details->case_no : $case_id ?>">
            <input type="hidden" name="complain_id" id="complain_id" value="<?php if(!empty($details)){ echo $details->id; }else{ echo '0'; } ?>">
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label for="staff_id" class="form-label">Select Staff<span class="text-danger">*</span></label>
            <select class="form-control" id="staff_id" name="staff_id">
                <option value="" selected disabled>Select Staff</option>
                <?php
                if(!empty($staff_list)):
                    foreach($staff_list as $key => $staff):
                        if($staff->admin == 0):
                            if(!empty($details)):
                                if($staff->staffid == $details->staff_id){
                                    $selected = 'selected';
                                }else{
                                    $selected = '';
                                }
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
                                if($staff->staffid == $details->manager):
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
            <label for="reason" class="form-label">Priority<span class="text-danger">*</span></label>
            <select class="form-control" name="priority" id="priority">
                <option value="" selected disabled>Select Priority</option>
                <option value="L" <?php if(!empty($details)){if($details->priority == 'L') echo 'selected'; } ?>>Low</option>
                <option value="M" <?php if(!empty($details)){if($details->priority == 'M') echo 'selected'; } ?>>Medium</option>
                <option value="H" <?php if(!empty($details)){if($details->priority == 'H') echo 'selected'; } ?>>High</option>
            </select>
        </div>
    </div>

    <div class="col-md-12">
        <div class="mb-3">
            <label for="reason" class="form-label">Reason<span class="text-danger">*</span></label>
            <textarea class="form-control" rows="6" placeholder="Reason" name="reason" id="reason"><?php if(!empty($details)){ echo $details->complain_reason; } ?></textarea>
        </div>
    </div>

</div>

<div class="row" id="approve_comments"></div>