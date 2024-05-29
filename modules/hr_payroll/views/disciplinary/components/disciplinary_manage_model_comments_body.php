<div class="container">
    <div class="row">
        <div class="col">
            <?php if(($details_complaint->is_approved == 'A') || ($details_complaint->is_approved == 'C')): ?>
            <div class="card">
                <div class="card-body">
                    <div style="overflow: scroll; height: 200px;">
                        <?php
                        if(!empty($details_comments)):
                            foreach($details_comments as $key => $value):
                        ?>
                        <div class="d-flex flex-start">
                            <div class="flex-grow-1 flex-shrink-1">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="mb-1">
                                            <?= $value->firstname . ' ' . $value->lastname ?> <span class="small">- <?= date('F d, Y h:i A', strtotime($value->created_at)) ?></span>
                                        </h4>
                                    </div>
                                    <p class="small mb-0">
                                        <?= $value->comments ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </div>

                    <div class="col-md-3 pb-3">
                        <div class="mb-3">
                            <label for="judge" class="form-label">Judge<span class="text-danger">*</span></label>
                            <select class="form-control" id="judge" name="judge" disabled>
                                <option value="" selected disabled>Select Judge</option>
                                <?php
                                if(!empty($staff_list)):
                                    foreach($staff_list as $key => $staff):
                                        if(($staff->admin == 1) && ($details_complaint->manager != $staff->staffid)):
                                            $selected = "";
                                            if(!empty($details_complaint)):
                                                if($staff->staffid == $details_complaint->judge):
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

                </div>

                <?php if($details_complaint->is_approved != 'C'): ?>
                <?php if($staff_id == $details_complaint->judge): ?>
                <button type="button" class="btn btn-primary" onclick="finalJudgement(<?= $case_id ?>)" id="approve_btn">Final Judgement</button>
                <?php endif; ?>


                <div class="card-footer py-3 border-0" style="background-color: #f8f9fa;">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <textarea class="form-control" rows="6" placeholder="Comments" name="comments_box" id="comments_box"></textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary btn-sm" onclick="saveComplainComment(<?= $staff_id ?>, <?= $case_id ?>)">Post comment</button>
                    </div>
                </div>
                <?php endif; ?>

            </div>
            <?php else: ?>
            <?php if(($details_complaint->manager == $staff_id) && ($details_complaint->is_approved != 'R')): ?>
            <div class="col-md-3">
                <div class="mb-3">
                    <label for="judge" class="form-label">Judge<span class="text-danger">*</span></label>
                    <select class="form-control" id="judge" name="judge">
                        <option value="" selected disabled>Select Judge</option>
                        <?php
                        if(!empty($staff_list)):
                            foreach($staff_list as $key => $staff):
                                if(($staff->admin == 1) && ($details_complaint->manager != $staff->staffid)):
                                    $selected = "";
                                    if(!empty($details_complaint)):
                                        if($staff->staffid == $details_complaint->judge):
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
            <button type="button" class="btn btn-success" onclick="approveReject(<?= $case_id ?>, 'A')" id="approve_btn">Approve</button>
            <button type="button" class="btn btn-danger" onclick="approveReject(<?= $case_id ?>, 'R')" id="reject_btn">Reject</button>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>