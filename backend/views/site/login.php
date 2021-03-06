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
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
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
<?php if (Yii::$app->session->hasFlash('error')): ?>
    <script>
        swal("", "<?= Yii::$app->session->getFlash('error'); ?>", "error");
    </script>
<?php endif; ?>

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
                                <form id="signup" method = "post" action = "<?= $baseURL; ?>site/login" class="col s12" onsubmit="return checkcookies()">

                                    <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />

                                    <div class="input-field col s12">
                                        <select name='LoginForm[authtype]' id="authtype" class="contact-authtypes browser-default required <?= isset($errors['authtype']) ? 'invalid' : 'validate' ?>">
                                            <option value=''>Select Product</option>
                                            <option value='12' selected>All Products</option>
                                            <option value='1'>Cables</option>
                                            <option value='2'>Lighting</option>
                                            <option value='3'>Fans</option>
                                            <option value='4'>Accessories</option>
                                            <option value='5'>Wires</option>
                                            <option value='6'>DB/MCB/MCCB/Timers</option>
                                            <option value='7'>Transformers</option>
                                            <option value='8'>Cable Jointing Kits</option>
                                            <option value='9'>Panels</option>
                                            <option value='10'>ACB</option>
                                            <option value='11'>Motors</option>
                                        </select>
                                        <?php
                                        if (isset($errors['authtype'])) {
                                            ?>
                                            <label id="authtype-error" class="error" for="authtype"><?= $errors['authtype'][0] ?></label>
                                            <?php
                                        }
                                        ?>
                                    </div>

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
                                        <button class="waves-effect waves-light btn teal btn-resp" id="sign">sign in</button>
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

    function checkcookies() {
        var cookieEnabled = navigator.cookieEnabled;
        if (!cookieEnabled) {
            document.cookie = "testcookie";
            cookieEnabled = document.cookie.indexOf("testcookie") != -1;
        }
        if (cookieEnabled == false) {
            swal("", "Please enable browser-cookies to use the Crispdata website and refresh the login page after enabling cookies.", "error");
            return false;
        }

        var cookie = '<?php echo count($_COOKIE); ?>';
        if (cookie > 0) {
            $("#signup").submit();
        } else {
            swal("", "Please enable browser-cookies to use the Crispdata website and refresh the login page after enabling cookies.", "error");
            return false;
        }
    }
</script>
