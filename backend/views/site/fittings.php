<?php
/* @var $this yii\web\View */

$this->title = 'Manage Fittings';

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
            <div class="page-title">Manage Fittings</div>
        </div>

        <a class="waves-effect waves-light btn blue m-b-xs add-contact" href="<?= $baseURL ?>site/create-fitting"> Add Fitting</a>
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
                    <form id="make-types" method = "post" action = "<?= $baseURL ?>site/fittings">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                        <label>Select fitting</label>
                        <select class="validate required materialSelect" id="statusFilterfit" name='mtypesortone'>
                            <option value="" disabled selected>All Fittings</option>
                            <option value="1" <?= (@$_POST['mtypesortone'] == 1) ? 'selected' : '' ?> >Type of fitting</option>
                            <option value="2" <?= (@$_POST['mtypesortone'] == 2) ? 'selected' : '' ?>>Capacity of fitting</option>
                        </select>
                    </form>
                    <?php
                    if (@$fittings) {
                        ?>
                    <div id="tables">
                        <table id = "current-project" class="responsive-table">
                            <thead>
                                <tr>
                                    <th data-field="name">Sr. No.</th>
                                    <th data-field="name">Type</th>
                                    <th data-field="email">Text</th>
                                    <th data-field="email">Actions</th>

                                </tr>
                            </thead>
                            <tbody id="contacts_list">
                                <?php
                                if (@$fittings) {
                                    $i = 0;
                                    $mtypetwo = '';
                                    $mtypethree = '';
                                    foreach ($fittings as $key => $fit) {
                                        if ($fit->type == 1) {
                                            $mtype = 'Type of fitting';
                                        } else {
                                            $mtype = 'Capacity of fitting';
                                        }
                                       
                                        ?>
                                        <tr data-id = "<?= $fit->id ?>">
                                            <td class = ""><?= $key + 1 ?></td>
                                            <td class = ""><?= $mtype ?></td>
                                            <td class = ""><?= $fit->text ?></td>
                                            <td>

                                                <a href="<?= $baseURL ?>site/create-fitting?id=<?= $fit->id; ?>" class="waves-effect waves-light btn blue">Edit</a>


                                                <a href="#modal<?= $fit->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>


                                            </td>

                                        </tr>
                                    <div id="modal<?= $fit->id; ?>" class="modal">
                                        <div class="modal-content">
                                            <h4>Confirmation Message</h4>
                                            <p>Are you sure you want to delete it ?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="javascript::void()" class=" modal-action modal-close waves-effect waves-green btn-flat">No</a>
                                            <a href="<?= $baseURL ?>site/delete-fitting?id=<?= $fit->id; ?>" class=" modal-action modal-close waves-effect waves-green btn-flat">Yes</a>
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
