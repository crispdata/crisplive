<?php
/* @var $this yii\web\View */

$this->title = 'Create New Make';

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
                        <form id="create-project-form" class="col s12" method = "post" action = "<?= $baseURL ?>site/create-make-em">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                            <input type="hidden" value="<?= @$make->id; ?>" name="id">
                            <div class="row">
                                <div class="input-fields col s12 row">
                                    <label>Select Type</label>
                                    <select class="validate required" name="mtype" id="mtype">
                                         <option value="" disabled selected>Select</option>
                                         <option value="1" <?= (@$make->mtype == 1)?'selected':'' ?> >Cables</option>
                                         <option value="2" <?= (@$make->mtype == 2)?'selected':'' ?>>Lighting</option>
                                         <option value="3" <?= (@$make->mtype == 3)?'selected':'' ?>>Fans</option>
                                         <option value="4" <?= (@$make->mtype == 4)?'selected':'' ?>>Accessories</option>
                                         <option value="5" <?= (@$make->mtype == 5)?'selected':'' ?>>Wire</option>
                                         <option value="6" <?= (@$make->mtype == 6)?'selected':'' ?>>DB/MCB/MCCB/Timers</option>
                                         <option value="7" <?= (@$make->mtype == 7)?'selected':'' ?>>Transformers</option>
                                         <option value="8" <?= (@$make->mtype == 8)?'selected':'' ?>>Cable Jointing Kits</option>
                                         <option value="9" <?= (@$make->mtype == 9)?'selected':'' ?>>Panels</option>
                                         <option value="10" <?= (@$make->mtype == 10)?'selected':'' ?>>ACB</option>
                                         <option value="11" <?= (@$make->mtype == 11)?'selected':'' ?>>VCB</option>
                                         <option value="12" <?= (@$make->mtype == 12)?'selected':'' ?>>Substations</option>
                                         <option value="13" <?= (@$make->mtype == 13)?'selected':'' ?>>Motors</option>
                                    </select>
                                </div>

                                <div class="input-field col s12">
                                    <input id="make" type="text" name = "make" class="validate required" value="<?= @$make->make; ?>">
                                    <label for="make">Make Name</label>
                                </div>
                                <div class="input-field col s12">
                                    <input id="email" type="email" name = "email" class="validate required" value="<?= @$make->email; ?>">
                                    <label for="email">Email</label>
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

