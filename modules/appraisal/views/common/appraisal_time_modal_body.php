<div class="row">

    <div class="col-md-12">
        <div class="mb-3">
            <label for="appraisal_time" class="form-label">Appraisal Time (Month)<span class="text-danger">*</span></label>
            <input type="float" class="form-control" name="appraisal_time" id="appraisal_time" value="<?= !empty($details)? $details->appraisal_time : '' ?>">
            <input type="hidden" name="appraisal_time_id" id="appraisal_time_id" value="<?php if(!empty($details)){ echo $details->id; }else{ echo '0'; } ?>">
        </div>
    </div>

</div>