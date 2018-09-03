<?php
/* @var $this yii\web\View */

$this->title = 'Edit Profile';
$user = Yii::$app->user->identity;
$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
?>
<style>
    .actions{display:none!important;}    
    .steps{display:none!important;}    
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
    $(function () {
        $("#Phone").keyup(function () {
            $("#Phone").val(this.value.match(/[0-9]*/));
        });
    });
</script>
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

                    <div class="row">
                        <form id="create-project-form" class="col s12" method = "post" action = "<?= $baseURL ?>site/editprofile" enctype="multipart/form-data">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />

                            <?php if ($user->ParentId == 0) { ?>
                                <input type="hidden" name="uid" value="<?= @$user->UserId; ?>" />


                                <div class="input-field col s12">
                                    <input id="username" type="text" maxlength="50" name = "username" class="validate required" value="<?= $user->username; ?>">
                                    <label for="username">Username</label>
                                </div>

                                <div class="input-field col s12">
                                    <input id="email" type="text" maxlength="50" name = "email" class="validate required" value="<?= $user->email; ?>">
                                    <label for="email">Email</label>
                                </div>

                                <div class="file-field input-field col s12">
                                    <div class="btn teal lighten-1">
                                        <span>Profile Image</span>
                                        <input type="file" name="LogoImage">
                                    </div>
                                    <div class="file-path-wrapper">
                                        <input class="file-path validate valid" type="text" placeholder="Upload Profile Image">
                                    </div>

                                </div>
                                <?php if ($user->Logo != '') { ?>
                                    <div class="input-field col s12">
                                        <img src="<?= $imageURL . $user->Logo; ?>" width="100" height="100">
                                    </div>
                                <?php } ?>

                                <div class="input-field col s12">
                                    <input id="password" type="password" name = "password" class="validate" value="<?= $user->password; ?>">
                                    <label for="password">Password</label>
                                </div>
                            <?php } else { ?>
                                <input type="hidden" name="cid" value="<?= @$user->UserId; ?>" />
                                <div class="row">
                                    <div class="input-field col s6">
                                        <input id="username" type="text" maxlength="50" name = "username" disabled class="validate required" value="<?= $user->username; ?>">
                                        <label for="username">Username</label>
                                    </div>

                                    <div class="input-field col s6">
                                        <input id="password" type="password" name = "password" class="validate" value="">
                                        <label for="password">Password</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s6">
                                        <input id="FirstName" type="text" maxlength="50" name = "CreateContact[FirstName]" class="validate required" value="<?= $user->FirstName; ?>">
                                        <label for="FirstName">First Name</label>
                                    </div>

                                    <div class="input-field col s6">
                                        <input id="LastName" type="text" maxlength="50" name = "CreateContact[LastName]" class="validate required" value="<?= $user->LastName; ?>">
                                        <label for="LastName">Last Name</label>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="input-field col s6">
                                        <input id="Street" type="text" name = "CreateContact[Street]" class="validate required" value="<?= $user->Street; ?>">
                                        <label for="Street">Street</label>
                                    </div>

                                    <div class="input-field col s6">
                                        <input id="City" type="text" name = "CreateContact[City]" class="validate required" value="<?= $user->City; ?>">
                                        <label for="City">City</label>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="input-field col s6">
                                        <input id="Email" type="email" maxlength="100" size="30" disabled name = "CreateContact[Email]" class="validate required" value="<?= $user->email; ?>">
                                        <label for="Email">Email</label>
                                    </div>

                                    <div class="input-field col s6">
                                        <input id="Phone" type="text" maxlength="50" pattern="([0-9]|[0-9]|[0-9])" name = "CreateContact[Mobile]" class="validate required" value="<?= $user->Mobile; ?>">
                                        <label for="Phone">Phone</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s6">
                                        <input id="State" type="text" name = "CreateContact[State]" class="validate required" value="<?= $user->State; ?>">
                                        <label for="State">State</label>
                                    </div>

                                    <div class="input-field col s6">
                                        <input id="Zip" type="text" name = "CreateContact[Zip]" class="validate required" value="<?= $user->Zip; ?>">
                                        <label for="Zip">ZIP Code</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s6">
                                        <input id="Office" type="text" name = "CreateContact[ContactNumber]" class="validate required" value="<?= $user->ContactNumber; ?>">
                                        <label for="Office">Office</label>
                                    </div>

                                    <div class="input-field col s6">
                                        <input id="Fax" type="text" name = "CreateContact[Fax]" class="validate required" value="<?= $user->Fax; ?>">
                                        <label for="Fax">Fax</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="file-field input-field col s12">
                                        <div class="btn teal lighten-1">
                                            <span>Profile Image</span>
                                            <input type="file" name="LogoImage">
                                        </div>
                                        <div class="file-path-wrapper">
                                            <input class="file-path validate valid" type="text" placeholder="Upload Profile Image">
                                        </div>

                                    </div>
                                </div>
                                <?php if ($user->Logo != '') { ?>
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <img src="<?= $imageURL . $user->Logo; ?>" width="100" height="100">
                                        </div>
                                    </div>
                                <?php } ?>

                            <?php } ?>

                            <input class="waves-effect waves-light btn blue m-b-xs" type="submit" value="Submit">

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

