<?php
/* @var $this yii\web\View */

use backend\controllers\SiteController;

$this->title = 'Edit Item';
$user = Yii::$app->user->identity;
$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
?>

<style>
    .actions{display:none!important;}    
    .steps{display:none!important;}  
    .select-wrapper input.select-dropdown, .select-wrapper input.select-dropdown:disabled {
        border-color: unset;
    }
    .select2-container{width:100%!important;}
    .select2-container--default .select2-selection--multiple .select2-selection__choice{margin-top:10px;}
    .select2-container .select2-selection--multiple .select2-selection__rendered{white-space:normal!important;}
    .select2-container--default .select2-results__option[aria-selected=true]{background-color: #00ACC1;}
    .select2-dropdown{margin-top:0px;}
    .row{margin-bottom: 0px;}
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
    .multiselect {
        width: 150px;
    }

    .selectBox {
        position: relative;
    }

    .selectBox select {
        width: 100%;
        font-weight: bold;
    }

    .overSelect {
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
    }

    .checkboxes {
        display: none;
        border: 1px #dadada solid;
        padding: 5px;
        position: absolute;
        width: 150px;
        margin-top: -21px;
        float: left;
        /* z-index: 9999999999; */
        background: #2196F3;
        /* color: #000; */
    }

    .checkboxes label {
        display: block;
        font-size:unset;
        margin-right: 10px;
        padding-left: 20px;
        /* margin-top: 10px; */
        position: initial;
        color: #000;
    }
    .checkboxes input[type="checkbox"]:not(:checked), input[type="checkbox"]:checked{
        position: initial;
        left: unset;
        opacity: 1;
        display:none;
    }

    .checkboxes label:hover {
        background-color: #1e90ff;
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

                        <form id="create-project-form" class="col s12" method = "post" action = "<?= $baseURL ?>site/edit-item?id=<?= @$item->id; ?>" enctype="multipart/form-data">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />

                            <input type="hidden" name="id" value="<?= @$item->id; ?>" />
                            <input type="hidden" name="item_id" value="<?= @$item->item_id; ?>" />
                            <?php if ($parentitems->tenderone != '') { ?>
                                <div class="input-fields col s12 row">
                                    <label>Select type of work</label>
                                    <select class="validate required materialSelect" required="" name="tenderone" id="tenderone" onchange="getdata(this.value)">
                                        <option value="" disabled selected>Select</option>
                                        <option value="1" <?php
                                        if ($parentitems->tenderone == 1) {
                                            echo "selected";
                                        }
                                        ?>>E/M</option>
                                        <!--option value="2">B/R</option-->
                                    </select>
                                </div>

                            <?php } else { ?>
                                <div class="input-fields col s12 row">
                                    <label>Select type of work</label>
                                    <select class="validate required materialSelect" required="" name="tenderone" id="tenderone" onchange="getdata(this.value)">
                                        <option value="" disabled selected>Select</option>
                                        <option value="1">E/M</option>
                                        <!--option value="2">B/R</option-->
                                    </select>
                                </div>

                            <?php } ?>
                            <?php if ($parentitems->tendertwo != '' || $parentitems->tendertwo != 0) { ?>
                                <div id="second">
                                    <div class="input-fields col s12 row">
                                        <label>Select Sub Type</label>
                                        <select class="validate required materialSelect" required="" name="tendertwo" id="tendertwo" onchange="getseconddata(this.value)">
                                            <?php SiteController::actionGettendertwo($parentitems->tenderone, $parentitems->tendertwo); ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div id="second" style="display: none;">
                                    <div class="input-fields col s12 row">
                                        <label>Select Sub Type</label>
                                        <select class="validate required materialSelect" required="" name="tendertwo" id="tendertwo" onchange="getseconddata(this.value)">
                                            <option value="" disabled selected>Select</option>
                                        </select>
                                    </div>
                                </div>

                            <?php } ?>
                            <?php if ($parentitems->tenderthree != '' || $parentitems->tenderthree != 0) { ?>
                                <div id="third">
                                    <div class="input-fields col s12 row">
                                        <label>Select Sub Type</label>
                                        <select class="validate required materialSelect" required="" name="tenderthree" id="tenderthree" onchange="getthirddata(this.value)">
                                            <?php SiteController::actionGettenderthree($parentitems->tendertwo, $parentitems->tenderthree); ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div id="third" style="display: none;">
                                    <div class="input-fields col s12 row">
                                        <label>Select Sub Type</label>
                                        <select class="validate required materialSelect" required="" name="tenderthree" id="tenderthree" onchange="getthirddata(this.value)">
                                            <option value="" disabled selected>Select</option>
                                        </select>
                                    </div>
                                </div>

                            <?php } ?>
                            <?php if ($parentitems->tenderfour != '' || $parentitems->tenderfour != 0) { ?>
                                <div id="fourth">
                                    <div class="input-fields col s12 row">
                                        <label>Select Sub Type</label>
                                        <select class="validate required materialSelect" required="" name="tenderfour" id="tenderfour" onchange="getfourdata(this.value)">
                                            <?php SiteController::actionGettenderfour($parentitems->tenderthree, $parentitems->tenderfour); ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div id="fourth" style="display: none;">
                                    <div class="input-fields col s12 row">
                                        <label>Select Sub Type</label>
                                        <select class="validate required materialSelect" required="" name="tenderfour" id="tenderfour" onchange="getfourdata(this.value)">
                                            <option value="" disabled selected>Select</option>
                                        </select>
                                    </div>
                                </div>

                            <?php } ?>
                            <?php if ($parentitems->tenderfive != '' || $parentitems->tenderfive != 0) { ?>
                                <div id="fifth">
                                    <div class="input-fields col s12 row">
                                        <label>Select Sub Type</label>
                                        <select class="validate required materialSelect" required="" name="tenderfive" id="tenderfive" onchange="getfivedata(this.value)">
                                            <?php SiteController::actionGettenderfive($parentitems->tenderfour, $parentitems->tenderfive); ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div id="fifth" style="display: none;">
                                    <div class="input-fields col s12 row">
                                        <label>Select Sub Type</label>
                                        <select class="validate required materialSelect" required="" name="tenderfive" id="tenderfive" onchange="getfivedata(this.value)">
                                            <option value="" disabled selected>Select</option>
                                        </select>
                                    </div>
                                </div>

                            <?php } ?>
                            <?php if ($parentitems->tendersix != '' || $parentitems->tendersix != 0) { ?>
                                <div id="sixth">
                                    <div class="input-fields col s12 row">
                                        <label>Select Sub Type</label>
                                        <select class="validate required materialSelect" required="" name="tendersix" id="tendersix" onchange="getsixdata(this.value)">
                                            <?php SiteController::actionGettendersix($parentitems->tenderfive, $parentitems->tendersix); ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div id="sixth" style="display: none;">
                                    <div class="input-fields col s12 row">
                                        <label>Select Sub Type</label>
                                        <select class="validate required materialSelect" required="" name="tendersix" id="tendersix" onchange="getsixdata(this.value)">
                                            <option value="" disabled selected>Select</option>
                                        </select>
                                    </div>
                                </div>

                            <?php } ?>
                            <div class='row'>
                                <div class="input-field col s6">
                                    <input id="username" type="text" name = "itemtender" class="validate required" value="<?= $item->itemtender; ?>">
                                    <label for="username">Item Sr. No. Tender</label>
                                </div>
                                <div class="input-field col s6">
                                    <input id="password" type="text" name = "units" class="validate" value="<?= $item->units; ?>">
                                    <label for="password">Units</label>
                                </div>
                            </div>

                            <div class="row">

                                <div class="input-field col s6">
                                    <input id="FirstName" type="text" name = "quantity" class="validate required" value="<?= $item->quantity; ?>">
                                    <label for="FirstName">Quantity</label>
                                </div>

                                <div class="input-field col s6">
                                    <input id="Street" type="text" name = "makeid" class="validate" value="<?= $item->makeid; ?>">
                                    <label for="Street">Cat Part Id</label>
                                </div>


                            </div>

                            <?php if ($parentitems->tenderfour == 1) { ?>

                                <div class="row">

                                    <div class="input-fields col s6" id='sizesdiv'>
                                        <label>Select Size</label>
                                        <select class="validate required materialSelectsize" required="" name="description" id="sizes0">
                                            <?php
                                            if ($sizes) {
                                                foreach ($sizes as $_size) {
                                                    ?>
                                                    <option value="<?= $_size->id ?>" <?php
                                                    if ($_size->id == $item->description) {
                                                        echo "selected";
                                                    }
                                                    ?>><?= $_size->size ?></option>
                                                            <?php
                                                        }
                                                    } else {
                                                        ?>
                                                <option value="" disabled required>No Sizes</option>
                                            <?php }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="input-fields col s6" id='corediv'>
                                        <label>Select Core</label>
                                        <select class="validate required materialSelectcore" required="" name="core" id="core0">
                                            <option value="">Select Core</option>
                                            <option value="1" <?php
                                            if ($item->core == 1) {
                                                echo "selected";
                                            }
                                            ?>>Core 1</option>
                                            <option value="2" <?php
                                            if ($item->core == 2) {
                                                echo "selected";
                                            }
                                            ?>>Core 2</option>
                                            <option value="3" <?php
                                            if ($item->core == 3) {
                                                echo "selected";
                                            }
                                            ?>>Core 3</option>
                                            <option value="4" <?php
                                            if ($item->core == 4) {
                                                echo "selected";
                                            }
                                            ?>>Core 3.5</option>
                                            <option value="5" <?php
                                            if ($item->core == 5) {
                                                echo "selected";
                                            }
                                            ?>>Core 4</option>

                                        </select>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="input-fields col s6" id='typefit' style='display: none;'>
                                        <label>Select Type of fitting</label>
                                        <select class="validate required materialSelecttypefit" required="" name="typefitting" id="type0">
                                            <?php
                                            if ($types) {
                                                foreach ($types as $_type) {
                                                    ?>
                                                    <option value="<?= $_type->id ?>" <?php
                                                    if ($_type->id == $item->typefitting) {
                                                        echo "selected";
                                                    }
                                                    ?>><?= $_type->text ?></option>
                                                            <?php
                                                        }
                                                    } else {
                                                        ?>
                                                <option value="" disabled required>No Types</option>
                                            <?php }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="input-fields col s6" id='capacityfit' style='display: none;'>
                                        <label>Select Capacity of fitting</label>
                                        <select class="validate required materialSelectcapacityfit" required="" name="capacityfitting" id="text0">
                                            <?php
                                            if ($capacities) {
                                                foreach ($capacities as $_text) {
                                                    ?>
                                                    <option value="<?= $_text->id ?>" <?php
                                                    if ($_text->id == $item->capacityfitting) {
                                                        echo "selected";
                                                    }
                                                    ?>><?= $_text->text ?></option>
                                                            <?php
                                                        }
                                                    } else {
                                                        ?>
                                                <option value="" disabled required>No Types</option>
                                            <?php }
                                            ?>
                                        </select>
                                    </div>


                                <?php } else { ?>


                                    <div class="row">
                                        <div class="input-fields col s6" id='typefit'>
                                            <label>Select Type of fitting</label>
                                            <select class="validate required materialSelecttypefit" required="" name="typefitting" id="type0">
                                                <?php
                                                if ($types) {
                                                    foreach ($types as $_type) {
                                                        ?>
                                                        <option value="<?= $_type->id ?>" <?php
                                                        if ($_type->id == $item->typefitting) {
                                                            echo "selected";
                                                        }
                                                        ?>><?= $_type->text ?></option>
                                                                <?php
                                                            }
                                                        } else {
                                                            ?>
                                                    <option value="" disabled required>No Types</option>
                                                <?php }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="input-fields col s6" id='capacityfit'>
                                            <label>Select Capacity of fitting</label>
                                            <select class="validate required materialSelectcapacityfit" required="" name="capacityfitting" id="text0">
                                                <?php
                                                if ($capacities) {
                                                    foreach ($capacities as $_text) {
                                                        ?>
                                                        <option value="<?= $_text->id ?>" <?php
                                                        if ($_text->id == $item->capacityfitting) {
                                                            echo "selected";
                                                        }
                                                        ?>><?= $_text->text ?></option>
                                                                <?php
                                                            }
                                                        } else {
                                                            ?>
                                                    <option value="" disabled required>No Types</option>
                                                <?php }
                                                ?>
                                            </select>
                                        </div>



                                        <div class="input-fields col s6" id='sizesdiv' style='display:none;'>
                                            <label>Select Size</label>
                                            <select class="validate required materialSelectsize" required="" name="description" id="sizes0">
                                                <?php
                                                if ($sizes) {
                                                    foreach ($sizes as $_size) {
                                                        ?>
                                                        <option value="<?= $_size->id ?>" <?php
                                                        if ($_size->id == $item->description) {
                                                            echo "selected";
                                                        }
                                                        ?>><?= $_size->size ?></option>
                                                                <?php
                                                            }
                                                        } else {
                                                            ?>
                                                    <option value="" disabled required>No Sizes</option>
                                                <?php }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="input-field col s6" id="corediv" style="display:none;">
                                            <select class="validate required materialSelectcore" required="" name="core" id="core0">
                                                <option value="">Select Core</option>
                                                <option value="1">Core 1</option>
                                                <option value="2">Core 2</option>
                                                <option value="3">Core 3</option>
                                                <option value="4">Core 3.5</option>
                                                <option value="5">Core 4</option>

                                            </select>
                                        </div>
                                    </div>


                                <?php } ?>
                                <div class='row'>
                                    <div class="input-fields col s6">
                                        <label>Select Makes</label>
                                        <select class="validate required materialSelectmake browser-default makeedit" required="" name="makes[]" multiple id="makes0">
                                            <?php
                                            if ($makes) {
                                                $allmakes = explode(',', $item->make);
                                                foreach ($makes as $_make) {
                                                    ?>
                                                    <option value="<?= $_make->id ?>" <?php
                                                    if (in_array($_make->id, $allmakes)) {
                                                        echo "selected";
                                                    }
                                                    ?>><?= $_make->make ?></option>
                                                            <?php
                                                        }
                                                    } else {
                                                        ?>
                                                <option value="" disabled required>No Makes</option>
                                            <?php }
                                            ?>
                                        </select>
                                    </div>
                                </div>



                                <input class="waves-effect waves-light btn blue m-b-xs row col s12" type="submit" name='submit' value="Submit">

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

