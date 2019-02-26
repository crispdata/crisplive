<?php
/* @var $this yii\web\View */

use backend\controllers\SiteController;

$this->title = 'Create New Tender';

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
                        <form id="create-project-form" class="col s12" enctype="multipart/form-data" method = "post" action = "<?= $baseURL ?>site/create-tender">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                            <input type="hidden" value="<?= @$tender->id; ?>" name="id">
                            <div class="row">
                                <div class="input-fields col s12 row">
                                    <label>Select Command</label>
                                    <select class="validate required materialSelect" name="command" id="command" onchange="getcengineer(this.value)">
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
                                if ($tender->cengineer != 0) {
                                    ?>
                                    <div id="ce">
                                        <div class="input-fields col s12 row">
                                            <label>Select CE</label>
                                            <select class="validate required materialSelect" name="cengineer" id="cengineer" onchange="getcwengineer(this.value)">
                                                <?php SiteController::actionGetcengineerbycommand($tender->command, $tender->cengineer); ?>
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
                                if ($tender->cwengineer != 0) {
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

                                if ($tender->gengineer != 0) {
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
                                    <input id="work" type="text" name = "work" class="validate required" value="<?= @$tender->work; ?>">
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
                                    <input id="pdate" type="text" name = "pdate" class="pdatepicker required" value="<?php
                                    if (@$tender->published_date) {
                                        echo @$tender->published_date;
                                    }
                                    ?>">
                                    <label for="pdate">Published date</label>

                                </div>
                            </div>

                            <div class="row">
                                <div class="input-field col s6">
                                    <input id="ddate" type="text" name = "ddate" class="required ddatepicker" value="<?php
                                    if (@$tender->document_date) {
                                        echo @$tender->document_date;
                                    }
                                    ?>">
                                    <label for="ddate">Document download date</label>
                                </div>

                                <div class="input-field col s6">
                                    <input id="subdate" type="text" name = "subdate" class="required bsdatepicker" value="<?php
                                    if (@$tender->bid_sub_date) {
                                        echo @$tender->bid_sub_date;
                                    }
                                    ?>">
                                    <label for="subdate">Bid submission date</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input id="enddate" type="text" name = "enddate" class="required bedatepicker" value="<?php
                                    if (@$tender->bid_end_date) {
                                        echo @$tender->bid_end_date;
                                    }
                                    ?>">
                                    <label for="enddate">Bid end date</label>
                                </div>

                                <div class="input-field col s6">
                                    <input id="odate" type="text" name = "odate" class="required bodatepicker" value="<?php
                                    if (@$tender->bid_opening_date) {
                                        echo @$tender->bid_opening_date;
                                    }
                                    ?>">
                                    <label for="odate">Bid opening date</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input id="costvalue" type="text" name = "costvalue" class="required" value="<?php
                                    if (@$tender->cvalue) {
                                        echo @$tender->cvalue;
                                    }
                                    ?>">
                                    <label for="costvalue">Cost Value</label>
                                </div>
                                <div class="input-field col s6 file-field input-field">
                                    <div class="btn teal lighten-1">
                                        <span>File</span>
                                        <input type="file" name="tfile">
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
