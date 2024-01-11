<div class="card">
    <div class="card-body">

        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="" class="form-label">Traning Name<span class="text-danger">*</span></label>
                    <select class="form-control form-control-sm" name="training_name" id="training_name">
                        <option value="" selected disabled>Select Training Name</option>
                        <?php
                        if(!empty($training)):
                            foreach($training as $key => $value):
                        ?>
                        <option value="<?= $value->training_process_id ?>"><?= $value->training_name ?></option>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label for="" class="form-label">Traning Type<span class="text-danger">*</span></label>
                    <select class="form-control form-control-sm" name="training_name" id="training_name">
                        <option value="" selected disabled>Select Training Type</option>
                        <?php
                        if(!empty($training_type)):
                            foreach($training_type as $key => $value):
                        ?>
                        <option value="<?= $value->id ?>"><?= $value->name ?></option>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label for="" class="form-label">Feedback<span class="text-danger">*</span></label>
                    <textarea class="form-control form-control-sm" name="feedback" id="feedback"></textarea>
                </div>
            </div>

    </div>
</div>