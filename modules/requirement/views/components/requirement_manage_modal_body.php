<div class="row">

    <div class="col-md-3">
        <div class="mb-3">
            <label for="form_id" class="form-label">Form ID<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="form_id" id="form_id" readonly value="<?= !empty($details)? $details->form_id : $form_id ?>">
            <input type="hidden" name="job_id" id="job_id" value="<?php if(!empty($details)){ echo $details->id; }else{ echo '0'; } ?>">
            <input type="hidden" name="form_link" id="form_link" value="<?= base_url('forms/jobs/'.base64_encode($form_id)) ?>">
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label for="job_title" class="form-label">Job Title<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="job_title" id="job_title" value="<?= !empty($details)? $details->job_title : '' ?>">
        </div>
    </div>

</div>

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="form_field_tbl">
                <thead>
                    <tr>
                        <th>Field Name</th>
                        <th>Field Type</th>
                        <th>
                            <a href="javascript:" class="addFieldsBtn" type="button" onclick="addFields()"><i class="fa fa-plus-square"></i></a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if(!empty($details->form_fields)):
                        $fields = json_decode($details->form_fields);
                        foreach($fields as $key => $value):
                    ?>
                    <tr>
                        <td>
                            <input type="text" class="form-control" name="field_name[]" value="<?= $value->field_name ?>" id="field_name_<?= $key ?>" onblur="setFieldNameSlug($(this), <?= $key ?>)">
                            <input type="hidden" name="field_name_slug[]" value="<?= $value->field_name_slug ?>" id="field_name_slug_<?= $key ?>">
                        </td>
                        <td>
                            <select class="form-control" name="field_type[]" id="field_type_<?= $key ?>">
                                <option value="" selected disabled>Select Field Type</option>
                                <?php
                                if(!empty($field_type)):
                                    foreach($field_type as $k => $val):
                                        $selected = '';
                                        if($val->type_name == $value->field_type){
                                            $selected = 'selected';
                                        }
                                ?>
                                <option value="<?= $val->type_name ?>" <?= $selected ?>><?= $val->type_name ?></option>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            </select>
                        </td>
                        <td class="delBtn" onclick="this.parentNode.remove()"><i class="fa fa-trash"></i></td>
                    </tr>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>