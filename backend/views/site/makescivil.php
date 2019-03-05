<?php
/* @var $this yii\web\View */

$this->title = 'Manage Makes';
use yii\helpers\Url;
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

        <a class="waves-effect waves-light btn blue m-b-xs add-contact" href="<?= $baseURL ?>site/create-make-civil"> Add Make</a>
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
                    <form id="make-types" method = "post" action = "<?= $baseURL ?>site/civil">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                        <label>Select make type</label>
                        <select class="validate required materialSelect" id="statusFilter" name='mtypesort'>
                            <option value="" disabled selected>All Make Types</option>
                            <option value="14" <?= (@$_POST['mtypesort'] == 14) ? 'selected' : '' ?> >Cement</option>
                            <option value="15" <?= (@$_POST['mtypesort'] == 15) ? 'selected' : '' ?>>Reinforcement Steel</option>
                            <option value="16" <?= (@$_POST['mtypesort'] == 16) ? 'selected' : '' ?>>Structural Steel</option>
                            <option value="17" <?= (@$_POST['mtypesort'] == 17) ? 'selected' : '' ?>>Non Structural Steel</option>
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
                                    $mtype='';
                                    $i = 0;
                                    foreach ($makes as $key => $make) {
                                        if ($make->mtype == 14) {
                                            $mtype = 'Cement';
                                        } elseif ($make->mtype == 15) {
                                            $mtype = 'Reinforcement Roads';
                                        } elseif ($make->mtype == 16) {
                                            $mtype = 'Structural Roads';
                                        } elseif ($make->mtype == 17) {
                                            $mtype = 'Non Structural Roads';
                                        }
                                        ?>
                                        <tr data-id = "<?= $make->id ?>">
                                            <td class = ""><?= $key + 1 ?></td>
                                            <td class = ""><?= $mtype ?></td>
                                            <td class = ""><?= $make->make ?></td>
                                            <td class = ""><?= $make->email ?></td>
                                            <td>

                                                <a href="<?= Url::to(['site/create-make-civil', 'id' => $make->id]) ?>" class="waves-effect waves-light btn blue">Edit</a>


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
                                            <a href="<?= Url::to(['site/delete-make', 'id' => $make->id]) ?>" class=" modal-action modal-close waves-effect waves-green btn-flat">Yes</a>
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
