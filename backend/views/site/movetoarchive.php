<?php
/* @var $this yii\web\View */

use backend\controllers\SiteController;
use yii\widgets\LinkPager;
use yii\helpers\Url;

$this->title = 'Move Tenders to Archive by Date';
$user = Yii::$app->user->identity;
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
    .select2-container--default .select2-results__option[aria-selected=true]{background-color: #00ACC1;}
    .select2-dropdown{margin-top:0px;}
    .select2-container{width:100%!important;}
    .select2-search__field{color:#000!important;}

    input:not([type]):disabled, input:not([type])[readonly="readonly"], input[type=text]:disabled, input[type=text][readonly="readonly"], input[type=password]:disabled, input[type=password][readonly="readonly"], input[type=email]:disabled, input[type=email][readonly="readonly"], input[type=url]:disabled, input[type=url][readonly="readonly"], input[type=time]:disabled, input[type=time][readonly="readonly"], input[type=date]:disabled, input[type=date][readonly="readonly"], input[type=datetime]:disabled, input[type=datetime][readonly="readonly"], input[type=datetime-local]:disabled, input[type=datetime-local][readonly="readonly"], input[type=tel]:disabled, input[type=tel][readonly="readonly"], input[type=number]:disabled, input[type=number][readonly="readonly"], input[type=search]:disabled, input[type=search][readonly="readonly"], textarea.materialize-textarea:disabled, textarea.materialize-textarea[readonly="readonly"]{color:#000;}
    .select-wrapper input.select-dropdown:disabled{color:#000;}
    .notavailable{color:red;}
    ul.pagination {
        float: right;
    }
    .card-tenders{padding-bottom: 60px!important;}
    .pagination li.active {
        background-color: #2196F3!important;
    }
    span.totaltenders {
        float: left;
        width: 50%;
        margin-top: 20px;
    }
    form#sort-data {
        float: left;
        width: 60%;
        z-index: 100000000;
    }
</style>

<main class="mn-inner">
    <div class="row">
        <div class="col s6">
            <div class="page-title">Move Tenders to Archive by Date</div>
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
                        <form id="command-types" class="col s12" method = "post" action = "<?= $baseURL ?>site/movetoarchive">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                            <div class="row">
                                <div class="input-field col s6">
                                    <input id="fromdate" type="text" name = "fromdate" class="required bedatepicker" value="<?php
                                    if (@$_POST['fromdate']) {
                                        echo @$_POST['fromdate'];
                                    }
                                    ?>">
                                    <label for="fromdate">From Date</label>
                                </div>

                                <div class="input-field col s6">
                                    <input id="todate" type="text" name = "todate" class="required bedatepicker" value="<?php
                                    if (@$_POST['todate']) {
                                        echo @$_POST['todate'];
                                    }
                                    ?>">
                                    <label for="todate">To Date</label>
                                </div>


                            </div>
                            <input class="waves-effect waves-light btn blue m-b-xs" name="submit" type="submit" value="Submit">
                        </form>
<?php if (isset($tenders) && count($tenders)) { ?>

                            <form id="create-item" method = "post" onsubmit="return deleteConfirm();" action = "<?= $baseURL ?>site/movearchivetenders">
                                <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                                <?php if ($user->group_id != 3) { ?>
                                    <input type="submit" class="waves-effect waves-light btn blue m-b-xs add-contact" name="btn_arc" value="Archive Tenders"/>
                                <?php }
                                ?>

                                <table id = "current-project" class="responsive-table">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" name="check_all" id="check_all" required="" value=""/><label for="check_all"></label></th>
                                            <th data-field="email">Tender Id</th>
                                            <th data-field="name">Details of Contracting Office</th>
                                            <th data-field="email">Awarded Amount</th>
                                            <th data-field="email">Bid End Date</th>
                                            <th data-field="email">AOC Date</th>
                                            <th data-field="email">Status</th>
                                            <th data-field="email">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="contacts_list">
                                        <?php
                                        if (@$tenders) {
                                            $i = 0;
                                            foreach ($tenders as $key => $tender) {
                                                $tdetails = '';
                                                $command = Sitecontroller::actionGetcommand($tender->command);
                                                $cengineer = \common\models\Cengineer::find()->where(['cid' => $tender->cengineer, 'status' => 1])->one();
                                                $cwengineer = \common\models\Cwengineer::find()->where(['cengineer' => $tender->cengineer, 'cid' => $tender->cwengineer, 'status' => 1])->one();
                                                $gengineer = \common\models\Gengineer::find()->where(['cwengineer' => $tender->cwengineer, 'gid' => $tender->gengineer, 'status' => 1])->one();
                                                $tdetails = @$command . ' ' . @$cengineer->text . ' ' . @$cwengineer->text . ' ' . @$gengineer->text;
                                                if ($tender->status == 1) {
                                                    $status = 'Approved';
                                                    $class = 'green';
                                                } else {
                                                    $status = 'Unapproved';
                                                    $class = 'red';
                                                }

                                                if ($tender->on_hold == 1) {
                                                    $classaoc = 'red';
                                                    $text = 'On Hold';
                                                } else {
                                                    $classaoc = 'green';
                                                    $text = 'Ready';
                                                }
                                                $stop_date = date('Y-m-d H:i:s', strtotime($tender->createdon . ' +1 day'));
                                                $contractor = \common\models\Contractor::find()->where(['id' => $tender->contractor])->one();
                                                ?>
                                                <tr data-id = "<?= $tender->tender_id ?>">
                                                    <td align="center"><input type="checkbox" name="selected_id[]" class="checkbox" id="check<?php echo $tender->id; ?>" value="<?php echo $tender->id; ?>"/><label for="check<?php echo $tender->id; ?>"></label></td> 
                                                    <td class = ""><?= $tender->tender_id ?></td>
                                                    <td class = ""><?= $tdetails ?></td>
                                                    <td class = ""><?= $tender->qvalue ?></td>
                                                    <td class = ""><span style="display:none;"><?= $contractor->firm ?></span><?= $tender->bid_end_date ?></td>
                                                    <td class = ""><?= $tender->aoc_date ?></td>
                                                    <td ><a class = "btn <?= $class ?>"><?= $status ?></a></td>
                                                    <td>
                                                        <?php
                                                        if ($user->group_id == 9) {
                                                            if ($stop_date >= date('Y-m-d H:i:s') && $tender->status == 0) {
                                                                ?>


                                                                <?php
                                                            }
                                                        } else {
                                                            if ($tender->status == 1) {
                                                                ?>
                                                                <a class = "waves-effect waves-light btn green">Approved</a>
                                                                <?php if ($tender->technical_status == 1) {
                                                                    ?>
                                                                    <!--a class="waves-effect waves-light btn green">Technical</a-->

                                                                <?php } else { ?>
                                                                    <!--a class="waves-effect waves-light btn blue proj-delete">Technical</a-->
                                                                    <?php
                                                                }
                                                                if ($tender->financial_status == 1) {
                                                                    ?>

                                                                    <!--a class="waves-effect waves-light btn green">Financial</a-->

                                                                    <?php
                                                                } else {
                                                                    if ($tender->technical_status == 1) {
                                                                        ?>
                                                                        <!--a class="waves-effect waves-light btn blue proj-delete">Financial</a-->
                                                                    <?php }
                                                                    ?>

                                                                    <?php
                                                                }
                                                                if ($tender->aoc_status == 1) {
                                                                    ?>

                                                                    <a class="waves-effect waves-light btn green">AOC</a>

                                                                    <?php
                                                                } else {
                                                                    if ($tender->status == 1) {
                                                                        ?>

                                                                        <a class="waves-effect waves-light btn blue proj-delete">AOC</a>
                                                                        <?php
                                                                    }
                                                                }

                                                                if ($user->group_id == 1) {
                                                                    ?>

                                                                    <a onclick="pop_up('<?= Url::to(['site/create-tender', 'id' => $tender->id]) ?>');" class="waves-effect waves-light btn blue">Edit</a>
                                                                    <a href="#modal<?= $tender->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>
                                                                    <a href="<?= Url::to(['site/create-item', 'id' => $tender->id]) ?>" class="waves-effect waves-light btn blue">Add Item</a>
                                                                    <?php
                                                                }
                                                            } else {
                                                                ?>
                    <!--a class="waves-effect waves-light btn blue" onclick='approvetender(<?php echo $tender->id; ?>)'>Approve</a-->
                                                                <a class="waves-effect waves-light btn red">Unapproved</a>

                                                            <?php }
                                                            ?>

                                                        <?php }
                                                        ?>

                                                        <a href="<?= Url::to(['site/view-items', 'id' => $tender->id]) ?>" class="waves-effect waves-light btn blue">View Items</a>
                                                        <a href="#modalfiles<?= $tender->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">View Files</a>
                                                        <a href="#modalcont<?= $tender->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Contractor</a>
                                                        <?php if ($tender->is_archived != 1) { ?>
                                                            <a onclick="changehold(<?= $tender->id; ?>)" id="tenderhold<?= $tender->id; ?>"  class="waves-effect waves-light btn <?= $classaoc; ?>"><?= $text ?></a>
                                                        <?php } ?>
                                                        <?php if ($user->group_id != 3) { ?>    
                                                            <a onclick="movearchive(<?= $tender->id; ?>)" id="tenderarc<?= $tender->id; ?>" class="waves-effect waves-light btn blue proj-delete">Archive</a>    
            <?php } ?>

                                                    </td>

                                                </tr>
                                            <div id="modal<?= $tender->id; ?>" class="modal">
                                                <div class="modal-content">
                                                    <h4>Confirmation Message</h4>
                                                    <p>Are you sure you want to delete it ?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <a href="javascript::void()" class=" modal-action modal-close waves-effect waves-green btn-flat">No</a>
                                                    <a href="<?= Url::to(['site/delete-tender', 'id' => $tender->id]) ?>" class=" modal-action modal-close waves-effect waves-green btn-flat">Yes</a>
                                                </div>
                                            </div>


                                            <?php
                                            $i++;
                                        }
                                    }
                                    ?>
                                    </tbody>

                                </table>
                            </form>
                            <?php
                            if (@$tenders) {
                                foreach ($tenders as $key => $tender) {
                                    ?>
                                    <div id="modalfiles<?= $tender->id; ?>" class="modal">
            <?php ?>
                                        <div class="modal-content">
                                            <h4>View Tender Files</h4>

                                            <div class="row">
                                                    <?php $tech = \common\models\Tender::find()->where(['id' => $tender->id])->one(); ?>
                                                <div class="input-field col s6">
                                                    <?php if ($tech->tfile != '') { ?>
                                                        <a href="<?= $tender->tfile; ?>" download>Tender Files</a>
                                                    <?php } else { ?>
                                                        <a class="notavailable">No Tender files yet</a>
            <?php } ?>
                                                </div>
                                            </div>

                                            <h4>View BOQ Sheet</h4>
                                                <?php $finfiles = \common\models\Tenderfile::find()->where(['tender_id' => $tender->id, 'type' => 2])->orderBy(['id' => SORT_DESC])->all(); ?>
                                            <div class="row">
                                                <?php
                                                if (@$finfiles) {
                                                    $i = 0;
                                                    foreach ($finfiles as $ffiles) {
                                                        if ($i == 0) {
                                                            $txt = 'BOQ Comparitive List';
                                                            ?>
                                                            <div class="input-field col s6">
                                                                <a href="<?= $ffiles->file; ?>" download><?= $txt; ?></a>
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>

                                                        <?php
                                                        $i++;
                                                    }
                                                } else {
                                                    ?>
                                                    <div class="input-field col s6">
                                                        <a class="notavailable">No BOQ Sheet yet</a>
                                                    </div>
                                                <?php }
                                                ?>

                                            </div>
                                            <h4>View AOC Files</h4>
                                                <?php $aocfiles = \common\models\Tenderfile::find()->where(['tender_id' => $tender->id, 'type' => 3])->orderBy(['id' => SORT_ASC])->all(); ?>
                                            <div class="row">
                                                <?php
                                                if (@$aocfiles) {
                                                    $i = 0;
                                                    foreach ($aocfiles as $ffiles) {
                                                        if ($i == 0) {
                                                            $txt = 'AOC Summary';
                                                        } else {
                                                            $txt = 'Opening Summary';
                                                        }
                                                        ?>
                                                        <div class="input-field col s6">
                                                            <a href="<?= $ffiles->file; ?>" download><?= $txt; ?></a>
                                                        </div>
                                                        <?php
                                                        $i++;
                                                    }
                                                } else {
                                                    ?>
                                                    <div class="input-field col s6">
                                                        <a class="notavailable">No AOC files yet</a>
                                                    </div>
                                                <?php }
                                                ?>

                                            </div>
                                            <div class="row">
                                                <?php
                                                if (@$tech->aoc_date) {
                                                    ?>
                                                    <div class="input-field col s6">
                                                        AOC Date - <?= $tech->aoc_date ?>
                                                    </div>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <div class="input-field col s6">
                                                        <a class="notavailable">AOC Date not available</a>
                                                    </div>
                                                <?php }
                                                ?>

                                            </div>

                                        </div>

                                    </div>
                                    <div id="modalcont<?= $tender->id; ?>" class="modal">
            <?php $contractor = \common\models\Contractor::find()->where(['id' => $tender->contractor])->one(); ?>
                                        <div class="modal-content"> 
                                            <h5>Contractor Information</h5>

                                            <div class="row">

                                                <div class="col s6">
                                                    <h4>Firm Name</h4>
            <?= $contractor->firm; ?>
                                                </div>

                                                <div class="col s6">
                                                    <h4>Name</h4>
            <?= $contractor->name; ?>
                                                </div>
                                            </div>
                                            <div class="row">

                                                <div class="col s6">
                                                    <h4>Address</h4>
            <?= $contractor->address; ?>
                                                </div>

                                                <div class="col s6">
                                                    <h4>Contact No.</h4>
            <?= $contractor->contact; ?>
                                                </div>
                                            </div>
                                            <div class="row">

                                                <div class="col s6">
                                                    <h4>Email</h4>
            <?= $contractor->email; ?>
                                                </div>

                                            </div>


                                        </div>
                                    </div>


                                </div>

                            </div>

                        </div>

                        <?php
                    }
                }
            }
            ?>
        </div>
    </div>
</div>
</div>
</div>
</main>
<script>
    function pop_up(url) {
        window.open(url, 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=1076,height=768,directories=no,location=no')
    }
</script>