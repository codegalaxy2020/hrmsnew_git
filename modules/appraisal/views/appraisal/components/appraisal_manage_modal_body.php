<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label for="staff_id" class="form-label">Select Staff<span class="text-danger">*</span></label>
            <input type="hidden" name="appraisal_id" id="appraisal_id" value="<?= !empty($details)?$details->id:'0' ?>">
            <input type="hidden" name="hdn_staff_id" id="hdn_staff_id" value="0">
            <select class="form-control form-control-sm" id="staff_id" name="staff_id">
                <option value="" selected disabled>Select Staff</option>
                <?php
                if(!empty($staff_list)):
                    foreach($staff_list as $key => $staff):
                        // if(!$staff->admin):
                            $selected = "";
                            if(!empty($details)):
                                if($staff['staffid'] == $details->staff_id):
                                    $selected = "selected";
                                else:
                                    $selected = "";
                                endif;
                            endif;
                ?>
                <option value="<?= $staff['staffid'] ?>" <?= $selected ?>><?= $staff['firstname'] . ' ' . $staff['lastname'] . ' (' . $staff['staff_identifi'] . ')'; ?></option>
                <?php
                        // endif;
                    endforeach;
                endif;
                ?>
            </select>
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label for="appraisal_type" class="form-label">Appraisal Type<span class="text-danger">*</span></label>
            
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" onchange="getAppraisalDetails('S', this)" id="salary" name="appraisal_type">
                <label class="form-check-label" for="salary">
                    Salary Hike
                </label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" onchange="getAppraisalDetails('D', this)" id="designation" name="appraisal_type">
                <label class="form-check-label" for="designation">
                    Designation Hike
                </label>
            </div>
            
        </div>
    </div>

    <div class="col-md-5">
        <div class="mb-3">
            <label for="appraisal_docs" class="form-label">Appraisal Document<span class="text-danger">*</span></label>
            <input class="form-control" type="file" id="appraisal_docs" name="appraisal_docs">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div id="appraisalDetailsDiv"></div>
    </div>
</div>