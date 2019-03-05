<?php
/* @var $this yii\web\View */

$this->title = 'Create New Accessory';

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
                        <form id="create-project-form" class="col s12" method = "post" action = "<?= $baseURL ?>products/create-accessory">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                            <input type="hidden" value="<?= @$accessory->id; ?>" name="id">
                            <div class="row">
                              

                                <div class="input-field col s12">
                                    <input id="text" type="text" name = "text" class="validate required" value="<?= @$accessory->text; ?>">
                                    <label for="text">Text</label>
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