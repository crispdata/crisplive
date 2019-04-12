<?php
/* @var $this yii\web\View */

use backend\controllers\SiteController;

$this->title = 'Add New Address';

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
<script>
    function GetFileSize() {
        var com = document.forms["myform"]["command"].value;
        var con = document.forms["myform"]["contact"].value;
        var email = document.forms["myform"]["email"].value;
        var address = document.forms["myform"]["address"].value;
        if (com == "") {
            swal("", "Please select Command", "warning");
            return false;
        }
        if (con == "") {
            swal("", "Please enter Contact No.", "warning");
            return false;
        }
        if (address == "") {
            swal("", "Please enter Address", "warning");
            return false;
        }
         $("#create-project-form-tender").submit();
    }
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
                        <form id="create-project-form-tender" name="myform" class="col s12" onsubmit="return GetFileSize()" method = "post" action = "<?= $baseURL ?>products/addaddress">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                            <input type="hidden" value="<?= @$address->id; ?>" name="id">
                            <div class="row">
                                <div class="input-fields col s12 row">
                                    <label>Select Command</label>
                                    <select class="validate required materialSelect" name="command" id="commandz" onchange="getcengineer(this.value)">
                                        <option value="">Select Command</option>
                                        <option value="1" <?php
                                        if (@$address->command == 1) {
                                            echo "selected";
                                        }
                                        ?>>ADG (CG AND PROJECT) CHENNAI AND CE (CG) GOA - MES</option>
                                        <option value="2" <?php
                                        if (@$address->command == 2) {
                                            echo "selected";
                                        }
                                        ?>>ADG (DESIGN and CONSULTANCY) PUNE - MES</option>
                                        <option value="3" <?php
                                        if (@$address->command == 3) {
                                            echo "selected";
                                        }
                                        ?>>ADG (OF and DRDO) AND CE (FY) HYDERABAD - MES</option>
                                        <option value="4" <?php
                                        if (@$address->command == 4) {
                                            echo "selected";
                                        }
                                        ?>>ADG (OF and DRDO)  AND CE (R and D) DELHI-  MES</option>
                                        <option value="5" <?php
                                        if (@$address->command == 5) {
                                            echo "selected";
                                        }
                                        ?>>ADG (OF and DRDO) AND CE (R and D) SECUNDERABAD - MES</option>
                                        <option value="13" <?php
                                        if (@$address->command == 13) {
                                            echo "selected";
                                        }
                                        ?>>ADG (Projects) AND CE (CG) Visakhapatnam - MES</option>
                                        <option value="6" <?php
                                        if (@$address->command == 6) {
                                            echo "selected";
                                        }
                                        ?>>CENTRAL COMMAND</option>
                                        <option value="7" <?php
                                        if (@$address->command == 7) {
                                            echo "selected";
                                        }
                                        ?>>EASTERN COMMAND</option>
                                        <option value="8" <?php
                                        if (@$address->command == 8) {
                                            echo "selected";
                                        }
                                        ?>>NORTHERN COMMAND</option>
                                        <option value="9" <?php
                                        if (@$address->command == 9) {
                                            echo "selected";
                                        }
                                        ?>>SOUTHERN COMMAND</option>
                                        <option value="10" <?php
                                        if (@$address->command == 10) {
                                            echo "selected";
                                        }
                                        ?>>SOUTH WESTERN COMMAND</option>
                                        <option value="11" <?php
                                        if (@$address->command == 11) {
                                            echo "selected";
                                        }
                                        ?>>WESTERN COMMAND</option>
                                        <option value="12" <?php
                                        if (@$address->command == 12) {
                                            echo "selected";
                                        }
                                        ?>>DGNP MUMBAI - MES</option>
                                        <!--option value="2">B/R</option-->
                                    </select>
                                </div>
                            </div>
                            <?php
                            if (@$address->id) {
                                if ($address->cengineer != 0 || $address->cengineer != null) {
                                    ?>
                                    <div id="ce">
                                        <div class="input-fields col s12 row">
                                            <label>Select CE</label>
                                            <select class="validate required materialSelect" name="cengineer" id="cengineer" onchange="getcwengineer(this.value)">
                                                <?php SiteController::actionGetcengineerbycommand($address->command, $address->cengineer); ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php } elseif ($address->cwengineer != 0 || $address->cwengineer != null) { ?>
                                    <div id="cwe">
                                        <div class="input-fields col s12 row">
                                            <label>Select CWE</label>
                                            <select class="validate required materialSelect" name="cwengineer" id="cwengineer" onchange="getgengineer(this.value)">
                                                <?php SiteController::actionGetcengineerbycommand($address->command, $address->cwengineer); ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php } elseif ($address->gengineer != 0 || $address->gengineer != null) { ?>
                                    <div id="ge">
                                        <div class="input-fields col s12 row">
                                            <label>Select GE</label>
                                            <select class="validate required materialSelect" name="gengineer" id="gengineer">
                                                <?php SiteController::actionGetcengineerbycommand($address->command, $address->gengineer); ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div id="ce" style="display: none;">
                                        <div class="input-fields col s12 row">
                                            <label>Select CE</label>
                                            <select class="validate required materialSelect" name="cengineer" id="cengineer" onchange="getcwengineer(this.value)">
                                                <option value="" disabled selected>Select</option>
                                            </select>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php
                                if (($address->cengineer != 0 || $address->cengineer != null) && ($address->cwengineer != 0 || $address->cwengineer != null)) {
                                    ?>
                                    <div id="cwe">
                                        <div class="input-fields col s12 row">
                                            <label>Select CWE</label>
                                            <select class="validate required materialSelect" name="cwengineer" id="cwengineer" onchange="getgengineer(this.value)">
                                                <?php SiteController::actionGetcwengineerbyce($address->cengineer, $address->cwengineer); ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div id="cwe" style="display: none;">
                                        <div class="input-fields col s12 row">
                                            <label>Select CWE</label>
                                            <select class="validate required materialSelect" name="cwengineer" id="cwengineer" onchange="getgengineer(this.value)">
                                                <option value="" disabled selected>Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    <?php
                                }

                                if (($address->cengineer != 0 || $address->cengineer != null) && ($address->cwengineer != 0 || $address->cwengineer != null) && $address->gengineer != 0) {
                                    ?>

                                    <div id="ge">
                                        <div class="input-fields col s12 row">
                                            <label>Select GE</label>
                                            <select class="validate required materialSelect" name="gengineer" id="gengineer">
                                                <?php SiteController::actionGetgengineerbycwe($address->cwengineer, $address->gengineer); ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div id="ge" style="display: none;">
                                        <div class="input-fields col s12 row">
                                            <label>Select GE</label>
                                            <select class="validate required materialSelect" name="gengineer" id="gengineer">
                                                <option value="" disabled selected>Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                ?>
                                <div id="ce" style="display: none;">
                                    <div class="input-fields col s12 row">
                                        <label>Select CE</label>
                                        <select class="validate required materialSelect" name="cengineer" id="cengineer" onchange="getcwengineer(this.value)">
                                            <option value="0" disabled selected>Select</option>
                                        </select>
                                    </div>
                                </div>


                                <div id="cwe" style="display: none;">
                                    <div class="input-fields col s12 row">
                                        <label>Select CWE</label>
                                        <select class="validate required materialSelect" name="cwengineer" id="cwengineer" onchange="getgengineer(this.value)">
                                            <option value="0" disabled selected>Select</option>
                                        </select>
                                    </div>
                                </div>

                                <div id="ge" style="display: none;">
                                    <div class="input-fields col s12 row">
                                        <label>Select GE</label>
                                        <select class="validate required materialSelect" name="gengineer" id="gengineer">
                                            <option value="0" disabled selected>Select</option>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?>


                            <div class="row">
                                <div class="input-field col s6">
                                    <input id="contact" type="text" name = "contact" class="validate required" value='<?= @$address->contact; ?>'>
                                    <label for="contact">Contact No.</label>
                                </div>

                                <div class="input-field col s6">
                                    <input id="email" type="email" name = "email" class="validate required" value="<?= @$address->email; ?>">
                                    <label for="email">Email ID</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <textarea name="address" id="address" class="materialize-textarea validate required" required=""><?= @$address->address; ?></textarea>
                                    <label for="address">Address</label>
                                </div>
                            </div>


                            <input class="waves-effect waves-light btn blue m-b-xs" name="submit" type="submit" value="Submit">

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

