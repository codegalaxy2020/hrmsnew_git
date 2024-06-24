<div class="row">
    <div class="col-md-12">
        <table class="table table-sm table-bordered" id="table-interview_details">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Interview By</th>
                    <th>Comments</th>
                    <th>Interview Time</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<?php
if(
    $details->is_shortlisted == 'Y'
):
?>
<div class="row">

    <div class="col-md-4">
        <div class="mb-3">
            <label for="interview_datetime" class="form-label">interview Date & Time<span class="text-danger">*</span></label>
            <input type="datetime-local" class="form-control" name="interview_datetime" id="interview_datetime">
            <input type="hidden" name="can_id" id="can_id" value="<?= $can_id ?>">
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label for="comments" class="form-label">Comments<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="comments" id="comments">
        </div>
    </div>
    <div class="col-md-2">
        <div class="mb-3">
            <label for="" class="form-label">&nbsp;</label>
            <button type="button" class="btn btn-primary btn-sm btn-block" onclick="submitInterviewComments()">Submit</button>
        </div>
    </div>
    <div class="col-md-2">
        <div class="mb-3">
            <label for="" class="form-label">&nbsp;</label>
            <button type="button" class="btn btn-secondary btn-sm btn-block" onclick="selectAsEmployee()">Confirm This as Employee</button>
        </div>
    </div>
</div>
<?php endif; ?>