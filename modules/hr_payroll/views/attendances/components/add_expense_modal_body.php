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
        <option value="<?= $staff->staffid ?>"><?= $staff->firstname . ' ' . $staff->lastname . ' (' . $staff->staff_identifi . ')'; ?></option>
        <?php
                    endif;
                else:
        ?>
        <option value="<?= $staff->staffid ?>"><?= $staff->firstname . ' ' . $staff->lastname . ' (' . $staff->staff_identifi . ')'; ?></option>
        <?php
                endif;
            endforeach;
        endif;
        ?>
    </select>
</div>

<div class="mb-3 mt-3">
    <label for="exp_type" class="form-label">Select Type<span class="text-danger">*</span></label>
    <select class="form-control" id="exp_type" name="exp_type">
        <option value="" selected disabled>Select Type</option>
        <option value="TA">Travel Allowance</option>
        <option value="DA">Dearness Allowance</option>
    </select>
</div>

<div class="mb-3 mt-3">
    <label for="date" class="form-label">Date<span class="text-danger">*</span></label>
    <input type="date" name="date" id="date" class="form-control">
</div>

<div class="mb-3 mt-3">
    <label for="exp_amount" class="form-label">Expense Amount (₹)<span class="text-danger">*</span></label>
    <input type="float" name="exp_amount" id="exp_amount" class="form-control">
</div>

<div class="mb-3">
    <label for="reason" class="form-label">Reason</label>
    <textarea class="form-control" id="reason" name="reason" rows="3"></textarea>
</div>

<div class="mb-3">
    <label for="document" class="form-label">Document</label>
    <input class="form-control" type="file" name="document" id="document">
</div>