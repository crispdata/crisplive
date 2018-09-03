<?php
/* @var $this yii\web\View */

$this->title = 'Edit User';

$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
?>
<style>
    .actions{display:none!important;}    
    .steps{display:none!important;}    
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
                        <form id="create-project-form" class="col s12" method = "post" action = "<?= $baseURL ?>site/edit-user">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                            <input type="hidden" name="CreateContact[id]" value="<?= @$contact[0]->UserId; ?>" />

                            <div class="row">
                                <div class="input-field col s6">
                                    <input id="FirstName" type="text" maxlength="50" name = "CreateContact[FirstName]" class="validate required" value="<?= @$contact[0]->FirstName; ?>">
                                    <label for="FirstName">First Name</label>
                                </div>

                                <div class="input-field col s6">
                                    <input id="LastName" type="text" maxlength="50" name = "CreateContact[LastName]" class="validate required" value="<?= @$contact[0]->LastName; ?>">
                                    <label for="LastName">Last Name</label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="input-field col s6">
                                    <input id="Street" type="text" name = "CreateContact[Street]" class="validate" value="<?= @$contact[0]->Street; ?>">
                                    <label for="Street">Street</label>
                                </div>

                                <div class="input-field col s6">
                                    <input id="City" type="text" name = "CreateContact[City]" class="validate" value="<?= @$contact[0]->City; ?>">
                                    <label for="City">City</label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="input-field col s6">
                                    <input id="Email" type="email" maxlength="100" disabled="" size="30" name = "CreateContact[Email]" class="validate required" value="<?= @$contact[0]->email; ?>">
                                    <label for="Email">Email</label>
                                </div>

                                <div class="input-field col s6">
                                    <input id="Phone" type="text" maxlength="50" pattern="([0-9]|[0-9]|[0-9])" name = "CreateContact[Phone]" class="validate" value="<?= @$contact[0]->Mobile; ?>">
                                    <label for="Phone">Phone</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input id="State" type="text" name = "CreateContact[State]" class="validate" value="<?= @$contact[0]->State; ?>">
                                    <label for="State">State</label>
                                </div>

                                <div class="input-field col s6">
                                    <input id="Zip" type="text" name = "CreateContact[Zip]" class="validate" value="<?= @$contact[0]->Zip; ?>">
                                    <label for="Zip">ZIP Code</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input id="Office" type="text" name = "CreateContact[Office]" class="validate" value="<?= @$contact[0]->ContactNumber; ?>">
                                    <label for="Office">Office</label>
                                </div>

                                <div class="input-field col s6">
                                    <input id="Fax" type="text" name = "CreateContact[Fax]" class="validate" value="<?= @$contact[0]->Fax; ?>">
                                    <label for="Fax">Fax</label>
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

