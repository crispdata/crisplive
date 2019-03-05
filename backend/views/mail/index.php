<?php
/* @var $this yii\web\View */

$this->title = 'Manage Mails';
$user = Yii::$app->user->identity;
$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
?>
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

       <div class="col s12 m12 l12">
            <div class="card">
                <div class="card-content">
                    <a href="<?= $baseURL ?>mail/sendmail" class="waves-effect waves-light btn blue m-b-xs" >Send Mail</a>
                   
                    
                </div>
                </div>
           </div>
            
        
               
        </div>
   

</main>