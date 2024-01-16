<script>
    function askToJoinw(trainingId, staffId, training_name) {
        // Perform AJAX request
        $.ajax({
            url: '<?php echo base_url();?>admin/hr_profile/check_allotted_slots_join',
            type: 'POST',
            dataType: 'json',
            data: { training_id: trainingId, staffId: staffId, training_name:training_name },
            success: function(response) {
                if (response.success) {
                    // Handle success, e.g., enable the join button
                    alert(response.message);
                    location.reload(true);
                } else {
                    // Handle failure, e.g., show an error message
                    alert(response.message);
                }
            },
            error: function() {
                // Handle AJAX error
                alert('Error making the AJAX request.');
            }
        });
    }

    $(document).ready(function () {
        staticDataTable("table-table_training_attendence", [0, 1, 2, 3, 4, 5, 6, 8]);
        // $("#table-table_training_attendence").DataTable();
        
        $('.attendance-dropdown').on('change', function () {
            // alert('hi');
            var staffId = $(this).data('staff-id');
            var leadId = $(this).data('lead-id');
            var attendanceValue = $(this).val();
            var staffname = $(this).data('staff-name');
            submitAttendance(staffId, leadId, attendanceValue, staffname);
        });

        function submitAttendance(staffId, leadId, attendanceValue, staffname) {

            $.ajax({
                type: 'POST',
                url: '<?php echo base_url();?>admin/hr_profile/attendance', // Replace with your controller URL
                data: {
                    staffId: staffId,
                    leadId: leadId,
                    attendanceValue: attendanceValue,
                    staffname: staffname
                },
                success: function (response) {
                   var responseData = JSON.parse(response);
                    if (responseData.success) {
                        alert('Attendance updated successfully!');
                        location.reload();
                    } else {
                        alert('Failed to update attendance. Please try again.');
                    }
                },
                error: function (error) {
                    // Handle errors if any
                }
            });
        }
        
    });
</script>