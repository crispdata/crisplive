<?php
/* @var $this yii\web\View */

$this->title = 'Get E/M Data';
$user = Yii::$app->user->identity;
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
    .select2-search__field{color:#000!important;}
    .boxes {
        /* float: left; */
        /* width: 100%; */
        border: 5px solid #000;
        padding: 10px;
        border-radius: 10px;
        margin-bottom: 10px;
    }
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
                        <form id="getdatas" class="col s12" method = "post" action = "<?= $baseURL ?>products/category-em" enctype="multipart/form-data">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                            <div class="input-field col s12">
                                <select class="contact-authtypes browser-default" name="command" id="command" required="">
                                    <option value="">Select Command</option>
                                    <option value="13" <?php
                                    if (@$_POST['command'] == 13) {
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
                            <div class="input-field col s12">
                                <select name='authtype' id="authtypes" class="contact-authtypes browser-default" required="">
                                    <option value=''>Select Product</option>
                                    <option value='1' <?= (@$_POST['authtype'] == 1) ? 'selected' : '' ?>>Cables</option>
                                    <option value='2' <?= (@$_POST['authtype'] == 2) ? 'selected' : '' ?>>Lighting</option>
                                </select>
                            </div>

                            <!--div class="input-field col s12">
                                
                                <input name="mails" class="contact-mails form-control" type="text" placeholder="Enter multiple E-mail IDs by putting comma">
                            </div-->

                            <!--button  id="signbutton" type="submit" name="sendmail"  class="waves-effect waves-light btn blue m-b-xs">Send Mail</button-->
                            <div class="input-field col s12">
                                <input  id="download" type="submit" name="submit"  class="waves-effect waves-light btn blue m-b-xs" value="Submit">
                            </div>

                        </form>

                    </div>

                    <?php if (isset($finalquantity) && count($finalquantity)) { ?>
                        <div class="boxes">
                            <h5>All <?= $head ?> Quantities - <?= array_sum($finalquantity) . ' ' . $unit ?></h5>
                            <table id = "current-projects" class="quantities responsive-table scroll">
                                <thead>
                                    <tr>
                                        <th data-field="name">Sr. No.</th>
                                        <th data-field="name"><?= $column ?></th>
                                        <th data-field="name">Quantity</th>
                                    </tr>
                                </thead>
                                <tbody id="contacts_list">
                                    <?php
                                    if (@$finalquantity) {
                                        $i = 0;
                                        $valuetwo = '';
                                        $valuethree = '';
                                        foreach (@$finalquantity as $key => $quantity) {
                                            ?>
                                            <tr>
                                                <td class = ""><?= $i ?></td>
                                                <td class = "desc"><?= $key . ' ' . $column ?></td>
                                                <td class = ""><?= $quantity ?></td>
                                            </tr>

                                            <?php
                                            $i++;
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                    <?php if (isset($finalaocquantity) && count($finalaocquantity)) { ?>
                        <div class="boxes">
                            <h5>All AOC <?= $head ?> Quantities - <?= array_sum($finalaocquantity) . ' ' . $unit ?></h5>
                            <table id = "current-projects" class="quantities responsive-table scroll">
                                <thead>
                                    <tr>
                                        <th data-field="name">Sr. No.</th>
                                        <th data-field="name"><?= $column ?></th>
                                        <th data-field="name">Quantity</th>
                                    </tr>
                                </thead>
                                <tbody id="contacts_list">
                                    <?php
                                    if ($finalaocquantity) {
                                        $i = 0;
                                        $valuetwo = '';
                                        $valuethree = '';
                                        foreach ($finalaocquantity as $key => $quantity) {
                                            ?>

                                            <tr>
                                                <td class = ""><?= $i ?></td>
                                                <td class = "desc"><?= $key . ' ' . $column ?></td>
                                                <td class = ""><?= $quantity ?></td>
                                            </tr>

                                            <?php
                                            $i++;
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                    <?php if (isset($finalarchivequantity) && count($finalarchivequantity)) { ?>
                        <div class="boxes">
                            <h5>All Archived <?= $head ?> Quantities - <?= array_sum($finalarchivequantity) . ' ' . $unit ?></h5>
                            <table id = "current-projects" class="quantities responsive-table scroll">
                                <thead>
                                    <tr>
                                        <th data-field="name">Sr. No.</th>
                                        <th data-field="name"><?= $column ?></th>
                                        <th data-field="name">Quantity</th>
                                    </tr>
                                </thead>
                                <tbody id="contacts_list">
                                    <?php
                                    if ($finalarchivequantity) {
                                        $i = 0;
                                        $valuetwo = '';
                                        $valuethree = '';
                                        foreach ($finalarchivequantity as $key => $quantity) {
                                            ?>
                                            <tr>
                                                <td class = ""><?= $i ?></td>
                                                <td class = "desc"><?= $key . ' ' . $column ?></td>
                                                <td class = ""><?= $quantity ?></td>
                                            </tr>

                                            <?php
                                            $i++;
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>



        </div>


</main>