<?php
/* @var $this yii\web\View */

$this->title = 'Create New Item';

use yii\helpers\Url;

$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
$rootURL = Yii::$app->params['ROOT_URL'];
?>
<link rel="stylesheet" type="text/css" href="/assets/css/multiselect.css"/>
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
    .select2-container--default .select2-results__option[aria-selected=true]{background-color: #00ACC1;}
    .select2-dropdown{margin-top:0px;}
    .select2-container{width:100%!important;}
    .select2-search__field{color:#000!important;}
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

    .selects{
        width: 16.6666666667%;
        margin-left: auto;
        left: auto;
        right: auto;
        float: left;
        box-sizing: border-box;
        padding: 0 0.75rem;
        min-height: 1px;
    }

    .checkboxes label:hover {
        background-color: #1e90ff;
    }
    textarea.materialize-textarea  {
        overflow-y: scroll;
        height: 45px!important;
    }
    td.desc{word-break: break-word;}
    .waves-input-wrapper{float:left; width:100%;}
    span.viewmake {
        background-color: #E4E4E4;
        border-radius: 15px;
        padding: 10px;
        margin-bottom: 5px;
        width: 80%;
        float: left;
    }
    #submitbutton {
        float: left;
        width: 100%;
    }
    input#itemsubmit {
        float: left;
        width: 100%;
    }
</style>
<script>
    // Change the selector if needed
    var $table = $('table.scroll'),
            $bodyCells = $table.find('tbody tr:first').children(),
            colWidth;

    // Adjust the width of thead cells when window resizes
    $(window).resize(function () {
        // Get the tbody columns width array
        colWidth = $bodyCells.map(function () {
            return $(this).width();
        }).get();

        // Set the width of thead columns
        $table.find('thead tr').children().each(function (i, v) {
            $(v).width(colWidth[i]);
        });
    }).resize(); // Trigger resize handler

    $(document).ready(function () {
        $(document).on('keyup', '.itemdescall', function () {
            $(".itemdescall").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        type: 'post',
                        url: baseUrl + 'site/getitemdesc',
                        dataType: "json",
                        data: {'client': request.term, '_csrf-backend': csrf_token},
                        success: function (resultData) {
                            response(resultData);
                        }
                    });
                },
                select: function (event, ui) {
                    $('.itemdescall').val(ui.item.value);
                    return false;
                }

            });
        });

        $(".itemdescall").autocomplete({
            source: function (request, response) {
                $.ajax({
                    type: 'post',
                    url: baseUrl + 'site/getitemdesc',
                    dataType: "json",
                    data: {'client': request.term, '_csrf-backend': csrf_token},
                    success: function (resultData) {
                        response(resultData);
                    }
                });
            },
            select: function (event, ui) {
                $('.itemdescall').val(ui.item.value);
                return false;
            }

        });
    });


</script>
<input type="hidden" name="base_url" id="base_url" value="<?= $baseURL; ?>">
<main class="mn-inner">
    <div class="row">
        <div class="col s6">
            <div class="page-title"><?= $this->title ?></div>
        </div>
        <a class="waves-effect waves-light btn blue m-b-xs add-contact" target="_blank" href="<?= Url::to(['site/view-items', 'id' => $tender->id]) ?>"> View Items</a>
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
                    <?php if (@$idetails) { ?>
                        <table id = "current-projects" class="responsive-table scroll">
                            <thead>
                                <tr>
                                    <th data-field="name">Sr. No.</th>
                                    <th data-field="name">Item Description</th>
                                    <th data-field="name">Units</th>
                                    <th data-field="email">Quantity</th>
                                    <th data-field="email">Make</th>
                                </tr>
                            </thead>
                            <tbody id="contacts_list">
                                <?php
                                if (@$idetails) {
                                    $i = 0;
                                    foreach ($idetails as $key => $idetail) {
                                        ?>
                                        <tr data-id = "<?= $idetail->id ?>">
                                            <td class = ""><?= $key + 1 ?></td>
                                            <td class = "desc"><?= $idetail->description ?></td>
                                            <td class = ""><?= ($idetail->units) ? $idetail->units : '---' ?></td>
                                            <td class = ""><?= ($idetail->quantity) ? $idetail->quantity : '---' ?></td>
                                            <td class = ""><?= ($idetail->make) ? $idetail->make : '---' ?></td>
                                        </tr>

                                        <?php
                                        $i++;
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <h5>Add New Item</h5>
                        <h6><?= $tender->work; ?></h6>
                    <?php } ?>
                    <div class="row">
                        <form id="create-item" class="col s12" method = "post" action = "<?= $baseURL ?>site/create-item">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                            <?php if ((isset($_GET['id'])) && (@$_GET['id'] != '') && (@$_GET['id'] != 0)) { ?>
                                <input type="hidden" name="tender_id" value="<?= @$_GET['id']; ?>" />
                            <?php } else { ?>
                                <div class="input-fields col s12 row">
                                    <label>Select Tender</label>
                                    <select class="validate required materialSelect" required="" name="tender_id">
                                        <option value="" disabled selected>Select Tender</option>
                                        <?php foreach ($tenders as $_tender) { ?>
                                            <option value="<?= $_tender->id; ?>"><?= $_tender->tender_id; ?></option>
                                        <?php } ?>

                                    </select>
                                </div>
                            <?php } ?>
                            <div id="block">
                                <div id="inforows">
                                    <div class="col s12">
                                        <div class="input-fields col s2 row">
                                            <label>Select type of work</label>
                                            <select class="validate required materialSelect" name="tenderone" id="tenderone" required="" onchange="getdata(this.value)">
                                                <option value="" disabled selected>Select</option>
                                                <option value="1">E/M</option>
                                                <option value="2">Civil</option>
                                            </select>
                                        </div>

                                        <div id="second" style="display: none;">
                                            <div class="input-fields col s2 row">
                                                <label>Select Sub Type</label>
                                                <select class="validate required materialSelect" name="tendertwo" id="tendertwo" required="" onchange="getseconddata(this.value)">
                                                    <option value="" disabled selected>Select</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div id="third" style="display: none;">
                                            <div class="input-fields col s2 row">
                                                <label>Select Sub Type</label>
                                                <select class="validate required materialSelect" name="tenderthree" id="tenderthree" onchange="getthirddata(this.value)">
                                                    <option value="" disabled selected>Select</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="fourth" style="display: none;">
                                            <div class="input-fields col s2 row">
                                                <label>Select Sub Type</label>
                                                <select class="validate required materialSelect" name="tenderfour" id="tenderfour" onchange="getfourdata(this.value)">
                                                    <option value="" disabled selected>Select</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div id="fifth" style="display: none;">
                                            <div class="input-fields col s2 row">
                                                <label>Select Sub Type</label>
                                                <select class="validate required materialSelect" name="tenderfive" id="tenderfive" onchange="getfivedata(this.value)">
                                                    <option value="" disabled selected>Select</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div id="sixth" style="display: none;">
                                            <div class="input-fields col s2 row">
                                                <label>Select Sub Type</label>
                                                <select class="validate required materialSelect" name="tendersix" id="tendersix" onchange="getsixdata(this.value)">
                                                    <option value="" disabled selected>Select</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div id='makes' style='display:none'>
                                            <div class="input-field col s12 row">
                                                <select class="validate required materialSelect browser-default" required="" name="makes[]" multiple id="makes0">
                                                    <?php /*
                                                      if ($makes) {
                                                      foreach ($makes as $_make) {
                                                      ?>
                                                      <option value="<?= $_make->id ?>"><?= $_make->make ?></option>
                                                      <?php
                                                      }
                                                      } */
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col s12" id="itemdata" style="display: none;">
                                        <div class="row iteminfo" id="inforows">
                                            <div class="input-field col s1">
                                                <input id="itemtender" type="text" name = "itemtender[]" required="" class="validate required" value="">
                                                <label for="itemtender">Sr. no</label>
                                                <!--textarea id="item" name="desc" class="materialize-textarea"></textarea>
                                                <label for="item">Item description</label-->
                                            </div>
                                            <div class="input-field col s2" id="sizesdiv">
                                                <select class="validate required materialSelectsize browser-default" required="" name="desc[]" id="sizes0">
                                                    <?php /*
                                                      if ($makes) {
                                                      foreach ($makes as $_make) {
                                                      ?>
                                                      <option value="<?= $_make->id ?>"><?= $_make->make ?></option>
                                                      <?php
                                                      }
                                                      } */
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="input-field col s3" id="corediv">
                                                <select class="validate required materialSelectcore" required="" name="core[]" id="core0">
                                                    <option value="">Select Core</option>
                                                    <option value="1">1 Core</option>
                                                    <option value="2">2 Core</option>
                                                    <option value="3">3 Core</option>
                                                    <option value="4">3.5 Core</option>
                                                    <option value="5">4 Core</option>
                                                    <option value="6">5 Core</option>
                                                    <option value="7">6 Core</option>
                                                    <option value="8">7 Core</option>
                                                    <option value="9">8 Core</option>
                                                    <option value="10">10 Core</option>
                                                </select>
                                            </div>
                                            <div class="input-field col s3" id="typefit">
                                                <select class="validate required materialSelecttypefit browser-default" required="" name="type[]" id="type0">


                                                </select>
                                            </div>
                                            <div class="input-field col s2" id="capacityfit">
                                                <select class="validate required materialSelectcapacityfit browser-default" required="" name="text[]" id="text0">


                                                </select>
                                            </div>
                                            <div class="input-field col s3" id="accessoryone">
                                                <select class="validate required materialSelectaccessoryone browser-default" required="" name="accessoryone[]" id="accone0">


                                                </select>
                                            </div>
                                            <div class="input-field col s2" id="accessorytwo">
                                                <input id="acctwo0" type="text" name = "accessorytwo[]" required="" class="validate required" value="">
                                                <label for="acctwo0">Model</label>
                                            </div>
                                            <div class="input-field col s1">
                                                <input id="itemunit" type="text" name = "units[]" required="" style="pointer-events: none;" class="validate required" value="RM">
                                                <!--textarea id="item" name="desc" class="materialize-textarea"></textarea>
                                                <label for="item">Item description</label-->
                                            </div>
                                            <div class="input-field col s1">
                                                <input id="quantity" type="number" name = "quantity[]" min="1" step="1" onkeypress='return event.charCode >= 48 && event.charCode <= 57' required="" class="validate required" value="">
                                                <label for="quantity">Quantity</label>
                                            </div>
                                            <div class="input-field col s2">
                                                <input id="makeid" type="text" name = "makeid[]" class="validate" value="">
                                                <label for="makeid">CatPart Id</label>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="makeids" value="" id="makeids">
                            <div class="row" id="itembutton" style="display: none;">
                                <a class="waves-effect waves-light btn blue m-b-xs" id="addrow" onclick="addrow('0')" >Add Row</a>
                            </div>

                            <div id="submitbutton">
                                <input class="btn blue m-b-xs" id="itemsubmit" name="submit" type="submit" value="Submit">
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="/assets/js/multiselect.js"></script>
<script>
                                    var expanded = false;

                                    function showCheckboxes() {
                                        var checkboxes = document.getElementById("checkboxes");
                                        if (!expanded) {
                                            checkboxes.style.display = "block";
                                            expanded = true;
                                        } else {
                                            checkboxes.style.display = "none";
                                            expanded = false;
                                        }
                                    }

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
