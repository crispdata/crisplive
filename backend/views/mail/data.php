<?php
/* @var $this yii\web\View */

$this->title = 'Get Data';
$user = Yii::$app->user->identity;
$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
    .actions{display:none!important;}    
    .steps{display:none!important;}  
    .select-wrapper input.select-dropdown, .select-wrapper input.select-dropdown:disabled {
        border-color: unset;
    }
    ::placeholder{color:rgba(0,0,0,.6)!important;}
    .row{margin-bottom: 0px;}
    .select2-container{width:100%!important;}
    .select2-search__field{color:#000!important;}
     .ui-datepicker {
        width: 25em!important;
        padding: .2em .2em 0;
        display: none;
        z-index: 2!important;
    }
    .ui-widget{font-size:20px!important;}
    .ui-datepicker table {
        width: 100%;
        font-size: .7em;
        border-collapse: collapse;
        font-family:verdana;
        margin: 0 0 .4em;
        color:#000000;
        background:#FDF8E4;    
    }
    .ui-datepicker td {

        border: 0;
        padding: 1px;


    }
    .ui-datepicker select {
        display: block!important;
        float: left;
        width: 45%!important;
        margin-left: 15px!important;
        border: 1px solid #000;
        border-radius: 10px;
    }
    .ui-datepicker td span,
    .ui-datepicker td a {
        display: block;
        padding: .8em;
        text-align: center!important;
        text-decoration: none;
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
                        <form id="getdatas" class="col s12" method = "post" action = "<?= $baseURL ?>mail/getdata" enctype="multipart/form-data">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />

                            <div class="input-field col s6">
                                <input id="fromdatesearch" type="text" name = "fromdate"  placeholder='From Date' autocomplete="off" value="">
                            </div>

                            <div class="input-field col s6">
                                <input id="todatesearch" type="text" name = "todate" autocomplete="off" placeholder="To Date" value="">
                            </div>

                            <div class="input-field col s12">
                                <select name='authtype' id="authtype" class="contact-authtypes browser-default" onchange="showdivs(this.value)" required="">
                                    <option value=''>Select Product</option>
                                    <option value='1'>Cables</option>
                                    <option value='2'>Lighting</option>
                                    <option value='3'>Cement</option>
                                    <option value='4'>Reinforcement Steel</option>
                                    <option value='5'>Structural Steel</option>
                                    <option value='6'>Non Structural Steel</option>
                                </select>
                            </div>
                            <div class="input-field col s12" id="cablesdiv" style="display: none;">
                                <select name='cables[]' class="cmakes browser-default" id="cables">
                                    <option value="">Select</option>
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
                                    <option value="">Select</option>
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
                                    <option value="">Select</option>
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
                                    <option value="">Select</option>
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
                                    <option value="">Select</option>
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
                                    <option value="">Select</option>
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