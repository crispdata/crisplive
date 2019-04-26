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
    .select2-container {
        width: 100%!important;
    }
</style>

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
                                <input id="name" type="text" name = "name" class="validate required" value="<?= $user->name; ?>">
                                <label for="name">Name</label>
                            </div>

                            <div class="input-field col s12">
                                <input id="email" type="text" maxlength="50" name = "email" class="validate required" value="<?= $user->email; ?>">
                                <label for="email">Email</label>
                            </div>

                            <div class="input-field col s12">
                                <select class="validate required" required="" name="group_id" id="group">
                                    <option value="" disabled selected>Select Group</option>
                                    <?php
                                    if (isset($groups) && count($groups)) {
                                        foreach ($groups as $_group) {
                                            ?>
                                            <option value="<?= $_group->id ?>" <?= ($user->group_id == $_group->id) ? 'selected' : '' ?> ><?= ucfirst($_group->name); ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <?php if ($user->group_id == 6) { ?>
                                <div class='row'>
                                    <div class="input-field col s12">
                                        <select name='authtype' id="authtype" class="contact-authtypes browser-default" onchange="showdivssearch(this.value)">
                                            <option value=''>Select Product</option>
                                            <option value='1' <?= (@$user->authtype == 1) ? 'selected' : '' ?>>Cables</option>
                                            <option value='2' <?= (@$user->authtype == 2) ? 'selected' : '' ?>>Lighting</option>
                                            <option value='3' <?= (@$user->authtype == 3) ? 'selected' : '' ?>>Wires</option>
                                            <option value='4' <?= (@$user->authtype == 4) ? 'selected' : '' ?>>Cement</option>
                                            <option value='5' <?= (@$user->authtype == 5) ? 'selected' : '' ?>>Reinforcement Steel</option>
                                            <option value='6' <?= (@$user->authtype == 6) ? 'selected' : '' ?>>Structural Steel</option>
                                            <option value='7' <?= (@$user->authtype == 7) ? 'selected' : '' ?>>Non Structural Steel</option>
                                        </select>
                                    </div>
                                    <div class="input-fields col s12" id="cablesdiv" <?= (@$user->cables) ? '' : 'style="display: none;"' ?> >
                                       <label for='cables'>Select Cables Makes</label>
                                        <select name='cables' class="cmakes browser-default" id="cables">
                                            <option value="0">Select Cables Makes</option>
                                            <?php
                                            if (@$cables) {
                                                foreach ($cables as $cable_) {
                                                    ?>
                                                    <option value="<?= $cable_->id ?>" <?= (@$user->cables == $cable_->id) ? 'selected' : '' ?>><?= $cable_->make ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="input-fields col s12" id="lightdiv" <?= (@$user->lighting) ? '' : 'style="display: none;"' ?>>
                                        <label for='lighting'>Select Lighting Makes</label>
                                        <select name='lighting' class="lmakes browser-default" id="lighting">
                                            <option value="0">Select Lighting Makes</option>
                                            <?php
                                            if (@$lights) {
                                                foreach ($lights as $light_) {
                                                    ?>
                                                    <option value="<?= $light_->id ?>" <?= (@$user->lighting == $light_->id) ? 'selected' : '' ?>><?= $light_->make ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="input-fields col s12" id="wiresdiv" <?= (@$user->wires) ? '' : 'style="display: none;"' ?>>
                                        <label for='wires'>Select Wire Makes</label>
                                        <select name='wires' class="wmakes browser-default" id="wires">
                                            <option value="0">Select Wire Makes</option>
                                            <?php
                                            if (@$wires) {
                                                foreach ($wires as $wire_) {
                                                    ?>
                                                    <option value="<?= $wire_->id ?>" <?= (@$user->wires == $wire_->id) ? 'selected' : '' ?>><?= $wire_->make ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="input-fields col s12" id="cementdiv" <?= (@$user->cement) ? '' : 'style="display: none;"' ?>>
                                        <label for='cement'>Select Cement Makes</label>
                                        <select name='cement' class="cementmakes browser-default" id="cement">
                                            <option value="0">Select Cement Makes</option>
                                            <?php
                                            if (@$cements) {
                                                foreach ($cements as $cement_) {
                                                    ?>
                                                    <option value="<?= $cement_->id ?>" <?= (@$user->cement == $cement_->id) ? 'selected' : '' ?>><?= $cement_->make ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="input-fields col s12" id="rsteeldiv" <?= (@$user->rsteel) ? '' : 'style="display: none;"' ?>>
                                        <label for='rsteel'>Select Reinforcement Steel Makes</label>
                                        <select name='rsteel' class="rmakes browser-default" id="rsteel">
                                            <option value="0">Select Reinforcement Steel Makes</option>
                                            <?php
                                            if (@$rsteel) {
                                                foreach ($rsteel as $rsteel_) {
                                                    ?>
                                                    <option value="<?= $rsteel_->id ?>" <?= (@$user->rsteel == $rsteel_->id) ? 'selected' : '' ?>><?= $rsteel_->make ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="input-fields col s12" id="ssteeldiv" <?= (@$user->ssteel) ? '' : 'style="display: none;"' ?>>
                                        <label for='ssteel'>Select Structural Steel Makes</label>
                                        <select name='ssteel' class="smakes browser-default" id="ssteel">
                                            <option value="0">Select Structural Steel Makes</option>
                                            <?php
                                            if (@$ssteel) {
                                                foreach ($ssteel as $ssteel_) {
                                                    ?>
                                                    <option value="<?= $ssteel_->id ?>" <?= (@$user->ssteel == $ssteel_->id) ? 'selected' : '' ?>><?= $ssteel_->make ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="input-fields col s12" id="nsteeldiv" <?= (@$user->nsteel) ? '' : 'style="display: none;"' ?>>
                                        <label for='nsteel'>Select Non Structural Steel Makes</label>
                                        <select name='nsteel' class="nmakes browser-default" id="nsteel">
                                            <option value="0">Select Non Structural Steel Makes</option>
                                            <?php
                                            if (@$nsteel) {
                                                foreach ($nsteel as $nsteel_) {
                                                    ?>
                                                    <option value="<?= $nsteel_->id ?>" <?= (@$user->nsteel == $nsteel_->id) ? 'selected' : '' ?>><?= $nsteel_->make ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?>
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
                                <input id="password" type="text" name = "password" autocomplete="off" class="validate" value="<?= $user->password; ?>">
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

