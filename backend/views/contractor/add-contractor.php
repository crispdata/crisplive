<?php
/* @var $this yii\web\View */

$this->title = 'Add Contractor';

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
                        <form id="create-project-form" class="col s12" method = "post" action = "<?= $baseURL ?>contractor/add-contractor">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                            <input type="hidden" value="<?= @$contractor->id; ?>" name="id">
                            <div class="row">
                                <div class="input-field col s6">
                                    <input id="firm" type="text" name = "firm" class="validate required" value="<?= @$contractor->firm; ?>">
                                    <label for="firm">Name of Firm/CO</label>
                                </div>
                               
                              

                                <div class="input-field col s6">
                                    <input id="name" type="text" name = "name" class="validate required" value="<?= @$contractor->name; ?>">
                                    <label for="name">Name</label>
                                </div>

                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <textarea id="address" name="address" class="materialize-textarea required"><?= @$contractor->address; ?></textarea>
                                    <label for="address">Address</label>
                                </div>
                               
                              

                                <div class="input-field col s6">
                                    <input id="contact" type="text" name = "contact" class="validate required" value="<?= @$contractor->contact; ?>">
                                    <label for="contact">Contact No.</label>
                                </div>

                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input id="email" type="text" name = "email" class="validate required" value="<?= @$contractor->email; ?>">
                                    <label for="email">Email-Id</label>
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