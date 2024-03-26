<div class="mb-3 mt-3">
    <input type="hidden" name="rule_id" id="rule_id" value="<?= !empty($exp_rule)?$exp_rule->id:'0' ?>">
    <label for="tada" class="form-label">Select TADA<span class="text-danger">*</span></label>
    <select class="form-control" id="tada" name="tada">
        <option value="" selected disabled>Select Type</option>
        <option value="TA" <?php if(!empty($exp_rule)){ if($exp_rule->tada == 'TA') echo "selected"; } ?>>Travel Allowance</option>
        <option value="DA" <?php if(!empty($exp_rule)){ if($exp_rule->tada == 'DA') echo "selected"; } ?>>Dearness Allowance</option>
    </select>
</div>

<div class="mb-3 mt-3">
    <label for="type" class="form-label">Select Type<span class="text-danger">*</span></label>
    <select class="form-control" id="type" name="type">
        <option value="" selected disabled>Select Type</option>
        <option value="Car" <?php if(!empty($exp_rule)){ if($exp_rule->type == 'Car') echo "selected"; } ?>>Car</option>
        <option value="Bike" <?php if(!empty($exp_rule)){ if($exp_rule->type == 'Bike') echo "selected"; } ?>>Bike</option>
        <option value="Bus" <?php if(!empty($exp_rule)){ if($exp_rule->type == 'Bus') echo "selected"; } ?>>Bus</option>
        <option value="Train" <?php if(!empty($exp_rule)){ if($exp_rule->type == 'Train') echo "selected"; } ?>>Train</option>
        <option value="Flight" <?php if(!empty($exp_rule)){ if($exp_rule->type == 'Flight') echo "selected"; } ?>>Flight</option>
        <option value="Ship" <?php if(!empty($exp_rule)){ if($exp_rule->type == 'ship') echo "selected"; } ?>>Ship</option>
        <option value="Food" <?php if(!empty($exp_rule)){ if($exp_rule->type == 'Food') echo "selected"; } ?>>Food</option>
    </select>
</div>

<div class="mb-3 mt-3">
    <label for="exp_amount" class="form-label">Expense Amount (₹)<span class="text-danger">*</span></label>
    <input type="float" name="exp_amount" value="<?php if(!empty($exp_rule)){ echo $exp_rule->rate; } ?>" id="exp_amount" class="form-control">
</div>

<div class="mb-3 mt-3">
    <label for="per" class="form-label">Select Per<span class="text-danger">*</span></label>
    <select class="form-control" id="per" name="per">
        <option value="" selected disabled>Select Per</option>
        <option value="KM" <?php if(!empty($exp_rule)){ if($exp_rule->per == 'KM') echo "selected"; } ?>>Kelomiter</option>
        <option value="Day" <?php if(!empty($exp_rule)){ if($exp_rule->per == 'Day') echo "selected"; } ?>>Day</option>
        <option value="Hour" <?php if(!empty($exp_rule)){ if($exp_rule->per == 'Hour') echo "selected"; } ?>>Hour</option>
    </select>
</div>