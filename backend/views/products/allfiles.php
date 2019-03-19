<?php
/* @var $this yii\web\View */

$this->title = 'All Files';

use backend\controllers\ProductsController;
use yii\helpers\Url;

$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
?>
<style>
    .add-contact{    float: right;
                     margin-right: 15px;}   
    .file {
        float: left;
        margin-top: 10px!important;
        margin-left: 0px!important;
        padding: 0px!important;
        width: 50%!important;
    }
    .boxfile {
        float: left;
        width: 30%;
        border: 1px solid #6C6C6C;
        border-radius:10px;
        margin: 10px;
    }
    .boxfile img{float:left;}
    .select-wrapper input.select-dropdown, .select-wrapper input.select-dropdown:disabled{border-color: unset;}
    .boxfile a{ word-wrap:break-word; }
</style>
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<main class="mn-inner">
    <div class="row">
        <div class="col s6">
            <div class="page-title">All Files</div>
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


        <div class="col s12 m12 l12">
            <div class="card">
                <div class="card-content">
                    <div id="tables" data-masonry='{ "itemSelector": ".boxfile", "columnWidth": 30 }'>
                        <?php
                        if (@$files) {
                            $i = 0;
                            foreach ($files as $key => $fit) {
                                $fileone = explode('/', $fit->file);
                                $filetwo = explode('-', $fileone[5]);
                                unset($filetwo[0]);
                                $fname = implode('-', $filetwo);
                                ?>
                                <div class="boxfile">
                                    <img src="<?= ProductsController::actionFileimages($fit->file); ?>" alt="<?= $fname ?>"><a class="file" download href="<?= $fit->file; ?>"><?= $fname ?></a>
                                </div>
                                <?php
                                $i++;
                            }
                        }
                        ?>
                    </div>

                </div>
            </div>
        </div>

    </div>
</main>
<script>
    $(document).ready(function () {
        $('#tables').masonry({
            // options...
            itemSelector: '.boxfile',
            columnWidth: 30
        });
    });
</script>