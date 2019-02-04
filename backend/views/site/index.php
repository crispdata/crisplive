<?php
/* @var $this yii\web\View */

$this->title = 'Dashboard';
$user = Yii::$app->user->identity;
$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
?>
<style>
    .add-contact{    float: right;
                     margin-right: 15px;}    
    </style>

    <main class="mn-inner">
    <div class="row">
        <div class="col s6">
            <div class="page-title">All Commands</div>
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
                    <div class="row">
                         <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= $baseURL ?>site/approvetenders?c=1">ADG (CG AND PROJECT) CHENNAI AND CE (CG) GOA - MES</a>
                         <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= $baseURL ?>site/approvetenders?c=2">ADG (DESIGN and CONSULTANCY) PUNE - MES</a>
                         <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= $baseURL ?>site/approvetenders?c=3">ADG (OF and DRDO) AND CE (FY) HYDERABAD - MES</a>
                         <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= $baseURL ?>site/approvetenders?c=4">ADG (OF and DRDO)  AND CE (R and D) DELHI-  MES</a>
                         <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= $baseURL ?>site/approvetenders?c=5">ADG (OF and DRDO) AND CE (R and D) SECUNDERABAD - MES</a>
                         <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= $baseURL ?>site/approvetenders?c=6">CENTRAL COMMAND</a>
                         <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= $baseURL ?>site/approvetenders?c=7">EASTERN COMMAND</a>
                         <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= $baseURL ?>site/approvetenders?c=8">NORTHERN COMMAND</a>
                         <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= $baseURL ?>site/approvetenders?c=9">SOUTHERN COMMAND</a>
                         <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= $baseURL ?>site/approvetenders?c=10">SOUTH WESTERN COMMAND</a>
                         <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= $baseURL ?>site/approvetenders?c=11">WESTERN COMMAND</a>
                         <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= $baseURL ?>site/approvetenders?c=12">DGNP MUMBAI - MES</a>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
