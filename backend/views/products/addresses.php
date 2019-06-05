<?php
/* @var $this yii\web\View */

use backend\controllers\SiteController;
use yii\helpers\Url;

$this->title = 'All Addresses';
$userdetail = Yii::$app->user->identity;
$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
?>
<style>
    .add-contact{    float: right;
                     margin-right: 15px;}    
    .btn, .btn-flat {
        font-size: 11px;
    }
    .select-wrapper input.select-dropdown, .select-wrapper input.select-dropdown:disabled {
        border-color: unset;
    }

</style>
<script>
    function GetFileSize() {
        var com = document.forms["myform"]["department"].value;
        if (com == "") {
            swal("", "Please select Department", "warning");
            return false;
        }
        $("#form-addresses").submit();
    }
</script>
<?php
$departments = \common\models\Departments::find()->orderBy(['name' => SORT_ASC])->all();
?>
<main class="mn-inner">
    <div class="row">
        <div class="col s6">
            <div class="page-title">Office Addresses</div>
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
                    <form id="form-addresses" name="myform" class="col s12" onsubmit="return GetFileSize()" method = "post" action = "<?= $baseURL ?>products/addresses">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                        <input type="hidden" value="<?= @$address->id; ?>" name="id">
                        <div class="input-fields col s12">
                            <label>Select Department</label>
                            <select class="validate required materialSelect" name="department" id="department">
                                <option value="">Select Department</option>
                                <?php
                                if (count($departments)) {
                                    foreach ($departments as $depart) {
                                        ?>
                                        <option value="<?= $depart->id ?>" <?= (@$_POST['department'] == $depart->id) ? 'selected' : '' ?> ><?= $depart->name ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="input-fields col s12">
                            <label>Select Command</label>
                            <select class="validate required materialSelect" name="command" id="commandz" onchange="getcengineeraddress(this.value)">
                                <option value="" selected>Select Command</option>
                                <option value="0" <?php
                                if (@$_POST['command'] == 0 && @$_POST['command'] != '') {
                                    echo "selected";
                                }
                                ?>>ALL COMMANDS</option>
                                <option value="1" <?php
                                if (@$_POST['command'] == 1) {
                                    echo "selected";
                                }
                                ?>>ADG (CG AND PROJECT) CHENNAI AND CE (CG) GOA - MES</option>
                                <option value="2" <?php
                                if (@$_POST['command'] == 2) {
                                    echo "selected";
                                }
                                ?>>ADG (DESIGN and CONSULTANCY) PUNE - MES</option>
                                <option value="3" <?php
                                if (@$_POST['command'] == 3) {
                                    echo "selected";
                                }
                                ?>>ADG (OF and DRDO) AND CE (FY) HYDERABAD - MES</option>
                                <option value="4" <?php
                                if (@$_POST['command'] == 4) {
                                    echo "selected";
                                }
                                ?>>ADG (OF and DRDO)  AND CE (R and D) DELHI-  MES</option>
                                <option value="5" <?php
                                if (@$_POST['command'] == 5) {
                                    echo "selected";
                                }
                                ?>>ADG (OF and DRDO) AND CE (R and D) SECUNDERABAD - MES</option>
                                <option value="13" <?php
                                if (@$_POST['command'] == 13) {
                                    echo "selected";
                                }
                                ?>>ADG (Projects) AND CE (CG) Visakhapatnam - MES</option>
                                <option value="14" <?php
                                if (@$_POST['command'] == 14) {
                                    echo "selected";
                                }
                                ?>>ADG (Project) Chennai AND CE (FY) Hyderabad - MES</option>
                                <option value="6" <?php
                                if (@$_POST['command'] == 6) {
                                    echo "selected";
                                }
                                ?>>CENTRAL COMMAND</option>
                                <option value="7" <?php
                                if (@$_POST['command'] == 7) {
                                    echo "selected";
                                }
                                ?>>EASTERN COMMAND</option>
                                <option value="8" <?php
                                if (@$_POST['command'] == 8) {
                                    echo "selected";
                                }
                                ?>>NORTHERN COMMAND</option>
                                <option value="9" <?php
                                if (@$_POST['command'] == 9) {
                                    echo "selected";
                                }
                                ?>>SOUTHERN COMMAND</option>
                                <option value="10" <?php
                                if (@$_POST['command'] == 10) {
                                    echo "selected";
                                }
                                ?>>SOUTH WESTERN COMMAND</option>
                                <option value="11" <?php
                                if (@$_POST['command'] == 11) {
                                    echo "selected";
                                }
                                ?>>WESTERN COMMAND</option>
                                <option value="12" <?php
                                if (@$_POST['command'] == 12) {
                                    echo "selected";
                                }
                                ?>>DGNP MUMBAI - MES</option>
                                <!--option value="2">B/R</option-->
                            </select>
                        </div>
                        <?php
                        if ((@$_POST['cengineer'] == 0 || @$_POST['cengineer'] == null) && (@$_POST['gengineer'] == 0 || @$_POST['gengineer'] == null)) {
                            $arrcommands = [1, 2, 3, 4, 5, 13, 14];
                            $getcengineers = \common\models\Cengineer::find()->where(['command' => @$_POST['command']])->all();
                            if (@$getcengineers && (!in_array(@$_POST['command'], $arrcommands))) {
                                ?>
                                <div id="ce">
                                    <div class="input-field col s12">
                                        <select class="validate required materialSelect" name="cengineer" id="cengineer" onchange="getcwengineer(this.value)">
                                            <option value="">Select CE</option>
                                            <?php SiteController::actionGetcengineeraddressbycommand(@$_POST['command'], ''); ?>
                                        </select>
                                    </div>
                                </div>
                                <?php
                            } elseif (@$getcengineers && (in_array(@$_POST['command'], $arrcommands))) {
                                ?>
                                <div id="ge">
                                    <div class="input-field col s12">
                                        <select class="validate required materialSelect" name="gengineer" id="gengineer">
                                            <option value="">Select GE</option>
                                            <?php SiteController::actionGetcengineeraddressbycommand(@$_POST['command'], ''); ?>
                                        </select>
                                    </div>
                                </div>  
                                <?php
                            }
                        }
                        if (@$_POST['cengineer'] != 0 || @$_POST['cengineer'] != null) {
                            ?>
                            <div id="ce">
                                <div class="input-field col s12">
                                    <select class="validate required materialSelect" name="cengineer" id="cengineer" onchange="getcwengineer(this.value)">
                                        <option value="">Select CE</option>
                                        <?php SiteController::actionGetcengineeraddressbycommand(@$_POST['command'], $_POST['cengineer']); ?>
                                    </select>
                                </div>
                            </div>
                            <?php
                            if (@$_POST['cwengineer'] == 0 || @$_POST['cwengineer'] == null) {
                                $getcwengineers = \common\models\Cwengineer::find()->where(['cengineer' => $_POST['cengineer']])->all();
                                ?>
                                <?php if (@$getcwengineers) { ?>            
                                    <div id="cwe">
                                        <div class="input-field col s12">
                                            <select class="validate required materialSelect" name="cwengineer" id="cwengineer" onchange="getgengineer(this.value)">
                                                <option value="">Select CWE</option>
                                                <?php SiteController::actionGetcwengineerbyce($_POST['cengineer'], ''); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        <?php } elseif (@$_POST['cwengineer'] != 0 || @$_POST['cwengineer'] != null) { ?>
                            <div id="cwe">
                                <div class="input-field col s12">
                                    <select class="validate required materialSelect" name="cwengineer" id="cwengineer" onchange="getgengineer(this.value)">
                                        <option value="">Select CE</option>
                                        <?php SiteController::actionGetcengineeraddressbycommand(@$_POST['command'], $_POST['cwengineer']); ?>
                                    </select>
                                </div>
                            </div>
                        <?php } elseif (@$_POST['gengineer'] != 0 || @$_POST['gengineer'] != null) { ?>
                            <div id="ge">
                                <div class="input-field col s12">
                                    <select class="validate required materialSelect" name="gengineer" id="gengineer">
                                        <option value="">Select CE</option>
                                        <?php SiteController::actionGetcengineeraddressbycommand(@$_POST['command'], $_POST['gengineer']); ?>
                                    </select>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div id="ce" style="display: none;">
                                <div class="input-field col s12">
                                    <select class="validate required materialSelect" name="cengineer" id="cengineer" onchange="getcwengineer(this.value)">
                                        <option value="" disabled selected>Select CE</option>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>

                        <?php
                        if ((@$_POST['cengineer'] != 0 || @$_POST['cengineer'] != null) && (@$_POST['cwengineer'] != 0 || @$_POST['cwengineer'] != null)) {
                            ?>
                            <div id="cwe">
                                <div class="input-field col s12">
                                    <select class="validate required materialSelect" name="cwengineer" id="cwengineer" onchange="getgengineer(this.value)">
                                        <option value="">Select CWE</option>
                                        <?php SiteController::actionGetcwengineerbyce($_POST['cengineer'], $_POST['cwengineer']); ?>
                                    </select>
                                </div>
                            </div>
                            <?php
                            if (@$_POST['gengineer'] == 0 || @$_POST['gengineer'] == null) {
                                $getgengineers = \common\models\Gengineer::find()->where(['cwengineer' => $_POST['cwengineer']])->all();
                                ?>
                                <?php if (@$getgengineers) { ?>
                                    <div id="ge">
                                        <div class="input-field col s12">
                                            <select class="validate required materialSelect" name="gengineer" id="gengineer">
                                                <option value="">Select GE</option>
                                                <?php SiteController::actionGetgengineerbycwe($_POST['cwengineer'], ''); ?>
                                            </select>
                                        </div>
                                    </div>  
                                    <?php
                                }
                            }
                        } else {
                            ?>
                            <div id="cwe" style="display: none;">
                                <div class="input-field col s12">
                                    <select class="validate required materialSelect" name="cwengineer" id="cwengineer" onchange="getgengineer(this.value)">
                                        <option value="" disabled selected>Select CWE</option>
                                    </select>
                                </div>
                            </div>
                            <?php
                        }

                        if ((@$_POST['cengineer'] != 0 || @$_POST['cengineer'] != null) && (@$_POST['cwengineer'] != 0 || @$_POST['cwengineer'] != null) && @$_POST['gengineer'] != 0) {
                            ?>

                            <div id="ge">
                                <div class="input-field col s12">
                                    <select class="validate required materialSelect" name="gengineer" id="gengineer">
                                        <option value="">Select GE</option>
                                        <?php SiteController::actionGetgengineerbycwe($_POST['cwengineer'], $_POST['gengineer']); ?>
                                    </select>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div id="ge" style="display: none;">
                                <div class="input-field col s12">
                                    <select class="validate required materialSelect" name="gengineer" id="gengineer">
                                        <option value="" disabled selected>Select GE</option>
                                    </select>
                                </div>
                            </div>
                            <div id="ce" style="display: none;">
                                <div class="input-field col s12">
                                    <select class="validate required materialSelect" name="cengineer" id="cengineer">
                                        <option value="" disabled selected>Select CE</option>
                                    </select>
                                </div>
                            </div>
                        <?php }
                        ?>

                        <input class="btn blue m-b-xs" name="submit" type="submit" value="Submit">

                    </form>

                    <?php if (@$addresses || count($_POST)) { ?>
                        <table id = "current-project" class="responsive-table">
                            <thead>
                                <tr>
                                    <th data-field="name">Sr. No.</th>
                                    <th data-field="name">Office</th>
                                    <th data-field="name">Contact No.</th>
                                    <th data-field="name">Email ID</th>
                                    <th data-field="name">Address</th>
                                    <?php if ($userdetail->group_id != 6) { ?>
                                        <th data-field="email">Actions</th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody id="contacts_list">
                                <?php
                                if (@$addresses) {
                                    $i = 0;
                                    foreach ($addresses as $key => $user) {
                                        ?>
                                        <tr data-id = "<?= $user->id ?>">
                                            <td class = ""><?= $key + 1 ?></td>
                                            <td class = ""><?= str_replace('-MES', '', str_replace(' - MES', '', $user->command)); ?></td>
                                            <td class = ""><?= $user->contact ?></td>
                                            <td class = ""><?= $user->email ?></td>
                                            <td class = ""><?= $user->address ?></td>
                                            <?php if ($userdetail->group_id != 6) { ?>
                                                <td>
                                                    <a href="<?= Url::to(['products/addaddress', 'id' => $user->id]) ?>" class="waves-effect waves-light btn blue">Edit</a>
                                                    <a href="#modal<?= $user->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                    <div id="modal<?= $user->id; ?>" class="modal">
                                        <div class="modal-content">
                                            <h4>Confirmation Message</h4>
                                            <p>Are you sure you want to delete it ?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="javascript::void()" class=" modal-action modal-close waves-effect waves-green btn-flat">No</a>
                                            <a href="<?= Url::to(['products/deleteaddress', 'id' => $user->id]) ?>" class=" modal-action modal-close waves-effect waves-green btn-flat">Yes</a>
                                        </div>
                                    </div>

                                    <?php
                                    $i++;
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</main>
