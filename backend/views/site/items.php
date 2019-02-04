<?php
/* @var $this yii\web\View */

$this->title = 'Manage Items';

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
            <div class="page-title">Manage Items</div>
        </div>

        <a class="waves-effect waves-light btn blue m-b-xs add-contact" href="<?= $baseURL ?>site/create-item"> Add Item</a>
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
                                <th data-field="name">Tender Id</th>
                                <th data-field="email">Item Description</th>
                                <th data-field="email">Quantity</th>
                                <th data-field="email">Make</th>
                                <th data-field="edit">Make Id</th>
                            </tr>
                        </thead>
                        <tbody id="contacts_list">
                            <?php
                            if (@$items) {
                                $i = 0;
                              
                                foreach ($items as $key => $item) {
                                   $tender =  common\models\Tender::findOne($item->tender_id);
                                    ?>
                                    <tr data-id = "<?= $item->user_id ?>">
                                        <td class = ""><?= $key + 1 ?></td>
                                        <td class = ""><?= $tender->tender_id ?></td>
                                    </tr>

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
