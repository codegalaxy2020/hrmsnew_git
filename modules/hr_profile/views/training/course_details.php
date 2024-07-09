<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <h1 class="text-center">Competency Details</h1>
                            <div class="card">
                                <div class="card-header">
                                    <h2><strong>Competency Name : </strong><?= $competency->competency_name ?></h2>
                                </div>
                                <div class="card-body">
                                <h3>Details</h3>
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th scope="row">Search By</th>
                                                <td><?= $competency->search_by ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Search With</th>
                                                <td><?= $competency->name ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Staff Name</th>
                                                <td><?= $competency->firstname ?> <?= $competency->lastname ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Email</th>
                                                <td><?= $competency->email ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Phone Number</th>
                                                <td><?= $competency->phonenumber ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Birthday</th>
                                                <td><?= $competency->birthday ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Sex</th>
                                                <td><?= $competency->sex ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Home Town</th>
                                                <td><?= $competency->home_town ?></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <h3>Job Details</h3>
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th scope="row">Job Title</th>
                                                <td>demo</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Department</th>
                                                <td>sfv</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Manager</th>
                                                <td>df</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Employment Date</th>
                                                <td>df</td>
                                            </tr>
                                            <!-- Add more job-related fields as necessary -->
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer">
                                    <a href="<?= base_url('hr_profile/competency/') ?>" class="btn btn-primary">Back to
                                    Competency</a>
                                    <!-- <button onclick="editCourse(<?= $competency->id ?>)" class="btn btn-info">Edit -->
                                    <!-- Course</button> -->
                                    <button onclick="confirmDelete(<?= $competency->id ?>)"
                                        class="btn btn-danger">Delete Competency</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="modal_wrapper"></div>
<?php init_tail(); ?>
<!-- Include jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function editCourse(id) {
        window.location.href = '<?= base_url('hr_profile/edit/') ?>' + id;
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            // imageUrl: 'path_to_your_smile_image', 
            imageWidth: 100,
            imageHeight: 100,
            imageAlt: 'Smile Image'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('hr_profile/delete_com') ?>',
                    type: 'POST',
                    data: { id: id },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                'Deleted!',
                                'The competency has been deleted.',
                                'success'
                            ).then(() => {
                                window.location.href = '<?= base_url('hr_profile/competency') ?>';
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                'There was an error deleting the competency.',
                                'error'
                            );
                        }
                    }
                });
            }
        });
    }
</script>


<?php init_tail(); ?>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

</body>

</html>