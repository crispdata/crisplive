<?php
/* @var $this yii\web\View */

$this->title = 'Upload New File';

use backend\controllers\ProductsController;

$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
?>
<style>
    .actions{display:none!important;}    
    .steps{display:none!important;}    
    .input-fields label.error {
        color: #F44336;
        position: static;
        top: .8rem;
        left: .75rem;
        font-size: .8rem;
        cursor: text;
        -webkit-transition: .2s ease-out;
        -moz-transition: .2s ease-out;
        -o-transition: .2s ease-out;
        -ms-transition: .2s ease-out;
        transition: .2s ease-out;
    }
    .select-wrapper input.select-dropdown, .select-wrapper input.select-dropdown:disabled {
        border-color: unset;
    }
    table.scroll tbody {
        height: 100px;
        overflow-y: auto;
        overflow-x: hidden;
    }
    table.scroll tbody,
    table.scroll thead { display: block; }
    thead tr th { 
        width:205px;
        /* text-align: left; */
    }
    tbody tr td { 
        width:205px;
        /* text-align: left; */
    }
    .input-field{height:65px;}
    .waves-input-wrapper{width: 105px;
                         padding-left: 24px;}
    .input-field.col.s12 {
        height: 130px;
    }
</style>
<main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="page-title"><?= $this->title ?></div>
        </div>

        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <script>
                swal({
                    title: "<?= Yii::$app->session->getFlash('success'); ?>",
                    timer: 2000,
                    type: "success",
                    showConfirmButton: false
                });
                //sweetAlert('Success', '<?= Yii::$app->session->getFlash('success'); ?>', 'success');
            </script>
        <?php endif; ?>

        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert alert-danger">
                <?= Yii::$app->session->getFlash('error'); ?>
            </div>
        <?php endif; ?>

        <?php
        $fileone = explode('/', @$file->file);
        $filetwo = explode('-', @$fileone[5]);
        unset($filetwo[0]);
        $fname = implode('-', @$filetwo);
        ?>

        <div class="col s12 m12 l12">
            <div class="card">
                <div class="card-content">

                    <div class="row">
                        <form id="create-project-form" enctype="multipart/form-data" class="col s12" method = "post" action = "<?= $baseURL ?>products/uploadfile">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                            <input type="hidden" value="<?= @$file->id; ?>" name="id">
                            <div class="row">


                                <div class="file-field input-field col s6">
                                    <div class="btn teal lighten-1">
                                        <span>File</span>
                                        <input type="file" name="file">
                                    </div>
                                    <div class="file-path-wrapper">
                                        <input class="file-path validate valid" required="" type="text" value="<?= @$fname ?>" placeholder="Upload File">
                                    </div>
                                </div>
                                <div class="input-field col s6">
                                    <select class="validate required" required="" name="did" id="did">
                                        <option value="" disabled selected>Select Department</option>
                                        <?php
                                        if (isset($departments) && count($departments)) {
                                            foreach ($departments as $_department) {
                                                ?>
                                                <option value="<?= $_department->id ?>" <?= (@$file->did == $_department->id) ? 'selected' : '' ?> ><?= ucfirst($_department->name); ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <?php if (@$file->file) { ?>
                                    <div class="input-field col s12">
                                        <img src="<?= ProductsController::actionFileimages(@$file->file); ?>" alt="<?= $fname ?>"><a class="file" download href="<?= @$file->file; ?>"><?= $fname ?></a>
                                    </div>
                                <?php } ?>

                            </div>

                            <input class="waves-effect waves-light btn blue m-b-xs" name="submit" type="submit" value="Submit">

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>

    $(document).ready(function () {
        // for HTML5 "required" attribute
        $("select[required]").css({
            display: "inline",
            height: 0,
            padding: 0,
            width: 0
        });
    });
</script>