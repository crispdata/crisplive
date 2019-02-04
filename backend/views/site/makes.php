<?php
/* @var $this yii\web\View */

$this->title = 'Manage Makes';

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
            <div class="page-title">Manage Makes</div>
        </div>

        <a class="waves-effect waves-light btn blue m-b-xs add-contact" href="<?= $baseURL ?>site/create-make"> Add Make</a>
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
                    <form id="make-types" method = "post" action = "<?= $baseURL ?>site/makes">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                        <label>Select make type</label>
                        <select class="validate required materialSelect" id="statusFilter" name='mtypesort'>
                            <option value="" disabled selected>All Make Types</option>
                            <option value="1" <?= (@$_POST['mtypesort'] == 1) ? 'selected' : '' ?> >Cables</option>
                            <option value="2" <?= (@$_POST['mtypesort'] == 2) ? 'selected' : '' ?>>Lighting</option>
                            <option value="3" <?= (@$_POST['mtypesort'] == 3) ? 'selected' : '' ?>>Fans</option>
                            <option value="4" <?= (@$_POST['mtypesort'] == 4) ? 'selected' : '' ?>>Accessories</option>
                            <option value="5" <?= (@$_POST['mtypesort'] == 5) ? 'selected' : '' ?>>Wire</option>
                            <option value="6" <?= (@$_POST['mtypesort'] == 6) ? 'selected' : '' ?>>DB/MCB/MCCB/Timers</option>
                            <option value="7" <?= (@$_POST['mtypesort'] == 7) ? 'selected' : '' ?>>Transformers</option>
                            <option value="8" <?= (@$_POST['mtypesort'] == 8) ? 'selected' : '' ?>>Cable Jointing Kits</option>
                            <option value="9" <?= (@$_POST['mtypesort'] == 9) ? 'selected' : '' ?>>Panels</option>
                            <option value="10" <?= (@$_POST['mtypesort'] == 10) ? 'selected' : '' ?>>ACB</option>
                            <option value="11" <?= (@$_POST['mtypesort'] == 11) ? 'selected' : '' ?>>VCB</option>
                            <option value="12" <?= (@$_POST['mtypesort'] == 12) ? 'selected' : '' ?>>Substations</option>
                            <option value="13" <?= (@$_POST['mtypesort'] == 13) ? 'selected' : '' ?>>Motors</option>
                        </select>
                    </form>
                    <?php
                    if (@$makes) {
                        ?>
                        
                        <table id = "current-project" class="responsive-table">
                            <thead>
                                <tr>
                                    <th data-field="name">Sr. No.</th>
                                    <th data-field="name">Make Type</th>
                                    <th data-field="email">Make</th>
                                    <th data-field="email">Email</th>
                                    <th data-field="email">Actions</th>

                                </tr>
                            </thead>
                            <tbody id="contacts_list">
                                <?php
                                if (@$makes) {
                                    $i = 0;
                                    foreach ($makes as $key => $make) {
                                        if ($make->mtype == 1) {
                                            $mtype = 'Cables';
                                        } elseif ($make->mtype == 2) {
                                            $mtype = 'Lighting';
                                        } elseif ($make->mtype == 3) {
                                            $mtype = 'Fans';
                                        } elseif ($make->mtype == 4) {
                                            $mtype = 'Accessories';
                                        } elseif ($make->mtype == 5) {
                                            $mtype = 'Wire';
                                        } elseif ($make->mtype == 6) {
                                            $mtype = 'DB/MCB/MCCB/Timers';
                                        } elseif ($make->mtype == 7) {
                                            $mtype = 'Transformers';
                                        } elseif ($make->mtype == 8) {
                                            $mtype = 'Cable Jointing Kits';
                                        } elseif ($make->mtype == 9) {
                                            $mtype = 'Panels';
                                        } elseif ($make->mtype == 10) {
                                            $mtype = 'ACB';
                                        } elseif ($make->mtype == 11) {
                                            $mtype = 'VCB';
                                        } elseif ($make->mtype == 12) {
                                            $mtype = 'Substations';
                                        } elseif ($make->mtype == 13) {
                                            $mtype = 'Motors';
                                        }
                                        ?>
                                        <tr data-id = "<?= $make->id ?>">
                                            <td class = ""><?= $key + 1 ?></td>
                                            <td class = ""><?= $mtype ?></td>
                                            <td class = ""><?= $make->make ?></td>
                                            <td class = ""><?= $make->email ?></td>
                                            <td>

                                                <a href="<?= $baseURL ?>site/create-make?id=<?= $make->id; ?>" class="waves-effect waves-light btn blue">Edit</a>


                                                <a href="#modal<?= $make->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>


                                            </td>

                                        </tr>
                                    <div id="modal<?= $make->id; ?>" class="modal">
                                        <div class="modal-content">
                                            <h4>Confirmation Message</h4>
                                            <p>Are you sure you want to delete it ?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="javascript::void()" class=" modal-action modal-close waves-effect waves-green btn-flat">No</a>
                                            <a href="<?= $baseURL ?>site/delete-make?id=<?= $make->id; ?>" class=" modal-action modal-close waves-effect waves-green btn-flat">Yes</a>
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
