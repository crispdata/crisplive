<?php
/* @var $this yii\web\View */

use backend\controllers\SiteController;

$this->title = 'Add New Tender';

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
        var fi = document.getElementById('file'); // GET THE FILE INPUT.
        var com = document.forms["myform"]["command"].value;
        var work = document.forms["myform"]["work"].value;
        var ref = document.forms["myform"]["refno"].value;
        var tid = document.forms["myform"]["tid"].value;
        var edate = document.forms["myform"]["enddate"].value;
        var odate = document.forms["myform"]["odate"].value;
        var cvalue = document.forms["myform"]["costvalue"].value;
        if (com == "") {
            swal("", "Please select Command", "warning");
            return false;
        }
        if (work == "") {
            swal("", "Please enter Name of work", "warning");
            return false;
        }
        if (ref == "") {
            swal("", "Please enter Ref. no.", "warning");
            return false;
        }
        if (tid == "") {
            swal("", "Please enter Tender Id", "warning");
            return false;
        }
        if (edate == "") {
            swal("", "Please enter Bid end date", "warning");
            return false;
        }
        if (odate == "") {
            swal("", "Please enter Bid opening date", "warning");
            return false;
        }
        if (cvalue == "") {
            swal("", "Please enter Cost Value", "warning");
            return false;
        }
        // VALIDATE OR CHECK IF ANY FILE IS SELECTED.
        if (fi.files.length > 0) {
            // RUN A LOOP TO CHECK EACH SELECTED FILE.
            for (var i = 0; i <= fi.files.length - 1; i++) {

                var fsize = fi.files.item(i).size;      // THE SIZE OF THE FILE.
                var returnsize = Math.round((fsize / 1024));
                if (returnsize > 80000) {
                    swal("", "Please upload file size less than 80MB", "warning");
                    return false;
                } else {
                    $("#create-project-form-tender").submit();
                }
            }
        }
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
                        <form id="create-project-form-tender" name="myform" class="col s12" enctype="multipart/form-data" method = "post" onsubmit="return GetFileSize()" action = "<?= $baseURL ?>site/create-tender">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                            <input type="hidden" value="<?= @$tender->id; ?>" name="id">
                            <div class="row">
                                <div class="input-fields col s12 row">
                                    <label>Select Command</label>
                                    <select class="validate required materialSelect" name="command" id="commandz" onchange="getcengineer(this.value)">
                                        <option value="">Select Command</option>
                                        <option value="1" <?php
                                        if (@$tender->command == 1) {
                                            echo "selected";
                                        }
                                        ?>>ADG (CG AND PROJECT) CHENNAI AND CE (CG) GOA - MES</option>
                                        <option value="2" <?php
                                        if (@$tender->command == 2) {
                                            echo "selected";
                                        }
                                        ?>>ADG (DESIGN and CONSULTANCY) PUNE - MES</option>
                                        <option value="3" <?php
                                        if (@$tender->command == 3) {
                                            echo "selected";
                                        }
                                        ?>>ADG (OF and DRDO) AND CE (FY) HYDERABAD - MES</option>
                                        <option value="4" <?php
                                        if (@$tender->command == 4) {
                                            echo "selected";
                                        }
                                        ?>>ADG (OF and DRDO)  AND CE (R and D) DELHI-  MES</option>
                                        <option value="5" <?php
                                        if (@$tender->command == 5) {
                                            echo "selected";
                                        }
                                        ?>>ADG (OF and DRDO) AND CE (R and D) SECUNDERABAD - MES</option>
                                        <option value="13" <?php
                                        if (@$tender->command == 13) {
                                            echo "selected";
                                        }
                                        ?>>ADG (Projects) AND CE (CG) Visakhapatnam - MES</option>
                                        <option value="6" <?php
                                        if (@$tender->command == 6) {
                                            echo "selected";
                                        }
                                        ?>>CENTRAL COMMAND</option>
                                        <option value="7" <?php
                                        if (@$tender->command == 7) {
                                            echo "selected";
                                        }
                                        ?>>EASTERN COMMAND</option>
                                        <option value="8" <?php
                                        if (@$tender->command == 8) {
                                            echo "selected";
                                        }
                                        ?>>NORTHERN COMMAND</option>
                                        <option value="9" <?php
                                        if (@$tender->command == 9) {
                                            echo "selected";
                                        }
                                        ?>>SOUTHERN COMMAND</option>
                                        <option value="10" <?php
                                        if (@$tender->command == 10) {
                                            echo "selected";
                                        }
                                        ?>>SOUTH WESTERN COMMAND</option>
                                        <option value="11" <?php
                                        if (@$tender->command == 11) {
                                            echo "selected";
                                        }
                                        ?>>WESTERN COMMAND</option>
                                        <option value="12" <?php
                                        if (@$tender->command == 12) {
                                            echo "selected";
                                        }
                                        ?>>DGNP MUMBAI - MES</option>
                                        <!--option value="2">B/R</option-->
                                    </select>
                                </div>
                            </div>
                            <?php
                            if (@$tender->id) {
                                if ($tender->cengineer != 0 || $tender->cengineer != null) {
                                    ?>
                                    <div id="ce">
                                        <div class="input-fields col s12 row">
                                            <label>Select CE</label>
                                            <select class="validate required materialSelect" name="cengineer" id="cengineer" onchange="getcwengineer(this.value)">
                                                <?php SiteController::actionGetcengineerbycommand($tender->command, $tender->cengineer); ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php } elseif ($tender->cwengineer != 0 || $tender->cwengineer != null) { ?>
                                    <div id="cwe">
                                        <div class="input-fields col s12 row">
                                            <label>Select CWE</label>
                                            <select class="validate required materialSelect" name="cwengineer" id="cwengineer" onchange="getgengineer(this.value)">
                                                <?php SiteController::actionGetcengineerbycommand($tender->command, $tender->cwengineer); ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php } elseif ($tender->gengineer != 0 || $tender->gengineer != null) { ?>
                                    <div id="ge">
                                        <div class="input-fields col s12 row">
                                            <label>Select GE</label>
                                            <select class="validate required materialSelect" name="gengineer" id="gengineer">
                                                <?php SiteController::actionGetcengineerbycommand($tender->command, $tender->gengineer); ?>
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
                                if (($tender->cengineer != 0 || $tender->cengineer != null) && ($tender->cwengineer != 0 || $tender->cwengineer != null)) {
                                    ?>
                                    <div id="cwe">
                                        <div class="input-fields col s12 row">
                                            <label>Select CWE</label>
                                            <select class="validate required materialSelect" name="cwengineer" id="cwengineer" onchange="getgengineer(this.value)">
                                                <?php SiteController::actionGetcwengineerbyce($tender->cengineer, $tender->cwengineer); ?>
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

                                if (($tender->cengineer != 0 || $tender->cengineer != null) && ($tender->cwengineer != 0 || $tender->cwengineer != null) && $tender->gengineer != 0) {
                                    ?>

                                    <div id="ge">
                                        <div class="input-fields col s12 row">
                                            <label>Select GE</label>
                                            <select class="validate required materialSelect" name="gengineer" id="gengineer">
                                                <?php SiteController::actionGetgengineerbycwe($tender->cwengineer, $tender->gengineer); ?>
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
                                    <textarea name="work" id="work" class="materialize-textarea validate required" required=""><?= @$tender->work; ?></textarea>
                                    <label for="work">Name of work</label>
                                </div>

                                <div class="input-field col s6">
                                    <input id="refno" type="text" name = "refno" class="validate required" value="<?= @$tender->reference_no; ?>">
                                    <label for="refno">Tender Ref. no.</label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="input-field col s6">
                                    <input id="tid" type="text" name = "tid" class="validate required" value="<?= @$tender->tender_id; ?>">
                                    <label for="tid">Tender Id</label>
                                </div>

                                <div class="input-field col s6">
                                    <input id="enddate" type="text" name = "enddate" class="required bedatepicker" value="<?php
                                    if (@$tender->bid_end_date) {
                                        echo @$tender->bid_end_date;
                                    }
                                    ?>">
                                    <label for="enddate">Bid end date</label>
                                </div>


                            </div>

                            <div class="row">

                                <div class="input-field col s6">
                                    <input id="odate" type="text" name = "odate" class="required bodatepicker" value="<?php
                                    if (@$tender->bid_opening_date) {
                                        echo @$tender->bid_opening_date;
                                    }
                                    ?>">
                                    <label for="odate">Bid opening date</label>
                                </div>

                                <div class="input-field col s6">
                                    <input id="costvalue" type="text" name = "costvalue" class="required" value="<?php
                                    if (@$tender->cvalue) {
                                        echo @$tender->cvalue;
                                    }
                                    ?>">
                                    <label for="costvalue">Cost Value</label>
                                </div>
                            </div>
                            <div class="row">

                                <div class="input-field col s12 file-field input-field">
                                    <div class="btn teal lighten-1">
                                        <span>File</span>
                                        <input type="file" name="tfile" id="file">
                                    </div>
                                    <div class="file-path-wrapper">
                                        <input class="file-path validate" type="text">
                                    </div>
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

