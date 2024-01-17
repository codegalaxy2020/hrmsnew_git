<div class="row">
    <div class="col-md-12">
        <?php if (is_admin()) { ?>
            <div class="form-group">
                <label for="staff_id" class="control-label">Staff</label>
                <select class="form-control" name="staff_id" id="staff_id" onchange="changestaff_id(this)">
                    <!-- <option value="" selected disabled>Select Staff</option> -->
                    <?php
                    if(!empty($staff)):
                        foreach($staff as $key => $val):
                    ?>
                    <option value="<?= $val->staffid ?>" <?php if($staff_id == $val->staffid){echo "selected"; } ?>><?= $val->firstname . ' ' . $val->lastname ?></option>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>
        <?php } ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="route_point_combobox hide">
            <br>
            <label for="route_point" class="control-label">Route point</label>
            <select id="route_point" name="route_point" class="selectpicker" data-width="100%"
                data-none-selected-text="Non selected" tabindex="-98">
            </select>
            <br>
            <br>
            <div class="clearfix"></div>
        </div>

        <div id="clock" class="clock">
            <div id="hourHand" class="hourHand"></div>
            <div id="minuteHand" class="minuteHand"></div>
            <div id="secondHand" class="secondHand"></div>
            <div id="center" class="center"></div>
            <ul>
                <li><span>1</span></li>
                <li><span>2</span></li>
                <li><span>3</span></li>
                <li><span>4</span></li>
                <li><span>5</span></li>
                <li><span>6</span></li>
                <li><span>7</span></li>
                <li><span>8</span></li>
                <li><span>9</span></li>
                <li><span>10</span></li>
                <li><span>11</span></li>
                <li><span>12</span></li>
            </ul>
        </div>
        
        <br>
        <div class="col-md-12 bottom_co_btn">
            <div class="bottom_co_btn_item">
                <?php
                // if ($type_check_in_out == '' || $type_check_in_out == 2 || $allows_updating_check_in_time == 1 || is_admin()) {
                    // echo form_open(admin_url('timesheets/check_in_ts'), array('id' => 'timesheets-form-check-in', 'onsubmit' => 'get_data()'));
                if(!empty($staff_attendance)){
                    if(empty($staff_attendance->check_in)):
                ?>
                <form id="checkInForm">
                    <input type="hidden" name="staff_id" value="<?php echo $staff_id; ?>">
                    <input type="hidden" name="type_check" value="1">
                    <input type="hidden" name="edit_date" value="">
                    <input type="hidden" name="point_id" value="">
                    <input type="hidden" name="location_user" value="">
                    <button class="btn btn-primary check_in" type="submit">
                        <?php echo _l('check_in'); ?>
                    </button>
                    <?php 
                    echo form_close();
                    endif;
                } else{ ?>
                <form id="checkInForm">
                    <input type="hidden" name="staff_id" value="<?php echo $staff_id; ?>">
                    <input type="hidden" name="type_check" value="1">
                    <input type="hidden" name="edit_date" value="">
                    <input type="hidden" name="point_id" value="">
                    <input type="hidden" name="location_user" value="">
                    <button class="btn btn-primary check_in" type="submit">
                        <?php echo _l('check_in'); ?>
                    </button>
                    <?php 
                    echo form_close();
                } ?>
            </div>
            <div class="bottom_co_btn_item">
                <?php 
                // if ($type_check_in_out == 1 || $allows_updating_check_in_time == 1 || is_admin()) {
                    // echo form_open(admin_url('timesheets/check_in_ts'), array('id' => 'timesheets-form-check-out', 'onsubmit' => 'get_data()'));
                if(!empty($staff_attendance)){
                    if(empty($staff_attendance->check_out)):
                ?>
                <form id="checkOutForm">
                    <input type="hidden" name="staff_id" value="<?php echo $staff_id; ?>">
                    <input type="hidden" name="type_check" value="2">
                    <input type="hidden" name="edit_date" value="">
                    <input type="hidden" name="point_id" value="">
                    <input type="hidden" name="location_user" value="">
                    <button class="btn btn-warning check_out">
                        <?php echo _l('check_out'); ?>
                    </button>
                    <?php echo form_close();
                endif;
                } ?>
            </div>
        </div>
        <div class="clearfix"></div>
        <br>
        <div class="col-mm-12" id="attendance_history">
            <?php
            if(!empty($staff_attendance)): 
                if(!empty($staff_attendance->check_in) && empty($staff_attendance->check_out)):
            ?>
            <div class="alert alert-warning"><strong>Check In:</strong> <?= date("F d, Y h:i:s A", strtotime($staff_attendance->check_in)) ?></div>
            <?php 
                
                else:
                    $checkInDateTime = new DateTime($staff_attendance->check_in);
                    $checkOutDateTime = new DateTime($staff_attendance->check_out);
                    $timeDifference = $checkInDateTime->diff($checkOutDateTime);
                    // Access the time difference components
                    $hours = $timeDifference->h;
                    $minutes = $timeDifference->i;
                    $seconds = $timeDifference->s;
            ?>
            <div class="alert alert-success">
                <strong>Check In:</strong> <?= date("F d, Y h:i:s A", strtotime($staff_attendance->check_in)) ?><br>
                <strong>Check Out:</strong> <?= date("F d, Y h:i:s A", strtotime($staff_attendance->check_out)) ?><br>
                <strong>Time Duration:</strong> <?= $hours . ' H, ' . $minutes . ' M, ' . $seconds . ' S' ?>
            </div>
            <?php
                endif;
            endif; 
            ?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#checkInForm").on('submit', function (e){
            e.preventDefault();
            var userConfirmation = confirm("Are you sure want to Check-In?");
            if (userConfirmation) {
                ajaxFromSubmit('timesheets/check_in_ts2', this, function (data){
                    alert(data.message);
                    $("#clock_attendance_modal").find(".close").trigger('click');
                });
            } else {
                alert("You clicked Cancel!");
            }
            
        });

        $("#checkOutForm").on('submit', function (e){
            e.preventDefault();
            var userConfirmation = confirm("Are you sure want to Check-Out?");
            if (userConfirmation) {
                ajaxFromSubmit('timesheets/check_in_ts2', this, function (data){
                    alert(data.message);
                    $("#clock_attendance_modal").find(".close").trigger('click');
                });
            } else {
                alert("You clicked Cancel!");
            }
            
        });
    });
</script>