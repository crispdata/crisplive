<?php
/* @var $this yii\web\View */

use backend\controllers\SiteController;
use yii\helpers\Url;

$this->title = 'All Sub Divisions';
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
            <div class="page-title">All Sub Divisions</div>
        </div>
        <a class="waves-effect waves-light btn blue m-b-xs add-contact" href="<?= $baseURL ?>site/addsubdivision">Add Sub Division</a>
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
                                <th data-field="name">Department</th>
                                <th data-field="name">Division</th>
                                <th data-field="name">Sub Division</th>
                                <th data-field="name" width="100px">Status</th>
                                <th data-field="email">Actions</th>

                            </tr>
                        </thead>
                        <tbody id="contacts_list">
                            <?php
                            if (@$subdivisions) {
                                $i = 0;
                                foreach ($subdivisions as $key => $direct) {
                                    $depart = \common\models\Departments::find()->where(['id'=>$direct->did])->one();
                                    $subdepart = \common\models\Directorates::find()->where(['id'=>$direct->direct_id])->one();
                                    $division = \common\models\Divisions::find()->where(['id'=>$direct->div_id])->one();
                                    if ($direct->status == 1) {
                                        $status = 'Active';
                                        $class = 'green';
                                    } else {
                                        $status = 'Inactive';
                                        $class = 'red';
                                    }
                                    ?>
                                    <tr data-id = "<?= $direct->id ?>">
                                        <td class = ""><?= $key + 1 ?></td>
                                        <td class = ""><?= @$depart->name ?></td>
                                        <td class = ""><?= @$subdepart->name ?></td>
                                        <td class = ""><?= @$division->name ?></td>
                                        <td class = ""><?= @$direct->name ?></td>
                                        <td ><a href="<?= Url::to(['site/change-subdivision-status', 'id' => $direct->id]) ?>" class = "btn <?= $class ?>"><?= $status ?></a></td>
                                        <td>
                                            <a href="<?= Url::to(['site/addsubdivision', 'id' => $direct->id]) ?>" class="waves-effect waves-light btn blue">Edit</a>
                                            <a href="#modal<?= $direct->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>
                                        </td>

                                    </tr>
                                <div id="modal<?= $direct->id; ?>" class="modal">
                                    <div class="modal-content">
                                        <h4>Confirmation Message</h4>
                                        <p>Are you sure you want to delete it ?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="javascript::void()" class=" modal-action modal-close waves-effect waves-green btn-flat">No</a>
                                        <a href="<?= Url::to(['site/delete-subdivision', 'id' => $direct->id]) ?>" class=" modal-action modal-close waves-effect waves-green btn-flat">Yes</a>
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
