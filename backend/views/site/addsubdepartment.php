<?php
/* @var $this yii\web\View */

$this->title = 'Add Department';

$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
?>
<style>
    .actions{display:none!important;}    
    .steps{display:none!important;}    
    .select-wrapper input.select-dropdown, .select-wrapper input.select-dropdown:disabled {
        border-color: unset;
    }
</style>
<main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="page-title"><?= $this->title ?></div>
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

                    <div class="row">
                        <form id="sort-data" method = "post" action = "<?= $baseURL ?>site/addsubdepartment">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                            <input type="hidden" name="id" value="<?= @$subdepart->id ?>">
                            <div class="row">
                                <div class="input-fields col s12 row">
                                    <label>Select Organisation</label>
                                    <select class="materialSelectorg browser-default" required="" name="organisation" id="organisation">
                                        <option value="">Select Organisation</option>
                                        <?php
                                        if (@$departments) {
                                            foreach ($departments as $_department) {
                                                ?>
                                                <option value="<?= $_department->id ?>" <?= (@$subdepart->did == $_department->id) ? 'selected' : '' ?>><?= $_department->name; ?></option>
                                                <?php
                                            }
                                        }
                                        ?>

                                    </select>
                                </div>
                                <div class="input-field col s12">
                                    <input id="subdepartment" type="text" name = "subdepartment" required="" class="validate required" value="<?= @$subdepart->name ?>">
                                    <label for="subdepartment">Department Name</label>
                                </div>
                            </div>
                            <input class="btn blue m-b-xs" name="submit" type="submit" value="Submit">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
