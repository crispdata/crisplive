<?php
/* @var $this yii\web\View */

use backend\controllers\SiteController;

$this->title = 'All Contractors';
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
            <div class="page-title">All Contractors</div>
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
                                <th data-field="name">Firm Name</th>
                                <th data-field="name">GST No.</th>
                                <th data-field="name">Address</th>
                                <th data-field="name">Contact Person</th>
                                <th data-field="name">Mobile Number</th>
                                <th data-field="name">Email Id</th>
                                <th data-field="name">Contractor Type</th>
                                <th data-field="name">Status</th>
                                <th data-field="email">Actions</th>

                            </tr>
                        </thead>
                        <tbody id="contacts_list">
                            <?php
                            if (@$clients) {
                                $i = 0;
                                foreach ($clients as $key => $user) {
                                    $type = '';
                                    $ctype = '---';
                                    if ($user->status == 1) {
                                        $status = 'Active';
                                        $class = 'green';
                                    } else {
                                        $status = 'Inactive';
                                        $class = 'red';
                                    }
                                   
                                    if ($user->contracttype == 1) {
                                        $ctype = 'Proprietorship';
                                    } elseif ($user->contracttype == 2) {
                                        $ctype = 'Partnership';
                                    } elseif ($user->contracttype == 3) {
                                        $ctype = 'Limited Liability Partnership';
                                    } elseif ($user->contracttype == 4) {
                                        $ctype = 'Pvt. Ltd. Company';
                                    } elseif ($user->contracttype == 5) {
                                        $ctype = 'Ltd. Company';
                                    }
                                    $city = Sitecontroller::actionAddress($user->city, 1);
                                    $state = Sitecontroller::actionAddress($user->state, 2);
                                    ?>
                                    <tr data-id = "<?= $user->id ?>">
                                        <td class = ""><?= $key + 1 ?></td>
                                        <td class = ""><?= $user->firm ?></td>
                                        <td class = ""><?= $user->gst ?></td>
                                        <td class = ""><?= $user->address . ' ' . $city . ' ' . $state . ' ' . $user->pcode ?></td>
                                        <td class = ""><?= $user->cperson ?></td>
                                        <td class = ""><?= $user->cnumber ?></td>
                                        <td class = ""><?= $user->cemail ?></td>
                                        <td class = ""><?= $ctype; ?></td>
                                        <td ><a href="<?= $baseURL ?>site/change-status-client?id=<?= $user->id; ?>" class = "btn <?= $class ?>"><?= $status ?></a></td>
                                        <td>
                                            <a href="<?= $baseURL ?>site/edit-client?id=<?= $user->id; ?>" class="waves-effect waves-light btn blue">Edit</a>
                                            <a href="#modal<?= $user->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>
                                            <?php if ($user->type == 3) { ?>
                                                <a href="#modalfiles<?= $user->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Products</a>
                                            <?php } ?>
                                        </td>

                                    </tr>
                                <div id="modal<?= $user->id; ?>" class="modal">
                                    <div class="modal-content">
                                        <h4>Confirmation Message</h4>
                                        <p>Are you sure you want to delete it ?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="javascript::void()" class=" modal-action modal-close waves-effect waves-green btn-flat">No</a>
                                        <a href="<?= $baseURL ?>site/delete-client?id=<?= $user->id; ?>" class=" modal-action modal-close waves-effect waves-green btn-flat">Yes</a>
                                    </div>
                                </div>
                                <div id="modalfiles<?= $user->id; ?>" class="modal">
                                    <div class="modal-content">
                                        <h4>Products</h4>
                                        <b>Cables</b>
                                        <p><?= Sitecontroller::actionProducts($user->cables,1) ?></p>
                                        <b>Lighting</b>
                                        <p><?= Sitecontroller::actionProducts($user->lighting,2) ?></p>
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
