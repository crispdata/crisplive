<?php
/* @var $this yii\web\View */

use backend\controllers\SiteController;

$this->title = 'All Mail Logs';
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
            <div class="page-title">All Mail Logs</div>
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
                                <th data-field="name">Client</th>
                                <th data-field="name">Client Type</th>
                                <th data-field="name" width="250px">Tenders</th>
                                <th data-field="name">Files</th>
                                <th data-field="name">Sent Date</th>
                                <th data-field="name">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="contacts_list">
                            <?php
                            if (@$logs) {
                                $i = 0;
                                foreach ($logs as $key => $_log) {
                                    $type = '';
                                    $client = \common\models\Clients::find()->where(['id' => $_log->cid])->one();
                                    if (@$client->type == 1) {
                                        $type = 'Manufacturer';
                                    } elseif (@$client->type == 2) {
                                        $type = 'Contractor';
                                    } elseif (@$client->type == 3) {
                                        $type = 'Dealer';
                                    } else {
                                        $type = 'Supplier';
                                    }
                                    $tenderids = '';
                                    $tids = explode(',', $_log->tid);
                                    $tids = array_unique($tids);
                                    $total = count($tids);
                                    if ($tids) {
                                        $t = 1;
                                        foreach ($tids as $_tid) {
                                            $tdetail = \common\models\Tender::find()->where(['id' => $_tid])->one();
                                            if ($total == $t) {
                                                $tenderids .= $tdetail->tender_id;
                                            } else {
                                                $tenderids .= $tdetail->tender_id . ', ';
                                            }
                                            $t++;
                                        }
                                    }
                                    ?>
                                    <tr data-id = "<?= $_log->id ?>">
                                        <td class = ""><?= $key + 1 ?></td>
                                        <td class = ""><?= @$client->firm ?></td>
                                        <td class = ""><?= $type ?></td>
                                        <td class = "tenderids"><?= $tenderids ?></td>
                                        <td class = "files">
                                            <?php
                                            $files = explode(',', $_log->filename);
                                            $filepaths = explode(',', $_log->filepath);
                                            if ($files) {
                                                foreach ($files as $k=>$_file) {
                                                    if (strpos($filepaths[$k], 'aadhdata') !== false) {
                                                        ?>
                                                        <a href="<?= $filepaths[$k] ?>" style="word-wrap: break-word;"><?= $_file ?></a><br>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <a href="<?= 'https://crispdata.co.in/backend/web/pdf/' . $_file ?>" style="word-wrap: break-word;"><?= $_file ?></a><br>
                                                        <?php
                                                    }
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td class=""><?= date('d F Y', strtotime($_log->createdon)); ?></td>
                                        <td class=""><a onclick="resendmail('<?= $client->cemail; ?>', '<?= $_log->filename ?>', '<?= $_log->filepath ?>')" class="waves-effect waves-light btn blue">Resend Mail</a></td>

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