<?php
/* @var $this yii\web\View */

$this->title = 'Edit User';
$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
?>

<style>
    .actions{display:none!important;}    
    .steps{display:none!important;}  
    .select-wrapper input.select-dropdown, .select-wrapper input.select-dropdown:disabled {
        border-color: unset;
    }
    .row{margin-bottom: 0px;}
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
                        <form id="create-project-form" class="col s12" method = "post" action = "<?= $baseURL ?>site/edit-user" enctype="multipart/form-data">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />

                            <input type="hidden" name="id" value="<?= @$user->UserId; ?>" />


                            <div class="input-field col s12">
                                <input id="username" type="text" maxlength="50" name = "username" class="validate required" value="<?= $user->username; ?>">
                                <label for="username">Username</label>
                            </div>

                            <div class="input-field col s12">
                                <input id="email" type="text" maxlength="50" name = "email" class="validate required" value="<?= $user->email; ?>">
                                <label for="email">Email</label>
                            </div>

                            <div class="input-field col s12">
                                <select class="validate required" required="" name="group_id" id="group">
                                    <option value="" disabled selected>Select Group</option>
                                    <option value="3" <?php
                                    if ($user->group_id == 3) {
                                        echo "selected";
                                    }
                                    ?> >User</option>
                                    <option value="2" <?php
                                    if ($user->group_id == 2) {
                                        echo "selected";
                                    }
                                    ?>>Admin</option>
                                </select>
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


                            <input class="waves-effect waves-light btn blue m-b-xs" type="submit" value="Submit">

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

