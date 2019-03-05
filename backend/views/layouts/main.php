<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);

$baseURL = Yii::$app->params['BASE_URL'];
$image_url = Yii::$app->params['IMAGE_URL'];
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <link rel="shortcut icon" href="<?= $image_url ?>images/favicon.ico" /> 
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
	<?php if (Yii::$app->user->isGuest) { ?>
		 <?= $content; ?>
	<?php }else{ ?>
		<?php //include('sidebar.php'); ?>
		<?php include('header.php'); ?>
		<?= $content; ?>
		<?php //include ('footer.php'); ?>
	<?php } ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
