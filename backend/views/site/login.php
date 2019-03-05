<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';

$baseURL = Yii::$app->params['BASE_URL'];

$errors = [];

if ($model->getErrors()) {
    $errors = $model->getErrors();
}

/* echo "<pre>";
  print_r($model->username);
  echo "</pre>"; */
?>
<link href='https://fonts.googleapis.com/css?family=Pacifico' rel='stylesheet' type='text/css'>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<style>
    body{
        background-color: #343a40!important;
    }
    p.help-block-error {
        color: #dc3545;
    }
    .modal {
        background-image: url('/images/datafour.png');
        background-repeat: no-repeat;
        background-size: cover;

    }
    .animate
    {
        transition: all 0.1s;
        -webkit-transition: all 0.1s;
    }

    .action-button
    {
        position: relative;
        padding: 10px 25px;
        margin: 0px 10px 10px 0px;
        float: left;
        border-radius: 10px;
        font-family: 'Pacifico', cursive;
        font-size: 25px;
        color:#fff;
        text-decoration: none;	
    }

    .action-button-head
    {
        position: relative;
        padding: 10px 40px;
        margin: 0px 10px 10px 0px;
        float: left;
        border-radius: 10px;
        font-family: 'Pacifico', cursive;
        font-size: 25px;
        text-decoration: none;
        text-align: center;
    }

    .blue
    {
        background-color: #3498DB;
        border-bottom: 5px solid #2980B9;
        text-shadow: 0px -2px #2980B9;
    }

    .red
    {
        background-color: #E74C3C;
        border-bottom: 5px solid #BD3E31;
        text-shadow: 0px -2px #BD3E31;
    }

    .green
    {
        background-color: #82BF56;
        border-bottom: 5px solid #669644;
        text-shadow: 0px -2px #669644;
    }

    .yellow
    {
        background-color: #F2CF66;
        border-bottom: 5px solid #D1B358;
        text-shadow: 0px -2px #D1B358;
    }

    .action-button:active
    {
        transform: translate(0px,5px);
        -webkit-transform: translate(0px,5px);
        border-bottom: 1px solid;
    }
    img.logoimage {
        width: 100%;
        margin-bottom:30px;
    }

    #text {display:none;margin-top:20px;}

</style>
<?php
$cookies = Yii::$app->response->cookies;
// add a new cookie to the response to be sent
$cookies->add(new \yii\web\Cookie([
    'name' => 'cookie',
    'value' => '1',
]));
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

    <main class="mn-inner container">

        <div class="valign">
            <div class="row">
                <div class="col s12 m6 l4 offset-l4 offset-m3 card-resp">
                    <img class="logoimage" src="/images/clogo.png" alt="Crispdata">
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
                                        if (isset($errors['username'])) {
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
                                        if (isset($errors['password'])) {
                                            ?>
                                            <label id="password-error" class="error" for="password"><?= $errors['password'][0] ?></label>
                                            <?php
                                        }
                                        ?>
                                        <label id="text" class="error">Caps lock is ON.</label>    
                                    </div>
                                    <div class="input-field col s6">
<!--a href = "<?= $baseURL ?>site/request-password-reset" class = "right">Forgot Password?</a-->

                                    </div>
                                    <div class="col s6 right-align m-t-sm">
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
<div id="modal" class="modal">

    <div class="modal-content">
        <div class="row">
            <h2><span class="col s12 action-button-head">Welcome to Crispdata</span></h2>
            <h1><span class="col s12 action-button-head">You want to register as</span></h1>
            <a href="#" class="action-button shadow animate blue">Manufacturer</a>
            <a href="#" class="action-button shadow animate red">Dealer</a>
            <a href="#" class="action-button shadow animate green">Supplier</a>
            <a href="#" class="action-button shadow animate yellow">Constructor</a>
        </div>

    </div>



</div>
<script>
    document.body.className += ' ' + 'signin-page';
    var input = document.getElementById("password");
    var text = document.getElementById("text");
    input.addEventListener("keyup", function (event) {

        if (event.getModifierState("CapsLock")) {
            text.style.display = "block";
        } else {
            text.style.display = "none"
        }
    });
</script>
