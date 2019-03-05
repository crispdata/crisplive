<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */
?>

<!-- BODY -->
<html>
    <body>
        <div class="content">
            Hello <?= $username; ?>,<br><br>
            Welcome To PM Software<br><br>

            Following are your credentials - <br><br>
            
            URL - <a href="http://root.projecttrt.com/site/login">http://root.projecttrt.com/site/login</a><br><br>

            Username - <?= $username; ?><br>
            Password - <?= $password; ?>
        </div>
    </body>
</html>