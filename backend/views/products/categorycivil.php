<?php
/* @var $this yii\web\View */

$this->title = 'Get Civil Data';
$user = Yii::$app->user->identity;
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
    .select2-container{width:100%!important;}
    .select2-search__field{color:#000!important;}
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
                        <form id="getdatas" class="col s12" method = "post" action = "<?= $baseURL ?>mail/getdata" enctype="multipart/form-data">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                            <div class="input-field col s12">
                                <select name='authtype' id="authtypes" class="contact-authtypes browser-default" required="">
                                    <option value=''>Select Product</option>
                                    <option value='3'>Cement</option>
                                    <option value='4'>Reinforcement Steel</option>
                                    <option value='5'>Structural Steel</option>
                                    <option value='6'>Non Structural Steel</option>
                                </select>
                            </div>
                            <div class="input-field col s12" id="cablesdiv" style="display: none;">
                                <select name='cables[]' class="cmakes browser-default" id="cables">
                                    <?php
                                    if (@$cables) {
                                        foreach ($cables as $cable_) {
                                            ?>
                                            <option value="<?= $cable_->id ?>"><?= $cable_->make ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="input-field col s12" id="lightdiv" style="display: none;">
                                <select name='lighting[]' class="lmakes browser-default" id="lighting">
                                    <?php
                                    if (@$lights) {
                                        foreach ($lights as $light_) {
                                            ?>
                                            <option value="<?= $light_->id ?>"><?= $light_->make ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="input-field col s12" id="cementdiv" style="display: none;">
                                <select name='cement[]' class="cementmakes browser-default" id="cement">
                                    <?php
                                    if (@$cements) {
                                        foreach ($cements as $cement_) {
                                            ?>
                                            <option value="<?= $cement_->id ?>"><?= $cement_->make ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="input-field col s12" id="rsteeldiv" style="display: none;">
                                <select name='rsteel[]' class="rmakes browser-default" id="rsteel">
                                    <?php
                                    if (@$rsteel) {
                                        foreach ($rsteel as $rsteel_) {
                                            ?>
                                            <option value="<?= $rsteel_->id ?>"><?= $rsteel_->make ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="input-field col s12" id="ssteeldiv" style="display: none;">
                                <select name='ssteel[]' class="smakes browser-default" id="ssteel">
                                    <?php
                                    if (@$ssteel) {
                                        foreach ($ssteel as $ssteel_) {
                                            ?>
                                            <option value="<?= $ssteel_->id ?>"><?= $ssteel_->make ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="input-field col s12" id="nsteeldiv" style="display: none;">
                                <select name='nsteel[]' class="nmakes browser-default" id="nsteel">
                                    <?php
                                    if (@$nsteel) {
                                        foreach ($nsteel as $nsteel_) {
                                            ?>
                                            <option value="<?= $nsteel_->id ?>"><?= $nsteel_->make ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <!--div class="input-field col s12">
                                
                                <input name="mails" class="contact-mails form-control" type="text" placeholder="Enter multiple E-mail IDs by putting comma">
                            </div-->

                            <!--button  id="signbutton" type="submit" name="sendmail"  class="waves-effect waves-light btn blue m-b-xs">Send Mail</button-->
                            <div class="input-field col s12">
                                <button  id="download" type="submit" name="download"  class="waves-effect waves-light btn blue m-b-xs">Download</button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>



        </div>


</main>