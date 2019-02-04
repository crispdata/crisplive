<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Forgot Password';

$baseURL = Yii::$app->params['BASE_URL'];

$errors = [];

if($model->getErrors()){
	$errors = $model->getErrors();
}
/* 
echo "<pre>";
print_r($errors);
echo "</pre>"; */


?>
<div class="loader-bg"></div>
        <div class="loader">
            <div class="preloader-wrapper big active">
                <div class="spinner-layer spinner-blue">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div><div class="gap-patch">
                    <div class="circle"></div>
                    </div><div class="circle-clipper right">
                    <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div><div class="gap-patch">
                    <div class="circle"></div>
                    </div><div class="circle-clipper right">
                    <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-yellow">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div><div class="gap-patch">
                    <div class="circle"></div>
                    </div><div class="circle-clipper right">
                    <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-green">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div><div class="gap-patch">
                    <div class="circle"></div>
                    </div><div class="circle-clipper right">
                    <div class="circle"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mn-content valign-wrapper">
            <main class="mn-inner container ">
                <div class="valign">
                      <div class="row">
                          <div class="col s12 m6 l4 offset-l4 offset-m3 card-resp">
                              <div class="card white darken-1">
                                  <div class="card-content ">
                                      <span class="card-title">Forgot Password</span>
                                       <div class="row">
                                           <form method = "post" action = "<?= $baseURL ?>site/request-password-reset" class="col s12">
										   
											   <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
										   
                                               <div class="input-field col s12">
                                                   <input id="email" type="email" name = "PasswordResetRequestForm[email]" value = "<?= !empty($model->email) ? $model->email : '' ?>" class="validate">
                                                   <label for="email">Email</label>
												    <?php
												   if(isset($errors['email'])){
												   ?>
												   <label id="email-error" class="error" for="email"><?= $errors['email'][0] ?></label>
												   <?php
												   }
												   ?>
                                               </div>
                                               <div class="col s12 right-align m-t-sm">
                                                   <a href="<?= $baseURL ?>/site/login" class="waves-effect waves-grey btn-flat btn-resp">back</a>
                                                   <button type = "submit" class="waves-effect waves-light btn teal btn-resp">reset</button>
                                               </div>
                                           </form>
                                      </div>
                                  </div>
                              </div>
                          </div>
                    </div>
                </div>
            </main>
        </div>
<script>
	document.body.className += ' ' + 'signin-page';
</script>
