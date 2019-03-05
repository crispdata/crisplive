<?php
/* @var $this yii\web\View */

$this->title = 'Add New Price';

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
                        <form id="create-project-form" class="col s12" method = "post" action = "<?= $baseURL ?>products/create-price">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                            <input type="hidden" value="<?= @$size->id; ?>" name="id">
                            <div class="row">
                                <div class="input-fields col s12 row">
                                    <label>Select Type</label>
                                    <select class="validate required" name="mtype" id="mtype">
                                        <option value="" disabled selected>Select</option>
                                        <option value="1" <?= (@$size->mtype == 1) ? 'selected' : '' ?> >LT</option>
                                        <option value="2" <?= (@$size->mtype == 2) ? 'selected' : '' ?>>HT</option>
                                    </select>
                                </div>
                                <div class="input-fields col s12 row">
                                    <label>Select Type</label>
                                    <select class="validate required" name="mtypeone" id="mtypeone" onchange="getsubpricetypes(this.value)">
                                        <option value="" disabled selected>Select</option>
                                        <option value="1" <?= (@$size->mtypeone == 1) ? 'selected' : '' ?> >Cables</option>
                                        <option value="2" <?= (@$size->mtypeone == 2) ? 'selected' : '' ?>>Lighting</option>
                                        <!--option value="3" <?= (@$size->mtypeone == 3) ? 'selected' : '' ?>>Fans</option>
                                        <option value="4" <?= (@$size->mtypeone == 4) ? 'selected' : '' ?>>Accessories</option>
                                        <option value="5" <?= (@$size->mtypeone == 5) ? 'selected' : '' ?>>Wire</option>
                                        <option value="6" <?= (@$size->mtypeone == 6) ? 'selected' : '' ?>>DB/MCB/MCCB/Timers</option>
                                        <option value="7" <?= (@$size->mtypeone == 7) ? 'selected' : '' ?>>Transformers</option>
                                        <option value="8" <?= (@$size->mtypeone == 8) ? 'selected' : '' ?>>Cable Jointing Kits</option>
                                        <option value="9" <?= (@$size->mtypeone == 9) ? 'selected' : '' ?>>Panels</option>
                                        <option value="10" <?= (@$size->mtypeone == 10) ? 'selected' : '' ?>>ACB</option>
                                        <option value="11" <?= (@$size->mtypeone == 11) ? 'selected' : '' ?>>VCB</option>
                                        <option value="12" <?= (@$size->mtypeone == 12) ? 'selected' : '' ?>>Substations</option>
                                        <option value="13" <?= (@$size->mtypeone == 13) ? 'selected' : '' ?>>Motors</option-->
                                    </select>
                                </div>
                                <div class="input-fields col s12 row" id="second" style="<?php
                                if (!isset($size->mtypetwo)) {
                                    echo "display:none";
                                }
                                ?>" >
                                    <label>Select Sub Type</label>
                                    <select class="validate required" required="" name="mtypetwo" id="mtypetwo" onchange="getparentonetypes(this.value)">
                                        <option value="" disabled selected>Select</option>
                                        <option value="1" <?= (@$size->mtypetwo == 1) ? 'selected' : '' ?> >Copper</option>
                                        <option value="2" <?= (@$size->mtypetwo == 2) ? 'selected' : '' ?>>Aluminium</option>

                                    </select>
                                </div>
                                <div class="input-fields col s12 row" id="third" style="<?php
                                if (!isset($size->mtypethree)) {
                                    echo "display:none";
                                }
                                ?>">
                                    <label>Select Sub Type</label>
                                    <select class="validate required" required="" name="mtypethree" id="mtypethree" onchange="getparenttwotypes(this.value)">
                                        <option value="" disabled selected>Select</option>
                                        <option value="1" <?= (@$size->mtypethree == 1) ? 'selected' : '' ?> >Armoured</option>
                                        <option value="2" <?= (@$size->mtypethree == 2) ? 'selected' : '' ?>>Unarmoured</option>

                                    </select>
                                </div>
                                <div class="input-fields col s12 row" id="fourth"  <?php if (!isset($size->mtypefour)) { ?>style="display:none;"<?php } ?>>
                                    <?php if (isset($size->mtypefour)) { ?>
                                        <label>Select Sub Type</label>
                                        <select class="validate required materialsize" required="" name="mtypefour" id="mtypefour">
                                            <?php
                                            if (isset($sizes) && count($sizes)) {
                                                foreach ($sizes as $_sizes) {
                                                    ?>
                                                    <option value="<?= $_sizes->id ?>" <?= ($size->mtypefour == $_sizes->id) ? 'selected' : '' ?>><?= $_sizes->size ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    <?php } else { ?>
                                        <label>Select Sub Type</label>
                                        <select class="validate required materialsize" required="" name="mtypefour[]" multiple id="mtypefour">
                                        </select>
                                    <?php }
                                    ?>

                                </div>
                                <div class="input-fields col s12 row" id="fifth" style="<?php
                                if (!isset($size->mtypefive)) {
                                    echo "display:none";
                                }
                                ?>">
                                    <label>Select Sub Type</label>
                                    <select class="validate required" required="" name="mtypefive[]" multiple id="mtypefive">
                                        <option value="">Select Core</option>
                                        <option value="1" <?= (@$size->mtypefive == 1) ? 'selected' : '' ?>>1 Core</option>
                                        <option value="2" <?= (@$size->mtypefive == 2) ? 'selected' : '' ?>>2 Core</option>
                                        <option value="3" <?= (@$size->mtypefive == 3) ? 'selected' : '' ?>>3 Core</option>
                                        <option value="4" <?= (@$size->mtypefive == 4) ? 'selected' : '' ?>>3.5 Core</option>
                                        <option value="5" <?= (@$size->mtypefive == 5) ? 'selected' : '' ?>>4 Core</option>
                                        <option value="6" <?= (@$size->mtypefive == 6) ? 'selected' : '' ?>>5 Core</option>
                                        <option value="7" <?= (@$size->mtypefive == 7) ? 'selected' : '' ?>>6 Core</option>
                                        <option value="8" <?= (@$size->mtypefive == 8) ? 'selected' : '' ?>>7 Core</option>
                                        <option value="9" <?= (@$size->mtypefive == 9) ? 'selected' : '' ?>>8 Core</option>
                                        <option value="10" <?= (@$size->mtypefive == 10) ? 'selected' : '' ?>>10 Core</option>
                                        <option value="11" <?= (@$size->mtypefive == 11) ? 'selected' : '' ?>>12 Core</option>
                                        <option value="12" <?= (@$size->mtypefive == 12) ? 'selected' : '' ?>>14 Core</option>
                                        <option value="13" <?= (@$size->mtypefive == 13) ? 'selected' : '' ?>>16 Core</option>
                                        <option value="14" <?= (@$size->mtypefive == 14) ? 'selected' : '' ?>>19 Core</option>
                                        <option value="15" <?= (@$size->mtypefive == 15) ? 'selected' : '' ?>>24 Core</option>
                                        <option value="16" <?= (@$size->mtypefive == 16) ? 'selected' : '' ?>>27 Core</option>
                                        <option value="17" <?= (@$size->mtypefive == 17) ? 'selected' : '' ?>>30 Core</option>
                                        <option value="18" <?= (@$size->mtypefive == 18) ? 'selected' : '' ?>>37 Core</option>
                                        <option value="19" <?= (@$size->mtypefive == 19) ? 'selected' : '' ?>>44 Core</option>
                                        <option value="20" <?= (@$size->mtypefive == 20) ? 'selected' : '' ?>>61 Core</option>
                                    </select>
                                </div>

                                <div class="input-field col s12" id="prices" style="<?php
                                if (!isset($size->price)) {
                                    echo "display:none";
                                }
                                ?>">
                                     <input id="price" type="text" name = "price" class="validate required" value="<?= @$size->price ?>">
                                     <label for="price">Price</label>
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