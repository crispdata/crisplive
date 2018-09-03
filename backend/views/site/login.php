<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';

$baseURL = Yii::$app->params['BASE_URL'];

$errors = [];

if($model->getErrors()){
	$errors = $model->getErrors();
}

/* echo "<pre>";
print_r($model->username);
echo "</pre>"; */

?>
<style>
body{
	background-color: #343a40!important;
}
p.help-block-error {
    color: #dc3545;
}
</style>
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
            <main class="mn-inner container">
                <div class="valign">
                      <div class="row">
                          <div class="col s12 m6 l4 offset-l4 offset-m3 card-resp">
                              <div class="card white darken-1">
                                  <div class="card-content ">
                                      <span class="card-title">Sign In</span>
                                       <div class="row">
                                           <form method = "post" action = "<?= $baseURL; ?>site/login" class="col s12">
                                               
											   <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
											   
											   <div class="input-field col s12">
                                                   <input id="username" type="text" name = "LoginForm[username]" value = "<?= !empty($model->username) ? $model->username : '' ?>" class="required <?= isset($errors['username']) ? 'invalid' : 'validate' ?>">
                                                   <label for="username">Username</label>
												   <?php
												   if(isset($errors['username'])){
												   ?>
												   <label id="username-error" class="error" for="username"><?= $errors['username'][0] ?></label>
												   <?php
												   }
												   ?>
                                               </div>
                                               <div class="input-field col s12">
                                                   <input id="password" type="password" name = "LoginForm[password]" class="required <?= isset($errors['password']) ? 'invalid' : 'validate' ?>">
                                                   <label for="password">Password</label>
												   <?php
												   if(isset($errors['password'])){
												   ?>
												   <label id="password-error" class="error" for="password"><?= $errors['password'][0] ?></label>
												   <?php
												   }
												   ?>
                                               </div>
											   <div class="input-field col s12">
                                                   <a href = "<?= $baseURL ?>site/request-password-reset" class = "right">Forgot Password?</a>
                                               </div>
                                               <div class="col s12 right-align m-t-sm">
                                                   <a href="<?= $baseURL ?>site/signup" class="waves-effect waves-grey btn-flat btn-resp">sign up</a>
                                                   <button class="waves-effect waves-light btn teal btn-resp">sign in</button>
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