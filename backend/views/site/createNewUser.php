<?php
/* @var $this yii\web\View */

$this->title = 'Create New User';

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



        <div class="col s12 m12 l12">
            <div class="card">
                <div class="card-content">

                    <div class="row">
                        <form id="create-project-form" class="col s12" method = "post" action = "<?= $baseURL ?>site/create-user">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                            <div class="row">
                                <div class="input-field col s4">
                                    <input id="FirstName" type="text" maxlength="50" name = "CreateUser[username]" class="validate required" value="">
                                    <label for="FirstName">Username</label>
                                </div>
                                <div class="input-field col s4">
                                    <input id="Name" type="text" name = "CreateUser[name]" class="validate required" value="">
                                    <label for="Name">Name</label>
                                </div>

                                <div class="input-field col s4">
                                    <input id="Email" type="email" autocomplete="off" maxlength="100" size="30" name = "CreateUser[Email]" class="validate required" value="">
                                    <label for="Email">Email</label>
                                </div>

                            </div>


                            <div class="row">
                                <div class="input-field col s6">
                                    <select class="validate required" required="" name="CreateUser[group_id]" id="group">
                                        <option value="" disabled selected>Select Group</option>
                                        <?php
                                        if (isset($groups) && count($groups)) {
                                            foreach ($groups as $_group) {
                                                ?>
                                                <option value="<?= $_group->id ?>" ><?= ucfirst($_group->name); ?></option>
                                                <?php
                                            }
                                        }
                                        ?>

                                    </select>
                                </div>

                                <div class="input-field col s6">
                                    <input id="password" type="password" autocomplete="off" name = "CreateUser[password]" class="validate" value="">
                                    <label for="password">Password</label>
                                </div>
                            </div>

                            <div class='row'>
                                <div class="input-field col s6">
                                    <select name='CreateUser[authtype]' id="authtype" class="contact-authtypes browser-default" onchange="showdivssearch(this.value)">
                                        <option value=''>Select Product</option>
                                        <option value='1'>Cables</option>
                                        <option value='2'>Lighting</option>
                                        <option value='3'>Wires</option>
                                        <option value='4'>Cement</option>
                                        <option value='5'>Reinforcement Steel</option>
                                        <option value='6'>Structural Steel</option>
                                        <option value='7'>Non Structural Steel</option>
                                    </select>
                                </div>
                                <div class="input-field col s6" id="cablesdiv" <?= (@$_GET['authtype'] == 1) ? '' : 'style="display: none;"' ?> >
                                    <select name='CreateUser[cables]' class="cmakes browser-default" id="cables">
                                        <option value="0">Select Cables Makes</option>
                                        <?php
                                        if (@$cables) {
                                            foreach ($cables as $cable_) {
                                                ?>
                                                <option value="<?= $cable_->id ?>" <?= (@$_GET['cables'] == $cable_->id) ? 'selected' : '' ?>><?= $cable_->make ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="input-field col s6" id="lightdiv" <?= (@$_GET['authtype'] == 2) ? '' : 'style="display: none;"' ?>>
                                    <select name='CreateUser[lighting]' class="lmakes browser-default" id="lighting">
                                        <option value="0">Select Lighting Makes</option>
                                        <?php
                                        if (@$lights) {
                                            foreach ($lights as $light_) {
                                                ?>
                                                <option value="<?= $light_->id ?>" <?= (@$_GET['lighting'] == $light_->id) ? 'selected' : '' ?>><?= $light_->make ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="input-field col s6" id="wiresdiv" <?= (@$_GET['authtype'] == 3) ? '' : 'style="display: none;"' ?>>
                                    <select name='CreateUser[wires]' class="wmakes browser-default" id="wires">
                                        <option value="0">Select Wire Makes</option>
                                        <?php
                                        if (@$wires) {
                                            foreach ($wires as $wire_) {
                                                ?>
                                                <option value="<?= $wire_->id ?>" <?= (@$_GET['wires'] == $wire_->id) ? 'selected' : '' ?>><?= $wire_->make ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="input-field col s6" id="cementdiv" <?= (@$_GET['authtype'] == 4) ? '' : 'style="display: none;"' ?>>
                                    <select name='CreateUser[cement]' class="cementmakes browser-default" id="cement">
                                        <option value="0">Select Cement Makes</option>
                                        <?php
                                        if (@$cements) {
                                            foreach ($cements as $cement_) {
                                                ?>
                                                <option value="<?= $cement_->id ?>" <?= (@$_GET['cement'] == $cement_->id) ? 'selected' : '' ?>><?= $cement_->make ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="input-field col s6" id="rsteeldiv" <?= (@$_GET['authtype'] == 5) ? '' : 'style="display: none;"' ?>>
                                    <select name='CreateUser[rsteel]' class="rmakes browser-default" id="rsteel">
                                        <option value="0">Select Reinforcement Steel Makes</option>
                                        <?php
                                        if (@$rsteel) {
                                            foreach ($rsteel as $rsteel_) {
                                                ?>
                                                <option value="<?= $rsteel_->id ?>" <?= (@$_GET['rsteel'] == $rsteel_->id) ? 'selected' : '' ?>><?= $rsteel_->make ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="input-field col s6" id="ssteeldiv" <?= (@$_GET['authtype'] == 6) ? '' : 'style="display: none;"' ?>>
                                    <select name='CreateUser[ssteel]' class="smakes browser-default" id="ssteel">
                                        <option value="0">Select Structural Steel Makes</option>
                                        <?php
                                        if (@$ssteel) {
                                            foreach ($ssteel as $ssteel_) {
                                                ?>
                                                <option value="<?= $ssteel_->id ?>" <?= (@$_GET['ssteel'] == $ssteel_->id) ? 'selected' : '' ?>><?= $ssteel_->make ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="input-field col s6" id="nsteeldiv" <?= (@$_GET['authtype'] == 7) ? '' : 'style="display: none;"' ?>>
                                    <select name='CreateUser[nsteel]' class="nmakes browser-default" id="nsteel">
                                        <option value="0">Select Non Structural Steel Makes</option>
                                        <?php
                                        if (@$nsteel) {
                                            foreach ($nsteel as $nsteel_) {
                                                ?>
                                                <option value="<?= $nsteel_->id ?>" <?= (@$_GET['nsteel'] == $nsteel_->id) ? 'selected' : '' ?>><?= $nsteel_->make ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class='row'>
                                <input class="waves-effect waves-light btn blue m-b-xs" type="submit" value="Submit">
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

