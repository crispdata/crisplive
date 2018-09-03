<?php
/* @var $this yii\web\View */

$this->title = 'Manage Tenders';

$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
?>
<style>
    .add-contact{    float: right;
                     margin-right: 15px;}    
    </style>

    <main class="mn-inner">
    <div class="row">
        <div class="col s6">
            <div class="page-title">Manage Tenders</div>
        </div>

        <a class="waves-effect waves-light btn blue m-b-xs add-contact" href="<?= $baseURL ?>site/create-tender"> Add Tender</a>
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
                                <th data-field="email">Email</th>
                                <th data-field="email">Password</th>
                                <th data-field="email">Active</th>
                                <th data-field="edit">Action</th>
                            </tr>
                        </thead>
                        <tbody id="contacts_list">
                            <?php
                            if ($contacts) {
                                $i = 0;
                                foreach ($contacts as $key => $contact) {
                                    ?>
                                    <tr data-id = "<?= $contact->UserId ?>">
                                        <td class = ""><?= $key + 1 ?></td>
                                        <td class = ""><?= $contact->username ?></td>
                                        <td class = ""><?= $contact->email ?></td>
                                        <td class = ""><a class="tooltipped" data-position="right" data-delay="50" data-tooltip="<?= $contact->password; ?>"><i class="material-icons">vpn_key</a></i></td>
                                        <td class = "">
                                            <input id="statususer<?= $contact->UserId ?>" onclick="changestatus(<?= $contact->UserId ?>)" type="checkbox" <?php
                                            if ($contact->status == 10) {
                                                echo "checked";
                                            } else {
                                                
                                            }
                                            ?> name="check" />
                                            <label for="statususer<?= $contact->UserId ?>"></label>
                                        </td>

                                        <td>

                                            <a href = "<?= $baseURL . 'site/edit-user?id=' . $contact->UserId ?>" class="waves-effect waves-light btn blue">Edit</a>


                                                                                                                                                                                                                                                                                                                                                                    <!--a href="#modal<?= $i; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a-->


                                        </td>
                                    </tr>
                                <div id="modal<?= $i; ?>" class="modal">
                                    <div class="modal-content">
                                        <h4>Confirmation Message</h4>
                                        <p>Are you sure you want to delete it ?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="javascript::void()" class=" modal-action modal-close waves-effect waves-green btn-flat">No</a>
                                        <a href="<?php echo Yii::$app->urlManager->createUrl(['site/delete-user', 'id' => $contact->UserId]); ?>" class=" modal-action modal-close waves-effect waves-green btn-flat">Yes</a>
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
