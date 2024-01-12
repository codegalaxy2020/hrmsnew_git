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
                    <label for="" class="form-label">Content<span class="text-danger">*</span></label>
                    <select class="form-control form-control-sm" name="content" id="content">
                        <option value="" selected disabled>Select Content</option>
                        <option value="good">Good</option>
                        <option value="medium">Medium</option>
                        <option value="bad">Bad</option>
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label for="" class="form-label">Trainer effectiveness<span class="text-danger">*</span></label>
                    <select class="form-control form-control-sm" name="trainer_effectiveness" id="trainer_effectiveness">
                        <option value="" selected disabled>Select Trainer Effectiveness</option>
                        <option value="good">Good</option>
                        <option value="medium">Medium</option>
                        <option value="bad">Bad</option>
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label for="" class="form-label">Overall experience<span class="text-danger">*</span></label>
                    <select class="form-control form-control-sm" name="overall_experience" id="overall_experience">
                        <option value="" selected disabled>Select Overall Experience</option>
                        <option value="good">Good</option>
                        <option value="medium">Medium</option>
                        <option value="bad">Bad</option>
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