<?php
/* @var $this yii\web\View */

use backend\controllers\SiteController;

$this->title = 'All Users';
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
            <div class="page-title">All Users</div>
        </div>

        <a class="waves-effect waves-light btn blue m-b-xs add-contact" href="<?= $baseURL ?>site/create-user"> Create User</a>
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
                                <th data-field="name">Username</th>
                                <th data-field="name">Group Type</th>
                                <th data-field="name">Email</th>
                                <th data-field="name">Image</th>
                                <th data-field="name">Status</th>
                                <th data-field="email">Actions</th>

                            </tr>
                        </thead>
                        <tbody id="contacts_list">
                            <?php
                            if (@$users) {
                                $i = 0;
                                foreach ($users as $key => $user) {
                                    if ($user->status == 10) {
                                        $status = 'Active';
                                        $class = 'green';
                                    } else {
                                        $status = 'Inactive';
                                        $class = 'red';
                                    }
                                    ?>
                                    <tr data-id = "<?= $user->UserId ?>">
                                        <td class = ""><?= $key + 1 ?></td>
                                        <td class = ""><?= $user->username ?></td>
                                        <td class = ""><?= SiteController::actionGetgroupbyid($user->group_id); ?></td>
                                        <td class = ""><?= $user->email ?></td>
                                        <td class = "">
                                            <?php if ($user->Logo) { ?>
                                                <img width="100" height="100" src="<?= $imageURL . $user->Logo ?>">
                                            <?php } ?>
                                        </td>
                                        <td ><a href="<?= $baseURL ?>site/change-status?id=<?= $user->UserId; ?>" class = "btn <?= $class ?>"><?= $status ?></a></td>
                                        <td>
                                            <a href="<?= $baseURL ?>site/edit-user?id=<?= $user->UserId; ?>" class="waves-effect waves-light btn blue">Edit</a>
                                            <a href="#modal<?= $user->UserId; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>
                                        </td>

                                    </tr>
                                <div id="modal<?= $user->UserId; ?>" class="modal">
                                    <div class="modal-content">
                                        <h4>Confirmation Message</h4>
                                        <p>Are you sure you want to delete it ?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="javascript::void()" class=" modal-action modal-close waves-effect waves-green btn-flat">No</a>
                                        <a href="<?= $baseURL ?>site/delete-user?id=<?= $user->UserId; ?>" class=" modal-action modal-close waves-effect waves-green btn-flat">Yes</a>
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
