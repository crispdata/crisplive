<?php
/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<!--div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
<?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        The above error occurred while the Web server was processing your request.
    </p>
    <p>
        Please contact us if you think this is a server error. Thank you.
    </p>

</div-->
<style>
    .errorpage{background: url('/assets/images/mountains5.png') no-repeat!important;
               background-size: cover!important;}
    .errorpage span{color:#fff!important;}
</style>
<div class="mn-content errorpage">
    <main class="mn-inner">
        <div class="center">
            <h1>
                <span><?= Html::encode($this->title) ?></span>
            </h1>
            <span class="text-white"><?= nl2br(Html::encode($message)) ?></span><br>
            <a class="btn-floating btn-large waves-effect waves-light teal lighten-2 m-t-lg" href="/">
                <i class="large material-icons">home</i>
            </a>
        </div>
    </main>
</div>
