<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

$this->title = 'Dashboard';
$user = Yii::$app->user->identity;
$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
?>
<style>
    .add-contact{    float: right;
                     margin-right: 15px;}    
    .select-wrapper input.select-dropdown, .select-wrapper input.select-dropdown:disabled{border-color: unset;}
    select#dashmake {
        float: left;
        width:35%;
    }
    .select-wrapper.validate.required.materialSelecttype.lights.initialized {
        float: left;
        width:70%;
    }
    .select-wrapper.validate.required.materialSelecttypecapacity.lights.initialized {
        float: left;
    }
    .card {
        float: left;
        width: 100%;
    }
    .quantity{margin-bottom: 20px!important;}
    .stats-card .makes{overflow: initial!important;}
    .mn-inner{min-height: 0px!important;}
    .stats-counter{width:100%;}
    .middle-content{width:100%!important;padding-top:0px!important;}
    .cables{float:left; width:60%;}
    .belowgraphs{float: left;width:100%}
    svg > g > g:last-child { pointer-events: none }
    #value img,#total img,#quantity img,.boxzz img,.upper img{width:20px;}
    span.stats-counter.quantitys {
        margin-bottom: 30px!important;
    }
    .card.stats-card.approved {
        border:5px solid #6666ff;
    }
    .approved .leftside{color:#6666ff;}

    .card.stats-card.archive {
        border:5px solid #00cc00;
    }
    .archive .leftside{color:#00cc00;}
    .card.server-card.balance {
        border:5px solid #000;
    }
    .balance .leftside{color:#000;}
    .card.visitors-card.make {
        border:5px solid #ff6666;
    }
    .make .counter{color:#ff6666;}
    label{color:#000!important;}
    label[for="quotedvalue"] {
       color:#9e9e9e!important;
    }
    .piechart {
        background-color: lightblue;
        border:5px solid lightblue;
    }
    .chart{background-color: lightgoldenrodyellow;}
    .products{background-color: lightblue;}
    .top{background-color: lightskyblue;}
    .numbers {
        float: right;
        width: 50%;
    }
    #fromdate,#todate{border-bottom:1px solid #365264;}
</style>
<?php if ($user->group_id == 4) { ?>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script type="text/javascript">
        google.charts.load('current', {'packages': ['corechart']});
    <?php if (isset($details) && count($details)) { ?>
            google.charts.setOnLoadCallback(drawChartpie);
            google.charts.setOnLoadCallback(drawChart);
            google.charts.setOnLoadCallback(drawChartce);
    <?php } ?>

        function drawPieChart(labels, values, id) {

            var years = labels;
            var sales = values;

            var data = new google.visualization.DataTable();
            data.addColumn('string', 'years');
            data.addColumn('number', 'sales');

            for (i = 0; i < years.length; i++)
                data.addRow([years[i], sales[i]]);


            var options = {
                legend: {position: 'labeled'},
                displayExactValues: true,
                'showRowNumber': false,
                'allowHtml': true,
                backgroundColor: 'lightblue',
                pieSliceText: 'value-and-percentage',
                is3D: true,
                chartArea: {
                    left: "3%",
                    top: "3%",
                    height: "94%",
                    width: "94%"
                },
                sliceVisibilityThreshold: 0
                        /* slices: {1: {offset: 0.2},
                         0: {offset: 0},
                         },*/
            };

            // Create and draw the visualization.
            new google.visualization.PieChart(document.getElementById(id)).
                    draw(data, options);

        }

        function drawChartpie() {

            var data = google.visualization.arrayToDataTable([
                ['type', 'value'],
                ['All Makes', '<?php echo $values; ?>']
            ]);



            var options = {
                legend: {position: 'labeled'},
                displayExactValues: true,
                'showRowNumber': false,
                'allowHtml': true,
                backgroundColor: 'lightblue',
                pieSliceText: 'value-and-percentage',
                is3D: true,
                chartArea: {
                    left: "3%",
                    top: "3%",
                    height: "94%",
                    width: "94%"
                },
                sliceVisibilityThreshold: 0

            };
            var chart = new google.visualization.PieChart(document.getElementById('piechart'));

            chart.draw(data, options);

        }


        function drawChart() {
            var data = google.visualization.arrayToDataTable((<?= json_encode($graphs); ?>));

            var options = {
                title: '',
                curveType: 'function',
                pointsVisible: true,
                focusTarget: 'category',
                backgroundColor: 'lightgoldenrodyellow',
                chartArea: {
                    left: 110,
                    top: 50,
                    width: '100%',
                    height: '70%'
                },
                legend: {position: 'top'},
                hAxis: {title: 'Commands',
                },
                vAxis: {title: '<?= $head ?>',
                    viewWindow: {min: 0},
                },
                crosshair: {
                    color: '#000',
                    trigger: 'selection'
                },
                animation: {
                    duration: 1200,
                    easing: 'out',
                    startup: true
                }
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('curve_chart'));

            chart.draw(data, options);
        }

        function drawChartce() {
            var data = google.visualization.arrayToDataTable((<?= json_encode($graphsce); ?>));

            var options = {
                title: '',
                curveType: 'function',
                pointsVisible: true,
                focusTarget: 'category',
                backgroundColor: 'lightgoldenrodyellow',
                chartArea: {
                    left: 110,
                    top: 50,
                    width: '100%',
                    height: '70%'
                },
                legend: {position: 'top'},
                hAxis: {title: 'Chief Engineers',
                },
                vAxis: {title: '<?= $head ?>',
                    viewWindow: {min: 0},
                },
                crosshair: {
                    color: '#000',
                    trigger: 'selection'
                },
                animation: {
                    duration: 1200,
                    easing: 'out',
                    startup: true
                }
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('curve_chart_ce'));

            chart.draw(data, options);
        }

        function drawLineChart(data, id) {
            var data = google.visualization.arrayToDataTable((data));

            var options = {
                title: '',
                curveType: 'function',
                pointsVisible: true,
                focusTarget: 'category',
                backgroundColor: 'lightgoldenrodyellow',
                chartArea: {
                    left: 110,
                    top: 50,
                    width: '100%',
                    height: '70%'
                },
                legend: {position: 'top'},
                hAxis: {title: 'Commands',
                },
                vAxis: {title: '<?= $head ?>',
                    viewWindow: {min: 0},
                },
                crosshair: {
                    color: '#000',
                    trigger: 'selection'
                },
                animation: {
                    duration: 1200,
                    easing: 'out',
                    startup: true
                }
            };

            var chart = new google.visualization.ColumnChart(document.getElementById(id));

            chart.draw(data, options);
        }

        function drawLineChartce(data, id) {
            var data = google.visualization.arrayToDataTable((data));

            var options = {
                title: '',
                curveType: 'function',
                pointsVisible: true,
                focusTarget: 'category',
                backgroundColor: 'lightgoldenrodyellow',
                chartArea: {
                    left: 110,
                    top: 50,
                    width: '100%',
                    height: '70%'
                },
                legend: {position: 'top'},
                hAxis: {title: 'Chief Engineers',
                },
                vAxis: {title: '<?= $head ?>',
                    viewWindow: {min: 0},
                },
                crosshair: {
                    color: '#000',
                    trigger: 'selection'
                },
                animation: {
                    duration: 1200,
                    easing: 'out',
                    startup: true
                }
            };

            var chart = new google.visualization.ColumnChart(document.getElementById(id));

            chart.draw(data, options);
        }


    </script>
<?php } ?>

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


<?php if ($user->group_id == 4) { ?>
    <div class="mn-content fixed-sidebar">
        <main class="mn-inner">
            <div class="row">
                <div class="col s12 m12 l12">
                    <div class="card top">
                        <div class="card-content">
                            <form id="product-types" method = "post" action = "<?= $baseURL ?>">
                                <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                                <label>Select Product</label>
                                <select class="validate required materialSelect" id="product" name='type'>
                                    <option value="" disabled selected>Select Product</option>
                                    <option value="1" <?= (@$_POST['type'] == 1) ? 'selected' : '' ?> >Cables</option>
                                    <option value="2" <?= (@$_POST['type'] == 2) ? 'selected' : '' ?>>Lighting</option>
                                </select>
                            </form>

    <?php if (isset($details) && count($details)) { ?>

                                <select class="validate required materialSelect" name="command" id="command" required="">
                                    <option value="">Select Command</option>
                                    <option value="14" selected <?php
                                    if (@$_POST['command'] == 14) {
                                        echo "selected";
                                    }
                                    ?>>ALL COMMANDS</option>
                                    <option value="1" <?php
                                    if (@$_POST['command'] == 1) {
                                        echo "selected";
                                    }
                                    ?>>ADG (CG AND PROJECT) CHENNAI AND CE (CG) GOA - MES</option>
                                    <option value="2" <?php
                                    if (@$_POST['command'] == 2) {
                                        echo "selected";
                                    }
                                    ?>>ADG (DESIGN and CONSULTANCY) PUNE - MES</option>
                                    <option value="3" <?php
                                    if (@$_POST['command'] == 3) {
                                        echo "selected";
                                    }
                                    ?>>ADG (OF and DRDO) AND CE (FY) HYDERABAD - MES</option>
                                    <option value="4" <?php
                                    if (@$_POST['command'] == 4) {
                                        echo "selected";
                                    }
                                    ?>>ADG (OF and DRDO)  AND CE (R and D) DELHI-  MES</option>
                                    <option value="5" <?php
                                    if (@$_POST['command'] == 5) {
                                        echo "selected";
                                    }
                                    ?>>ADG (OF and DRDO) AND CE (R and D) SECUNDERABAD - MES</option>
                                    <option value="13" <?php
                                    if (@$tender->command == 13) {
                                        echo "selected";
                                    }
                                    ?>>ADG (Projects) AND CE (CG) Visakhapatnam - MES</option>
                                    <option value="6" <?php
                                    if (@$_POST['command'] == 6) {
                                        echo "selected";
                                    }
                                    ?>>CENTRAL COMMAND</option>
                                    <option value="7" <?php
                                    if (@$_POST['command'] == 7) {
                                        echo "selected";
                                    }
                                    ?>>EASTERN COMMAND</option>
                                    <option value="8" <?php
                                    if (@$_POST['command'] == 8) {
                                        echo "selected";
                                    }
                                    ?>>NORTHERN COMMAND</option>
                                    <option value="9" <?php
                                    if (@$_POST['command'] == 9) {
                                        echo "selected";
                                    }
                                    ?>>SOUTHERN COMMAND</option>
                                    <option value="10" <?php
                                    if (@$_POST['command'] == 10) {
                                        echo "selected";
                                    }
                                    ?>>SOUTH WESTERN COMMAND</option>
                                    <option value="11" <?php
                                    if (@$_POST['command'] == 11) {
                                        echo "selected";
                                    }
                                    ?>>WESTERN COMMAND</option>
                                    <option value="12" <?php
                                    if (@$_POST['command'] == 12) {
                                        echo "selected";
                                    }
                                    ?>>DGNP MUMBAI - MES</option>

                                    <!--option value="2">B/R</option-->
                                </select>
                                <div class="input-field col s6">
                                    <input id="fromdate" type="text" name = "fromdate" class="required fromdatepicker" value="<?php
                                    if (@$_POST['fromdate']) {
                                        echo @$_POST['fromdate'];
                                    }
                                    ?>">
                                    <label for="fromdate">From Date</label>
                                </div>

                                <div class="input-field col s6">
                                    <input id="todate" type="text" name = "todate" disabled="" class="required todatepicker" value="<?php
                                    if (@$_POST['todate']) {
                                        echo @$_POST['todate'];
                                    }
                                    ?>">
                                    <label for="todate">To Date</label>
                                </div>


                            </div></div></div></div></main>
            <main class="mn-inner inner-active-sidebar">
                <div class="middle-content">
                    <div class="row no-m-t no-m-b">
                        <div class="col s12 m12 l4">
                            <div class="card stats-card approved">
                                <div class="card-content">
                                    <?php
                                    if (@$details) {
                                        $i = 0;
                                        foreach ($details as $key => $_log) {
                                            if ($key == 0) {
                                                ?>
                                                <span class="card-title leftside"><?= $_log['title']; ?></span>
                                                <span class="stats-counter"><span class="counter upper leftside" id="u1<?= $key ?>"><?= $_log['total']; ?></span><small>Tenders</small></span>
                                                <span class="stats-counter"><span class="counter upper leftside" id="u2<?= $key ?>"><?= $_log['quantity']; ?></span><small><?= $head ?></small></span>
                                                <span class="stats-counter"><span class="counter upper leftside" id="u3<?= $key ?>"><?= $_log['value']; ?></span><small>Value in Rs.</small></span>
                                                <?php
                                            }
                                            $i++;
                                        }
                                    }
                                    if (@$_POST['type'] == 1) {
                                        $unit = 'RM';
                                    } else {
                                        $unit = 'NOS';
                                    }
                                    ?>
                                </div>
                                <div id="sparkline-bar"></div>
                            </div>
                        </div>
                        <div class="col s12 m12 l4">
                            <div class="card stats-card archive">
                                <div class="card-content">
                                    <?php
                                    if (@$details) {
                                        $i = 0;
                                        foreach ($details as $key => $_log) {
                                            if ($key == 1) {
                                                ?>
                                                <span class="card-title leftside"><?= $_log['title']; ?></span>
                                                <span class="stats-counter"><span class="counter upper leftside" id="u1<?= $key ?>"><?= $_log['total']; ?></span><small>Tenders</small></span>
                                                <span class="stats-counter"><span class="counter upper leftside" id="u2<?= $key ?>"><?= $_log['quantity']; ?></span><small><?= $head ?></small></span>
                                                <span class="stats-counter"><span class="counter upper leftside" id="u3<?= $key ?>"><?= $_log['value']; ?></span><small>Value in Rs.</small></span>
                                                <?php
                                            }
                                            $i++;
                                        }
                                    }
                                    ?>

                                </div>
                                <div id="sparkline-line"></div>
                            </div>
                        </div>
                        <div class="col s12 m12 l4">
                            <div class="card server-card balance">
                                <div class="card-content">
                                    <?php
                                    if (@$details) {
                                        $i = 0;
                                        foreach ($details as $key => $_log) {
                                            if ($key == 2) {
                                                ?>
                                                <span class="card-title leftside"><?= $_log['title']; ?></span>
                                                <span class="stats-counter"><span class="counter upper leftside" id="u1<?= $key ?>"><?= $_log['total']; ?></span><small>Tenders</small></span>
                                                <span class="stats-counter"><span class="counter upper leftside" id="u2<?= $key ?>"><?= $_log['quantity']; ?></span><small><?= $head ?></small></span>
                                                <span class="stats-counter quantitys"><span class="counter upper leftside" id="u3<?= $key ?>"><?= $_log['value']; ?></span><small>Value in Rs.</small></span>
                                                <?php
                                            }
                                            $i++;
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row no-m-t no-m-b">
                        <div class="col s12 m12 l8">
                            <div class="card visitors-card make">
                                <div class="card-content">
                                    <span class="card-title">Select Make</span>
                                    <select class="validate required materialSelect browser-default dashmake" id="dashmake" name='make' style="float:left; width:30%;">
                                        <option value="" disabled selected>Select Make</option>
                                        <?php
                                        if (isset($makes) && count($makes)) {
                                            foreach ($makes as $_make) {
                                                ?>
                                                <option value="<?= $_make->id ?>"><?= $_make->make ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="numbers">
                                        <span class="stats-counter quantity total"><span class="counter" id="total">0</span><small>Tenders</small></span>
                                        <span class="stats-counter quantity quantity"><span class="counter" id="quantity">0</span><small><?= $head ?></small></span>
                                        <span class="stats-counter quantity quantity"><span class="counter" id="value">0</span><small>Value in Rs.</small></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col s12 m12 l4">
                            <div class="card server-card piechart">
                                <div class="card-content">
                                    <span class="card-title">stats of Quantities of all tenders</span>
                                    <div id="piechart" style="width: 100%; height: 150px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col s12 m12 l12">
                            <div class="card visitors-card chart">
                                <div class="card-content">
                                    <span class="card-title">Comparison between all commands<span class="secondary-title">Stats of all tenders</span></span>
                                    <!--input type="text" id="firstdate" class='datepicker' Placeholder='Select Date'-->
                                    <div id="curve_chart" style="width: 100%; height: 500px"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col s12 m12 l12" id='chief'>
                            <div class="card visitors-card chart">
                                <div class="card-content">

                                    <span class="card-title">Comparison between all chief engineers<span class="secondary-title">Stats of all tenders</span></span>
                                    <div id="curve_chart_ce" style="width: 100%; height: 500px"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row no-m-t no-m-b"  id = "cable-size" style="display: none;">
                        <div class="col s12 m12 l12">
                            <div class="card invoices-card products">
                                <div class="card-content">

                                    <span class="card-title">Products</span>
                                    <table class="responsive-table bordered" >
                                        <thead>
                                            <tr>
                                                <th data-field="0" width="200px">Type of Cables</th>
                                                <th data-field="1"><select class="validate required materialSelect cables" data-field="1" id="typeone" name='typeone'>
                                                        <option value="" disabled>Select LT/HT</option>
                                                        <option value="1" >LT</option>
                                                        <option value="2" >HT</option>
                                                    </select></th>
                                                <th data-field="2"><select class="validate required materialSelect cables" disabled data-field="2" id="typetwo" name='typetwo'>
                                                        <option value="" disabled>Select Type</option>
                                                        <option value="1" >Copper</option>
                                                        <option value="2" >Aluminium</option>
                                                    </select></th>
                                                <th data-field="3"><select class="validate required materialSelect cables" disabled data-field="3" id="typethree" name='typethree'>
                                                        <option value="" disabled>Select Type</option>
                                                        <option value="1" >Armoured</option>
                                                        <option value="2" >Unarmoured</option>
                                                    </select></th>
                                                <th data-field="4"><select class="validate required materialSelectsizes cables" disabled data-field="4" id="typefour" name='typefour'>
                                                        <option value="" disabled>Select Size</option>
                                                        <?php
                                                        if (isset($sizes) && count($sizes)) {
                                                            foreach ($sizes as $_size) {
                                                                ?>
                                                                <option value="<?= $_size->id ?>"><?= $_size->size ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select></th>
                                            </tr>
                                        </thead>
                                        <tbody id="sizes-list">
                                            <tr>
                                                <td class = "boxzz" id="o1"></td>
                                                <td class = "boxzz" id="o2"></td>
                                                <td class = "boxzz" id="o3"></td>
                                                <td class = "boxzz" id="o4"></td>
                                                <td class = "boxzz" id="o5"></td>
                                            </tr>
                                            <tr>
                                                <td class = "boxzz" id="a1"></td>
                                                <td class = "boxzz" id="a2"></td>
                                                <td class = "boxzz" id="a3"></td>
                                                <td class = "boxzz" id="a4"></td>
                                                <td class = "boxzz" id="a5"></td>
                                            </tr>
                                            <tr>
                                                <td class = "boxzz" id="b1"></td>
                                                <td class = "boxzz" id="b2"></td>
                                                <td class = "boxzz" id="b3"></td>
                                                <td class = "boxzz" id="b4"></td>
                                                <td class = "boxzz" id="b5"></td>
                                            </tr>
                                            <tr>
                                                <td class = "boxzz" id="c1"></td>
                                                <td class = "boxzz" id="c2"></td>
                                                <td class = "boxzz" id="c3"></td>
                                                <td class = "boxzz" id="c4"></td>
                                                <td class = "boxzz" id="c5"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="belowgraphs">
                                        <div class="upper" id="p2" style="width: 25%; height: 150px;float:left;display:none;"></div>
                                        <div class="upper" id="p3" style="width: 25%; height: 150px;float:left;display:none;"></div>
                                        <div class="upper" id="p4" style="width: 25%; height: 150px;float:left;display:none;"></div>
                                        <div class="upper" id="p5" style="width: 25%; height: 150px;float:left;display:none;"></div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row no-m-t no-m-b"  id = "light-type" style="display: none;">
                        <div class="col s12 m12 l12">
                            <div class="card invoices-card products">
                                <div class="card-content">
                                    <span class="card-title">Products</span>
                                    <table class="responsive-table bordered">
                                        <thead>
                                            <tr>
                                                <th data-field="0">Type of Fittings</th>
                                                <th data-field="1">Approved</th>
                                                <th data-field="2">Archived</th>
                                                <th data-field="3" id="lighthead"></th>
                                                <th data-field="4" id="lightheadtwo"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="types-list">
                                            <tr>
                                                <td class = "" id="d1"><select class="validate required materialSelecttype lights" data-field="5" id="typelights" name='typelights'>
                                                    </select></td>
                                                <td class = "boxzz" id="d2"></td>
                                                <td class = "boxzz" id="d3"></td>
                                                <td class = "boxzz" id="d4"></td>
                                                <td class = "boxzz" id="d5"></td>

                                            </tr>

                                        </tbody>
                                    </table>
                                    <div class="belowgraphs">
                                        <div class="upper" id="l2" style="width: 50%; height: 200px;float:left;margin-left:355px;display:none;"></div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>



        <!--table id = "light-capacity" class="responsive-table" style="display:none;">
            <thead>
                <tr>
                    <th data-field="0">Capacity of Fittings</th>
                    <th data-field="1">Total</th>
                    <th data-field="2" id="capacityhead"></th>
                </tr>
            </thead>
            <tbody id="types-list">

                <tr>
                    <td class = "" id="e1"><select class="validate required materialSelecttypecapacity lights" data-field="6" id="capacitylights" name='capacitylights'>

                        </select></td>
                    <td class = "boxzz" id="e2"></td>
                    <td class = "boxzz" id="e3"></td>

                </tr>

            </tbody>
        </table-->

        <?php
    }
} else {
    ?>
    <main class="mn-inner">
        <div class="row">
            <div class="col s6">
                <div class="page-title">Dashboard</div>
            </div>
            <div class="col s12 m12 l12">
                <div class="card">
                    <div class="card-content">

                        <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= Url::to(['site/approvetenders', 'c' => '1']) ?>">ADG (CG AND PROJECT) CHENNAI AND CE (CG) GOA - MES</a>
                        <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= Url::to(['site/approvetenders', 'c' => '2']) ?>">ADG (DESIGN and CONSULTANCY) PUNE - MES</a>
                        <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= Url::to(['site/approvetenders', 'c' => '3']) ?>">ADG (OF and DRDO) AND CE (FY) HYDERABAD - MES</a>
                        <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= Url::to(['site/approvetenders', 'c' => '4']) ?>">ADG (OF and DRDO)  AND CE (R and D) DELHI-  MES</a>
                        <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= Url::to(['site/approvetenders', 'c' => '5']) ?>">ADG (OF and DRDO) AND CE (R and D) SECUNDERABAD - MES</a>
                        <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= Url::to(['site/approvetenders', 'c' => '13']) ?>">ADG (PROJECTS) AND CE (CG) VISAKHAPATNAM - MES</a>
                        <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= Url::to(['site/approvetenders', 'c' => '6']) ?>">CENTRAL COMMAND</a>
                        <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= Url::to(['site/approvetenders', 'c' => '7']) ?>">EASTERN COMMAND</a>
                        <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= Url::to(['site/approvetenders', 'c' => '8']) ?>">NORTHERN COMMAND</a>
                        <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= Url::to(['site/approvetenders', 'c' => '9']) ?>">SOUTHERN COMMAND</a>
                        <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= Url::to(['site/approvetenders', 'c' => '10']) ?>">SOUTH WESTERN COMMAND</a>
                        <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= Url::to(['site/approvetenders', 'c' => '11']) ?>">WESTERN COMMAND</a>
                        <a class="col s12 waves-effect waves-light btn blue m-b-xs" href="<?= Url::to(['site/approvetenders', 'c' => '12']) ?>">DGNP MUMBAI - MES</a>


                    </div></div></div></div></main>
<?php } ?>



