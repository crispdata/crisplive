<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
$imageURL = Yii::$app->params['IMAGE_URL'];
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" oncontextmenu="return false">
    <head>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-133548958-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());

            gtag('config', 'UA-133548958-1');

            $(document).keydown(function (e) {
                if (e.which === 123) {

                    return false;

                }

            });
        </script>

        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="title" content ="Crispdata - Data Management Company, Mohali, India">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="AADH Datamatics Pvt. Ltd. (Crispdata)">
        <meta name="description" content="Contractors - Manufacturers - Dealers - Suppliers - Government - MES - Crispdata - AADH Datamatics">
        <meta name="keywords" content="LTD,CABLE,GOVT,US,PVT,GOVT DEPARTMENTS,TENDERS,CONTRACTORS MANUFACTURERS,DEALERS DISTRIBUTORS,SUPPLIERS,AADH DATAMATICS LTD,CRISPDATA,crispdata.co.in">
        <link rel="shortcut icon" href="<?= $imageURL ?>assets/images/favicon.ico" /> 
        <link rel="canonical" href="https://www.crispdata.co.in/"/>
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?> - Data Management Company, Mohali, India</title>
        <?php $this->head() ?>
    </head>
    <body oncontextmenu="return false">
        <script src="//code.jquery.com/jquery-1.12.4.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
        <?php $this->beginBody() ?>

        <?php /* NavBar::begin([
          'brandLabel' => Yii::$app->name,
          'brandUrl' => Yii::$app->homeUrl,
          'options' => [
          'class' => 'navbar-inverse navbar-fixed-top',
          ],
          ]);
          $menuItems = [
          ['label' => 'Home', 'url' => ['/site/index']],
          ['label' => 'About', 'url' => ['/site/about']],
          ['label' => 'Contact', 'url' => ['/site/contact']],
          ];
          if (Yii::$app->user->isGuest) {
          $menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
          $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
          } else {
          $menuItems[] = '<li>'
          . Html::beginForm(['/site/logout'], 'post')
          . Html::submitButton(
          'Logout (' . Yii::$app->user->identity->username . ')',
          ['class' => 'btn btn-link logout']
          )
          . Html::endForm()
          . '</li>';
          }
          echo Nav::widget([
          'options' => ['class' => 'navbar-nav navbar-right'],
          'items' => $menuItems,
          ]);
          NavBar::end();
          ?>

          <?= Breadcrumbs::widget([
          'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
          ]) ?>
          <?= Alert::widget() */ ?>
        <?= $content ?>


        <!--footer class="footer">
            <div class="container">
                <p class="pull-left">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>
        
                <p class="pull-right"><?= Yii::powered() ?></p>
            </div>
        </footer-->




        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
