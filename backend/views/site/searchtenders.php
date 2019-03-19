<?php
/* @var $this yii\web\View */

use backend\controllers\SiteController;
use yii\widgets\LinkPager;

$this->title = 'Search Tenders';
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
<?php //$contractors = \common\models\Contractor::find()->orderBy(['firm' => SORT_ASC])->all();  ?>
<main class="mn-inner">
    <div class="row">
        <div class="col s6">
            <div class="page-title">Search Tenders</div>
        </div>
        <div class="col s12 m12 l12">
            <div class="card">
                <div class="card-content">
                    <div class="row">
                        <form id="getdatas" class="col s12" method = "post" action = "<?= $baseURL ?>site/searchtenders" enctype="multipart/form-data">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />

                            <div class="input-field col s12">
                                <select name='authtype' id="authtype" class="contact-authtypes browser-default" onchange="showdivs(this.value)" required="">
                                    <option value=''>Select Product</option>
                                    <option value='1' <?= (@$_POST['authtype'] == 1) ? 'selected' : '' ?>>Cables</option>
                                    <option value='2' <?= (@$_POST['authtype'] == 2) ? 'selected' : '' ?>>Lighting</option>
                                    <option value='3' <?= (@$_POST['authtype'] == 3) ? 'selected' : '' ?>>Wires</option>
                                    <option value='4' <?= (@$_POST['authtype'] == 4) ? 'selected' : '' ?>>Cement</option>
                                    <option value='5' <?= (@$_POST['authtype'] == 5) ? 'selected' : '' ?>>Reinforcement Steel</option>
                                    <option value='6' <?= (@$_POST['authtype'] == 6) ? 'selected' : '' ?>>Structural Steel</option>
                                    <option value='7' <?= (@$_POST['authtype'] == 7) ? 'selected' : '' ?>>Non Structural Steel</option>
                                </select>
                            </div>
                            <div class="input-field col s12" id="cablesdiv" <?= (@$_POST['authtype'] == 1) ? '' : 'style="display: none;"' ?> >
                                <select name='cables' class="cmakes browser-default" id="cables">
                                    <option value="">Select</option>
                                    <?php
                                    if (@$cables) {
                                        foreach ($cables as $cable_) {
                                            ?>
                                            <option value="<?= $cable_->id ?>" <?= (@$_POST['cables'] == $cable_->id) ? 'selected' : '' ?>><?= $cable_->make ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="input-field col s12" id="lightdiv" <?= (@$_POST['authtype'] == 2) ? '' : 'style="display: none;"' ?>>
                                <select name='lighting' class="lmakes browser-default" id="lighting">
                                    <option value="">Select</option>
                                    <?php
                                    if (@$lights) {
                                        foreach ($lights as $light_) {
                                            ?>
                                            <option value="<?= $light_->id ?>" <?= (@$_POST['lighting'] == $light_->id) ? 'selected' : '' ?>><?= $light_->make ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="input-field col s12" id="wiresdiv" <?= (@$_POST['authtype'] == 3) ? '' : 'style="display: none;"' ?>>
                                <select name='wires' class="wmakes browser-default" id="wires">
                                    <option value="">Select</option>
                                    <?php
                                    if (@$wires) {
                                        foreach ($wires as $wire_) {
                                            ?>
                                            <option value="<?= $wire_->id ?>" <?= (@$_POST['wires'] == $wire_->id) ? 'selected' : '' ?>><?= $wire_->make ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="input-field col s12" id="cementdiv" <?= (@$_POST['authtype'] == 4) ? '' : 'style="display: none;"' ?>>
                                <select name='cement' class="cementmakes browser-default" id="cement">
                                    <option value="">Select</option>
                                    <?php
                                    if (@$cements) {
                                        foreach ($cements as $cement_) {
                                            ?>
                                            <option value="<?= $cement_->id ?>" <?= (@$_POST['cement'] == $cement_->id) ? 'selected' : '' ?>><?= $cement_->make ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="input-field col s12" id="rsteeldiv" <?= (@$_POST['authtype'] == 5) ? '' : 'style="display: none;"' ?>>
                                <select name='rsteel' class="rmakes browser-default" id="rsteel">
                                    <option value="">Select</option>
                                    <?php
                                    if (@$rsteel) {
                                        foreach ($rsteel as $rsteel_) {
                                            ?>
                                            <option value="<?= $rsteel_->id ?>" <?= (@$_POST['rsteel'] == $rsteel_->id) ? 'selected' : '' ?>><?= $rsteel_->make ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="input-field col s12" id="ssteeldiv" <?= (@$_POST['authtype'] == 6) ? '' : 'style="display: none;"' ?>>
                                <select name='ssteel' class="smakes browser-default" id="ssteel">
                                    <option value="">Select</option>
                                    <?php
                                    if (@$ssteel) {
                                        foreach ($ssteel as $ssteel_) {
                                            ?>
                                            <option value="<?= $ssteel_->id ?>" <?= (@$_POST['ssteel'] == $ssteel_->id) ? 'selected' : '' ?>><?= $ssteel_->make ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="input-field col s12" id="nsteeldiv" <?= (@$_POST['authtype'] == 7) ? '' : 'style="display: none;"' ?>>
                                <select name='nsteel' class="nmakes browser-default" id="nsteel">
                                    <option value="">Select</option>
                                    <?php
                                    if (@$nsteel) {
                                        foreach ($nsteel as $nsteel_) {
                                            ?>
                                            <option value="<?= $nsteel_->id ?>" <?= (@$_POST['nsteel'] == $nsteel_->id) ? 'selected' : '' ?>><?= $nsteel_->make ?></option>
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
                                <button  id="download" type="submit" name="download"  class="waves-effect waves-light btn blue m-b-xs">Submit</button>
                            </div>

                        </form>
                    </div></div></div></div>
        <?php if (isset($tenders) && count($tenders)) { ?>


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

                        <table id = "current-project" class="responsive-table">
                            <thead>
                                <tr>
                                    <th data-field="email">Tender Id</th>
                                    <th data-field="name">Details of Contracting Office</th>
                                    <th data-field="email">Cost of Tender</th>
                                    <th data-field="email">Bid end date</th>
                                    <th data-field="email">Bid open date</th>
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
                                        if (!isset($tender->cengineer) && isset($tender->gengineer)) {
                                            $cengineer = \common\models\Cengineer::find()->where(['cid' => $tender->gengineer, 'status' => 1])->one();
                                        } else {
                                            $cengineer = \common\models\Cengineer::find()->where(['cid' => $tender->cengineer, 'status' => 1])->one();
                                        }
                                        $cwengineer = \common\models\Cwengineer::find()->where(['cid' => $tender->cwengineer, 'status' => 1])->one();
                                        $gengineer = \common\models\Gengineer::find()->where(['gid' => $tender->gengineer, 'status' => 1])->one();
                                        $tdetails = @$command . ' ' . @$cengineer->text . ' ' . @$cwengineer->text . ' ' . @$gengineer->text;
                                        if ($tender->status == 1) {
                                            $status = 'Approved';
                                            $class = 'green';
                                        } else {
                                            $status = 'Unapproved';
                                            $class = 'red';
                                        }
                                        $stop_date = date('Y-m-d H:i:s', strtotime($tender->createdon . ' +1 day'));
                                        ?>
                                        <tr data-id = "<?= $tender->tender_id ?>">
                                            <td class = ""><?= $tender->tender_id ?></td>
                                            <td class = ""><?= $tdetails ?></td>
                                            <td class = ""><?= $tender->cvalue ?></td>
                                            <td class = ""><?= $tender->bid_end_date ?></td>
                                            <td class = ""><?= $tender->bid_opening_date ?></td>
                                            <td ><a class = "btn <?= $class ?>"><?= $status ?></a></td>
                                            <td>

                                                <?php if ($tender->aoc_status == 1 && $tender->is_archived != 1) {
                                                    ?>

                                                    <a class="waves-effect waves-light btn green">AOC</a>

                                                    <?php
                                                } else {
                                                    if ($tender->status == 1 && $tender->is_archived != 1) {
                                                        ?>

                                                        <a class="waves-effect waves-light btn blue proj-delete">AOC</a>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                                <a href="<?= $baseURL ?>site/view-items?id=<?= $tender->id; ?>" class="waves-effect waves-light btn blue">View Items</a>

                                                <a href="#modalfiles<?= $tender->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">View Files</a>

                                            </td>

                                        </tr>

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

                                    <?php
                                    $i++;
                                }
                            }
                            ?>
                            </tbody>

                        </table>

                    </div>
                </div>
            </div>
<?php } ?>
    </div>
</main>
<script>
    function showform(id) {
        $('.contractform' + id + '').toggle(function () {
            var $this = $(this);
            if ($this.is(":visible")) {
                $('.cont' + id + '').removeAttr('required')
                $("#firm" + id + "").attr('required', 'true');
                $("#name" + id + "").attr('required', 'true');
                $("#address" + id + "").attr('required', 'true');
                $("#contact" + id + "").attr('required', 'true');
                $("#email" + id + "").attr('required', 'true');
            } else {
                $('.cont' + id + '').attr('required', 'true')
                $("#firm" + id + "").removeAttr('required');
                $("#name" + id + "").removeAttr('required');
                $("#address" + id + "").removeAttr('required');
                $("#contact" + id + "").removeAttr('required');
                $("#email" + id + "").removeAttr('required');
            }
        });
    }

    function pop_up(url) {
        window.open(url, 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=1076,height=768,directories=no,location=no')
    }

</script>