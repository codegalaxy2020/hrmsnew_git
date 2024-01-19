<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="table_attendance_details">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Staff Name</th>
                                <th>Check In Time</th>
                                <th>Check Out Time</th>
                                <th>Total Duration (Hours)</th>
                                <th>Check-in Location</th>
                                <th>Check-out Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if(!empty($attendance_list)):
                                foreach($attendance_list as $key => $val):
                            ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <th><?= $val->firstname . ' ' . $val->lastname ?></th>
                                <td>
                                    <?php
                                    if(!empty($val->check_in)){
                                        echo date('F d, Y h:i:s A', strtotime($val->check_in));
                                    }
                                    ?>
                                </td>
                                <th>
                                    <?php
                                    if(!empty($val->check_out)){
                                        echo date('F d, Y h:i:s A', strtotime($val->check_out));
                                    }
                                    ?>
                                </th>
                                <th>
                                    <?php
                                    if(!empty($val->check_out)){
                                        // $checkInDateTime = new DateTime($val->check_in);
                                        // $checkOutDateTime = new DateTime($val->check_out);
                                        // $timeDifference = $checkInDateTime->diff($checkOutDateTime);
                                        // // Access the time difference components
                                        // $hours = $timeDifference->h;
                                        // $minutes = $timeDifference->i;
                                        // $seconds = $timeDifference->s;

                                        echo $val->today_hour . ' Hours';
                                    }
                                    ?>
                                </th>
                                <td><a target="_blank" href="http://maps.google.co.in/maps?q=<?= $val->check_in_location ?>"><?= $val->check_in_location ?></a></td>
                                <td><a target="_blank" href="http://maps.google.co.in/maps?q=<?= $val->check_out_location ?>"><?= $val->check_out_location ?></a></td>
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
    </div>
</div>