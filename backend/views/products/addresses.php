<?php
/* @var $this yii\web\View */

use backend\controllers\SiteController;
use yii\helpers\Url;

$this->title = 'All Addresses';
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
    #contacts_list a{width:65px!important;}
</style>

<main class="mn-inner">
    <div class="row">
        <div class="col s6">
            <div class="page-title">All Addresses</div>
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
                                <th data-field="name">Details of Contracting Office</th>
                                <th data-field="name" width="100px">Contact No.</th>
                                <th data-field="name" width="100px">Email ID</th>
                                <th data-field="email">Actions</th>

                            </tr>
                        </thead>
                        <tbody id="contacts_list">
                            <?php
                            if (@$addresses) {
                                $i = 0;
                                foreach ($addresses as $key => $user) {
                                    $tdetails = '';
                                    $command = Sitecontroller::actionGetcommand($user->command);
                                    if (!isset($user->cengineer) && isset($user->gengineer)) {
                                        $cengineer = \common\models\Cengineer::find()->where(['cid' => $user->gengineer, 'status' => 1])->one();
                                    } else {
                                        $cengineer = \common\models\Cengineer::find()->where(['cid' => $user->cengineer, 'status' => 1])->one();
                                    }
                                    $cwengineer = \common\models\Cwengineer::find()->where(['cengineer' => $user->cengineer, 'cid' => $user->cwengineer, 'status' => 1])->one();
                                    $gengineer = \common\models\Gengineer::find()->where(['cwengineer' => $user->cwengineer, 'gid' => $user->gengineer, 'status' => 1])->one();
                                    $tdetails = @$command . ' ' . @$cengineer->text . ' ' . @$cwengineer->text . ' ' . @$gengineer->text;
                                    ?>
                                    <tr data-id = "<?= $user->id ?>">
                                        <td class = ""><?= $key + 1 ?></td>
                                        <td class = ""><?= $tdetails ?></td>
                                        <td class = ""><?= $user->contact ?></td>
                                        <td class = ""><?= $user->email ?></td>
                                        <td>
                                            <a href="<?= Url::to(['products/addaddress', 'id' => $user->id]) ?>" class="waves-effect waves-light btn blue">Edit</a>
                                            <a href="#modal<?= $user->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>
                                        </td>

                                    </tr>
                                <div id="modal<?= $user->id; ?>" class="modal">
                                    <div class="modal-content">
                                        <h4>Confirmation Message</h4>
                                        <p>Are you sure you want to delete it ?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="javascript::void()" class=" modal-action modal-close waves-effect waves-green btn-flat">No</a>
                                        <a href="<?= Url::to(['products/deleteaddress', 'id' => $user->id]) ?>" class=" modal-action modal-close waves-effect waves-green btn-flat">Yes</a>
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
