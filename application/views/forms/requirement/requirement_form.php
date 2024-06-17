<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <title><?= $details->job_title ?></title>
    <!-- Added by DEEP BASAK on June 14, 2024 -->
    <style>
        .page_loader{
			position: fixed;
			z-index: 99999;
			background: rgba(255,255,255,.5);
			width: 100%;
			height: 100%;
			overflow: hidden;
            text-align: center;
		}
    </style>
</head>

<body>
    <!-- Added by DEEP BASAK on June 14, 2024 -->
    <div class="page_loader" style="display:none;">
		<div class="d-flex page_loader_content justify-content-center">
			<img src="<?= base_url('assets/images/preloader.gif') ?>" style="width: 60px; padding-top: 210px;">
		</div>
	</div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <h1><?= $details->job_title ?></h1>
                <form id="modalForm">
                    <!-- Defining CSRF Token in JS Format -->
                    <input type="hidden" name="token_name" id="token_name" value="<?= $this->security->get_csrf_token_name() ?>">
                    <input type="hidden" name="token_hash" id="token_hash" value="<?= $this->security->get_csrf_hash() ?>">
                    <input type="hidden" name="form_id" id="form_id" value="<?= base64_encode($details->form_id) ?>">
                    <?php
                    if(!empty($details->form_fields)){
                        $fields = json_decode($details->form_fields);
                        foreach($fields as $key => $val){
                            if($val->field_type != 'select'){
                    ?>
                    <div class="mb-3">
                        <label for="<?= $val->field_name_slug ?>" class="form-label"><?= $val->field_name ?></label>
                        <input type="<?= $val->field_type ?>" class="form-control" id="<?= $val->field_name_slug ?>" name="<?= $val->field_name_slug ?>">
                    </div>
                    <?php
                            }
                        }
                    }
                    ?>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" type="submit">Submit Form</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= base_url('assets/js/custom/ajaxRequest.js') ?>"></script>
    <script src="<?= base_url('assets/js/custom/commonValidation.js') ?>"></script>
    <script src="<?= base_url('assets/js/custom/init.js') ?>"></script>
    <script src="<?= base_url('assets/js/custom/message.js') ?>"></script>

    <script type="text/javascript">
        var modelId = 'requirement_modal';
        var baseUrl = '<?= base_url() ?>';
        $(document).ready(function () {

            $('#modalForm').on('submit', function (e){
                e.preventDefault();
                ajaxFromSubmit('requirement/save_requirement_data', this, function(data){
                    //SwalSuccess2(data.message, '', data.status);
                    warnMsg2(data.message, false, false, "Okay!", "", function (){
                        window.location.reload();
                    });
                    
                    
                });
            });
        });
    </script>
</body>

</html>