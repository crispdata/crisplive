<?php
/* @var $this yii\web\View */

use backend\controllers\SiteController;
use yii\widgets\LinkPager;

$this->title = $type . ' Tenders';
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
            <div class="page-title"><?= $type ?> Tenders of <?= @$commandname; ?></div>
        </div>
        <form id="sort-data" method = "post" action = "<?= $baseURL ?>site/<?= $url ?>">
            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
            <input type="hidden" name="page" value="<?= @$_GET['page'] ?>">
            <input type="hidden" name="commandid" value="<?= @$_REQUEST['c'] ?>">
            <div class="col s2">
                <select class="validate required" name="sort" onchange="submitform()" id="sort">
                    <option value="10" <?= (@$_GET['filter'] == 10) ? 'selected' : '' ?>>10</option>
                    <option value="25" <?= (@$_GET['filter'] == 25) ? 'selected' : '' ?>>25</option>
                    <option value="50" <?= (@$_GET['filter'] == 50) ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= (@$_GET['filter'] == 100) ? 'selected' : '' ?>>100</option>
                </select>
            </div>
        </form>


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
                <div class="card-content card-tenders">
                    <form id="command-types" class="col s12" method = "post" action = "<?= $baseURL ?>site/changecommand">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                        <label>Change Command</label>
                        <select class="validate required materialSelect" id="commanddropdown" name='c'>
                            <option value="" selected>Change Command</option>
                            <option value="1" <?php
                            if (@$_REQUEST['c'] == 1) {
                                echo "selected";
                            }
                            ?>>ADG (CG AND PROJECT) CHENNAI AND CE (CG) GOA - MES</option>
                            <option value="2" <?php
                            if (@$_REQUEST['c'] == 2) {
                                echo "selected";
                            }
                            ?>>ADG (DESIGN and CONSULTANCY) PUNE - MES</option>
                            <option value="3" <?php
                            if (@$_REQUEST['c'] == 3) {
                                echo "selected";
                            }
                            ?>>ADG (OF and DRDO) AND CE (FY) HYDERABAD - MES</option>
                            <option value="4" <?php
                            if (@$_REQUEST['c'] == 4) {
                                echo "selected";
                            }
                            ?>>ADG (OF and DRDO)  AND CE (R and D) DELHI-  MES</option>
                            <option value="5" <?php
                            if (@$_REQUEST['c'] == 5) {
                                echo "selected";
                            }
                            ?>>ADG (OF and DRDO) AND CE (R and D) SECUNDERABAD - MES</option>
                            <option value="6" <?php
                            if (@$_REQUEST['c'] == 6) {
                                echo "selected";
                            }
                            ?>>CENTRAL COMMAND</option>
                            <option value="7" <?php
                            if (@$_REQUEST['c'] == 7) {
                                echo "selected";
                            }
                            ?>>EASTERN COMMAND</option>
                            <option value="8" <?php
                            if (@$_REQUEST['c'] == 8) {
                                echo "selected";
                            }
                            ?>>NORTHERN COMMAND</option>
                            <option value="9" <?php
                            if (@$_REQUEST['c'] == 9) {
                                echo "selected";
                            }
                            ?>>SOUTHERN COMMAND</option>
                            <option value="10" <?php
                            if (@$_REQUEST['c'] == 10) {
                                echo "selected";
                            }
                            ?>>SOUTH WESTERN COMMAND</option>
                            <option value="11" <?php
                            if (@$_REQUEST['c'] == 11) {
                                echo "selected";
                            }
                            ?>>WESTERN COMMAND</option>
                            <option value="12" <?php
                            if (@$_REQUEST['c'] == 12) {
                                echo "selected";
                            }
                            ?>>DGNP MUMBAI - MES</option>
                        </select>
                    </form>
                    <table id = "current-project-tenders" class="responsive-table">
                        <thead>
                            <tr>
                                <th data-field="name">Command</th>
                                <th data-field="name">CE</th>
                                <th data-field="name">CWE</th>
                                <th data-field="email">Tender Id</th>
                                <th data-field="email">Tender Reference No</th>
                                <th data-field="email">Bid end date</th>
                                <th data-field="email">Status</th>
                                <th data-field="email">Actions</th>

                            </tr>
                        </thead>
                        <tbody id="contacts_list">
                            <?php
                            if (@$tenders) {
                                $i = 0;
                                foreach ($tenders as $key => $tender) {
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
                                        <td class = ""><?= SiteController::actionGetcommand($tender->command); ?></td>
                                        <td class = ""><?= SiteController::actionGetcebyid($tender->cengineer); ?></td>
                                        <td class = ""><?= SiteController::actionGetcwebyid($tender->cwengineer); ?></td>
                                        <td class = ""><?= $tender->tender_id ?></td>
                                        <td class = ""><?= $tender->reference_no ?></td>
                                        <td class = ""><?= $tender->bid_end_date ?></td>
                                        <td ><a class = "btn <?= $class ?>"><?= $status ?></a></td>
                                        <td>
                                            <?php
                                            if ($user->group_id == 9) {
                                                if ($stop_date >= date('Y-m-d H:i:s') && $tender->status == 0) {
                                                    ?>
                                                    <a href="<?= $baseURL ?>site/create-tender?id=<?= $tender->id; ?>" class="waves-effect waves-light btn blue">Edit</a>
                                                    <a href="#modal<?= $tender->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>
                                                    <a href="<?= $baseURL ?>site/create-item?id=<?= $tender->id; ?>" class="waves-effect waves-light btn blue">Add Item</a>

                                                    <?php
                                                }
                                            } else {
                                                if ($tender->status == 1) {
                                                    ?>
                                                    <a class = "waves-effect waves-light btn green">Approved</a>
                                                    <?php if ($tender->technical_status == 1) {
                                                        ?>
                                                        <a href="javascript:void(0);" class="waves-effect waves-light btn green">Technical</a>

                                                    <?php } else { ?>
                                                        <a href="#modaltech<?= $tender->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Technical</a>
                                                        <?php
                                                    }
                                                    if ($tender->financial_status == 1) {
                                                        ?>

                                                        <a href="javascript:void(0);" class="waves-effect waves-light btn green">Financial</a>

                                                        <?php
                                                    } else {
                                                        if ($tender->technical_status == 1) {
                                                            ?>
                                                            <a href="#modalfin<?= $tender->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Financial</a>
                                                        <?php }
                                                        ?>

                                                        <?php
                                                    }
                                                    if ($tender->aoc_status == 1) {
                                                        ?>

                                                        <a href="javascript:void(0);" class="waves-effect waves-light btn green">AOC</a>

                                                        <?php
                                                    } else {
                                                        if ($tender->financial_status == 1) {
                                                            ?>

                                                            <a href="<?= $baseURL ?>site/aocstatus?id=<?= $tender->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">AOC</a>
                                                            <?php
                                                        }
                                                    }

                                                    if ($user->group_id == 1) {
                                                        ?>
                                                        <a href="<?= $baseURL ?>site/create-tender?id=<?= $tender->id; ?>" class="waves-effect waves-light btn blue">Edit</a>
                                                        <a href="#modal<?= $tender->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>
                                                        <a href="<?= $baseURL ?>site/create-item?id=<?= $tender->id; ?>" class="waves-effect waves-light btn blue">Add Item</a>
                                                        <?php
                                                    }
                                                } else {
                                                    ?>
                                                    <a class="waves-effect waves-light btn red">Unapproved</a>
                                                    <a href="<?= $baseURL ?>site/create-tender?id=<?= $tender->id; ?>" class="waves-effect waves-light btn blue">Edit</a>
                                                    <a href="#modal<?= $tender->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>
                                                    <a href="<?= $baseURL ?>site/create-item?id=<?= $tender->id; ?>" class="waves-effect waves-light btn blue">Add Item</a>
                                                <?php }
                                                ?>

                                            <?php }
                                            ?>

                                            <a href="<?= $baseURL ?>site/view-items?id=<?= $tender->id; ?>" class="waves-effect waves-light btn blue">View Items</a>

                                            <a href="#modalfiles<?= $tender->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">View Files</a>


                                        </td>

                                    </tr>
                                <div id="modal<?= $tender->id; ?>" class="modal">
                                    <div class="modal-content">
                                        <h4>Confirmation Message</h4>
                                        <p>Are you sure you want to delete it ?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="javascript::void()" class=" modal-action modal-close waves-effect waves-green btn-flat">No</a>
                                        <a href="<?= $baseURL ?>site/delete-tender?id=<?= @$tender->id; ?>" class=" modal-action modal-close waves-effect waves-green btn-flat">Yes</a>
                                    </div>
                                </div>

                                <?php
                                $i++;
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                     <span class="totaltenders"><?= $type ?> Tenders: <?= $total; ?></span>
                    <?php
                    echo LinkPager::widget([
                        'pagination' => $pages,
                    ]);
                    ?>
                    <?php
                    if (@$tenders) {
                        foreach ($tenders as $key => $tender) {
                            ?>
                            <div id="modaltech<?= $tender->id; ?>" class="modal">

                                <div class="modal-content">
                                    <h4>Technical Bid Opening Summary Upload</h4>
                                    <form id="create-item" method = "post" enctype="multipart/form-data" action = "<?= $baseURL ?>site/technicalstatus">
                                        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                                        <input type="hidden" name="tid" value="<?= $tender->id; ?>">
                                        <div class="file-field input-field">
                                            <div class="btn teal lighten-1">
                                                <span>File</span>
                                                <input type="file" name="filetoupload" required="">
                                            </div>
                                            <div class="file-path-wrapper">
                                                <input class="file-path validate" type="text">
                                            </div>
                                        </div>
                                        <input type="submit" name="submit" class="waves-effect waves-light btn blue" value="Submit">
                                    </form>

                                </div>



                            </div>
                            <div id="modalfin<?= $tender->id; ?>" class="modal">

                                <div class="modal-content">
                                    <form id="create-item" method = "post" enctype="multipart/form-data" action = "<?= $baseURL ?>site/financialstatus">
                                        <h4>Financial Bid Opening Summary Upload</h4>
                                        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                                        <input type="hidden" name="tid" value="<?= $tender->id; ?>">
                                        <div class="file-field input-field">
                                            <div class="btn teal lighten-1">
                                                <span>File</span>
                                                <input type="file" name="fileone" required="">
                                            </div>
                                            <div class="file-path-wrapper">
                                                <input class="file-path validate" type="text">
                                            </div>
                                        </div>
                                        <h4>BOQ Comparative Summary</h4>
                                        <div class="file-field input-field">
                                            <div class="btn teal lighten-1">
                                                <span>File</span>
                                                <input type="file" name="filetwo" required="">
                                            </div>
                                            <div class="file-path-wrapper">
                                                <input class="file-path validate" type="text">
                                            </div>
                                        </div>
                                        <div class="input-field col s12">
                                            <input id="quotedvalue" type="text" name = "qvalue" required="" class="validate required" value="">
                                            <label for="quotedvalue">Quoted Value</label>
                                            <!--textarea id="item" name="desc" class="materialize-textarea"></textarea>
                                            <label for="item">Item description</label-->
                                        </div>
                                        <input type="submit" name="submit" class="waves-effect waves-light btn blue" value="Submit">
                                    </form>

                                </div>



                            </div>

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
                                    <h4>View Technical Files</h4>
                                    <?php $techfiles = \common\models\Tenderfile::find()->where(['tender_id' => $tender->id, 'type' => 1])->orderBy(['id' => SORT_ASC])->all(); ?>
                                    <div class="row">
                                        <?php
                                        if (@$techfiles) {
                                            foreach ($techfiles as $tfiles) {
                                                ?>
                                                <div class="input-field col s6">
                                                    <a href="<?= $tfiles->file; ?>" download>Technical Summary</a>
                                                </div>
                                                <?php
                                            }
                                        } else {
                                            ?>
                                            <div class="input-field col s6">
                                                <a class="notavailable">No Technical files yet</a>
                                            </div>
                                        <?php }
                                        ?>

                                    </div>
                                    <h4>View Financial Files</h4>
                                    <?php $finfiles = \common\models\Tenderfile::find()->where(['tender_id' => $tender->id, 'type' => 2])->orderBy(['id' => SORT_ASC])->all(); ?>
                                    <div class="row">
                                        <?php
                                        if (@$finfiles) {
                                            $i = 0;
                                            foreach ($finfiles as $ffiles) {
                                                if ($i == 0) {
                                                    $txt = 'Financial Summary';
                                                } else {
                                                    $txt = 'BOQ Comparitive List';
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
                                                <a class="notavailable">No Financial files yet</a>
                                            </div>
                                        <?php }
                                        ?>

                                    </div>
                                    <h4>View AOC Files</h4>
                                    <?php $aocfiles = \common\models\Tenderfile::find()->where(['tender_id' => $tender->id, 'type' => 3])->orderBy(['id' => SORT_ASC])->all(); ?>
                                    <div class="row">
                                        <?php
                                        if (@$aocfiles) {

                                            foreach ($aocfiles as $afiles) {
                                                ?>
                                                <div class="input-field col s6">
                                                    <a href="<?= $afiles->file; ?>" download>AOC Summary</a>
                                                </div>
                                                <?php
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
                                        if (@$aocfiles) {

                                            foreach ($aocfiles as $afiles) {
                                                ?>
                                                <div class="input-field col s6">
                                                    AOC Date - <?= $afiles->aoc_date ?>
                                                </div>
                                                <?php
                                            }
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
                            <div id="modalaoc<?= $tender->id; ?>" class="modal">

                                <div class="modal-content">
                                    <form id="create-item" method = "post" enctype="multipart/form-data" action = "<?= $baseURL ?>site/aocstatus">
                                        <h4>AOC Bid Opening Summary Upload</h4>
                                        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                                        <input type="hidden" name="tid" value="<?= $tender->id; ?>">
                                        <div class="file-field input-field">
                                            <div class="btn teal lighten-1">
                                                <span>File</span>
                                                <input type="file" name="fileone" required="">
                                            </div>
                                            <div class="file-path-wrapper">
                                                <input class="file-path validate" type="text">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="input-field col s12">
                                                <input id="pdate" type="text" name = "aoc_date" class="pdatepicker required">
                                                <label for="pdate">AOC Date</label>
                                            </div>
                                        </div>
                                        <div class="input-field col s12 row">
                                            <select class="validate required materialSelectcontractor browser-default cont<?= $tender->id; ?>" required="" name="contractor" id="contractor">
                                                <option value="">Select Contractor</option>
                                                <?php
                                                if ($contractors) {
                                                    foreach ($contractors as $contract) {
                                                        ?>
                                                        <option value="<?= $contract->id; ?>"><?= $contract->firm; ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                            </select>
                                        </div>
                                        <a class="waves-effect waves-light btn blue" onclick="showform('<?= $tender->id; ?>')">Add Contractor</a>
                                        <div class="row contractform<?= $tender->id; ?>" style="display: none;">
                                            <div class="row">
                                                <div class="input-field col s6">
                                                    <input id="firm<?= $tender->id; ?>" type="text" name = "firm" class="validate required firm" value="<?= @$contractor->firm; ?>">
                                                    <label for="firm">Name of Firm/CO</label>
                                                </div>



                                                <div class="input-field col s6">
                                                    <input id="name<?= $tender->id; ?>" type="text" name = "name" class="validate required name" value="<?= @$contractor->name; ?>">
                                                    <label for="name">Name</label>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="input-field col s6">
                                                    <textarea id="address<?= $tender->id; ?>" name="address" class="materialize-textarea required address"><?= @$contractor->address; ?></textarea>
                                                    <label for="address">Address</label>
                                                </div>



                                                <div class="input-field col s6">
                                                    <input id="contact<?= $tender->id; ?>" type="text" name = "contact" class="validate required contact" value="<?= @$contractor->contact; ?>">
                                                    <label for="contact">Contact No.</label>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="input-field col s6">
                                                    <input id="email<?= $tender->id; ?>" type="email" name = "email" class="validate required email" value="<?= @$contractor->email; ?>">
                                                    <label for="email">Email-Id</label>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="submit" name="submit" class="waves-effect waves-light btn blue" value="Submit">
                                    </form>

                                </div>



                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</main>