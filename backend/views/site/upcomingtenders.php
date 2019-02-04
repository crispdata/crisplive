<?php
/* @var $this yii\web\View */

use backend\controllers\SiteController;

$this->title = 'Upcoming Tenders';
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
            <div class="page-title">Upcoming tenders by bid submission end date</div>
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
                                <th data-field="name">Command</th>
                                <th data-field="name">CE</th>
                                <th data-field="name">CWE</th>
                                <th data-field="name">Tender Ref. no.</th>
                                <th data-field="email">Tender Id</th>
                                <th data-field="email">Bid end date</th>
                                <th data-field="email">Status</th>
                                <th data-field="email">Actions</th>

                            </tr>
                        </thead>
                        <tbody id="contacts_list">
                            <?php
                            if (@$tenders) {
                                $i = 0;
                                foreach ($tenders as $key => $tender) {
                                    if ($tender->status == 1) {
                                        $status = 'Approved';
                                        $class = 'green';
                                    } else {
                                        $status = 'Unapproved';
                                        $class = 'red';
                                    }
                                    $stop_date = date('Y-m-d H:i:s', strtotime($tender->createdon . ' +1 day'));
                                    ?>
                                    <tr data-id = "<?= $tender->tender_id ?>">
                                        <td class = ""><?= $key + 1 ?></td>
                                        <td class = ""><?= SiteController::actionGetcommand($tender->command); ?></td>
                                        <td class = ""><?= SiteController::actionGetcebyid($tender->cengineer); ?></td>
                                        <td class = ""><?= SiteController::actionGetcwebyid($tender->cwengineer); ?></td>
                                        <td class = ""><?= $tender->reference_no ?></td>
                                        <td class = ""><?= $tender->tender_id ?></td>
                                        <td class = ""><?= $tender->bid_end_date ?></td>
                                        <td ><a class = "btn <?= $class ?>"><?= $status ?></a></td>
                                        <td>
                                            <?php
                                            if ($user->group_id == 3) {
                                                if ($stop_date >= date('Y-m-d H:i:s') && $tender->status == 0) {
                                                    ?>
                                                    <a href="<?= $baseURL ?>site/create-tender?id=<?= $tender->id; ?>" class="waves-effect waves-light btn blue">Edit</a>
                                                    <a href="#modal<?= $tender->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>
                                                    <a href="<?= $baseURL ?>site/create-item?id=<?= $tender->id; ?>" class="waves-effect waves-light btn blue">Add Item</a>

                                                    <?php
                                                }
                                            } else {
                                                if ($tender->status == 1) {
                                                    ?>
                                                    <a class="waves-effect waves-light btn green">Approved</a>
                                                <?php } else { ?>
                                                    <a class="waves-effect waves-light btn blue" onclick='approvetender(<?php echo $tender->id; ?>)'>Approve</a>
            <?php }
            ?>
                                                <a href="<?= $baseURL ?>site/create-tender?id=<?= $tender->id; ?>" class="waves-effect waves-light btn blue">Edit</a>
                                                <a href="#modal<?= $tender->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>
                                                <a href="<?= $baseURL ?>site/create-item?id=<?= $tender->id; ?>" class="waves-effect waves-light btn blue">Add Item</a>
        <?php }
        ?>

                                            <a href="<?= $baseURL ?>site/view-items?id=<?= $tender->id; ?>" class="waves-effect waves-light btn blue">View Items</a>



                                        </td>

                                    </tr>
                                <div id="modal<?= $tender->id; ?>" class="modal">
                                    <div class="modal-content">
                                        <h4>Confirmation Message</h4>
                                        <p>Are you sure you want to delete it ?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="javascript::void()" class=" modal-action modal-close waves-effect waves-green btn-flat">No</a>
                                        <a href="<?= $baseURL ?>site/delete-tender?id=<?= @$tender->id; ?>" class=" modal-action modal-close waves-effect waves-green btn-flat">Yes</a>
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
