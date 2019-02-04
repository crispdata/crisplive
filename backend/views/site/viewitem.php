<?php
/* @var $this yii\web\View */

$this->title = 'View Items';
$user = Yii::$app->user->identity;
$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
$stop_date = date('Y-m-d H:i:s', strtotime(@$tdetails->createdon . ' +1 day'));
?>
<style>
    .add-contact{    float: right;
                     margin-right: 15px;}    
    </style>

    <main class="mn-inner">
    <div class="row">
        <div class="col s6">
            <div class="page-title">View Items of <?= $tname; ?></div>
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

        <form id="create-item" method = "post" onsubmit="return deleteConfirm();" action = "<?= $baseURL ?>site/delete-items">
            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
            <input type="hidden" name="tid" value="<?php echo $tid; ?>">
            <?php
            if ($user->group_id == 3) {
                if ($stop_date >= date('Y-m-d H:i:s') && $tdetails->status == 0) {
                    ?>
                    <a class="waves-effect waves-light btn blue m-b-xs add-contact" href="<?= $baseURL ?>site/create-item?id=<?= $tid; ?>"> Add Item</a>
                    <input type="submit" class="waves-effect waves-light btn blue m-b-xs add-contact" name="btn_delete" value="Delete Items"/>
                    <?php
                }
            } else {
                ?>
                <a class="waves-effect waves-light btn blue m-b-xs add-contact" href="<?= $baseURL ?>site/create-item?id=<?= $tid; ?>"> Add Item</a>
                <input type="submit" class="waves-effect waves-light btn blue m-b-xs add-contact" name="btn_delete" value="Delete Items"/>
            <?php }
            ?>




            <div class="col s12 m12 l12">
                <div class="card">
                    <div class="card-content">
                        <table id = "view-items" class="responsive-table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" name="check_all" id="check_all" value=""/><label for="check_all"></label></th>
                                    <th data-field="name">Sr. No.</th>
                                    <th data-field="name">Item Sr. No. of Tender</th>
                                    <th data-field="name">Item Description</th>
                                    <th data-field="name">Units</th>
                                    <th data-field="email">Quantity</th>
                                    <th data-field="email">Make</th>
                                    <th data-field="email">CatPart Id</th>
                                    <?php
                                    if ($user->group_id == 3) {
                                        if ($stop_date >= date('Y-m-d H:i:s') && $tdetails->status == 0) {
                                            ?>
                                            <th data-field="email">Actions</th>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <th data-field="email">Actions</th>
                                    <?php }
                                    ?>

                                </tr>
                            </thead>
                            <tbody id="contacts_list">
                                <?php
                                if (@$idetails) {
                                    $i = 0;
                                    foreach ($idetails as $key => $idetail) {
                                        ?>
                                        <tr data-id = "<?= $idetail->id ?>">
                                            <td align="center"><input type="checkbox" name="selected_id[]" class="checkbox" id="check<?php echo $idetail->id; ?>" value="<?php echo $idetail->id; ?>"/><label for="check<?php echo $idetail->id; ?>"></label></td> 
                                            <td class = ""><?= $key + 1 ?></td>
                                            <td class = ""><?= $idetail->itemtender ?></td>
                                            <td class = ""><?= $idetail->description ?></td>
                                            <td class = ""><?= $idetail->units ?></td>
                                            <td class = ""><?= $idetail->quantity ?></td>
                                            <td class = ""><?= $idetail->make ?></td>
                                            <td class = ""><?= $idetail->makeid ?></td>

                                            <?php
                                            if ($user->group_id == 9) {
                                                if ($stop_date >= date('Y-m-d H:i:s') && $tdetails->status == 0) {
                                                    ?>
                                                    <td>
                                                        <a href="<?= $baseURL ?>site/edit-item?id=<?= $idetail->id; ?>" class="waves-effect waves-light btn blue">Edit</a>
                                                        <a href="#modal<?= $idetail->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>
                                                    </td>
                                                    <?php
                                                }
                                            } else {
                                                ?>

                                                <td><?php
                                                    if ($idetail->status == 1) {
                                                        ?>
                                                        <a class="waves-effect waves-light btn green">Approved</a>
                                                    <?php } else { ?>
                                                        <a class="waves-effect waves-light btn blue" onclick='approveitem(<?php echo $idetail->id; ?>)'>Approve</a>
                                                    <?php }
                                                    ?>
                                                    <?php if ($tdetails->aoc_status != 1) { ?>
                                                        <a href="<?= $baseURL ?>site/edit-item?id=<?= $idetail->id; ?>" class="waves-effect waves-light btn blue">Edit</a>
                                                        <a href="#modal<?= $idetail->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>
                                                    <?php } ?>
                                                </td>                               
                                            <?php }
                                            ?>

                                        </tr>
                                    <div id="modal<?= $idetail->id; ?>" class="modal">
                                        <div class="modal-content">
                                            <h4>Confirmation Message</h4>
                                            <p>Are you sure you want to delete it ?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="javascript::void()" class=" modal-action modal-close waves-effect waves-green btn-flat">No</a>
                                            <a href="<?= $baseURL ?>site/delete-item?id=<?= @$idetail->id; ?>&tid=<?= @$tid; ?>" class=" modal-action modal-close waves-effect waves-green btn-flat">Yes</a>
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
        </form>
    </div>
</main>
