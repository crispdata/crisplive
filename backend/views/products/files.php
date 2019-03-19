<?php
/* @var $this yii\web\View */

$this->title = 'Manage Files';

use backend\controllers\ProductsController;
use yii\helpers\Url;

$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
?>
<style>
    .add-contact{    float: right;
                     margin-right: 15px;}   
    #contacts_list img{float:left;}
    .select-wrapper input.select-dropdown, .select-wrapper input.select-dropdown:disabled{border-color: unset;}
    table td a{ word-wrap:break-word; }
    a.file {
        width: 30%!important;
        float: left;
    }
</style>

<main class="mn-inner">
    <div class="row">
        <div class="col s6">
            <div class="page-title">Manage Files</div>
        </div>

        <a class="waves-effect waves-light btn blue m-b-xs add-contact" href="<?= $baseURL ?>products/uploadfile">Upload New File</a>
        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <script>
                swal({
                    title: "<?= Yii::$app->session->getFlash('success'); ?>",
                    timer: 2000,
                    type: "success",
                    showConfirmButton: false
                });
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
                    <div id="tables">
                        <table id = "current-project" class="responsive-table">
                            <thead>
                                <tr>
                                    <th data-field="name">Sr. No.</th>
                                    <th data-field="email" width="300px">File</th>
                                    <th data-field="email">Department</th>
                                    <th data-field="email">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="contacts_list">
                                <?php
                                if (@$files) {
                                    $i = 0;
                                    foreach ($files as $key => $fit) {
                                        $department = \common\models\User::find()->where(['UserId' => $fit->did])->one();
                                        $fileone = explode('/', $fit->file);
                                        $filetwo = explode('-', $fileone[5]);
                                        unset($filetwo[0]);
                                        $fname = implode('-', $filetwo);
                                        ?>
                                        <tr data-id = "<?= $fit->id ?>">
                                            <td class = ""><?= $key + 1 ?></td>
                                            <td class = ""><img src="<?= ProductsController::actionFileimages($fit->file); ?>" alt="<?= $fname ?>"><a class="file" download href="<?= $fit->file; ?>"><?= $fname ?></a></td>
                                            <td class = ""><?= $department->name ?></td>
                                            <td>

                                                <a href="<?= Url::to(['products/uploadfile', 'id' => $fit->id]) ?>" class="waves-effect waves-light btn blue">Edit</a>


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
                                            <a href="<?= Url::to(['products/delete-file', 'id' => $fit->id]) ?>" class=" modal-action modal-close waves-effect waves-green btn-flat">Yes</a>
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
    </div>
</main>
