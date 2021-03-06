<?php
/* @var $this yii\web\View */

use backend\controllers\SiteController;
use yii\widgets\LinkPager;
use yii\helpers\Url;

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
    ::placeholder{color:#9e9e9e;}
</style>

<main class="mn-inner">
    <div class="row">
        <div class="col s6">
            <div class="page-title"><?= $type ?> Tenders of <?= @$commandname; ?></div>
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
                    <form id="sort-data" method = "post" action = "<?= $baseURL ?>site/<?= $url ?>">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                        <input type="hidden" name="page" value="<?= @$_GET['page'] ?>">
                        <input type="hidden" name="commandid" value="<?= @$_GET['c'] ?>">
                        <div class="col s2">
                            <select class="validate required" name="sort" onchange="submitform()" id="sort">
                                <option value="10" <?= (@$_GET['filter'] == 10) ? 'selected' : '' ?>>10</option>
                                <option value="25" <?= (@$_GET['filter'] == 25) ? 'selected' : '' ?>>25</option>
                                <option value="50" <?= (@$_GET['filter'] == 50) ? 'selected' : '' ?>>50</option>
                                <option value="100" <?= (@$_GET['filter'] == 100) ? 'selected' : '' ?>>100</option>
                            </select>
                        </div>
                    </form>
                    <table class="responsive-table bordered">
                        <thead>
                            <tr>
                                <th data-field="email" width="100px">Tender Id</th>
                                <th data-field="name">Details of Contracting Office</th>
                                <th data-field="email" width="120px">Cost of Tender</th>
                                <th data-field="email" width="100px">Bid end date</th>
                                <th data-field="email" width="100px">Bid open date</th>
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
                                    if ($tender->department == 1) {
                                        $command = Sitecontroller::actionGetcommand($tender->command);
                                        if (!isset($tender->cengineer) && isset($tender->gengineer)) {
                                            $cengineer = \common\models\Cengineer::find()->where(['cid' => $tender->gengineer, 'status' => 1])->one();
                                        } else {
                                            $cengineer = \common\models\Cengineer::find()->where(['cid' => $tender->cengineer, 'status' => 1])->one();
                                        }
                                        $cwengineer = \common\models\Cwengineer::find()->where(['cengineer' => $tender->cengineer, 'cid' => $tender->cwengineer, 'status' => 1])->one();
                                        $gengineer = \common\models\Gengineer::find()->where(['cwengineer' => $tender->cwengineer, 'gid' => $tender->gengineer, 'status' => 1])->one();
                                        $tdetails = @$command . ' ' . @$cengineer->text . ' ' . @$cwengineer->text . ' ' . @$gengineer->text;
                                    } else {
                                        $dname = \common\models\Departments::find()->where(['id' => $tender->department])->one();
                                        $tdetails = $dname->name;
                                    }
                                    if ($tender->status == 1 && $tender->is_archived == 1) {
                                        $status = 'Archived';
                                        $class = 'orange';
                                    } elseif ($tender->status == 1) {
                                        $status = 'Approved';
                                        $class = 'green';
                                    } else {
                                        $status = 'Unapproved';
                                        $class = 'red';
                                    }
                                    $contractor = \common\models\Contractor::find()->where(['id' => $tender->contractor])->one();
                                    if ($tender->on_hold == 1) {
                                        $classaoc = 'red';
                                        $text = 'On Hold';
                                    } else {
                                        $classaoc = 'green';
                                        $text = 'Ready';
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
                                            <?php
                                            if ($user->group_id == 9) {
                                                if ($stop_date >= date('Y-m-d H:i:s') && $tender->status == 0) {
                                                    ?>
                                                    <a href="<?= Url::to(['site/create-tender', 'id' => $tender->id]) ?>" class="waves-effect waves-light btn blue">Edit</a>
                                                    <a href="#modal<?= $tender->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>
                                                    <a href="<?= Url::to(['site/create-item', 'id' => $tender->id]) ?>" class="waves-effect waves-light btn blue">Add Item</a>

                                                    <?php
                                                }
                                            } else {
                                                if ($tender->status == 1) {
                                                    if ($tender->aoc_status == 1 && $tender->is_archived != 1) {
                                                        ?>

                                                        <a href="javascript:void(0);" class="waves-effect waves-light btn green">AOC</a>

                                                        <?php
                                                    } else {
                                                        if ($tender->status == 1 && $tender->is_archived != 1) {
                                                            ?>

                                                            <a href="#modalaoc<?= $tender->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">AOC</a>
                                                            <?php
                                                        }
                                                    }
                                                }

                                                if ($tender->status == 1) {
                                                    if ($user->group_id == 1) {
                                                        ?>
                                                        <a href="<?= Url::to(['site/create-tender', 'id' => $tender->id]) ?>" class="waves-effect waves-light btn blue">Edit</a>
                                                        <a href="#modal<?= $tender->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>
                                                        <a href="<?= Url::to(['site/create-item', 'id' => $tender->id]) ?>" class="waves-effect waves-light btn blue">Add Item</a>
                                                        <?php
                                                    }
                                                } else {
                                                    ?>
                                                    <a href="<?= Url::to(['site/create-tender', 'id' => $tender->id]) ?>" class="waves-effect waves-light btn blue">Edit</a>
                                                    <a href="#modal<?= $tender->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>
                                                    <a href="<?= Url::to(['site/create-item', 'id' => $tender->id]) ?>" class="waves-effect waves-light btn blue">Add Item</a>
                                                    <?php
                                                }
                                                ?>

                                            <?php }
                                            ?>

                                            <a href="<?= Url::to(['site/view-items', 'id' => $tender->id]) ?>" class="waves-effect waves-light btn blue">View Items</a>

                                            <a href="#modalfiles<?= $tender->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">View Files</a>
                                            <?php if ($contractor) { ?>
                                                <a href="#modalcont<?= $tender->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Contractor</a>
                                            <?php } ?>
                                            <?php if ($tender->is_archived != 1 && $contractor) {
                                                ?>
                                                <a onclick="changehold(<?= $tender->id; ?>)" id="tenderhold<?= $tender->id; ?>"  class="waves-effect waves-light btn <?= $classaoc; ?>"><?= $text ?></a>
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
                                        <a href="<?= Url::to(['site/delete-approve-tender', 'id' => $tender->id, 'command' => $tender->command, 'page' => @$_GET['page']]) ?>" class=" modal-action modal-close waves-effect waves-green btn-flat">Yes</a>
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
                            <?php $contractor = \common\models\Contractor::find()->where(['id' => $tender->contractor])->one(); ?>
                            <?php if ($contractor) { ?>        
                                <div id="modalcont<?= $tender->id; ?>" class="modal">

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
                                        <?php if ($tender->is_archived != 1) { ?>
                                            <div class="row">

                                                <div class="col s6">
                                                    <a target="_blank" class="waves-effect waves-light btn blue proj-delete" href="<?= Url::to(['contractor/add-contractor', 'id' => $contractor->id]) ?>">Edit</a>
                                                </div>

                                            </div>
                                        <?php } ?>



                                    </div>
                                </div>
                            <?php } ?>
                            <div id="modalaoc<?= $tender->id; ?>" class="modal">

                                <div class="modal-content">
                                    <form id="create-item" method = "post" enctype="multipart/form-data" action = "<?= $baseURL ?>site/aocapprovestatus">
                                        <h4>AOC Bid Opening Summary Upload</h4>
                                        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                                        <input type="hidden" name="tid" value="<?= $tender->id; ?>">
                                        <input type="hidden" name="command" value="<?= $tender->command; ?>">
                                        <input type="hidden" name="url" value="approvetenders">
                                        <input type="hidden" name="page" value="<?= @$_GET['page']; ?>">
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
                                        <h4>Opening Summary</h4>
                                        <div class="file-field input-field">
                                            <div class="btn teal lighten-1">
                                                <span>File</span>
                                                <input type="file" name="filethree" required="">
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
                                        <div class="row">
                                            <div class="input-field col s12 tender<?= $tender->id; ?>">
                                                <input id="pdate" type="text" name = "aoc_date" data-tid ="<?= $tender->id; ?>" class="pdatepicker required" placeholder="AOC Date">
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
