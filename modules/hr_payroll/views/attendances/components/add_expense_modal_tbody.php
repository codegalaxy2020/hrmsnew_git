<tr class="expenseList">
    <!-- <td><?= $count+1 ?></td> -->
    <td>
        <select class="form-control form-control-sm" id="tada_<?= $count ?>" name="tada[]" onchange="dynamicTADAOption(<?= $count ?>)">
            <option value="" selected disabled>Select Type</option>
            <option value="TA" <?php if(!empty($exp_rule)){ if($exp_rule->tada == 'TA') echo "selected"; } ?>>Travel Allowance</option>
            <option value="DA" <?php if(!empty($exp_rule)){ if($exp_rule->tada == 'DA') echo "selected"; } ?>>Dearness Allowance</option>
        </select>
    </td>
    <td>
        <select class="form-control form-control-sm" id="type_<?= $count ?>" name="type[]">
            <option value="" selected disabled>Select Type</option>
            <option class="ta_option" value="Car" <?php if(!empty($exp_rule)){ if($exp_rule->type == 'Car') echo "selected"; } ?>>Car</option>
            <option class="ta_option" value="Bike" <?php if(!empty($exp_rule)){ if($exp_rule->type == 'Bike') echo "selected"; } ?>>Bike</option>
            <option class="ta_option" value="Bus" <?php if(!empty($exp_rule)){ if($exp_rule->type == 'Bus') echo "selected"; } ?>>Bus</option>
            <option class="ta_option" value="Train" <?php if(!empty($exp_rule)){ if($exp_rule->type == 'Train') echo "selected"; } ?>>Train</option>
            <option class="ta_option" value="Flight" <?php if(!empty($exp_rule)){ if($exp_rule->type == 'Flight') echo "selected"; } ?>>Flight</option>
            <option class="ta_option" value="Ship" <?php if(!empty($exp_rule)){ if($exp_rule->type == 'ship') echo "selected"; } ?>>Ship</option>
            <option class="da_option" value="Food" <?php if(!empty($exp_rule)){ if($exp_rule->type == 'Food') echo "selected"; } ?>>Food</option>
        </select>
    </td>
    <td>
        <select class="form-control form-control-sm" id="per_<?= $count ?>" name="per[]" onchange="getExpenseRate(<?= $count ?>)">
            <option value="" selected disabled>Select Per</option>
            <option class="ta_option" value="KM" <?php if(!empty($exp_rule)){ if($exp_rule->per == 'KM') echo "selected"; } ?>>Kelomiter</option>
            <option class="da_option" value="Day" <?php if(!empty($exp_rule)){ if($exp_rule->per == 'Day') echo "selected"; } ?>>Day</option>
            <option class="da_option" value="Hour" <?php if(!empty($exp_rule)){ if($exp_rule->per == 'Hour') echo "selected"; } ?>>Hour</option>
        </select>
    </td>
    <td>
        <input type="float" name="distance[]" value="<?php if(!empty($exp_rule)){ echo $exp_rule->distance; }else{ echo "0"; } ?>" id="distance_<?= $count ?>" class="form-control form-control-sm" onblur="getExpenseRate(<?= $count ?>)">
    </td>
    <td>
        <textarea class="form-control form-control-sm" id="reason" name="reason" rows="1"><?php if(!empty($exp_rule)){ echo $exp_rule->reason; } ?></textarea>
    </td>
    <td>
        <input type="float" name="amount[]" value="<?php if(!empty($exp_rule)){ echo $exp_rule->amount; }else{ echo "0"; } ?>" id="amount_<?= $count ?>" class="form-control form-control-sm amount" readonly>
    </td>
    <?php if($count != 0): ?>
    <td onclick="removeElm(this, <?= $count ?>)"><i class="fa fa-trash"></i></td>
    <?php endif; ?>
</tr>
