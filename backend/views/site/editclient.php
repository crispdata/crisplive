<?php
/* @var $this yii\web\View */

$this->title = 'Edit Client';
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
                        <form id="create-project-form" class="col s12" method = "post" action = "<?= $baseURL ?>site/edit-client" enctype="multipart/form-data">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />

                            <input type="hidden" name="id" value="<?= @$_GET['id']; ?>" />
                            <input type="hidden" name="type" value="<?= @$client->type; ?>" />


                            <div class="input-field col s12">
                                <select name='type' id="rtype" class="validate required" disabled="">
                                    <option value=''>Register as</option>
                                    <option value='1' <?= ($client->type == 1) ? 'selected' : '' ?>>Manufacturer</option>
                                    <option value='2' <?= ($client->type == 2) ? 'selected' : '' ?>>Contractor</option>
                                    <option value='3' <?= ($client->type == 3) ? 'selected' : '' ?>>Dealer</option>
                                    <option value='4' <?= ($client->type == 4) ? 'selected' : '' ?>>Supplier</option>
                                </select>
                            </div>

                            <div class="input-field col s12">
                                <input name="firm" id="firm" class="validate required" type="text" placeholder="Firm name*" value="<?= $client->firm ?>" required="">
                                <label for="firm">Firm Name</label>
                            </div>
                            <?php if ($client->type == 2) { ?>
                                <div class="input-field col s12">
                                    <select name='contracttype' id="contracttype" class="validate required" required="">
                                        <option value=''>Select Firm Type</option>
                                        <option value='1' <?= ($client->contracttype == 1) ? 'selected' : '' ?>>Proprietorship</option>
                                        <option value='2' <?= ($client->contracttype == 2) ? 'selected' : '' ?>>Partnership</option>
                                        <option value='3' <?= ($client->contracttype == 3) ? 'selected' : '' ?>>Limited Liability Partnership</option>
                                        <option value='4' <?= ($client->contracttype == 4) ? 'selected' : '' ?>>Pvt. Ltd. Company</option>
                                        <option value='5' <?= ($client->contracttype == 5) ? 'selected' : '' ?>>Ltd. Company</option>
                                    </select>
                                    <label for="contracttype">Firm Type</label>
                                </div>
                            <?php } ?>
                            <div class="input-field col s12">
                                <textarea name="address" id="address" class="materialize-textarea"  placeholder="Address*" required=""><?= $client->address ?></textarea>
                                <label for="address">Address</label>
                            </div>
                            <div class="input-field col s12">
                                <select name='state' class="validate required" id="state" onchange="getcity(this.value)" required="">
                                    <option value="">Select State</option>
                                    <?php
                                    if (@$states) {
                                        foreach ($states as $state) {
                                            ?>
                                            <option value="<?= $state->id ?>" <?= ($client->state == $state->id) ? 'selected' : '' ?>><?= $state->name ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="state">State</label>
                            </div>
                            <div class="input-field col s12">
                                <select name='city' class="contact-city validate required" id="city"  required="">
                                    <option value="">Select City</option>
                                    <?php
                                    if (@$cities) {
                                        foreach ($cities as $city) {
                                            ?>
                                            <option value="<?= $city->id ?>" <?= ($client->city == $city->id) ? 'selected' : '' ?>><?= $city->name ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="city">City</label>
                            </div>
                            <div class="input-field col s12">
                                <input name="pcode" class="contact-code form-control" placeholder="Pincode" value="<?= $client->pcode ?>" id="pincode" type="text" required="">
                                <label for="pcode">Pin Code</label>
                            </div>

                            <div class="input-field col s12">
                                <input name="cperson" class="contact-person form-control" type="text" value="<?= $client->cperson ?>" placeholder="Contact Person*" required="">
                                <label for="cperson">Contact Person</label>
                            </div>
                            <div class="input-field col s12">
                                <input name="phone" class="contact-phone form-control" type="text" value="<?= $client->phone ?>" placeholder="Phone Number*" required="">
                                <label for="phone">Contact Number</label>
                            </div>
                            <div class="input-field col s12">
                                <input name="cnumber" id="mobile" class="contact-number form-control" type="number" value="<?= $client->cnumber ?>" placeholder="Mobile No.*" required="">
                                <label for="mobile">Mobile Number</label>
                            </div>
                            <div class="input-field col s12">
                                <input name="cemail" id="email" class="contact-email form-control" type="email" value="<?= $client->cemail ?>" placeholder="E-mail Id*" required="">
                                <label for="email">Email Address</label>
                            </div>
                            <?php if ($client->type == 3 || $client->type == 1) { ?>
                                <div class="input-field col s12">
                                    <select name='authtype' id="authtype" class="contact-authtype validate required" onchange="showdivs(this.value)">
                                        <option value=''>Select Product</option>
                                        <option value='1'>Cables</option>
                                        <option value='2'>Lighting</option>
                                    </select>
                                </div>
                                <div class="input-field col s12" id="cablesdiv" style="display: none;">
                                    <select name='cables[]' class="cmakes browser-default" id="cables" multiple>
                                        <?php
                                        $selectedcables = explode(',', $client->cables);
                                        if (@$cables) {
                                            foreach ($cables as $cable_) {
                                                ?>
                                                <option value="<?= $cable_->id ?>" <?php
                                                if (in_array($cable_->id, $selectedcables)) {
                                                    echo "selected";
                                                }
                                                ?>><?= $cable_->make ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                    </select>
                                </div>
                                <div class="input-field col s12" id="lightdiv" style="display: none;">
                                    <select name='lighting[]' class="lmakes browser-default" id="lighting" multiple>
                                        <?php
                                        $selectedlights = explode(',', $client->lighting);
                                        if (@$lights) {
                                            foreach ($lights as $light_) {
                                                ?>
                                                <option value="<?= $light_->id ?>" <?php
                                                if (in_array($light_->id, $selectedlights)) {
                                                    echo "selected";
                                                }
                                                ?>><?= $light_->make ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                    </select>
                                </div>
                            <?php } ?>



                            <input class="waves-effect waves-light btn blue m-b-xs" type="submit" name="submit" value="Submit">

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script>

    $(document).ready(function () {
        // for HTML5 "required" attribute
        $("select[required]").css({
            display: "inline",
            height: 0,
            padding: 0,
            width: 0
        });
    });
</script>
