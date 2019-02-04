<?php
/* @var $this yii\web\View */

$this->title = 'Create New User';

$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
?>
<style>
    .actions{display:none!important;}    
    .steps{display:none!important;}   
    .select-wrapper input.select-dropdown, .select-wrapper input.select-dropdown:disabled {
        border-color: unset;
    }
    .row{margin-bottom: 0px;}
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
    $(function () {
        $("#Phone").keyup(function () {
            $("#Phone").val(this.value.match(/[0-9]*/));
        });
    });
</script>
<main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="page-title"><?= $this->title ?></div>
        </div>



        <div class="col s12 m12 l12">
            <div class="card">
                <div class="card-content">

                    <div class="row">
                        <form id="create-project-form" class="col s12" method = "post" action = "<?= $baseURL ?>site/create-user">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                            <div class="row">
                                <div class="input-field col s6">
                                    <input id="FirstName" type="text" maxlength="50" name = "CreateUser[username]" class="validate required" value="">
                                    <label for="FirstName">Username</label>
                                </div>
                                
                                 <div class="input-field col s6">
                                     <input id="Email" type="email" autocomplete="off" maxlength="100" size="30" name = "CreateUser[Email]" class="validate required" value="">
                                    <label for="Email">Email</label>
                                </div>

                            </div>

                            <div class="row">
                                <div class="input-field col s6">
                                        <select class="validate required" required="" name="CreateUser[group_id]" id="group">
                                            <option value="" disabled selected>Select Group</option>
                                            <option value="3" >User</option>
                                            <option value="2" >Admin</option>
                                        </select>
                                    </div>

                                 <div class="input-field col s6">
                                     <input id="password" type="password" autocomplete="off" name = "CreateUser[password]" class="validate" value="">
                                    <label for="password">Password</label>
                                </div>
                            </div>


                            <input class="waves-effect waves-light btn blue m-b-xs" type="submit" value="Submit">

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

