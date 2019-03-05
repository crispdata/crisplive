<?php
/* @var $this yii\web\View */

use backend\controllers\SiteController;

$this->title = 'Manage Prices';

$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
?>
<style>
    .add-contact{    float: right;
                     margin-right: 15px;}    
    .select-wrapper input.select-dropdown, .select-wrapper input.select-dropdown:disabled{border-color: unset;}
    .card {
        float: left;
        width: 100%;
    }
</style>

<main class="mn-inner">
    <div class="row">
        <div class="col s6">
            <div class="page-title">Manage Prices</div>
        </div>

        <a class="waves-effect waves-light btn blue m-b-xs add-contact" href="<?= $baseURL ?>products/create-price"> Add Price</a>
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
                    <form id="make-types" method = "post" action = "<?= $baseURL ?>products/prices">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                        <div class="input-fields col s12 row">
                            <label>Select LT/HT</label>
                            <select class="validate required materialSelect" name='mtypesort'>
                                <option value="" disabled selected>Select LT/HT</option>
                                <option value="1" <?= (@$_POST['mtypesort'] == 1) ? 'selected' : '' ?> >LT</option>
                                <option value="2" <?= (@$_POST['mtypesort'] == 2) ? 'selected' : '' ?>>HT</option>
                            </select>
                        </div>
                        <div class="input-fields col s12 row">
                            <label>Select make type</label>
                            <select class="validate required materialSelect" id="statusFiltersizes" name='mtypesortone'>
                                <option value="" disabled selected>All Make Types</option>
                                <option value="1" <?= (@$_POST['mtypesortone'] == 1) ? 'selected' : '' ?> >Cables</option>
                                <option value="2" <?= (@$_POST['mtypesortone'] == 2) ? 'selected' : '' ?>>Lighting</option>
                                <!--option value="3" <?= (@$_POST['mtypesortone'] == 3) ? 'selected' : '' ?>>Fans</option>
                                <option value="4" <?= (@$_POST['mtypesortone'] == 4) ? 'selected' : '' ?>>Accessories</option>
                                <option value="5" <?= (@$_POST['mtypesortone'] == 5) ? 'selected' : '' ?>>Wire</option>
                                <option value="6" <?= (@$_POST['mtypesortone'] == 6) ? 'selected' : '' ?>>DB/MCB/MCCB/Timers</option>
                                <option value="7" <?= (@$_POST['mtypesortone'] == 7) ? 'selected' : '' ?>>Transformers</option>
                                <option value="8" <?= (@$_POST['mtypesortone'] == 8) ? 'selected' : '' ?>>Cable Jointing Kits</option>
                                <option value="9" <?= (@$_POST['mtypesortone'] == 9) ? 'selected' : '' ?>>Panels</option>
                                <option value="10" <?= (@$_POST['mtypesortone'] == 10) ? 'selected' : '' ?>>ACB</option>
                                <option value="11" <?= (@$_POST['mtypesortone'] == 11) ? 'selected' : '' ?>>VCB</option>
                                <option value="12" <?= (@$_POST['mtypesortone'] == 12) ? 'selected' : '' ?>>Substations</option>
                                <option value="13" <?= (@$_POST['mtypesortone'] == 13) ? 'selected' : '' ?>>Motors</option-->
                            </select>
                        </div>
                        <div class="input-fields col s12 row" id="second" style="<?php
                        if (!isset($_POST['mtypesorttwo'])) {
                            echo "display:none";
                        }
                        ?>" >
                            <label>Select Sub Type</label>
                            <select class="validate required" required="" name="mtypesorttwo" id="mtypesorttwo">
                                <option value="" disabled selected>Select</option>
                                <option value="1" <?= (@$_POST['mtypesorttwo'] == 1) ? 'selected' : '' ?> >Copper</option>
                                <option value="2" <?= (@$_POST['mtypesorttwo'] == 2) ? 'selected' : '' ?>>Aluminium</option>

                            </select>
                        </div>
                        <div class="input-fields col s12 row" id="third" style="<?php
                        if (!isset($_POST['mtypesortthree'])) {
                            echo "display:none";
                        }
                        ?>">
                            <label>Select Sub Type</label>
                            <select class="validate required" required="" name="mtypesortthree" id="mtypesortthree">
                                <option value="" disabled selected>Select</option>
                                <option value="1" <?= (@$_POST['mtypesortthree'] == 1) ? 'selected' : '' ?> >Armoured</option>
                                <option value="2" <?= (@$_POST['mtypesortthree'] == 2) ? 'selected' : '' ?>>Unarmoured</option>

                            </select>
                        </div>
                    </form>
                    <?php
                    if (@$prices) {
                        ?>
                        <div id="tables">
                            <table id = "current-project" class="responsive-table">
                                <thead>
                                    <tr>
                                        <th data-field="name">Sr. No.</th>
                                        <th data-field="email">Size</th>
                                        <th data-field="email">Core</th>
                                        <th data-field="email">Price</th>
                                        <th data-field="email">Quantity</th>
                                        <th data-field="email">Amount</th>
                                      

                                    </tr>
                                </thead>
                                <tbody id="contacts_list">
                                    <?php
                                    if (@$prices) {
                                        $tenders = \common\models\Tender::find()->where(['status' => 1])->all();
                                        if (isset($tenders)) {
                                            foreach ($tenders as $_tender) {
                                                $tids[] = $_tender->id;
                                            }
                                        }
                                        $items = \common\models\Item::find()->where(['items.tender_id' => $tids, 'items.tenderthree' => @$_POST['mtypesort'], 'items.tenderfour' => @$_POST['mtypesortone'], 'items.tenderfive' => @$_POST['mtypesorttwo'], 'items.tendersix' => @$_POST['mtypesortthree']])->all();
                                        if (isset($items)) {
                                            foreach ($items as $_item) {
                                                $iids[] = $_item->id;
                                            }
                                        }
                                        $i = 0;
                                        foreach ($prices as $key => $size) {
                                            $qunty = 0;
                                            $core = Sitecontroller::actionGetcore($size->mtypefive);
                                            $sizename = \common\models\Size::find()->where(['id' => $size->mtypefour])->one();
                                            $itemdetails = \common\models\ItemDetails::find()->where(['item_id' => $iids, 'description' => $size->mtypefour, 'core' => $size->mtypefive])->all();
                                            if (isset($itemdetails)) {
                                                foreach ($itemdetails as $_idetail) {
                                                    $qunty += $_idetail->quantity;
                                                }
                                            }
                                            
                                            $eprice = ($qunty * $size->price);
                                            ?>
                                            <tr data-id = "<?= $size->id ?>">
                                                <td class = ""><?= $key + 1 ?></td>
                                                <td class = ""><?= $sizename->size ?></td>
                                                <td class = ""><?= $core ?></td>
                                                <td class = ""><?= $size->price ?></td>
                                                <td class = ""><?= $qunty ?></td>
                                                <td class = ""><?= $eprice ?></td>

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
    </div>
</main>
<script>
    function pop_up(url) {
        window.open(url, 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=1076,height=768,directories=no,location=no')
    }
</script>