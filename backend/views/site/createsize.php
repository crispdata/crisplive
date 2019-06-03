<?php
/* @var $this yii\web\View */

$this->title = 'Create New Size';

$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
?>
<style>
    .actions{display:none!important;}    
    .steps{display:none!important;}    
    .input-fields label.error {
        color: #F44336;
        position: static;
        top: .8rem;
        left: .75rem;
        font-size: .8rem;
        cursor: text;
        -webkit-transition: .2s ease-out;
        -moz-transition: .2s ease-out;
        -o-transition: .2s ease-out;
        -ms-transition: .2s ease-out;
        transition: .2s ease-out;
    }
    .select-wrapper input.select-dropdown, .select-wrapper input.select-dropdown:disabled {
        border-color: unset;
    }
    table.scroll tbody {
        height: 100px;
        overflow-y: auto;
        overflow-x: hidden;
    }
    table.scroll tbody,
    table.scroll thead { display: block; }
    thead tr th { 
        width:205px;
        /* text-align: left; */
    }
    tbody tr td { 
        width:205px;
        /* text-align: left; */
    }
    .input-field{height:65px;}
    .waves-input-wrapper{width: 105px;
                         padding-left: 24px;}
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
                        <form id="create-project-form" class="col s12" method = "post" action = "<?= $baseURL ?>site/create-size">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                            <input type="hidden" value="<?= @$size->id; ?>" name="id">
                            <div class="row">
                                <div class="input-fields col s12 row">
                                    <label>Select Type</label>
                                    <select class="validate required" name="mtypeone" id="mtypeone" onchange="getsubtypes(this.value)">
                                        <option value="" disabled selected>Select</option>
                                        <option value="1" <?= (@$size->mtypeone == 1) ? 'selected' : '' ?> >Cables</option>
                                        <option value="2" <?= (@$size->mtypeone == 2) ? 'selected' : '' ?>>Lighting</option>
                                        <option value="3" <?= (@$size->mtypeone == 3) ? 'selected' : '' ?>>Fans</option>
                                        <option value="4" <?= (@$size->mtypeone == 4) ? 'selected' : '' ?>>Accessories</option>
                                        <option value="5" <?= (@$size->mtypeone == 5) ? 'selected' : '' ?>>Wire</option>
                                        <option value="6" <?= (@$size->mtypeone == 6) ? 'selected' : '' ?>>DB/MCB/MCCB/Timers</option>
                                        <option value="7" <?= (@$size->mtypeone == 7) ? 'selected' : '' ?>>Transformers</option>
                                        <option value="8" <?= (@$size->mtypeone == 8) ? 'selected' : '' ?>>Cable Jointing Kits</option>
                                        <option value="9" <?= (@$size->mtypeone == 9) ? 'selected' : '' ?>>Panels</option>
                                        <option value="10" <?= (@$size->mtypeone == 10) ? 'selected' : '' ?>>ACB</option>
                                        <option value="11" <?= (@$size->mtypeone == 11) ? 'selected' : '' ?>>VCB</option>
                                        <option value="12" <?= (@$size->mtypeone == 12) ? 'selected' : '' ?>>Substations</option>
                                        <option value="13" <?= (@$size->mtypeone == 13) ? 'selected' : '' ?>>Motors</option>
                                    </select>
                                </div>
                                <div class="input-fields col s12 row" id="second" style="<?php
                                if (!isset($size->mtypetwo)) {
                                    echo "display:none";
                                }
                                ?>" >
                                    <label>Select Sub Type</label>
                                    <select class="validate required" required="" name="mtypetwo" id="mtypetwo" onchange="getsubsubtypes(this.value)">
                                        <option value="" disabled selected>Select</option>
                                        <option value="1" <?= (@$size->mtypetwo == 1) ? 'selected' : '' ?> >Copper</option>
                                        <option value="2" <?= (@$size->mtypetwo == 2) ? 'selected' : '' ?>>Aluminium</option>
                                        <option value="3" <?= (@$size->mtypetwo == 3) ? 'selected' : '' ?>>ABC Cable</option>

                                    </select>
                                </div>
                                <div class="input-fields col s12 row" id="third" style="<?php
                                if (!isset($size->mtypethree)) {
                                    echo "display:none";
                                }
                                ?>">
                                    <label>Select Sub Type</label>
                                    <select class="validate required" required="" name="mtypethree" id="mtypethree">
                                        <option value="" disabled selected>Select</option>
                                        <option value="1" <?= (@$size->mtypethree == 1) ? 'selected' : '' ?> >Armoured</option>
                                        <option value="2" <?= (@$size->mtypethree == 2) ? 'selected' : '' ?>>Unarmoured</option>

                                    </select>
                                </div>

                                <div class="input-field col s12">
                                    <input id="size" type="text" name = "size" class="validate required" value="<?= @$size->size; ?>">
                                    <label for="size">Size</label>
                                </div>

                            </div>



                            <input class="waves-effect waves-light btn blue m-b-xs" name="submit" type="submit" value="Submit">

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>

    $(document).ready(function () {
        // for HTML5 "required" attribute
        $("select[required]").css({
            display: "inline",
            height: 0,
            padding: 0,
            width: 0
        });
    });
</script>