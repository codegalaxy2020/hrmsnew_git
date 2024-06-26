<div class="row">
    <div class="col-md-2">
        <div class="mb-3">
            <label for="staff_id" class="form-label">Select Staff<span class="text-danger">*</span></label>
            <input type="hidden" name="krakpi_id" id="krakpi_id" value="<?= !empty($details)?$details->id:'0' ?>">
            <select class="form-control form-control-sm" id="staff_id" name="staff_id">
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
                    endforeach;
                endif;
                ?>
            </select>
        </div>
    </div>

    <div class="col-md-2">
        <div class="mb-3">
            <label for="rating" class="form-label">Rating (0 to 5)<span class="text-danger">*</span></label>
            <input type="float" class="form-control form-control-sm" id="rating" name="rating" value="<?= !empty($details)?$details->rating:'' ?>">
        </div>
    </div>

    <div class="col-md-8">
        <div class="mb-3">
            <label for="comments" class="form-label">Comments<span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-sm" id="comments" name="comments" value="<?= !empty($details)?$details->comments:'' ?>">
        </div>
    </div>

</div>