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
    .rateboxes {
        float: left;
        width: 90%;
    }
    input.rates{width:100%;margin:0;}
    .itemid.itemtenders {
        /* line-height: 70px; */
        height:3rem;
    }
    .conts img {
        width: 35px;
    }
    .srnos {
        background-color: #E4E4E4;
        border-radius: 15px;
        padding: 10px;
        margin-bottom: 5px;
        width: 100%;
        text-align: center;
        float: left;
    }
    .boximg .itemid {
        height: 3REM;
        margin-bottom: 5PX;
    }
    .boximg img{float: left; width:50px; margin:70px;}
    .deletebox img {
        float: left;
        width: 30px;
        margin-left: 75px;
        margin-top: 4px;
    }
    .input-field.col.s1.itemnos {
        float: left;
        width: 7%;
        margin-bottom:25px;
    }
    .ratespopup {
        float: left;
        width: 95%;
    }
    .input-field.col.s3.conts {
        float: left;
        width: 18%;
        margin-bottom: 25px;
        margin-left: 25px;
        text-align: center;
    }
    .deletebox {
        float: left;
        width: 100%;
        margin-top: 25px;
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
    .modal .modal-content h4{margin-bottom: 0px;color:#00ACC1;}
    .modal .modal-content h5{text-align: center;}

</style>
<?php //$contractors = \common\models\Contractor::find()->orderBy(['firm' => SORT_ASC])->all();   ?>
<main class="mn-inner">
    <div class="row">
        <div class="col s6">
            <div class="page-title"><?= $type ?> Tenders</div>
        </div>
        <?php if ($user->group_id == 6) { ?>
            <form id="command-types" class="col s12" method = "post" action = "<?= $baseURL ?>site/changecommand">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                <label>Change Command</label>
                <select class="validate required materialSelect" id="commanddropdown" name='c'>
                    <option value="" selected>Change Command</option>
                    <option value="1" <?php
                    if (@$_GET['c'] == 1) {
                        echo "selected";
                    }
                    ?>>ADG (CG AND PROJECT) CHENNAI AND CE (CG) GOA - MES</option>
                    <option value="2" <?php
                    if (@$_GET['c'] == 2) {
                        echo "selected";
                    }
                    ?>>ADG (DESIGN and CONSULTANCY) PUNE - MES</option>
                    <option value="3" <?php
                    if (@$_GET['c'] == 3) {
                        echo "selected";
                    }
                    ?>>ADG (OF and DRDO) AND CE (FY) HYDERABAD - MES</option>
                    <option value="4" <?php
                    if (@$_GET['c'] == 4) {
                        echo "selected";
                    }
                    ?>>ADG (OF and DRDO)  AND CE (R and D) DELHI-  MES</option>
                    <option value="5" <?php
                    if (@$_GET['c'] == 5) {
                        echo "selected";
                    }
                    ?>>ADG (OF and DRDO) AND CE (R and D) SECUNDERABAD - MES</option>
                    <option value="13" <?php
                    if (@$_GET['c'] == 13) {
                        echo "selected";
                    }
                    ?>>ADG (Projects) AND CE (CG) Visakhapatnam - MES</option>
                    <option value="6" <?php
                    if (@$_GET['c'] == 6) {
                        echo "selected";
                    }
                    ?>>CENTRAL COMMAND</option>
                    <option value="7" <?php
                    if (@$_GET['c'] == 7) {
                        echo "selected";
                    }
                    ?>>EASTERN COMMAND</option>
                    <option value="8" <?php
                    if (@$_GET['c'] == 8) {
                        echo "selected";
                    }
                    ?>>NORTHERN COMMAND</option>
                    <option value="9" <?php
                    if (@$_GET['c'] == 9) {
                        echo "selected";
                    }
                    ?>>SOUTHERN COMMAND</option>
                    <option value="10" <?php
                    if (@$_GET['c'] == 10) {
                        echo "selected";
                    }
                    ?>>SOUTH WESTERN COMMAND</option>
                    <option value="11" <?php
                    if (@$_GET['c'] == 11) {
                        echo "selected";
                    }
                    ?>>WESTERN COMMAND</option>
                    <option value="12" <?php
                    if (@$_GET['c'] == 12) {
                        echo "selected";
                    }
                    ?>>DGNP MUMBAI - MES</option>
                </select>
            </form>
        <?php }
        ?>
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
        <form id="create-item" method = "post" novalidate onsubmit="return deleteConfirm();" action = "<?= $baseURL ?>site/delete-tenders">
            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
            <?php if ($user->group_id == 1) { ?>
                <input type="submit" class="waves-effect waves-light btn red m-b-xs add-contact" name="btn_delete" value="Delete Tenders"/>
            <?php } ?>
            <?php if ($user->group_id != 4 && $user->group_id != 5 && $user->group_id != 6) { ?>
                <a class="waves-effect waves-light btn blue m-b-xs add-contact" href="<?= $baseURL ?>site/create-tender"> Add Tender</a>
            <?php } ?>
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

                        <table class="responsive-table bordered">
                            <thead>
                                <tr>
                                    <?php if ($user->group_id != 4 && $user->group_id != 5 && $user->group_id != 6) { ?><th><input type="checkbox" name="check_all" <?= ($user->group_id != 1) ? 'disabled' : '' ?> id="check_all" value=""/><label for="check_all"></label></th><?php } ?>
                                    <th data-field="email">Tender Id</th>
                                    <th data-field="name">Details of Contracting Office</th>
                                    <th data-field="email">Awarded Amount</th>
                                    <th data-field="email" width="100px">AOC Date</th>
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
                                        $cwengineer = \common\models\Cwengineer::find()->where(['cengineer' => $tender->cengineer, 'cid' => $tender->cwengineer, 'status' => 1])->one();
                                        $gengineer = \common\models\Gengineer::find()->where(['cwengineer' => $tender->cwengineer, 'gid' => $tender->gengineer, 'status' => 1])->one();
                                        $tdetails = @$command . ' ' . @$cengineer->text . ' ' . @$cwengineer->text . ' ' . @$gengineer->text;
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
                                            <?php if ($user->group_id != 4 && $user->group_id != 5 && $user->group_id != 6) { ?><td align="center"><input type="checkbox" name="selected_id[]" <?= ($tender->status == 1 && $user->group_id != 1) ? 'disabled' : '' ?> class="checkbox" id="check<?php echo $tender->id; ?>" value="<?php echo $tender->id; ?>"/><label for="check<?php echo $tender->id; ?>"></label></td><?php } ?> 
                                            <td class = ""><?= $tender->tender_id ?></td>
                                            <td class = ""><?= $tdetails ?></td>
                                            <td class = ""><?= $tender->qvalue ?></td>
                                            <td class = ""><span style="display:none;"><?= @$contractor->firm ?></span><?= $tender->aoc_date ?></td>
                                            <td ><a class = "btn <?= $class ?>"><?= $status ?></a></td>
                                            <td>
                                                <?php
                                                if ($tender->status == 1) {
                                                    if ($tender->technical_status == 1) {
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
                                                    if ($tender->aoc_status == 1 && $tender->is_archived != 1) {
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

                                                    if ($user->group_id == 1) {
                                                        ?>

                                                        <a onclick="pop_up('<?= Url::to(['site/create-tender', 'id' => $tender->id]) ?>');" class="waves-effect waves-light btn blue">Edit</a>
                                                        <a href="#modal<?= $tender->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>
                                                        <a href="<?= Url::to(['site/create-item', 'id' => $tender->id]) ?>" class="waves-effect waves-light btn blue">Add Item</a>
                                                        <a href="#modalrate<?= $tender->id; ?>" class="waves-effect waves-light btn pink modal-trigger">Rates</a>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                                <a href="<?= Url::to(['site/view-items', 'id' => $tender->id]) ?>" class="waves-effect waves-light btn blue">View Items</a>
                                                <a href="#modalfiles<?= $tender->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">View Files</a>
                                                <a href="#modalcont<?= $tender->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Contractor</a>
                                                <a href="<?= Url::to(['mail/create-excel-items', 'id' => $tender->id]) ?>" class="waves-effect waves-light btn m-b-xs">Download Items in Excel</a>
                                                <?php if ($tender->is_archived != 1 && $user->group_id != 6) { ?>
                                                    <a onclick="changehold(<?= $tender->id; ?>)" id="tenderhold<?= $tender->id; ?>"  class="waves-effect waves-light btn <?= $classaoc; ?>"><?= $text ?></a>
                                                <?php } ?>
                                                <?php if ($user->group_id != 3 && $tender->is_archived != 1) { ?>    
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
                                            <a href="<?= Url::to(['site/delete-tender', 'id' => $tender->id, 'url' => $url]) ?>" class=" modal-action modal-close waves-effect waves-green btn-flat">Yes</a>
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
                                                <?= @$contractor->firm; ?>
                                            </div>

                                            <div class="col s6">
                                                <h4>Name</h4>
                                                <?= @$contractor->name; ?>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col s6">
                                                <h4>Address</h4>
                                                <?= @$contractor->address; ?>
                                            </div>

                                            <div class="col s6">
                                                <h4>Contact No.</h4>
                                                <?= @$contractor->contact; ?>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col s6">
                                                <h4>Email</h4>
                                                <?= @$contractor->email; ?>
                                            </div>

                                        </div>
                                        <?php if ($tender->is_archived != 1 & $user->group_id != 6) { ?>
                                            <div class="row">

                                                <div class="col s6">
                                                    <a target="_blank" class="waves-effect waves-light btn blue proj-delete" href="<?= Url::to(['contractor/add-contractor', 'id' => $contractor->id]) ?>">Edit</a>
                                                </div>

                                            </div>
                                        <?php } ?>



                                    </div>
                                </div>
                                <div id="modalrate<?= $tender->id; ?>" class="modal ratespopup">
                                    <div class="modal-content"> 
                                        <h5>Rates By Contractors</h5>
                                        <h5>Tender Id - <?= $tender->tender_id ?></h5>
                                        <a class="waves-effect waves-light btn blue m-b-xs" id="addrate<?= $tender->id ?>" onclick="addcontractor('0', '<?= $tender->id ?>')" >Add Contractor</a>
                                        <div id="allrates<?= $tender->id ?>" class="allrates">  
                                            <div class='input-field col s1 itemnos'>
                                                <div class="itemid itemtenders">
                                                    Item Sr Nos.
                                                </div>
                                                <?php
                                                $idetails = \common\models\ItemDetails::find()->leftJoin('items', 'itemdetails.item_id = items.id')->where(['items.tender_id' => $tender->id, 'items.tendertwo' => 1])->orderBy(['itemdetails.id' => SORT_ASC])->all();
                                                $rates = \common\models\Itemrates::find()->where(['tid' => $tender->id])->orderBy(['id' => SORT_ASC])->groupBy(['contractor'])->all();
                                                if (isset($idetails) && count($idetails)) {
                                                    foreach ($idetails as $_item) {
                                                        ?>
                                                        <div class="itemid itemtenders srnos">
                                                            <?= $_item->itemtender; ?>
                                                        </div>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </div> 
                                            <div class="rateboxes" id="rateboxes<?= $tender->id ?>">

                                                <?php
                                                $conts = '';
                                                if (isset($rates) && count($rates)) {
                                                    $i = 1;

                                                    foreach ($rates as $_rate) {
                                                        $conts .= $_rate->contractor . ',';
                                                        $contractor = \common\models\Contractor::find()->where(['id' => $_rate->contractor])->one();
                                                        ?>
                                                        <div class="input-field col s3 conts" id='upperbox<?= $tender->id . $i; ?>'>
                                                            <div class="itemid">
                                                                <select class="validate required contype materialSelectcon browser-default" required="" disabled="" name="cont[]">
                                                                    <option value='<?= $contractor->id ?>'><?= $contractor->firm ?></option>
                                                                </select></div>
                                                            <input type="hidden" name="contid" id="contid'<?= $tender->id . $i; ?>'" value="">
                                                            <div id="boxstatic<?= $tender->id . $i; ?>" class="boximg">
                                                                <?php
                                                                if (isset($idetails) && count($idetails)) {
                                                                    foreach ($idetails as $_item) {
                                                                        $getrate = \common\models\Itemrates::find()->where(['iid' => $_item->id, 'item_id' => $_item->item_id, 'contractor' => $_rate->contractor])->one();
                                                                        ?>
                                                                        <div class="itemid">
                                                                            <input id='rate<?= $_item->id ?>' class='rates' type='number' onblur='saverate(this.value, "<?= $_rate->contractor ?>", "<?= $_item->id ?>", "<?= $_item->item_id ?>", "<?= $_rate->tid ?>")' name = 'rate[]' min='1' step='1' onkeypress='return event.charCode >= 46 && event.charCode <= 57' value='<?= @$getrate->rate ?>'>
                                                                        </div>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                                <a class="waves-effect waves-light btn red m-b-xs deletebox" id="del<?= $tender->id . $i; ?>" onclick="delcontractor(<?= $_rate->contractor ?>,<?= $tender->id ?>,<?= $i ?>)">Delete</a>
                                                            </div>
                                                        </div>
                                                        <?php
                                                        $i++;
                                                    }
                                                }
                                                ?>
                                                <input type="hidden" name="allconts" id="allconts<?= $tender->id ?>" value="<?= rtrim($conts, ','); ?>">
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
            ?>

    </div>
</div>
</div>

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