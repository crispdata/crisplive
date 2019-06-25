<?php
/* @var $this yii\web\View */

use backend\controllers\SiteController;

$this->title = 'Add Sub Division';

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
<script>
    function GetFileSizeTender() {
        var org = document.forms["myform"]["organisation"].value;
        var dep = document.forms["myform"]["subdepartment"].value;
        var div = document.forms["myform"]["division"].value;
        var subdiv = document.forms["myform"]["subdivision"].value;
        if (org == "") {
            swal("", "Please select Organisation", "warning");
            return false;
        }
        if (dep == "") {
            swal("", "Please select Department", "warning");
            return false;
        }
        if (div == "") {
            swal("", "Please select Division", "warning");
            return false;
        }
        if (subdiv == "") {
            swal("", "Please enter Sub Division name", "warning");
            return false;
        }
    }
</script>
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
                        <form id="sort-data" name="myform" method = "post" action = "<?= $baseURL ?>site/addsubdivision" onsubmit="return GetFileSizeTender()">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                            <input type="hidden" name="id" value="<?= @$subdivision->id ?>">
                            <div class="row">
                                <div class="input-fields col s12 row">
                                    <label>Select Organisation</label>
                                    <select class="materialSelectorg browser-default" name="organisation" id="organisation" onchange="getsubdepartments(this.value)">
                                        <option value="">Select Organisation</option>
                                        <?php
                                        if (@$departments) {
                                            foreach ($departments as $_department) {
                                                ?>
                                                <option value="<?= $_department->id ?>" <?= (@$subdivision->did == $_department->id) ? 'selected' : '' ?>><?= $_department->name; ?></option>
                                                <?php
                                            }
                                        }
                                        ?>

                                    </select>
                                </div>
                                <?php if (!@$subdivision->did) { ?>
                                    <div class="input-fields col s12 row subdepartments" style="display:none">
                                        <label>Select Department</label>
                                        <select class="materialSelect validate required" name="subdepartment" id="subdepartment" onchange="getdivisions(this.value)">
                                            <option value="0" disabled selected>Select</option>
                                        </select>
                                    </div>
                                <?php } else { ?>
                                    <div class="input-fields col s12 row subdepartments">
                                        <label>Select Department</label>
                                        <select class="materialSelect validate required" name="subdepartment" id="subdepartment" onchange="getdivisions(this.value)">
                                            <?php SiteController::actionGetsubdepartmentsbyorg(@$subdivision->did, @$subdivision->direct_id); ?>
                                        </select>
                                    </div>
                                <?php } ?>

                                <?php if (!@$subdivision->direct_id) { ?>
                                    <div class="input-fields col s12 row divisions" style="display:none">
                                        <label>Select Division</label>
                                        <select class="materialSelect validate required" name="division" id="division">
                                            <option value="0" disabled selected>Select</option>
                                        </select>
                                    </div>
                                <?php } else { ?>
                                    <div class="input-fields col s12 row divisions">
                                        <label>Select Division</label>
                                        <select class="materialSelect validate required" name="division" id="division">
                                            <?php SiteController::actionGetdivisionsbydepart(@$subdivision->did, @$subdivision->direct_id, @$subdivision->div_id); ?>
                                        </select>
                                    </div>
                                <?php } ?>

                                <div class="input-field col s12 subdivision"  <?php
                                if (!@$subdivision->did) {
                                    echo 'style="display:none"';
                                } else {
                                    
                                }
                                ?>>
                                    <input id="subdivision" type="text" name = "subdivision" class="validate required" value="<?= @$subdivision->name ?>">
                                    <label for="subdivision">Sub Division Name</label>
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
