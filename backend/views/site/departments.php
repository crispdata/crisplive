<?php
/* @var $this yii\web\View */

use backend\controllers\SiteController;
use yii\helpers\Url;

$this->title = 'All Organisations';
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
</style>

<main class="mn-inner">
    <div class="row">
        <div class="col s6">
            <div class="page-title">All Organisations</div>
        </div>

        <a href="#department" class="waves-effect waves-light btn blue m-b-xs modal-trigger add-contact">Add Organisation</a>
        <div id="department" class="modal">
            <div class="modal-content">
                <h4>Add new organisation</h4>
                <form id="sort-data" method = "post" action = "<?= $baseURL ?>site/adddepartment">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="department" type="text" name = "department" required="" class="validate required" value="">
                            <label for="department">Organisation Name</label>
                        </div>
                    </div>
                    <input class="btn blue m-b-xs" name="submit" type="submit" value="Submit">
                </form>

            </div>

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

                    <table id = "current-project" class="responsive-table">
                        <thead>
                            <tr>
                                <th data-field="name">Sr. No.</th>
                                <th data-field="name">Organisation</th>
                                <th data-field="name">Status</th>
                                <th data-field="email">Actions</th>

                            </tr>
                        </thead>
                        <tbody id="contacts_list">
                            <?php
                            if (@$departments) {
                                $i = 0;
                                foreach ($departments as $key => $department) {
                                    if ($department->status == 1) {
                                        $status = 'Active';
                                        $class = 'green';
                                    } else {
                                        $status = 'Inactive';
                                        $class = 'red';
                                    }
                                    ?>
                                    <tr data-id = "<?= $department->id ?>">
                                        <td class = ""><?= $key + 1 ?></td>
                                        <td class = ""><?= $department->name ?></td>
                                        <td ><a href="<?= Url::to(['site/change-department-status', 'id' => $department->id]) ?>" class = "btn <?= $class ?>"><?= $status ?></a></td>
                                        <td>
                                            <a href="#editmodal<?= $department->id; ?>" class="waves-effect waves-light btn blue modal-trigger">Edit</a>
                                            <a href="#modal<?= $department->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>
                                        </td>

                                    </tr>
                                <div id="modal<?= $department->id; ?>" class="modal">
                                    <div class="modal-content">
                                        <h4>Confirmation Message</h4>
                                        <p>Are you sure you want to delete it ?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="javascript::void()" class=" modal-action modal-close waves-effect waves-green btn-flat">No</a>
                                        <a href="<?= Url::to(['site/delete-department', 'id' => $department->id]) ?>" class=" modal-action modal-close waves-effect waves-green btn-flat">Yes</a>
                                    </div>
                                </div>

                                <div id="editmodal<?= $department->id; ?>" class="modal">
                                    <div class="modal-content">
                                        <h4>Edit organisation</h4>
                                        <form id="sort-data" method = "post" action = "<?= $baseURL ?>site/adddepartment">
                                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                                            <input type="hidden" name="did" value="<?= $department->id; ?>">
                                            <div class="row">
                                                <div class="input-field col s12">
                                                    <input id="department" type="text" name = "department" required="" class="validate required" value="<?= $department->name; ?>">
                                                    <label for="department">Organisation Name</label>
                                                </div>
                                            </div>
                                            <input class="btn blue m-b-xs" name="submit" type="submit" value="Submit">
                                        </form>

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
    </div>
</main>
