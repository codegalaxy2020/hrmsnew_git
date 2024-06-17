<tr>
    <td>
        <input type="text" class="form-control" name="field_name[]" id="field_name_<?= $count ?>" onblur="setFieldNameSlug($(this), <?= $count ?>)">
        <input type="hidden" name="field_name_slug[]" id="field_name_slug_<?= $count ?>">
    </td>
    <td>
        <select class="form-control" name="field_type[]" id="field_type_<?= $count ?>">
            <option value="" selected disabled>Select Field Type</option>
            <?php
            if(!empty($field_type)):
                foreach($field_type as $key => $val):
            ?>
            <option value="<?= $val->type_name ?>"><?= $val->type_name ?></option>
            <?php
                endforeach;
            endif;
            ?>
        </select>
    </td>
    <td class="delBtn" onclick="this.parentNode.remove()"><i class="fa fa-trash"></i></td>
</tr>