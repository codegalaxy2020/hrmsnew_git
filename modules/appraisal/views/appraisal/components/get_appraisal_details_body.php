<?php if($salary != ''): ?>
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="current_salary" class="form-label">Current Salary (Hourly)<span class="text-danger">*</span></label>
            <input type="float" readonly class="form-control form-control-sm" name="current_salary" id="current_salary" value="<?= !empty($staff_details)?$staff_details->hourly_rate:'0' ?>">
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="new_salary" class="form-label">New Salary (Hourly)<span class="text-danger">*</span></label>
            <input type="float" class="form-control form-control-sm" name="new_salary" id="new_salary">
        </div>
    </div>
</div>
<?php endif; ?>
<?php if($designation != ''): ?>
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="current_designation" class="form-label">Current Designation<span class="text-danger">*</span></label>
            <input type="hidden" name="hdn_current_designation" id="hdn_current_designation" value="<?= !empty($staff_details)?$staff_details->job_position:'' ?>">
            <input type="text" readonly class="form-control form-control-sm" name="current_designation" id="current_designation" value="<?= !empty($staff_details)?$staff_details->position_name:'' ?>">
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="new_designation" class="form-label">New Designation<span class="text-danger">*</span></label>
            <select name="new_designation" id="new_designation" class="form-control form-control-sm">
                <option value="" selected disabled>Select Designation</option>
                <?php
                if(!empty($job_position)):
                    foreach($job_position as $key => $val):
                ?>
                <option value="<?= $val->position_id ?>"><?= $val->position_name ?></option>
                <?php
                    endforeach;
                endif?>
            </select>
        </div>
    </div>
</div>
<?php endif; ?>