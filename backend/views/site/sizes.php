<?php
/* @var $this yii\web\View */

$this->title = 'Manage Sizes';

$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
?>
<style>
    .add-contact{    float: right;
                     margin-right: 15px;}    
    .select-wrapper input.select-dropdown, .select-wrapper input.select-dropdown:disabled{border-color: unset;}
</style>

<main class="mn-inner">
    <div class="row">
        <div class="col s6">
            <div class="page-title">Manage Sizes</div>
        </div>

        <a class="waves-effect waves-light btn blue m-b-xs add-contact" href="<?= $baseURL ?>site/create-size"> Add Size</a>
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
                    <form id="make-types" method = "post" action = "<?= $baseURL ?>site/sizes">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                        <label>Select make type</label>
                        <select class="validate required materialSelect" id="statusFiltersizes" name='mtypesortone'>
                            <option value="" disabled selected>All Make Types</option>
                            <option value="1" <?= (@$_POST['mtypesortone'] == 1) ? 'selected' : '' ?> >Cables</option>
                            <option value="2" <?= (@$_POST['mtypesortone'] == 2) ? 'selected' : '' ?>>Lighting</option>
                            <option value="3" <?= (@$_POST['mtypesortone'] == 3) ? 'selected' : '' ?>>Fans</option>
                            <option value="4" <?= (@$_POST['mtypesortone'] == 4) ? 'selected' : '' ?>>Accessories</option>
                            <option value="5" <?= (@$_POST['mtypesortone'] == 5) ? 'selected' : '' ?>>Wire</option>
                            <option value="6" <?= (@$_POST['mtypesortone'] == 6) ? 'selected' : '' ?>>DB/MCB/MCCB/Timers</option>
                            <option value="7" <?= (@$_POST['mtypesortone'] == 7) ? 'selected' : '' ?>>Transformers</option>
                            <option value="8" <?= (@$_POST['mtypesortone'] == 8) ? 'selected' : '' ?>>Cable Jointing Kits</option>
                            <option value="9" <?= (@$_POST['mtypesortone'] == 9) ? 'selected' : '' ?>>Panels</option>
                            <option value="10" <?= (@$_POST['mtypesortone'] == 10) ? 'selected' : '' ?>>ACB</option>
                            <option value="11" <?= (@$_POST['mtypesortone'] == 11) ? 'selected' : '' ?>>VCB</option>
                            <option value="12" <?= (@$_POST['mtypesortone'] == 12) ? 'selected' : '' ?>>Substations</option>
                            <option value="13" <?= (@$_POST['mtypesortone'] == 13) ? 'selected' : '' ?>>Motors</option>
                        </select>
                        <div class="" id="second" style="<?php
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
                        <div class="" id="third" style="<?php
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
                    if (@$sizes) {
                        ?>
                    <div id="tables">
                        <table id = "current-project" class="responsive-table">
                            <thead>
                                <tr>
                                    <th data-field="name">Sr. No.</th>
                                    <th data-field="name">Make Type</th>
                                    <th data-field="email">Size</th>
                                    <th data-field="email">Actions</th>

                                </tr>
                            </thead>
                            <tbody id="contacts_list">
                                <?php
                                if (@$sizes) {
                                    $i = 0;
                                    $mtypetwo = '';
                                    $mtypethree = '';
                                    foreach ($sizes as $key => $size) {
                                        if ($size->mtypeone == 1) {
                                            $mtype = 'Cables';
                                        } elseif ($size->mtypeone == 2) {
                                            $mtype = 'Lighting';
                                        } elseif ($size->mtypeone == 3) {
                                            $mtype = 'Fans';
                                        } elseif ($size->mtypeone == 4) {
                                            $mtype = 'Accessories';
                                        } elseif ($size->mtypeone == 5) {
                                            $mtype = 'Wire';
                                        } elseif ($size->mtypeone == 6) {
                                            $mtype = 'DB/MCB/MCCB/Timers';
                                        } elseif ($size->mtypeone == 7) {
                                            $mtype = 'Transformers';
                                        } elseif ($size->mtypeone == 8) {
                                            $mtype = 'Cable Jointing Kits';
                                        } elseif ($size->mtypeone == 9) {
                                            $mtype = 'Panels';
                                        } elseif ($size->mtypeone == 10) {
                                            $mtype = 'ACB';
                                        } elseif ($size->mtypeone == 11) {
                                            $mtype = 'VCB';
                                        } elseif ($size->mtypeone == 12) {
                                            $mtype = 'Substations';
                                        } elseif ($size->mtypeone == 13) {
                                            $mtype = 'Motors';
                                        }
                                        if ($size->mtypetwo == 1) {
                                            $mtypetwo = 'Copper';
                                        } elseif ($size->mtypetwo == 2) {
                                            $mtypetwo = 'Aluminium';
                                        }
                                        if ($size->mtypethree == 1) {
                                            $mtypethree = 'Armoured';
                                        } elseif ($size->mtypethree == 2) {
                                            $mtypethree = 'unarmoured';
                                        }
                                        ?>
                                        <tr data-id = "<?= $size->id ?>">
                                            <td class = ""><?= $key + 1 ?></td>
                                            <td class = ""><?= $mtype . ' ' . $mtypetwo . ' ' . $mtypethree ?></td>
                                            <td class = ""><?= $size->size ?></td>
                                            <td>

                                                <a href="<?= $baseURL ?>site/create-size?id=<?= $size->id; ?>" class="waves-effect waves-light btn blue">Edit</a>


                                                <a href="#modal<?= $size->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>


                                            </td>

                                        </tr>
                                    <div id="modal<?= $size->id; ?>" class="modal">
                                        <div class="modal-content">
                                            <h4>Confirmation Message</h4>
                                            <p>Are you sure you want to delete it ?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="javascript::void()" class=" modal-action modal-close waves-effect waves-green btn-flat">No</a>
                                            <a href="<?= $baseURL ?>site/delete-size?id=<?= $size->id; ?>" class=" modal-action modal-close waves-effect waves-green btn-flat">Yes</a>
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
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</main>
