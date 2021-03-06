var baseUrl = $('#base_url').val();
var clients = {};
var csrf_token = $('meta[name="csrf-token"]').attr('content');
var user_email = $('meta[name="user_email"]').attr('content');
var isTimeline = $('input[name="GenerateReport[IsTimeline]"]:checked').val();
var pathname = window.location.pathname;
if (isTimeline == "0" || isTimeline == "1") {
    setTimeout(function () {
        $('#steps-uid-0-t-3').trigger('click');
    }, 1000);
}

$(document).ready(function () {
    $(".button").click(function () {
        $(this).closest('div.row').remove();
    });

    $('#fromdatesearch').datepicker({
        showAnim: "fold",
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        onSelect: function (selectedDate) {
            var fromdate = $(this).datepicker('getDate');
            var newDate = $(this).datepicker('getDate');
            newDate.setDate(newDate.getDate() + 30);
            //$('#todatesearch').datepicker('setDate', newDate);
            $('#todatesearch').datepicker('option', 'minDate', fromdate);
            $('#todatesearch').datepicker('option', 'maxDate', newDate);
        }
    });

    $('#todatesearch').datepicker({
        showAnim: "fold",
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
    });

    $("#conform").submit(function (e) {
        e.preventDefault();
        var val = $("#consearch").val() // get the current value of the input field.
        if (val) {
            $.ajax({
                url: baseUrl + 'products/searchcontractor',
                type: "post",
                data: 'val=' + val + '&_csrf-backend=' + csrf_token,
                beforeSend: function () {
                    $(".mn-inner .card-content.card-contractors").html('<span class="fetchmess"><div class="loadercolorz"></div><p>Fetching <span class="greencolor">Contractor</span></p></span>');
                },
                success: function (data) {
                    if (data) {
                        $('.mn-inner .card-content.card-contractors').html(data);
                        $(".modalclose").css('z-index', '1');
                        $('#current-project').DataTable({
                            language: {
                                searchPlaceholder: 'Search records',
                                sSearch: '',
                                sLengthMenu: 'Show _MENU_',
                                sLength: 'dataTables_length',
                                oPaginate: {
                                    sFirst: '<i class="material-icons">chevron_left</i>',
                                    sPrevious: '<i class="material-icons">chevron_left</i>',
                                    sNext: '<i class="material-icons">chevron_right</i>',
                                    sLast: '<i class="material-icons">chevron_right</i>'
                                }
                            },
                            "deferRender": true,
                        });
                        $("select[name='current-project_length']").css('display', 'block');
                    } else {
                        $('.mn-inner .card-content.card-contractors').html('No Contractors Found.');
                    }
                    // $('#notification-bar').text('The page has been successfully loaded');
                },
                error: function () {
                    // $('#notification-bar').text('An error occurred');
                }
            });
        } else {
            alert('Please enter firm name!');
        }
    });

    $("#searchform").submit(function (e) {
        e.preventDefault();
        var val = $("#searchdata").val() // get the current value of the input field.
        var vallength = $("#searchdata").val().length; // get the current value of the input field.
        if (vallength < 5) {
            alert('Please enter minimum 5 digits Tender Id');
            return false;
        }
        if (val) {
            $.ajax({
                url: baseUrl + 'site/searchtender',
                type: "post",
                data: 'val=' + val + '&_csrf-backend=' + csrf_token,
                beforeSend: function () {
                    $(".mn-inner .col .page-title").html('');
                    $(".mn-inner #sort-data").css('display', 'none');
                    $(".mn-inner #command-types").css('display', 'none');
                    $(".mn-inner .add-contact").css('display', 'none');
                    $(".mn-inner .card.top").css('background-color', '#fff');
                    $(".mn-inner .row.departmentview").css('background-color', '#fff');
                    $(".mn-inner .row.departmentview").css('position', 'relative');
                    $(".mn-inner .row.departmentview").css('box-shadow', '0 2px 2px 0 rgba(0,0,0,.05), 0 3px 1px -2px rgba(0,0,0,.08), 0 1px 5px 0 rgba(0,0,0,.08)');
                    $(".mn-inner.inner-active-sidebar").css('display', 'none');
                    $(".mn-inner .card-content").html('<span class="fetchmess"><div class="loadercolorz"></div><p>Fetching <span class="greencolor">Tender</span></p></span>');
                    //$(".mn-inner .card").css('width','1058px');
                    $(".mn-inner form p").css('text-align', 'center');
                },
                success: function (data) {
                    if (data) {
                        $('.mn-inner').html(data);
                        $(".modalclose").css('z-index', '1');
                        $('select.materialSelectcontractor').select2({
                            closeOnSelect: true,
                            placeholder: 'Select Contractor',
                            allowClear: true,
                            ajax: {
                                headers: {
                                    "Authorization": "Bearer " + csrf_token,
                                    "Content-Type": "application/json",
                                },
                                type: 'get',
                                url: baseUrl + 'contractor/getcontractors',
                                dataType: 'json',
                                data: function (params) {
                                    return {
                                        term: params.term || '',
                                        page: params.page || 1
                                    }
                                },
                                cache: true,
                            }
                        });
                        $('#current-project').DataTable({
                            language: {
                                searchPlaceholder: 'Search records',
                                sSearch: '',
                                sLengthMenu: 'Show _MENU_',
                                sLength: 'dataTables_length',
                                oPaginate: {
                                    sFirst: '<i class="material-icons">chevron_left</i>',
                                    sPrevious: '<i class="material-icons">chevron_left</i>',
                                    sNext: '<i class="material-icons">chevron_right</i>',
                                    sLast: '<i class="material-icons">chevron_right</i>'
                                }
                            },
                            "deferRender": true

                        });

                        $('.pdatepicker').pickadate({
                            format: 'dd-mm-yyyy',
                            selectMonths: true, // Creates a dropdown to control month
                            selectYears: 15, // Creates a dropdown of 15 years to control year
                            onSet: function (ele) {
                                if (ele.select) {
                                    $('.picker__holder').css('height', '0px');
                                    this.close();
                                }
                            },
                            onClose: function (ele) {
                                $('.picker__holder').css('height', '0px');
                            }
                        });
                        $('select').css('display', 'block');
                    } else {
                        $('.mn-inner .card-content').html('No Tenders Found.');
                    }
                    // $('#notification-bar').text('The page has been successfully loaded');
                },
                error: function () {
                    // $('#notification-bar').text('An error occurred');
                }
            });
        } else {
            alert('Please enter Tender Id');
        }
    });


    $("#getdata").submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: baseUrl + 'mail/getdata',
            data: $('#getdata').serialize(),
            beforeSend: function () {
            },
            success: function (response) {

                alert(response);

            }
        });
    });

    /*$('.fromdatepicker').pickadate({
     format: 'yyyy-mm-dd',
     selectMonths: true, // Creates a dropdown to control month
     selectYears: 15, // Creates a dropdown of 15 years to control year
     onSet: function (ele) {
     if (ele.select) {
     var chosen_date = $('.fromdatepicker').val();
     $('.todatepicker').pickadate('picker').set('min', chosen_date);
     $(".todatepicker").removeAttr('disabled');
     this.close();
     }
     },
     onClose: function () {
     //$('.datepicker').blur();
     $(document.activeElement).blur()
     }
     });*/

    $('.fromdatepicker').datepicker({
        showAnim: "fold",
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        onSelect: function (selectedDate) {
            var newDate = $(this).datepicker('getDate');
            $('.todatepicker').datepicker('option', 'minDate', newDate);
            $(".todatepicker").removeAttr('disabled');
        }
    });

    $('#feedback').on('submit', function (e) {

        e.preventDefault();
        $.ajax({
            type: 'post',
            url: baseUrl + 'site/feedback',
            data: $('#feedback').serialize(),
            beforeSend: function () {
                $("#signbutton").html('<img src="assets/images/loading.gif" alt="">');
                $("#signbutton").attr('disabled', 'true');
            },
            success: function (response) {
                if (response == 1) {
                    var message = 'Thank you for giving your valuable suggestions/feedback.';
                    swal({
                        title: "Success!",
                        text: message,
                        type: "success",
                        showCancelButton: false,
                        showConfirmButton: false,
                        timer: 3000,
                    }
                    , function () {
                        window.location.reload();
                    })
                            ;
                } else {
                    swal("Sorry", 'Your feedback process has been failed. If you want you can try again', "error");
                }
                $("#signbutton").text('Submit');
                $("#signbutton").removeAttr('disabled');
            }
        });
    });

    $('.todatepicker').datepicker({
        showAnim: "fold",
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        onSelect: function (selectedDate) {
            if (selectedDate) {
                var fromdate = $('.fromdatepicker').val();
                var todate = $('.todatepicker').val();

                var command = $("#command option:selected").val();
                var product = $("#product option:selected").val();
                var make = $("#dashmake option:selected").val();
                var sizeval = $("#typefour option:selected").val();
                $("#typeone").prop('selectedIndex', 0);
                $("#typeone").material_select();
                $("#typetwo").prop('selectedIndex', 0);
                $("#typetwo").material_select();
                $("#typethree").prop('selectedIndex', 0);
                $("#typethree").material_select();
                $("#typefour").prop('selectedIndex', 0);
                $("#typefour").material_select();

                if (make != '') {
                    if (product == 1) {
                        $("#cable-size").show();
                        $("#light-type").hide();
                        var ext = 'RM';
                        //$("#light-capacity").hide();
                    } else if (product == 2) {
                        $("#cable-size").hide();
                        $("#light-type").show();
                        var ext = 'NOS';
                        //$("#light-capacity").show();
                    } else {
                        $("#cable-size").hide();
                        $("#light-type").hide();
                        var ext = 'RM';
                    }

                }

                var sizes = '';
                var types = '';
                var ctypes = '';
                $.ajax({
                    type: 'post',
                    url: baseUrl + 'site/getmakedetails',
                    data: 'type=1&make=' + make + '&product=' + product + '&sizeval=' + sizeval + '&command=' + command + '&fromdate=' + fromdate + '&todate=' + todate + '&_csrf-backend=' + csrf_token,
                    beforeSend: function () {
                        $("#u10").html('<img src="/assets/images/loading.gif" alt="">');
                        $("#u20").html('<img src="/assets/images/loading.gif" alt="">');
                        $("#u30").html('<img src="/assets/images/loading.gif" alt="">');
                        $("#u11").html('<img src="/assets/images/loading.gif" alt="">');
                        $("#u21").html('<img src="/assets/images/loading.gif" alt="">');
                        $("#u31").html('<img src="/assets/images/loading.gif" alt="">');
                        $("#u12").html('<img src="/assets/images/loading.gif" alt="">');
                        $("#u22").html('<img src="/assets/images/loading.gif" alt="">');
                        $("#u32").html('<img src="/assets/images/loading.gif" alt="">');
                        $("#chief").hide();
                        $("#cwengg").hide();
                        $("#gengg").hide();
                        $("#curve_chart").html('<img src="/assets/images/loading.gif" alt="">');
                        $("#piechart").html('<img src="/assets/images/loading.gif" alt="">');
                        $("#lightchart").html('<img src="/assets/images/loading.gif" alt="">');
                        //$("#lightmakechart").html('<img src="/assets/images/loading.gif" alt="">');
                        $("#l2").hide();
                        if (make != '') {
                            $("#total").html('<img src="/assets/images/loading.gif" alt="">');
                            $("#quantity").html('<img src="/assets/images/loading.gif" alt="">');
                            $("#value").html('<img src="/assets/images/loading.gif" alt="">');
                            $(".boxzz").html('<img src="/assets/images/loading.gif" alt="">');
                            $("#p2").hide();
                            $("#p3").hide();
                            $("#p4").hide();
                            $("#p5").hide();
                        }
                    },
                    success: function (response) {


                        var myJSON = JSON.parse(response);
                        if (myJSON) {
                            if (make != '') {
                                $("#total").html(myJSON.first.total);
                                $("#quantity").html(myJSON.first.quantity);
                                //$("#value").html(myJSON.first.value);
                                $("#value").html('<a class="btn red" onclick="getpricewithparams(30, 4, ' + product + ',' + make + ',4)">Click Here</a>');
                            }
                            var checkone = myJSON.valuesone[1] + myJSON.valuesone[2];
                            if (checkone != 0) {
                                $("#piechart").show();
                                drawPieChart(myJSON.labelsone, myJSON.valuesone, "piechart");
                            } else {
                                $("#piechart").html('No Data Available');
                            }
                            if (myJSON.user != 6 && product == 2) {
                                $("#lightchart").show();
                                drawPiemakeChart(myJSON.lightchart, "lightchart");
                            }
                            $("#u10").html(myJSON.first.aptenderstotal);
                            $("#u20").html(myJSON.first.aptendersquantity);
                            //$("#u30").html(myJSON.first.aptendersprice);
                            if (make != '') {
                                $("#u30").html('<a class="btn green" onclick="getpricewithparams(30, 1, ' + product + ',' + make + ',0)">Click Here</a>');
                            } else {
                                $("#u30").html('<a class="btn green" onclick="getpricewithparams(30, 1, ' + product + ',0,0)">Click Here</a>');
                            }
                            $("#u11").html(myJSON.first.artenderstotal);
                            $("#u21").html(myJSON.first.artendersquantity);
                            //$("#u31").html(myJSON.first.artendersprice);
                            if (make != '') {
                                $("#u31").html('<a class="btn blue" onclick="getpricewithparams(31, 2, ' + product + ',' + make + ',1)">Click Here</a>');
                            } else {
                                $("#u31").html('<a class="btn blue" onclick="getpricewithparams(31, 2, ' + product + ',0,1)">Click Here</a>');
                            }
                            $("#u12").html(myJSON.first.bltenderstotal);
                            $("#u22").html(myJSON.first.bltendersquantity);
                            //$("#u32").html(myJSON.first.bltendersprice);
                            if (make != '') {
                                $("#u32").html('<a class="btn red" onclick="getpricewithparams(32, 3, ' + product + ',' + make + ',2)">Click Here</a>');
                            } else {
                                $("#u32").html('<a class="btn red" onclick="getpricewithparams(32, 3, ' + product + ',0,2)">Click Here</a>');
                            }
                            if (myJSON.first.artenders == 0) {
                                $("#cable-size").hide();
                            }
                            if (make != '') {
                                $("#a1").html(myJSON.second.headone);
                                $("#a2").html('0 ' + ext + '');
                                $("#a3").html('0 ' + ext + '');
                                $("#a4").html('0 ' + ext + '');
                                $("#a5").html('0 ' + ext + '');
                                $("#b1").html(myJSON.second.headtwo);
                                $("#b2").html('0 ' + ext + '');
                                $("#b3").html('0 ' + ext + '');
                                $("#b4").html('0 ' + ext + '');
                                $("#b5").html('0 ' + ext + '');
                                $("#c1").html(myJSON.second.headthree);
                                $("#c2").html('0 ' + ext + '');
                                $("#c3").html('0 ' + ext + '');
                                $("#c4").html('0 ' + ext + '');
                                $("#c5").html('0 ' + ext + '');
                                $("#o1").html(myJSON.second.headfour);
                                $("#o2").html('0 ' + ext + '');
                                $("#o3").html('0 ' + ext + '');
                                $("#o4").html('0 ' + ext + '');
                                $("#o5").html('0 ' + ext + '');
                                $("#d2").html(myJSON.second.atotallight);
                                $("#d3").html(myJSON.second.totallight);
                                $("#d4").html(myJSON.second.withlight);
                                $("#d5").html(myJSON.second.withoutlight);
                                $("#e2").html(myJSON.second.totalclight);
                                $("#e3").html(myJSON.second.withclight);
                                $("#lighthead").html('With ' + myJSON.makename);
                                $("#lightheadtwo").html('Without ' + myJSON.makename);
                                $("#capacityhead").html(myJSON.makename);


                                /*$('.materialSelectsize').on('contentChanged', function () {
                                 $(this).material_select();
                                 });
                                 
                                 $.each(myJSON.sizes, function (key, value) {
                                 if (key != 0) {
                                 sizes += '<option value="' + key + '">' + value + '</option>';
                                 } else {
                                 sizes += '<option value="" disabled required>No Sizes</option>';
                                 }
                                 }
                                 );
                                 $("#typefour").html(sizes);
                                 $("#typefour").trigger('contentChanged');*/

                                $('.materialSelecttype').on('contentChanged', function () {
                                    $(this).material_select();
                                });
                                types += '<option value="">Select Fitting</option>';
                                $.each(myJSON.tlights, function (key, value) {
                                    if (key != 0) {
                                        types += '<option value="' + key + '">' + value + '</option>';
                                    } else {
                                        types += '<option value="" disabled required>No Types</option>';
                                    }
                                }
                                );
                                $("#typelights").html(types);
                                $("#typelights").trigger('contentChanged');

                                /*$('.materialSelecttypecapacity').on('contentChanged', function () {
                                 $(this).material_select();
                                 });
                                 
                                 $.each(myJSON.clights, function (key, value) {
                                 if (key != 0) {
                                 ctypes += '<option value="' + key + '">' + value + '</option>';
                                 } else {
                                 ctypes += '<option value="" disabled required>No Capacity</option>';
                                 }
                                 }
                                 );
                                 $("#capacitylights").html(ctypes);
                                 $("#capacitylights").trigger('contentChanged');*/
                                drawLineChart(myJSON.graph, "curve_chart", myJSON.makename);
                            } else {
                                drawLineChart(myJSON.graph, "curve_chart");
                            }

                            /*if (command == 1 || command == 2 || command == 3 || command == 4 || command == 5 || command == 12 || command == 13) {
                             $("#curve_chart_ce").html('');
                             $("#chief").hide();
                             } else {
                             $("#chief").show();
                             drawLineChartce(myJSON.graphce, "curve_chart_ce");
                             }*/
                        }

                    }
                });
            }
        }
    });

    /* $('.todatepicker').pickadate({
     format: 'yyyy-mm-dd',
     selectMonths: true, // Creates a dropdown to control month
     selectYears: 15, // Creates a dropdown of 15 years to control year
     onSet: function (ele) {
     if (ele.select) {
     var fromdate = $('.fromdatepicker').val();
     var todate = $('.todatepicker').val();
     this.close();
     
     var command = $("#command option:selected").val();
     var product = $("#product option:selected").val();
     var make = $("#dashmake option:selected").val();
     var sizeval = $("#typefour option:selected").val();
     $("#typeone").prop('selectedIndex', 0);
     $("#typeone").material_select();
     $("#typetwo").prop('selectedIndex', 0);
     $("#typetwo").material_select();
     $("#typethree").prop('selectedIndex', 0);
     $("#typethree").material_select();
     $("#typefour").prop('selectedIndex', 0);
     $("#typefour").material_select();
     
     if (make != '') {
     if (product == 1) {
     $("#cable-size").show();
     $("#light-type").hide();
     var ext = 'RM';
     //$("#light-capacity").hide();
     } else if (product == 2) {
     $("#cable-size").hide();
     $("#light-type").show();
     var ext = 'NOS';
     //$("#light-capacity").show();
     } else {
     $("#cable-size").hide();
     $("#light-type").hide();
     var ext = 'RM';
     }
     
     }
     
     var sizes = '';
     var types = '';
     var ctypes = '';
     $.ajax({
     type: 'post',
     url: baseUrl + 'site/getmakedetails',
     data: 'type=1&make=' + make + '&product=' + product + '&sizeval=' + sizeval + '&command=' + command + '&fromdate=' + fromdate + '&todate=' + todate + '&_csrf-backend=' + csrf_token,
     beforeSend: function () {
     $("#u10").html('<img src="/assets/images/loading.gif" alt="">');
     $("#u20").html('<img src="/assets/images/loading.gif" alt="">');
     $("#u30").html('<img src="/assets/images/loading.gif" alt="">');
     $("#u11").html('<img src="/assets/images/loading.gif" alt="">');
     $("#u21").html('<img src="/assets/images/loading.gif" alt="">');
     $("#u31").html('<img src="/assets/images/loading.gif" alt="">');
     $("#u12").html('<img src="/assets/images/loading.gif" alt="">');
     $("#u22").html('<img src="/assets/images/loading.gif" alt="">');
     $("#u32").html('<img src="/assets/images/loading.gif" alt="">');
     $("#chief").hide();
     $("#cwengg").hide();
     $("#gengg").hide();
     $("#curve_chart").html('<img src="/assets/images/loading.gif" alt="">');
     $("#piechart").html('<img src="/assets/images/loading.gif" alt="">');
     if (make != '') {
     $("#total").html('<img src="/assets/images/loading.gif" alt="">');
     $("#quantity").html('<img src="/assets/images/loading.gif" alt="">');
     $("#value").html('<img src="/assets/images/loading.gif" alt="">');
     $(".boxzz").html('<img src="/assets/images/loading.gif" alt="">');
     $("#p2").hide();
     $("#p3").hide();
     $("#p4").hide();
     $("#p5").hide();
     }
     },
     success: function (response) {
     
     
     var myJSON = JSON.parse(response);
     if (myJSON) {
     if (make != '') {
     $("#total").html(myJSON.first.total);
     $("#quantity").html(myJSON.first.quantity);
     $("#value").html(myJSON.first.value);
     }
     var checkone = myJSON.valuesone[1] + myJSON.valuesone[2];
     if (checkone != 0) {
     $("#piechart").show();
     drawPieChart(myJSON.labelsone, myJSON.valuesone, "piechart");
     } else {
     $("#piechart").html('No Data Available');
     }
     $("#u10").html(myJSON.first.aptenderstotal);
     $("#u20").html(myJSON.first.aptendersquantity);
     $("#u30").html(myJSON.first.aptendersprice);
     $("#u11").html(myJSON.first.artenderstotal);
     $("#u21").html(myJSON.first.artendersquantity);
     $("#u31").html(myJSON.first.artendersprice);
     $("#u12").html(myJSON.first.bltenderstotal);
     $("#u22").html(myJSON.first.bltendersquantity);
     $("#u32").html(myJSON.first.bltendersprice);
     if (myJSON.first.artenders == 0) {
     $("#cable-size").hide();
     }
     if (make != '') {
     $("#a1").html(myJSON.second.headone);
     $("#a2").html('0 ' + ext + '');
     $("#a3").html('0 ' + ext + '');
     $("#a4").html('0 ' + ext + '');
     $("#a5").html('0 ' + ext + '');
     $("#b1").html(myJSON.second.headtwo);
     $("#b2").html('0 ' + ext + '');
     $("#b3").html('0 ' + ext + '');
     $("#b4").html('0 ' + ext + '');
     $("#b5").html('0 ' + ext + '');
     $("#c1").html(myJSON.second.headthree);
     $("#c2").html('0 ' + ext + '');
     $("#c3").html('0 ' + ext + '');
     $("#c4").html('0 ' + ext + '');
     $("#c5").html('0 ' + ext + '');
     $("#o1").html(myJSON.second.headfour);
     $("#o2").html('0 ' + ext + '');
     $("#o3").html('0 ' + ext + '');
     $("#o4").html('0 ' + ext + '');
     $("#o5").html('0 ' + ext + '');
     $("#d2").html(myJSON.second.atotallight);
     $("#d3").html(myJSON.second.totallight);
     $("#d4").html(myJSON.second.withlight);
     $("#d5").html(myJSON.second.withoutlight);
     $("#e2").html(myJSON.second.totalclight);
     $("#e3").html(myJSON.second.withclight);
     $("#lighthead").html('With ' + myJSON.makename);
     $("#lightheadtwo").html('Without ' + myJSON.makename);
     $("#capacityhead").html(myJSON.makename);
     
     
     /*$('.materialSelectsize').on('contentChanged', function () {
     $(this).material_select();
     });
     
     $.each(myJSON.sizes, function (key, value) {
     if (key != 0) {
     sizes += '<option value="' + key + '">' + value + '</option>';
     } else {
     sizes += '<option value="" disabled required>No Sizes</option>';
     }
     }
     );
     $("#typefour").html(sizes);
     $("#typefour").trigger('contentChanged');*/

    /*$('.materialSelecttype').on('contentChanged', function () {
     $(this).material_select();
     });
     types += '<option value="">Select Fitting</option>';
     $.each(myJSON.tlights, function (key, value) {
     if (key != 0) {
     types += '<option value="' + key + '">' + value + '</option>';
     } else {
     types += '<option value="" disabled required>No Types</option>';
     }
     }
     );
     $("#typelights").html(types);
     $("#typelights").trigger('contentChanged');
     
     /*$('.materialSelecttypecapacity').on('contentChanged', function () {
     $(this).material_select();
     });
     
     $.each(myJSON.clights, function (key, value) {
     if (key != 0) {
     ctypes += '<option value="' + key + '">' + value + '</option>';
     } else {
     ctypes += '<option value="" disabled required>No Capacity</option>';
     }
     }
     );
     $("#capacitylights").html(ctypes);
     $("#capacitylights").trigger('contentChanged');*/
    /* drawLineChart(myJSON.graph, "curve_chart", myJSON.makename);
     } else {
     drawLineChart(myJSON.graph, "curve_chart");
     }
     
     /*if (command == 1 || command == 2 || command == 3 || command == 4 || command == 5 || command == 12 || command == 13) {
     $("#curve_chart_ce").html('');
     $("#chief").hide();
     } else {
     $("#chief").show();
     drawLineChartce(myJSON.graphce, "curve_chart_ce");
     }*/
    /*}
     
     }
     });
     }
     },
     onClose: function () {
     //$('.datepicker').blur();
     $(document.activeElement).blur()
     }
     });
     */

    $("#makes0").on('change', function () {
        var make = $(this).children("option:selected").val();
        if (make == 0) {
            $(this).prop('selected', false);
            $('#makes0 > option').prop("selected", true);
        }
    });

    $(".singlemake").on('click', function () {
        var itemid = $(this).attr("itemid");
        var mid = $(this).attr("mid");
        $.ajax({
            type: 'post',
            url: baseUrl + 'site/unselectmake',
            dataType: "json",
            data: {'itemid': itemid, mid: mid, '_csrf-backend': csrf_token},
            beforeSend: function () {
                $("#inner" + itemid + mid + "").html('<img src="/assets/images/loading.gif" alt="">');
            },
            success: function (data) {
                if (data.success == 1) {
                    $("#" + itemid + mid + "").remove();
                }
            }
        });
    });

    $("#dashmake").on('change', function () {
        var make = $(this).children("option:selected").val();
        var product = $("#product option:selected").val();
        var sizeval = $("#typefour option:selected").val();
        var command = $("#command option:selected").val();
        var fromdate = $("#fromdate").val();
        var todate = $("#todate").val();
        var cquantity = $('#u20').text();
        $("#typeone").prop('selectedIndex', 0);
        $("#typeone").material_select();
        $("#typetwo").prop('selectedIndex', 0);
        $("#typetwo").material_select();
        $("#typethree").prop('selectedIndex', 0);
        $("#typethree").material_select();
        $("#typefour").prop('selectedIndex', 0);
        $("#typefour").material_select();
        if (product == 1) {
            $("#cable-size").show();
            $("#light-type").hide();
            var ext = 'RM';
            //$("#light-capacity").hide();
        } else if (product == 2) {
            $("#cable-size").hide();
            $("#light-type").show();
            var ext = 'NOS';
            //$("#light-capacity").show();
        } else {
            $("#cable-size").hide();
            $("#light-type").hide();
            var ext = 'RM';
        }

        var sizes = '';
        var types = '';
        var ctypes = '';
        $.ajax({
            type: 'post',
            url: baseUrl + 'site/getmakedetails',
            data: 'type=2&make=' + make + '&product=' + product + '&sizeval=' + sizeval + '&command=' + command + '&fromdate=' + fromdate + '&todate=' + todate + '&cquantity=' + cquantity + '&_csrf-backend=' + csrf_token,
            beforeSend: function () {
                $("#total").html('<img src="/assets/images/loading.gif" alt="">');
                $("#quantity").html('<img src="/assets/images/loading.gif" alt="">');
                $("#value").html('<img src="/assets/images/loading.gif" alt="">');
                $(".boxzz").html('<img src="/assets/images/loading.gif" alt="">');
                $("#curve_chart").html('<img src="/assets/images/loading.gif" alt="">');
                $("#piechart").html('<img src="/assets/images/loading.gif" alt="">');
                $("#p2").hide();
                $("#p3").hide();
                $("#p4").hide();
                $("#p5").hide();
                $("#l2").hide();
                $("#chief").hide();
                $("#cwengg").hide();
                $("#gengg").hide();
                //$("#p22").html('<img src="/assets/images/loading.gif" alt="">');
            },
            success: function (response) {

                var myJSON = JSON.parse(response);
                if (myJSON) {
                    $("#total").html(myJSON.first.total);
                    $("#quantity").html(myJSON.first.quantity);
                    //$("#value").html(myJSON.first.value);
                    $("#value").html('<a class="btn red" onclick="getpricewithparams(30, 4, ' + product + ',' + make + ',4)">Click Here</a>');
                    $("#a1").html(myJSON.second.headone);
                    $("#a2").html('0 ' + ext + '');
                    $("#a3").html('0 ' + ext + '');
                    $("#a4").html('0 ' + ext + '');
                    $("#a5").html('0 ' + ext + '');
                    $("#b1").html(myJSON.second.headtwo);
                    $("#b2").html('0 ' + ext + '');
                    $("#b3").html('0 ' + ext + '');
                    $("#b4").html('0 ' + ext + '');
                    $("#b5").html('0 ' + ext + '');
                    $("#c1").html(myJSON.second.headthree);
                    $("#c2").html('0 ' + ext + '');
                    $("#c3").html('0 ' + ext + '');
                    $("#c4").html('0 ' + ext + '');
                    $("#c5").html('0 ' + ext + '');
                    $("#o1").html(myJSON.second.headfour);
                    $("#o2").html('0 ' + ext + '');
                    $("#o3").html('0 ' + ext + '');
                    $("#o4").html('0 ' + ext + '');
                    $("#o5").html('0 ' + ext + '');
                    $("#d2").html(myJSON.second.atotallight);
                    $("#d3").html(myJSON.second.totallight);
                    $("#d4").html(myJSON.second.withlight);
                    $("#d5").html(myJSON.second.withoutlight);
                    $("#e2").html(myJSON.second.totalclight);
                    $("#e3").html(myJSON.second.withclight);
                    $("#lighthead").html('With ' + myJSON.makename);
                    $("#lightheadtwo").html('Without ' + myJSON.makename);
                    $("#capacityhead").html(myJSON.makename);

                    /*$('.materialSelectsize').on('contentChanged', function () {
                     $(this).material_select();
                     });
                     
                     $.each(myJSON.sizes, function (key, value) {
                     if (key != 0) {
                     sizes += '<option value="' + key + '">' + value + '</option>';
                     } else {
                     sizes += '<option value="" disabled required>No Sizes</option>';
                     }
                     }
                     );
                     $("#typefour").html(sizes);
                     $("#typefour").trigger('contentChanged');*/

                    $('.materialSelecttype').on('contentChanged', function () {
                        $(this).material_select();
                    });

                    types += '<option value="">Select Fitting</option>';
                    $.each(myJSON.tlights, function (key, value) {
                        if (key != 0) {
                            types += '<option value="' + key + '">' + value + '</option>';
                        } else {
                            types += '<option value="" disabled required>No Types</option>';
                        }
                    }
                    );
                    $("#typelights").html(types);
                    $("#typelights").trigger('contentChanged');

                    /*$('.materialSelecttypecapacity').on('contentChanged', function () {
                     $(this).material_select();
                     });
                     
                     $.each(myJSON.clights, function (key, value) {
                     if (key != 0) {
                     ctypes += '<option value="' + key + '">' + value + '</option>';
                     } else {
                     ctypes += '<option value="" disabled required>No Capacity</option>';
                     }
                     }
                     );
                     $("#capacitylights").html(ctypes);
                     $("#capacitylights").trigger('contentChanged');*/
                    var checkone = myJSON.valuesone[1] + myJSON.valuesone[2];
                    if (checkone != 0) {
                        $("#piechart").show();
                        drawPieChart(myJSON.labelsone, myJSON.valuesone, "piechart");
                    } else {
                        $("#piechart").html('No Data Available');
                    }
                    drawLineChart(myJSON.graph, "curve_chart", myJSON.makename);
                    /*if (command == 1 || command == 2 || command == 3 || command == 4 || command == 5 || command == 12) {
                     $("#curve_chart_ce").html('');
                     $("#chief").hide();
                     } else {
                     $("#chief").show();
                     drawLineChartce(myJSON.graphce, "curve_chart_ce");
                     }*/

                }

            }
        });
    });

    $("#command").on('change', function () {
        var command = $(this).children("option:selected").val();
        var product = $("#product option:selected").val();
        var make = $("#dashmake option:selected").val();
        var sizeval = $("#typefour option:selected").val();
        var fromdate = $("#fromdate").val();
        var todate = $("#todate").val();
        $("#typeone").prop('selectedIndex', 0);
        $("#typeone").material_select();
        $("#typetwo").prop('selectedIndex', 0);
        $("#typetwo").material_select();
        $("#typethree").prop('selectedIndex', 0);
        $("#typethree").material_select();
        $("#typefour").prop('selectedIndex', 0);
        $("#typefour").material_select();

        if (make != '') {
            if (product == 1) {
                $("#cable-size").show();
                $("#light-type").hide();
                var ext = 'RM';
                //$("#light-capacity").hide();
            } else if (product == 2) {
                $("#cable-size").hide();
                $("#light-type").show();
                var ext = 'NOS';
                //$("#light-capacity").show();
            } else {
                $("#cable-size").hide();
                $("#light-type").hide();
                var ext = 'RM';
            }

        }

        var sizes = '';
        var types = '';
        var ctypes = '';
        $.ajax({
            type: 'post',
            url: baseUrl + 'site/getmakedetails',
            data: 'type=1&make=' + make + '&product=' + product + '&sizeval=' + sizeval + '&command=' + command + '&fromdate=' + fromdate + '&todate=' + todate + '&_csrf-backend=' + csrf_token,
            beforeSend: function () {
                $("#u10").html('<img src="/assets/images/loading.gif" alt="">');
                $("#u20").html('<img src="/assets/images/loading.gif" alt="">');
                $("#u30").html('<img src="/assets/images/loading.gif" alt="">');
                $("#u11").html('<img src="/assets/images/loading.gif" alt="">');
                $("#u21").html('<img src="/assets/images/loading.gif" alt="">');
                $("#u31").html('<img src="/assets/images/loading.gif" alt="">');
                $("#u12").html('<img src="/assets/images/loading.gif" alt="">');
                $("#u22").html('<img src="/assets/images/loading.gif" alt="">');
                $("#u32").html('<img src="/assets/images/loading.gif" alt="">');
                $("#piechart").html('<img src="/assets/images/loading.gif" alt="">');
                $("#lightchart").html('<img src="/assets/images/loading.gif" alt="">');
                //$("#lightmakechart").html('<img src="/assets/images/loading.gif" alt="">');
                $("#chief").hide();
                $("#cwengg").hide();
                $("#gengg").hide();
                $("#l2").hide();
                if (make != '') {
                    $("#total").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#quantity").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#value").html('<img src="/assets/images/loading.gif" alt="">');
                    $(".boxzz").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#p2").hide();
                    $("#p3").hide();
                    $("#p4").hide();
                    $("#p5").hide();
                }
            },
            success: function (response) {


                var myJSON = JSON.parse(response);
                console.log(myJSON);
                if (myJSON) {
                    if (make != '') {
                        $("#total").html(myJSON.first.total);
                        $("#quantity").html(myJSON.first.quantity);
                        //$("#value").html(myJSON.first.value);
                        $("#value").html('<a class="btn red" onclick="getpricewithparams(30, 4, ' + product + ',' + make + ',4)">Click Here</a>');
                    }
                    var checkone = myJSON.valuesone[1] + myJSON.valuesone[2];
                    if (checkone != 0) {
                        $("#piechart").show();
                        drawPieChart(myJSON.labelsone, myJSON.valuesone, "piechart");
                    } else {
                        $("#piechart").html('No Data Available');
                    }
                    if (myJSON.user != 6 && product == 2) {
                        $("#lightchart").show();
                        drawPiemakeChart(myJSON.lightchart, "lightchart");
                    }
                    $("#u10").html(myJSON.first.aptenderstotal);
                    $("#u20").html(myJSON.first.aptendersquantity);
                    //$("#u30").html(myJSON.first.aptendersprice);
                    if (make != '') {
                        $("#u30").html('<a class="btn green" onclick="getpricewithparams(30, 1, ' + product + ',' + make + ',0)">Click Here</a>');
                    } else {
                        $("#u30").html('<a class="btn green" onclick="getpricewithparams(30, 1, ' + product + ',0,0)">Click Here</a>');
                    }
                    $("#u11").html(myJSON.first.artenderstotal);
                    $("#u21").html(myJSON.first.artendersquantity);
                    //$("#u31").html(myJSON.first.artendersprice);
                    if (make != '') {
                        $("#u31").html('<a class="btn blue" onclick="getpricewithparams(31, 2, ' + product + ',' + make + ',1)">Click Here</a>');
                    } else {
                        $("#u31").html('<a class="btn blue" onclick="getpricewithparams(31, 2, ' + product + ',0,1)">Click Here</a>');
                    }
                    $("#u12").html(myJSON.first.bltenderstotal);
                    $("#u22").html(myJSON.first.bltendersquantity);
                    //$("#u32").html(myJSON.first.bltendersprice);
                    if (make != '') {
                        $("#u32").html('<a class="btn red" onclick="getpricewithparams(32, 3, ' + product + ',' + make + ',2)">Click Here</a>');
                    } else {
                        $("#u32").html('<a class="btn red" onclick="getpricewithparams(32, 3, ' + product + ',0,2)">Click Here</a>');
                    }
                    if (myJSON.first.artenders == 0) {
                        $("#cable-size").hide();
                    }
                    if (make != '') {
                        $("#a1").html(myJSON.second.headone);
                        $("#a2").html('0 ' + ext + '');
                        $("#a3").html('0 ' + ext + '');
                        $("#a4").html('0 ' + ext + '');
                        $("#a5").html('0 ' + ext + '');
                        $("#b1").html(myJSON.second.headtwo);
                        $("#b2").html('0 ' + ext + '');
                        $("#b3").html('0 ' + ext + '');
                        $("#b4").html('0 ' + ext + '');
                        $("#b5").html('0 ' + ext + '');
                        $("#c1").html(myJSON.second.headthree);
                        $("#c2").html('0 ' + ext + '');
                        $("#c3").html('0 ' + ext + '');
                        $("#c4").html('0 ' + ext + '');
                        $("#c5").html('0 ' + ext + '');
                        $("#o1").html(myJSON.second.headfour);
                        $("#o2").html('0 ' + ext + '');
                        $("#o3").html('0 ' + ext + '');
                        $("#o4").html('0 ' + ext + '');
                        $("#o5").html('0 ' + ext + '');
                        $("#d2").html(myJSON.second.atotallight);
                        $("#d3").html(myJSON.second.totallight);
                        $("#d4").html(myJSON.second.withlight);
                        $("#d5").html(myJSON.second.withoutlight);
                        $("#e2").html(myJSON.second.totalclight);
                        $("#e3").html(myJSON.second.withclight);
                        $("#lighthead").html('With ' + myJSON.makename);
                        $("#lightheadtwo").html('Without ' + myJSON.makename);
                        $("#capacityhead").html(myJSON.makename);


                        /*$('.materialSelectsize').on('contentChanged', function () {
                         $(this).material_select();
                         });
                         
                         $.each(myJSON.sizes, function (key, value) {
                         if (key != 0) {
                         sizes += '<option value="' + key + '">' + value + '</option>';
                         } else {
                         sizes += '<option value="" disabled required>No Sizes</option>';
                         }
                         }
                         );
                         $("#typefour").html(sizes);
                         $("#typefour").trigger('contentChanged');*/

                        $('.materialSelecttype').on('contentChanged', function () {
                            $(this).material_select();
                        });

                        types += '<option value="">Select Fitting</option>';
                        $.each(myJSON.tlights, function (key, value) {
                            if (key != 0) {
                                types += '<option value="' + key + '">' + value + '</option>';
                            } else {
                                types += '<option value="" disabled required>No Types</option>';
                            }
                        }
                        );
                        $("#typelights").html(types);
                        $("#typelights").trigger('contentChanged');

                        /*$('.materialSelecttypecapacity').on('contentChanged', function () {
                         $(this).material_select();
                         });
                         
                         $.each(myJSON.clights, function (key, value) {
                         if (key != 0) {
                         ctypes += '<option value="' + key + '">' + value + '</option>';
                         } else {
                         ctypes += '<option value="" disabled required>No Capacity</option>';
                         }
                         }
                         );
                         $("#capacitylights").html(ctypes);
                         $("#capacitylights").trigger('contentChanged');*/
                        drawLineChart(myJSON.graph, "curve_chart", myJSON.makename);
                    } else {
                        drawLineChart(myJSON.graph, "curve_chart");
                    }

                    //drawChart();
                    /*if (command == 1 || command == 2 || command == 3 || command == 4 || command == 5 || command == 12 || command == 13) {
                     $("#curve_chart_ce").html('');
                     $("#chief").hide();
                     } else {
                     $("#chief").show();
                     drawLineChartce(myJSON.graphce, "curve_chart_ce");
                     }*/
                }

            }
        });
    });

    $(".cables").on('change', function () {
        var product = $("#product option:selected").val();
        var make = $("#dashmake option:selected").val();
        var command = $("#command option:selected").val();
        var typecable = $(this).attr("data-field");
        var val = $(this).children("option:selected").val();
        var fromdate = $("#fromdate").val();
        var todate = $("#todate").val();
        var typeone = '';
        var typetwo = '';
        var typethree = '';
        var typefour = '';
        var sizes = '';

        if (product == 1) {
            var ext = 'RM';
        } else {
            var ext = 'NOS';
        }

        if (typecable == 1) {
            typetwo = $("#typetwo option:selected").val();
            typethree = $("#typethree option:selected").val();
            typefour = $("#typefour option:selected").val();
        } else if (typecable == 2) {
            typeone = $("#typeone option:selected").val();
            typethree = $("#typethree option:selected").val();
            typefour = $("#typefour option:selected").val();
        } else if (typecable == 3) {
            typeone = $("#typeone option:selected").val();
            typetwo = $("#typetwo option:selected").val();
            typefour = $("#typefour option:selected").val();
        } else {
            typeone = $("#typeone option:selected").val();
            typetwo = $("#typetwo option:selected").val();
            typethree = $("#typethree option:selected").val();
        }

        $.ajax({
            type: 'post',
            url: baseUrl + 'site/getsingledata',
            data: 'val=' + val + '&make=' + make + '&product=' + product + '&command=' + command + '&typetwo=' + typetwo + '&typethree=' + typethree + '&typeone=' + typeone + '&typefour=' + typefour + '&type=' + typecable + '&fromdate=' + fromdate + '&todate=' + todate + '&_csrf-backend=' + csrf_token,
            beforeSend: function () {
                if (typecable == 1) {
                    $("#p2").hide();
                    $("#p3").hide();
                    $("#p4").hide();
                    $("#p5").hide();
                    $("#a2").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#a3").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#a4").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#a5").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#o2").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#o3").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#o4").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#o5").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#b2").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#b3").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#b4").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#b5").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#c2").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#c3").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#c4").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#c5").html('<img src="/assets/images/loading.gif" alt="">');
                } else if (typecable == 2) {
                    $("#p3").hide();
                    $("#p4").hide();
                    $("#p5").hide();
                    $("#a3").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#a4").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#a5").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#o3").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#o4").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#o5").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#b3").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#b4").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#b5").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#c3").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#c4").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#c5").html('<img src="/assets/images/loading.gif" alt="">');
                } else if (typecable == 3) {
                    $("#p4").hide();
                    $("#p5").hide();
                    $("#a4").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#a5").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#o4").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#o5").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#b4").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#b5").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#c4").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#c5").html('<img src="/assets/images/loading.gif" alt="">');
                } else {
                    $("#p5").hide();
                    $("#a5").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#b5").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#c5").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#o5").html('<img src="/assets/images/loading.gif" alt="">');
                }

            },
            success: function (response) {

                var myJSON = JSON.parse(response);
                if (myJSON) {


                    if (typecable == 1) {
                        $("#a2").html(myJSON.quantities.archived);
                        $("#b2").html(myJSON.quantities.without);
                        $("#c2").html(myJSON.quantities.with);
                        $("#o2").html(myJSON.quantities.approved);
                        $("#a3").html('0 ' + ext + '');
                        $("#b3").html('0 ' + ext + '');
                        $("#c3").html('0 ' + ext + '');
                        $("#o3").html('0 ' + ext + '');
                        $("#a4").html('0 ' + ext + '');
                        $("#b4").html('0 ' + ext + '');
                        $("#c4").html('0 ' + ext + '');
                        $("#o4").html('0 ' + ext + '');
                        $("#a5").html('0 ' + ext + '');
                        $("#b5").html('0 ' + ext + '');
                        $("#c5").html('0 ' + ext + '');
                        $("#o5").html('0 ' + ext + '');
                        var checktwo = myJSON.values[1] + myJSON.values[2];
                        if (checktwo != 0) {
                            $("#p2").show();
                            drawPieChart(myJSON.labels, myJSON.values, "p2");
                        } else {
                            $("#p2").hide();
                        }
                        $("#p5").html('');
                        $("#typetwo").prop('selectedIndex', 0);
                        $("#typetwo").removeAttr('disabled');
                        $("#typetwo").material_select();
                        $("#typethree").prop('selectedIndex', 0);
                        $("#typethree").prop('disabled', true);
                        $("#typethree").material_select();
                        $("#typefour").prop('selectedIndex', 0);
                        $("#typefour").prop('disabled', true);
                        $("#typefour").material_select();
                    } else if (typecable == 2) {
                        $("#a3").html(myJSON.quantities.archived);
                        $("#b3").html(myJSON.quantities.without);
                        $("#c3").html(myJSON.quantities.with);
                        $("#o3").html(myJSON.quantities.approved);
                        $("#a4").html('0 ' + ext + '');
                        $("#b4").html('0 ' + ext + '');
                        $("#c4").html('0 ' + ext + '');
                        $("#o4").html('0 ' + ext + '');
                        $("#a5").html('0 ' + ext + '');
                        $("#b5").html('0 ' + ext + '');
                        $("#c5").html('0 ' + ext + '');
                        $("#o5").html('0 ' + ext + '');
                        var checktwo = myJSON.values[1] + myJSON.values[2];
                        if (checktwo != 0) {
                            $("#p3").show();
                            drawPieChart(myJSON.labels, myJSON.values, "p3");
                        } else {
                            $("#p3").hide();
                        }
                        $("#p5").html('');
                        $("#typethree").prop('selectedIndex', 0);
                        $("#typethree").removeAttr('disabled');
                        $("#typethree").material_select();
                        $("#typefour").prop('selectedIndex', 0);
                        $("#typefour").prop('disabled', true);
                        $("#typefour").material_select();
                    } else if (typecable == 3) {
                        $("#a4").html(myJSON.quantities.archived);
                        $("#b4").html(myJSON.quantities.without);
                        $("#c4").html(myJSON.quantities.with);
                        $("#o4").html(myJSON.quantities.approved);
                        $("#a5").html('0 ' + ext + '');
                        $("#b5").html('0 ' + ext + '');
                        $("#c5").html('0 ' + ext + '');
                        $("#o5").html('0 ' + ext + '');
                        var checktwo = myJSON.values[1] + myJSON.values[2];
                        if (checktwo != 0) {
                            $("#p4").show();
                            drawPieChart(myJSON.labels, myJSON.values, "p4");
                        } else {
                            $("#p4").hide();
                        }
                        $("#p5").html('');
                        $("#typefour").prop('selectedIndex', 0);
                        $("#typefour").removeAttr('disabled');
                        $("#typefour").material_select();
                    } else {
                        $("#a5").html(myJSON.quantities.archivedsize);
                        $("#b5").html(myJSON.quantities.withoutsize);
                        $("#c5").html(myJSON.quantities.withsize);
                        $("#o5").html(myJSON.quantities.approvedsize);
                        var checktwo = myJSON.valuessize[1] + myJSON.valuessize[2];
                        if (checktwo != 0) {
                            $("#p5").show();
                            drawPieChart(myJSON.labels, myJSON.valuessize, "p5");
                        } else {
                            $("#p5").hide();
                        }

                    }

                }

                if (typecable == 2 || typecable == 3) {
                    $('.materialSelectsizes').on('contentChanged', function () {
                        $(this).material_select();
                    });

                    sizes += '<option value="">Select Size</option>';
                    $.each(myJSON.quantities.sizes, function (key, value) {
                        if (key != 0) {
                            sizes += '<option value="' + key + '">' + value + '</option>';
                        } else {
                            sizes += '<option value="" disabled required>No Sizes</option>';
                        }
                    }
                    );
                    $("#typefour").html(sizes);
                    $("#typefour").trigger('contentChanged');
                }

            }
        });
    });

    $(".lights").on('change', function () {
        var product = $("#product option:selected").val();
        var make = $("#dashmake option:selected").val();
        var command = $("#command option:selected").val();
        var val = $(this).children("option:selected").val();
        var fromdate = $("#fromdate").val();
        var todate = $("#todate").val();
        var typecable = $(this).attr("data-field");
        var sizes = '';


        $.ajax({
            type: 'post',
            url: baseUrl + 'site/getsinglelightdata',
            data: 'val=' + val + '&make=' + make + '&product=' + product + '&type=' + typecable + '&command=' + command + '&fromdate=' + fromdate + '&todate=' + todate + '&_csrf-backend=' + csrf_token,
            beforeSend: function () {
                if (typecable != 5) {
                    $("#e2").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#e3").html('<img src="/assets/images/loading.gif" alt="">');
                } else {
                    $("#l2").hide();
                    $("#d2").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#d3").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#d4").html('<img src="/assets/images/loading.gif" alt="">');
                    $("#d5").html('<img src="/assets/images/loading.gif" alt="">');
                }
            },
            success: function (response) {

                var myJSON = JSON.parse(response);
                if (myJSON) {
                    if (typecable != 5) {
                        $("#e2").html(myJSON.archivedcsize);
                        $("#e3").html(myJSON.withcsize);
                    } else {
                        $("#d2").html(myJSON.approvedsize);
                        $("#d3").html(myJSON.archivedsize);
                        $("#d4").html(myJSON.withsize);
                        $("#d5").html(myJSON.withoutsize);
                        var checksix = myJSON.graph[1] + myJSON.graph[2];
                        if (checksix != 0) {
                            $("#l2").show();
                            drawPieChart(myJSON.labels, myJSON.graph, "l2");
                        }
                    }

                }

            }
        });
    });


    $('#sbutton').on('click', function () {
        var val = $("#searchdata").val() // get the current value of the input field.
        var vallength = $("#searchdata").val().length; // get the current value of the input field.
        if (vallength < 6) {
            alert('Please enter minimum 6 digits Tender Id');
            return false;
        }
        if (val) {
            $.ajax({
                url: baseUrl + 'site/searchtender',
                type: "post",
                data: 'val=' + val + '&_csrf-backend=' + csrf_token,
                beforeSend: function () {
                    $(".mn-inner .col .page-title").html('');
                    $(".mn-inner #sort-data").css('display', 'none');
                    $(".mn-inner .add-contact").css('display', 'none');
                    $(".mn-inner .card.top").css('background-color', '#fff');
                    $(".mn-inner.inner-active-sidebar").css('display', 'none');
                    $(".mn-inner .card-content").html('<span class="fetchmess"><div class="loadercolorz"></div><p>Fetching <span class="greencolor">Tender</span></p></span>');
                    //$(".mn-inner .card").css('width','1058px');
                    $(".mn-inner form p").css('text-align', 'center');
                },
                success: function (data) {
                    if (data) {
                        $('.mn-inner').html(data);
                        $(".modalclose").css('z-index', '1');
                        $('select.materialSelectcontractor').select2({closeOnSelect: true, placeholder: 'Select Contractor'});
                        $('#current-project').DataTable({
                            language: {
                                searchPlaceholder: 'Search records',
                                sSearch: '',
                                sLengthMenu: 'Show _MENU_',
                                sLength: 'dataTables_length',
                                oPaginate: {
                                    sFirst: '<i class="material-icons">chevron_left</i>',
                                    sPrevious: '<i class="material-icons">chevron_left</i>',
                                    sNext: '<i class="material-icons">chevron_right</i>',
                                    sLast: '<i class="material-icons">chevron_right</i>'
                                }
                            },
                            "deferRender": true

                        });

                        $('.pdatepicker').pickadate({
                            format: 'dd-mm-yyyy',
                            selectMonths: true, // Creates a dropdown to control month
                            selectYears: 15, // Creates a dropdown of 15 years to control year
                            onSet: function (ele) {
                                if (ele.select) {
                                    $('.picker__holder').css('height', '0px');
                                    this.close();
                                }
                            },
                            onClose: function (ele) {
                                $('.picker__holder').css('height', '0px');
                            }
                        });
                        $('select').css('display', 'block');
                    } else {
                        $('.mn-inner .card-content').html('No Tenders Found.');
                    }
                    // $('#notification-bar').text('The page has been successfully loaded');
                },
                error: function () {
                    // $('#notification-bar').text('An error occurred');
                }
            });
        } else {
            alert('Please enter Tender Id');
        }
    });


    /*$(".itemdescall").autocomplete({
     source: baseUrl + 'site/getitemdesc',
     select: function( event, ui ) {
     event.preventDefault();
     $(".itemdescall").val(ui.item.value);
     }
     });*/




});

function getdivision(val) {
    if (val != '') {
        $.ajax({
            type: 'post',
            url: baseUrl + 'site/getdivisions',
            dataType: "json",
            data: {'value': val, '_csrf-backend': csrf_token},
            beforeSend: function () {
                $("#subdivision").prop('selectedIndex', '');
                $(".subdivisions").hide();
            },
            success: function (resultData) {
                var selects = '';
                selects += '<option value="" readonly>Select Division</option>';
                $.each(resultData.divisions, function (key, value) {
                    if (key != 0) {
                        selects += '<option value="' + key + '">' + value + '</option>';
                    } else {
                        selects += '<option value="" disabled required>No Divisions</option>';
                    }
                }
                );

                $("#division").html(selects);
                $("#division").material_select();
                $(".divisions").show();
            }
        });
    }
}

function getsubdivision(val) {
    if (val != '') {
        $.ajax({
            type: 'post',
            url: baseUrl + 'site/getsubdivisions',
            dataType: "json",
            data: {'value': val, '_csrf-backend': csrf_token},
            beforeSend: function () {
            },
            success: function (resultData) {
                var selects = '';
                selects += '<option value="" readonly>Select Sub Division</option>';
                $.each(resultData.sdivisions, function (key, value) {
                    if (key != 0) {
                        selects += '<option value="' + key + '">' + value + '</option>';
                    } else {
                        selects += '<option value="" disabled required>No Sub Divisions</option>';
                    }
                }
                );

                $("#subdivision").html(selects);
                $("#subdivision").material_select();
                $(".subdivisions").show();
            }
        });
    }
}

function getsubdepartments(val) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/getsubdepartments',
        dataType: "json",
        data: {'value': val, '_csrf-backend': csrf_token},
        beforeSend: function () {
        },
        success: function (resultData) {
            var selects = '';

            $.each(resultData.departments, function (key, value) {
                if (key != 0) {
                    selects += '<option value="' + key + '">' + value + '</option>';
                } else {
                    selects += '<option value="" disabled required>No Departments</option>';
                }
            }
            );

            $("#subdepartment").html(selects);
            $("#subdepartment").material_select();
            $(".subdepartments").show();
            $(".division").show();
        }
    });
}

function getdivisions(val) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/getalldivisions',
        dataType: "json",
        data: {'value': val, '_csrf-backend': csrf_token},
        beforeSend: function () {
        },
        success: function (resultData) {
            var selects = '';

            $.each(resultData.divisions, function (key, value) {
                if (key != 0) {
                    selects += '<option value="' + key + '">' + value + '</option>';
                } else {
                    selects += '<option value="" disabled required>No Divisions</option>';
                }
            }
            );

            $("#division").html(selects);
            $("#division").material_select();
            $(".divisions").show();
            $(".subdivision").show();
        }
    });
}

function showstatedepart(val) {
    var org = $("#department option:selected").val();
    if (org == '') {
        alert('Please select Organisation');
        return false;
    }
    if (org != 1) {
        $.ajax({
            type: 'post',
            url: baseUrl + 'site/getsubdepartmentsbystate',
            dataType: "json",
            data: {'value': val, org: org, '_csrf-backend': csrf_token},
            beforeSend: function () {
                $("#division").prop('selectedIndex', '');
                $(".divisions").hide();
            },
            success: function (resultData) {
                var selects = '';
                selects += '<option value="" readonly>Select Department</option>';
                $.each(resultData.departments, function (key, value) {
                    if (key != 0) {
                        selects += '<option value="' + key + '">' + value + '</option>';
                    } else {
                        selects += '<option value="" disabled required>No Departments</option>';
                    }
                }
                );

                $("#directorate").html(selects);
                $("#directorate").material_select();
                $("#subtypes").show();
                $(".departments").show();
                $(".states").css('margin-bottom', '20px');
                $("#commandz").prop('selectedIndex', '');
                $("#commandlist").hide();
                $("#cengineer").prop('selectedIndex', '');
                $("#ce").hide();
                $("#cwengineer").prop('selectedIndex', '');
                $("#cwe").hide();
                $("#gengineer").prop('selectedIndex', '');
                $("#ge").hide();
                $("#ddfavour").prop('selectedIndex', '');
                $("#ddlist").hide();
            }
        });
    }
}

function showsubtypes(val) {
    if (val == 1) {
        $("#commandlist").show();
        $("#ddlist").show();
        $(".ddfavour").select2();
        $(".states").css('margin-top', '20px');
        $(".states").css('margin-bottom', '20px');
        $("#directorate").prop('selectedIndex', '');
        $(".departments").hide();
        $("#division").prop('selectedIndex', '');
        $(".divisions").hide();
    } else {
        $.ajax({
            type: 'post',
            url: baseUrl + 'site/getsubdepartments',
            dataType: "json",
            data: {'value': val, '_csrf-backend': csrf_token},
            beforeSend: function () {
                $("#state").select2('val', '0');
                $("#division").prop('selectedIndex', '');
                $(".divisions").hide();
                $("#subdivision").prop('selectedIndex', '');
                $(".subdivisions").hide();
            },
            success: function (resultData) {
                var selects = '';
                selects += '<option value="" readonly>Select Department</option>';
                $.each(resultData.departments, function (key, value) {
                    if (key != 0) {
                        selects += '<option value="' + key + '">' + value + '</option>';
                    } else {
                        selects += '<option value="" disabled required>No Departments</option>';
                    }
                }
                );

                $("#directorate").html(selects);
                $("#directorate").material_select();
                $("#subtypes").show();
                $(".departments").show();
                $(".states").css('margin-bottom', '20px');
                $("#commandz").prop('selectedIndex', '');
                $("#commandlist").hide();
                $("#cengineer").prop('selectedIndex', '');
                $("#ce").hide();
                $("#cwengineer").prop('selectedIndex', '');
                $("#cwe").hide();
                $("#gengineer").prop('selectedIndex', '');
                $("#ge").hide();
                $("#ddfavour").prop('selectedIndex', '');
                $("#ddlist").hide();
            }
        });
    }

}

function gettype(val) {
    if (val == 19) {
        var accessoriestwo = '';
        var accessoriesthree = '';
        $("#accessorytwo").addClass('makesload');
        $("#accessorythree").addClass('makesload');


        $('.materialSelectaccessorytwo').on('contentChanged', function () {
            $(this).select2({closeOnSelect: true, placeholder: 'Select Type'});
        });

        accessoriestwo += '<option value="1" selected>1 Way</option>';
        accessoriestwo += '<option value="2">2 Way</option>';

        $("#acctwo0").html(accessoriestwo);
        $("#acctwo0").trigger('contentChanged');

        $('.materialSelectaccessorythree').on('contentChanged', function () {
            $(this).select2({closeOnSelect: true, placeholder: 'Select Sub Type'});
        });

        accessoriesthree += '<option value="0">Select Sub Type</option>';
        accessoriesthree += '<option value="1" selected>5 A</option>';
        accessoriesthree += '<option value="2">6 A</option>';
        accessoriesthree += '<option value="3">10 A</option>';
        accessoriesthree += '<option value="4">15 A</option>';
        accessoriesthree += '<option value="5">16 A</option>';
        accessoriesthree += '<option value="6">25 A</option>';
        accessoriesthree += '<option value="7">32 A</option>';

        $("#accthree0").html(accessoriesthree);
        $("#accthree0").trigger('contentChanged');

        $("#accthree0").attr('required');
        $("#accthree0").addClass('required');
        $("#accessorythree").show();

        $("#accessorytwo").removeClass('makesload');
        $("#accessorythree").removeClass('makesload');

    } else if (val == 20) {
        var accessoriestwo = '';
        var accessoriesthree = '';
        $("#accessorytwo").addClass('makesload');
        $("#accessorythree").addClass('makesload');

        $('.materialSelectaccessorytwo').on('contentChanged', function () {
            $(this).select2({closeOnSelect: true, placeholder: 'Select Type'});
        });

        accessoriestwo += '<option value="3" selected>3 Pin</option>';
        accessoriestwo += '<option value="4">5 Pin</option>';
        accessoriestwo += '<option value="5">6 Pin</option>';
        accessoriestwo += '<option value="6">Universal</option>';
        accessoriestwo += '<option value="7">Telephone Socket RJ-11</option>';
        accessoriestwo += '<option value="8">Computer Jack RJ-45</option>';
        accessoriestwo += '<option value="9">TV Socket</option>';
        accessoriestwo += '<option value="10">USB Socket</option>';

        $("#acctwo0").html(accessoriestwo);
        $("#acctwo0").trigger('contentChanged');

        $('.materialSelectaccessorythree').on('contentChanged', function () {
            $(this).select2({closeOnSelect: true, placeholder: 'Select Sub Type'});
        });

        accessoriesthree += '<option value="0">Select Sub Type</option>';
        accessoriesthree += '<option value="8" selected>5 A</option>';
        accessoriesthree += '<option value="9">6 A</option>';
        accessoriesthree += '<option value="10">10 A</option>';
        accessoriesthree += '<option value="11">13 A</option>';
        accessoriesthree += '<option value="12">15 A</option>';
        accessoriesthree += '<option value="13">16 A</option>';

        $("#accthree0").html(accessoriesthree);
        $("#accthree0").trigger('contentChanged');

        $("#accthree0").attr('required');
        $("#accthree0").addClass('required');
        $("#accessorythree").show();

        $("#accessorytwo").removeClass('makesload');
        $("#accessorythree").removeClass('makesload');

    } else {
        var accessoriestwo = '';
        $("#accessorytwo").addClass('makesload');

        if ($("#accthree0").data('select2')) {
            $("#accthree0").select2("val", "0");
            $("#accthree0").removeAttr('required');
            $("#accthree0").removeClass('required');
            $("#accessorythree").hide();
        }

        $('.materialSelectaccessorytwo').on('contentChanged', function () {
            $(this).select2({closeOnSelect: true, placeholder: 'Select Type'});
        });

        accessoriestwo += '<option value="11" selected>Dimmer</option>';
        accessoriestwo += '<option value="12">5 Step</option>';

        $("#acctwo0").html(accessoriestwo);
        $("#acctwo0").trigger('contentChanged');

        $("#accessorytwo").removeClass('makesload');
    }

}

function gettypeinner(val, id, newnum) {
    if (val == 19) {
        var accessoriestwo = '';
        var accessoriesthree = '';
        $("#accessorytwo" + id + "").addClass('makesload');
        $("#accessorythree" + id + "").addClass('makesload');


        $('.materialSelectaccessorytwo').on('contentChanged', function () {
            $(this).select2({closeOnSelect: true, placeholder: 'Select Type'});
        });

        accessoriestwo += '<option value="1" selected>1 Way</option>';
        accessoriestwo += '<option value="2">2 Way</option>';

        $("#acctwo" + newnum + "").html(accessoriestwo);
        $("#acctwo" + newnum + "").trigger('contentChanged');

        $('.materialSelectaccessorythree').on('contentChanged', function () {
            $(this).select2({closeOnSelect: true, placeholder: 'Select Sub Type'});
        });

        accessoriesthree += '<option value="0">Select Sub Type</option>';
        accessoriesthree += '<option value="1" selected>5 A</option>';
        accessoriesthree += '<option value="2">6 A</option>';
        accessoriesthree += '<option value="3">10 A</option>';
        accessoriesthree += '<option value="4">15 A</option>';
        accessoriesthree += '<option value="5">16 A</option>';
        accessoriesthree += '<option value="6">25 A</option>';
        accessoriesthree += '<option value="7">32 A</option>';

        $("#accthree" + newnum + "").html(accessoriesthree);
        $("#accthree" + newnum + "").trigger('contentChanged');

        $("#accthree" + newnum + "").attr('required');
        $("#accthree" + newnum + "").addClass('required');
        $("#accessorythree" + id + "").show();

        $("#accessorytwo" + id + "").removeClass('makesload');
        $("#accessorythree" + id + "").removeClass('makesload');

    } else if (val == 20) {
        var accessoriestwo = '';
        var accessoriesthree = '';
        $("#accessorytwo" + id + "").addClass('makesload');
        $("#accessorythree" + id + "").addClass('makesload');

        $('.materialSelectaccessorytwo').on('contentChanged', function () {
            $(this).select2({closeOnSelect: true, placeholder: 'Select Type'});
        });

        accessoriestwo += '<option value="3" selected>3 Pin</option>';
        accessoriestwo += '<option value="4">5 Pin</option>';
        accessoriestwo += '<option value="5">6 Pin</option>';
        accessoriestwo += '<option value="6">Universal</option>';
        accessoriestwo += '<option value="7">Telephone Socket RJ-11</option>';
        accessoriestwo += '<option value="8">Computer Jack RJ-45</option>';
        accessoriestwo += '<option value="9">TV Socket</option>';
        accessoriestwo += '<option value="10">USB Socket</option>';

        $("#acctwo" + newnum + "").html(accessoriestwo);
        $("#acctwo" + newnum + "").trigger('contentChanged');

        $('.materialSelectaccessorythree').on('contentChanged', function () {
            $(this).select2({closeOnSelect: true, placeholder: 'Select Sub Type'});
        });

        accessoriesthree += '<option value="0">Select Sub Type</option>';
        accessoriesthree += '<option value="8" selected>5 A</option>';
        accessoriesthree += '<option value="9">6 A</option>';
        accessoriesthree += '<option value="10">10 A</option>';
        accessoriesthree += '<option value="11">13 A</option>';
        accessoriesthree += '<option value="12">15 A</option>';
        accessoriesthree += '<option value="13">16 A</option>';

        $("#accthree" + newnum + "").html(accessoriesthree);
        $("#accthree" + newnum + "").trigger('contentChanged');

        $("#accthree" + newnum + "").attr('required');
        $("#accthree" + newnum + "").addClass('required');
        $("#accessorythree" + id + "").show();

        $("#accessorytwo" + id + "").removeClass('makesload');
        $("#accessorythree" + id + "").removeClass('makesload');

    } else {
        var accessoriestwo = '';
        $("#accessorytwo" + id + "").addClass('makesload');

        if ($("#accthree" + newnum + "").data('select2')) {
            $("#accthree" + newnum + "").select2("val", "0");
            $("#accthree" + newnum + "").removeAttr('required');
            $("#accthree" + newnum + "").removeClass('required');
            $("#accessorythree" + id + "").hide();
        }

        $('.materialSelectaccessorytwo').on('contentChanged', function () {
            $(this).select2({closeOnSelect: true, placeholder: 'Select Type'});
        });

        accessoriestwo += '<option value="11" selected>Dimmer</option>';
        accessoriestwo += '<option value="12">5 Step</option>';

        $("#acctwo" + newnum + "").html(accessoriestwo);
        $("#acctwo" + newnum + "").trigger('contentChanged');

        $("#accessorytwo" + id + "").removeClass('makesload');
    }

}

function getsubtype(val) {
    if (val == 1) {
        var accessoriesthree = '';
        $("#accessorythree").addClass('makesload');

        $('.materialSelectaccessorythree').on('contentChanged', function () {
            $(this).select2({closeOnSelect: true, placeholder: 'Select Sub Type'});
        });

        accessoriesthree += '<option value="0">Select Sub Type</option>';
        accessoriesthree += '<option value="1" selected>5 A</option>';
        accessoriesthree += '<option value="2">6 A</option>';
        accessoriesthree += '<option value="3">10 A</option>';
        accessoriesthree += '<option value="4">15 A</option>';
        accessoriesthree += '<option value="5">16 A</option>';
        accessoriesthree += '<option value="6">25 A</option>';
        accessoriesthree += '<option value="7">32 A</option>';


        $("#accthree0").html(accessoriesthree);
        $("#accthree0").trigger('contentChanged');

        $("#accessorythree").removeClass('makesload');

        $("#accthree0").attr('required');
        $("#accthree0").addClass('required');
        $("#accessorythree").show();

    } else if (val == 2) {
        var accessoriesthree = '';
        $("#accessorythree").addClass('makesload');

        $('.materialSelectaccessorythree').on('contentChanged', function () {
            $(this).select2({closeOnSelect: true, placeholder: 'Select Sub Type'});
        });

        accessoriesthree += '<option value="0">Select Sub Type</option>';
        accessoriesthree += '<option value="1" selected>5 A</option>';
        accessoriesthree += '<option value="2">6 A</option>';
        accessoriesthree += '<option value="3">10 A</option>';

        $("#accthree0").html(accessoriesthree);
        $("#accthree0").trigger('contentChanged');

        $("#accessorythree").removeClass('makesload');

        $("#accthree0").attr('required');
        $("#accthree0").addClass('required');
        $("#accessorythree").show();

    } else if (val == 3 || val == 4 || val == 5 || val == 6) {
        var accessoriesthree = '';
        $("#accessorythree").addClass('makesload');

        $('.materialSelectaccessorythree').on('contentChanged', function () {
            $(this).select2({closeOnSelect: true, placeholder: 'Select Sub Type'});
        });

        accessoriesthree += '<option value="0">Select Sub Type</option>';
        accessoriesthree += '<option value="8" selected>5 A</option>';
        accessoriesthree += '<option value="9">6 A</option>';
        accessoriesthree += '<option value="10">10 A</option>';
        accessoriesthree += '<option value="11">13 A</option>';
        accessoriesthree += '<option value="12">15 A</option>';
        accessoriesthree += '<option value="13">16 A</option>';

        $("#accthree0").html(accessoriesthree);
        $("#accthree0").trigger('contentChanged');

        $("#accessorythree").removeClass('makesload');

        $("#accthree0").attr('required');
        $("#accthree0").addClass('required');
        $("#accessorythree").show();

    } else {
        if ($("#accthree0").data('select2')) {
            $("#accthree0").select2("val", "0");
            $("#accthree0").removeAttr('required');
            $("#accthree0").removeClass('required');
            $("#accessorythree").hide();
        }
    }
}

function getsubtypeinner(val, id, newnum) {
    if (val == 1) {
        var accessoriesthree = '';
        $("#accessorythree" + id + "").addClass('makesload');

        $('.materialSelectaccessorythree').on('contentChanged', function () {
            $(this).select2({closeOnSelect: true, placeholder: 'Select Sub Type'});
        });

        accessoriesthree += '<option value="0">Select Sub Type</option>';
        accessoriesthree += '<option value="1" selected>5 A</option>';
        accessoriesthree += '<option value="2">6 A</option>';
        accessoriesthree += '<option value="3">10 A</option>';
        accessoriesthree += '<option value="4">15 A</option>';
        accessoriesthree += '<option value="5">16 A</option>';
        accessoriesthree += '<option value="6">25 A</option>';
        accessoriesthree += '<option value="7">32 A</option>';


        $("#accthree" + newnum + "").html(accessoriesthree);
        $("#accthree" + newnum + "").trigger('contentChanged');

        $("#accessorythree" + id + "").removeClass('makesload');

        $("#accthree" + newnum + "").attr('required');
        $("#accthree" + newnum + "").addClass('required');
        $("#accessorythree" + id + "").show();

    } else if (val == 2) {
        var accessoriesthree = '';
        $("#accessorythree" + id + "").addClass('makesload');

        $('.materialSelectaccessorythree').on('contentChanged', function () {
            $(this).select2({closeOnSelect: true, placeholder: 'Select Sub Type'});
        });

        accessoriesthree += '<option value="0">Select Sub Type</option>';
        accessoriesthree += '<option value="1" selected>5 A</option>';
        accessoriesthree += '<option value="2">6 A</option>';
        accessoriesthree += '<option value="3">10 A</option>';

        $("#accthree" + newnum + "").html(accessoriesthree);
        $("#accthree" + newnum + "").trigger('contentChanged');

        $("#accessorythree" + id + "").removeClass('makesload');

        $("#accthree" + newnum + "").attr('required');
        $("#accthree" + newnum + "").addClass('required');
        $("#accessorythree" + id + "").show();

    } else if (val == 3 || val == 4 || val == 5 || val == 6) {
        var accessoriesthree = '';
        $("#accessorythree" + id + "").addClass('makesload');

        $('.materialSelectaccessorythree').on('contentChanged', function () {
            $(this).select2({closeOnSelect: true, placeholder: 'Select Sub Type'});
        });

        accessoriesthree += '<option value="0">Select Sub Type</option>';
        accessoriesthree += '<option value="8" selected>5 A</option>';
        accessoriesthree += '<option value="9">6 A</option>';
        accessoriesthree += '<option value="10">10 A</option>';
        accessoriesthree += '<option value="11">13 A</option>';
        accessoriesthree += '<option value="12">15 A</option>';
        accessoriesthree += '<option value="13">16 A</option>';

        $("#accthree" + newnum + "").html(accessoriesthree);
        $("#accthree" + newnum + "").trigger('contentChanged');

        $("#accessorythree" + id + "").removeClass('makesload');

        $("#accthree" + newnum + "").attr('required');
        $("#accthree" + newnum + "").addClass('required');
        $("#accessorythree" + id + "").show();

    } else {
        if ($("#accthree" + newnum + "").data('select2')) {
            $("#accthree" + newnum + "").select2("val", "0");
            $("#accthree" + newnum + "").removeAttr('required');
            $("#accthree" + newnum + "").removeClass('required');
            $("#accessorythree" + id + "").hide();
        }
    }
}

function showdivs(val) {
    if (val == 1) {
        $("#cablesdiv").show();
        $("#cables").attr('required', 'true');
        $("#lightdiv").hide();
        $("#lighting").removeAttr('required');
        $("#wiresdiv").hide();
        $("#wires").removeAttr('required');
        $("#cementdiv").hide();
        $("#rsteeldiv").hide();
        $("#ssteeldiv").hide();
        $("#nsteeldiv").hide();
        $("#cement").removeAttr('required');
        $("#rsteel").removeAttr('required');
        $("#ssteel").removeAttr('required');
        $("#nsteel").removeAttr('required');
    } else if (val == 2) {
        $("#lightdiv").show();
        $("#lighting").attr('required', 'true');
        $("#cablesdiv").hide();
        $("#cables").removeAttr('required');
        $("#wiresdiv").hide();
        $("#wires").removeAttr('required');
        $("#cementdiv").hide();
        $("#rsteeldiv").hide();
        $("#ssteeldiv").hide();
        $("#nsteeldiv").hide();
        $("#cement").removeAttr('required');
        $("#rsteel").removeAttr('required');
        $("#ssteel").removeAttr('required');
        $("#nsteel").removeAttr('required');
    } else if (val == 3) {
        $("#lightdiv").hide();
        $("#lighting").removeAttr('required');
        $("#cablesdiv").hide();
        $("#cables").removeAttr('required');
        $("#wiresdiv").show();
        $("#wires").attr('required', 'true');
        $("#cementdiv").hide();
        $("#rsteeldiv").hide();
        $("#ssteeldiv").hide();
        $("#nsteeldiv").hide();
        $("#cement").removeAttr('required');
        $("#rsteel").removeAttr('required');
        $("#ssteel").removeAttr('required');
        $("#nsteel").removeAttr('required');
    } else if (val == 4) {
        $("#cablesdiv").hide();
        $("#lightdiv").hide();
        $("#lighting").removeAttr('required');
        $("#cables").removeAttr('required');
        $("#wiresdiv").hide();
        $("#wires").removeAttr('required');
        $("#cementdiv").show();
        $("#rsteeldiv").hide();
        $("#ssteeldiv").hide();
        $("#nsteeldiv").hide();
        $("#cement").attr('required', 'true');
        $("#rsteel").removeAttr('required');
        $("#ssteel").removeAttr('required');
        $("#nsteel").removeAttr('required');
    } else if (val == 5) {
        $("#lightdiv").hide();
        $("#cables").removeAttr('required');
        $("#cablesdiv").hide();
        $("#lighting").removeAttr('required');
        $("#cementdiv").hide();
        $("#cement").removeAttr('required');
        $("#wiresdiv").hide();
        $("#wires").removeAttr('required');
        $("#rsteeldiv").show();
        $("#rsteel").attr('required', 'true');
        $("#ssteeldiv").hide();
        $("#nsteeldiv").hide();
        $("#ssteel").removeAttr('required');
        $("#nsteel").removeAttr('required');
        //$("#cables").removeAttr('required');
    } else if (val == 6) {
        $("#lightdiv").hide();
        $("#lighting").removeAttr('required');
        $("#wiresdiv").hide();
        $("#wires").removeAttr('required');
        //$("#lighting").attr('required', 'true');
        $("#cablesdiv").hide();
        $("#cables").removeAttr('required');
        $("#cementdiv").hide();
        $("#cement").removeAttr('required');
        $("#rsteeldiv").hide();
        $("#rsteel").removeAttr('required');
        $("#ssteeldiv").show();
        $("#ssteel").attr('required', 'true');
        $("#nsteeldiv").hide();
        $("#nsteel").removeAttr('required');
        //$("#cables").removeAttr('required');
    } else if (val == 7) {
        $("#lightdiv").hide();
        $("#lighting").removeAttr('required');
        $("#wiresdiv").hide();
        $("#wires").removeAttr('required');
        //$("#lighting").attr('required', 'true');
        $("#cablesdiv").hide();
        $("#cables").removeAttr('required');
        $("#cementdiv").hide();
        $("#cement").removeAttr('required');
        $("#rsteeldiv").hide();
        $("#rsteel").removeAttr('required');
        $("#ssteeldiv").hide();
        $("#ssteel").removeAttr('required');
        $("#nsteeldiv").show();
        $("#nsteel").attr('required', 'true');
        //$("#cables").removeAttr('required');
    } else {
        $("#cablesdiv").hide();
        $("#lightdiv").hide();
        $("#wiresdiv").hide();
        $("#cementdiv").hide();
        $("#rsteeldiv").hide();
        $("#ssteeldiv").hide();
        $("#nsteeldiv").hide();
        //$("#lighting").removeAttr('required');
        //$("#cables").removeAttr('required');
    }
}

function showdivssearch(val) {
    if (val == 1) {
        $("#cablesdiv").show();
        $("#lightdiv").hide();
        $("#wiresdiv").hide();
        $("#cementdiv").hide();
        $("#rsteeldiv").hide();
        $("#ssteeldiv").hide();
        $("#nsteeldiv").hide();
    } else if (val == 2) {
        $("#lightdiv").show();
        $("#cablesdiv").hide();
        $("#wiresdiv").hide();
        $("#cementdiv").hide();
        $("#rsteeldiv").hide();
        $("#ssteeldiv").hide();
        $("#nsteeldiv").hide();
    } else if (val == 3) {
        $("#lightdiv").hide();
        $("#cablesdiv").hide();
        $("#wiresdiv").show();
        $("#cementdiv").hide();
        $("#rsteeldiv").hide();
        $("#ssteeldiv").hide();
        $("#nsteeldiv").hide();
    } else if (val == 4) {
        $("#cablesdiv").hide();
        $("#lightdiv").hide();
        $("#wiresdiv").hide();
        $("#cementdiv").show();
        $("#rsteeldiv").hide();
        $("#ssteeldiv").hide();
        $("#nsteeldiv").hide();
    } else if (val == 5) {
        $("#lightdiv").hide();
        $("#cablesdiv").hide();
        $("#cementdiv").hide();
        $("#wiresdiv").hide();
        $("#rsteeldiv").show();
        $("#ssteeldiv").hide();
        $("#nsteeldiv").hide();
    } else if (val == 6) {
        $("#lightdiv").hide();
        $("#wiresdiv").hide();
        $("#cablesdiv").hide();
        $("#cementdiv").hide();
        $("#rsteeldiv").hide();
        $("#ssteeldiv").show();
        $("#nsteeldiv").hide();
    } else if (val == 7) {
        $("#lightdiv").hide();
        $("#wiresdiv").hide();
        $("#cablesdiv").hide();
        $("#cementdiv").hide();
        $("#rsteeldiv").hide();
        $("#ssteeldiv").hide();
        $("#nsteeldiv").show();
    } else {
        $("#cablesdiv").hide();
        $("#lightdiv").hide();
        $("#wiresdiv").hide();
        $("#cementdiv").hide();
        $("#rsteeldiv").hide();
        $("#ssteeldiv").hide();
        $("#nsteeldiv").hide();
    }
}

function getcity(val) {

    var selects = '';
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/getcities',
        dataType: "json",
        data: {'val': val, '_csrf-backend': csrf_token},
        success: function (data) {
            // setup listener for custom event to re-initialize on change
            $('.contact-city').on('contentChanged', function () {
                $(this).material_select();
            });
            $.each(data.cities, function (key, value) {
                if (key != 0) {
                    selects += '<option value="' + key + '">' + value + '</option>';
                } else {
                    selects += '<option value="" disabled required>No Cities</option>';
                }
            }
            );
            $("#city").html(selects);
            $("#city").trigger('contentChanged');
        }
    });

}

function getsubtypes(value) {
    if (value == 1) {
        $("#second").show();
        $("#third").show();
    } else {
        $("#second").hide();
        $("#third").hide();
    }
}

function getsubsubtypes(value) {
    if (value == 3) {
        $("#third").hide();
    } else {
        $("#third").show();
    }
}

function getsubpricetypes(value) {
    if (value == 1) {
        $("#second").show();
        $("#third").show();
    } else if (value == 2) {
        $("#second").hide();
        $("#third").hide();
    } else {
        $("#second").show();
        $("#third").hide();
    }
}

function getparentonetypes(value) {
    var one = $('#mtypeone :selected').val();
    var three = $('#mtypethree :selected').val();
    var two = value;
    if (one == 1) {
        if (three != '') {
            $("#fourth").show();
        }
    } else if (one == 5) {
        $("#fourth").show();
        $("#prices").show();
    }
    var selects = '';
    $.ajax({
        type: 'post',
        url: baseUrl + 'products/getsizes',
        dataType: "json",
        data: {'one': one, 'two': two, 'three': three, '_csrf-backend': csrf_token},
        success: function (resultData) {
            // setup listener for custom event to re-initialize on change

            $('.materialsize').on('contentChanged', function () {
                $(this).material_select();
            });

            $.each(resultData.select, function (key, value) {
                if (key != 0) {
                    selects += '<option value="' + key + '">' + value + '</option>';
                } else {
                    selects += '<option value="" disabled required>No Sizes</option>';
                }
            }
            );
            $("#mtypefour").html(selects);
            $("#mtypefour").trigger('contentChanged');
        }
    });

}

function getparenttwotypes(value) {
    var one = $('#mtypeone :selected').val();
    var two = $('#mtypetwo :selected').val();
    var three = value;
    $("#fourth").show();
    $("#fifth").show();
    $("#prices").show();
    var selects = '';
    $.ajax({
        type: 'post',
        url: baseUrl + 'products/getsizes',
        dataType: "json",
        data: {'one': one, 'two': two, 'three': three, '_csrf-backend': csrf_token},
        success: function (resultData) {
            // setup listener for custom event to re-initialize on change

            $('.materialsize').on('contentChanged', function () {
                $(this).material_select();
            });

            $.each(resultData.select, function (key, value) {
                if (key != 0) {
                    selects += '<option value="' + key + '">' + value + '</option>';
                } else {
                    selects += '<option value="" disabled required>No Sizes</option>';
                }
            }
            );
            $("#mtypefour").html(selects);
            $("#mtypefour").trigger('contentChanged');
        }
    });

}

function getprice(id, ptype, type, key) {
    var quantity = $('#u2' + key + '').text();
    $.ajax({
        type: 'post',
        url: baseUrl + 'search/getprice',
        data: {'type': type, ptype: ptype, quantity: quantity, key: key, '_csrf-backend': csrf_token},
        beforeSend: function () {
            $("#" + id + "").html('<img src="/assets/images/loading.gif" alt="">');
        },
        success: function (resultData) {
            $('#' + id + '').html(resultData);
        }
    });
}

function getpricewithparams(id, ptype, type, make, key) {
    var command = $("#command option:selected").val();
    var fromdate = $("#fromdate").val();
    var todate = $("#todate").val();
    if (ptype == 4) {
        var quantity = $('#quantity').text();
    } else {
        var quantity = $('#u2' + key + '').text();
    }
    $.ajax({
        type: 'post',
        url: baseUrl + 'search/getpricewithparams',
        data: {'type': type, ptype: ptype, command: command, fromdate: fromdate, todate: todate, make: make, key: key, quantity: quantity, '_csrf-backend': csrf_token},
        beforeSend: function () {
            if (ptype == 4) {
                $("#value").html('<img src="/assets/images/loading.gif" alt="">');
            } else {
                $("#u" + id + "").html('<img src="/assets/images/loading.gif" alt="">');
            }
        },
        success: function (resultData) {
            if (ptype == 4) {
                $('#value').html(resultData);
            } else {
                $('#u' + id + '').html(resultData);
            }

        }
    });
}

function addrate(num, itemid) {
    var newnum = (parseInt(num) + parseInt(1));
    var dt = new Date();
    var time = dt.getHours() + dt.getMinutes() + dt.getSeconds();
    var rand = Math.floor((Math.random() * 100) + 1);
    var id = rand + time;
    var rowitems = "<div class='row added rateinfo' id='inforates" + newnum + id + "' ><div class='input-field col s6' id='contractorsdiv" + id + "'><select class='validate required contype materialSelectcon ratelist" + id + " browser-default' required='' name='cont[]' ><option value='' disabled required>No Contractors</option></select></div><div class='input-field col s3'><input id='rate" + id + "' type='number' name = 'rate[]' min='1' step='1' onkeypress='return event.charCode >= 48 && event.charCode <= 57' required='' class='validate required' value=''><label for='rate" + id + "'>Rate</label></div> <div class='input-field col s2'><a class='waves-effect waves-light btn blue m-b-xs button' onclick='deleteratebutton(" + id + "," + newnum + ")'>Delete</a></div></div>";
    $("#allrates" + itemid + "").append(rowitems);
    $('select.materialSelectcon').select2({
        closeOnSelect: true,
        placeholder: 'Select Contractor',
        allowClear: true,
        ajax: {
            headers: {
                "Authorization": "Bearer " + csrf_token,
                "Content-Type": "application/json",
            },
            type: 'get',
            url: baseUrl + 'contractor/getcontractors',
            dataType: 'json',
            data: function (params) {
                return {
                    term: params.term || '',
                    page: params.page || 1
                }
            },
            cache: true,
        }
    });
}

function addcontractor(num, tid) {
    var newnum = (parseInt(num) + parseInt(1));
    var dt = new Date();
    var time = dt.getHours() + dt.getMinutes() + dt.getSeconds();
    var rand = Math.floor((Math.random() * 100) + 1);
    var id = rand + time;
    $("#addrate" + tid + "").removeAttr('onclick');
    $("#addrate" + tid + "").attr('onclick', 'addcontractor("' + newnum + '","' + tid + '")');
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/getcolumns',
        data: {'tid': tid, newnum: newnum, '_csrf-backend': csrf_token},
        beforeSend: function () {
            $("#addrate" + tid + "").html('<img src="/assets/images/loading.gif" alt="">');
        },
        success: function (resultData) {
            $("#rateboxes" + tid + "").append(resultData);
            $("#addrate" + tid + "").html('Add Contractor');
            $('select.materialSelectcon').select2({
                closeOnSelect: true,
                placeholder: 'Select Contractor',
                allowClear: true,
                ajax: {
                    headers: {
                        "Authorization": "Bearer " + csrf_token,
                        "Content-Type": "application/json",
                    },
                    type: 'get',
                    url: baseUrl + 'contractor/getcontractors',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        }
                    },
                    cache: true,
                }
            });
        }
    });



}

function repeatcontractor(num, tid) {
    $("#" + tid + num + "").html('<img src="/assets/images/loading.gif" alt="">');
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/getrepeatcolumns',
        data: {'tid': tid, newnum: num, '_csrf-backend': csrf_token},
        success: function (resultData) {
            $("#" + tid + num + "").html(resultData);
            $('select.materialSelectcon').select2({
                closeOnSelect: true,
                placeholder: 'Select Contractor',
                allowClear: true,
                ajax: {
                    headers: {
                        "Authorization": "Bearer " + csrf_token,
                        "Content-Type": "application/json",
                    },
                    type: 'get',
                    url: baseUrl + 'contractor/getcontractors',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        }
                    },
                    cache: true,
                }
            });
        }
    });



}

function showcolumns(cid, tid, newnum) {
    var valcont = $('#allconts' + tid + '').val();
    if (valcont.indexOf(cid) != -1) {
        alert('Contractor already added');
        repeatcontractor(newnum, tid);
        return false;
    }
    $("#select" + tid + newnum + "").prop("disabled", true);
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/getallcolumns',
        data: {'tid': tid, cid: cid, '_csrf-backend': csrf_token},
        beforeSend: function () {
            $("#box" + tid + newnum + "").html('<img src="/assets/images/loading.gif" alt="">');
        },
        success: function (resultData) {
            if ($('#allconts' + tid + '').val() === '') {
                $('#allconts' + tid + '').val($('#allconts' + tid + '').val() + cid);
            } else {
                $('#allconts' + tid + '').val($('#allconts' + tid + '').val() + ',' + cid);
            }
            $("#box" + tid + newnum + "").html(resultData);
            $("#box" + tid + newnum + "").append('<a class="waves-effect waves-light btn red m-b-xs deletebox" id="delstatic' + tid + newnum + '" onclick="delcontractorstatic(' + cid + ',' + tid + ',' + newnum + ')">Delete</a>');
            $("#contid" + tid + newnum + "").val(cid);


        }
    });
}

function delcontractor(cid, tid, newnum) {
    var string = $("#allconts" + tid + "").val();
    var res = string.replace(cid, "");
    $("#allconts" + tid + "").val(res);
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/delcontractor',
        data: {'tid': tid, cid: cid, '_csrf-backend': csrf_token},
        beforeSend: function () {
            $("#del" + tid + newnum + "").html('<img src="/assets/images/loading.gif" alt="">');
        },
        success: function (resultData) {

            if (resultData == 1) {
                $("#upperbox" + tid + newnum + "").remove();
            } else {
                alert('Could not delete contractor data');
            }

        }
    });
}

function delcontractorstatic(cid, tid, newnum) {
    var string = $("#allconts" + tid + "").val();
    var res = string.replace(cid, "");
    $("#allconts" + tid + "").val(res);
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/delcontractor',
        data: {'tid': tid, cid: cid, '_csrf-backend': csrf_token},
        beforeSend: function () {
            $("#delstatic" + tid + newnum + "").html('<img src="/assets/images/loading.gif" alt="">');
        },
        success: function (resultData) {

            $("#" + tid + newnum + "").remove();

        }
    });
}

function saverate(rate, cid, iid, itemid, tid) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/saverate',
        data: {'rate': rate, cid: cid, iid: iid, itemid: itemid, tid: tid, '_csrf-backend': csrf_token},
        success: function (resultData) {


        }
    });
}

function addrow(num) {
    var selected = $('#tenderfour :selected').val();
    var subone = $('#tenderfive :selected').val();
    var subtwo = $('#tendersix :selected').val();
    var selects = '';
    var sizes = '';
    var types = '';
    var cap = '';
    var newnum = (parseInt(num) + parseInt(1));
    var dt = new Date();
    var time = dt.getHours() + dt.getMinutes() + dt.getSeconds();
    var rand = Math.floor((Math.random() * 100) + 1);
    var id = rand + time;
    //var rowitems = "<div class='row added iteminfo' id='inforows" + id + "' ><div class='input-field col s1'><input id='itemtender" + id + "' type='text' name = 'itemtender[]' required='' class='validate required' value=''><label for='itemtender'>Sr. no</label></div><div class='input-field col s2' id='sizesdiv" + id + "'><select class='validate required materialSelectsize" + id + "' required='' name='desc[]' id='sizes" + newnum + "' style='display: inline; height: 0px; padding: 0px; width: 0px;'><option value='' disabled required>No Sizes</option></select></div><div class='input-field col s2' id='corediv" + id + "'><select class='validate required materialSelectcore' required='' name='core[]' id='core" + newnum + "'><option value=''>Select Core</option><option value='1'>Core 1</option><option value='2'>Core 2</option><option value='3'>Core 3</option><option value='4'>Core 3.5</option><option value='5'>Core 4</option></select></div><div class='input-field col s2' id='typefit" + id + "'><select class='validate required materialSelecttypefit' required='' name='type[]' id='type" + newnum + "'></select></div> <div class='input-field col s2' id='capacityfit" + id + "'><select class='validate required materialSelectcapacityfit' required='' name='text[]' id='text" + newnum + "'></select></div><div class='input-field col s1'><input id='itemunit" + id + "' type='text' name = 'units[]' required='' class='validate required' value='RM'><label for='itemunit" + id + "'>Units</label><!--textarea id='item' name='desc' class='materialize-textarea'></textarea><label for='item'>Item description</label--></div><div class='input-field col s1'><input id='quantity" + id + "' type='text' name = 'quantity[]' required='' class='validate required' value=''><label for='quantity" + id + "'>Quantity</label></div> <div class='input-field col s3'><select class='validate required materialSelect" + id + " browser-default' required='' name='makes[]' multiple id='makes" + newnum + "'></select></div><div class='input-field col s2'><input id='makeid" + id + "' type='text' name = 'makeid[]' class='validate' value=''><label for='makeid" + id + "'>CatPart Id </label></div> <div class='input-field col s2'><a class='waves-effect waves-light btn blue m-b-xs button' onclick='deletebutton(" + id + ")'>Delete</a></div></div>";
    if (selected == 1 || selected == 5) {
        var label = "<label for='itemunit" + id + "'>Unit</label>";
    } else {
        var label = '';
    }

    var rowitems = "<div class='row added iteminfo' id='inforows" + newnum + id + "' ><div class='input-field col s1'><input id='itemtender" + id + "' type='text' name = 'itemtender[]' required='' class='validate required' value=''><label for='itemtender'>Sr. no</label></div><div class='input-field col s2' id='sizesdiv" + id + "'><select class='validate required materialSelectsize" + id + " browser-default' required='' name='desc[]' id='sizes" + newnum + "' style='display: inline; height: 0px; padding: 0px; width: 0px;'><option value='' disabled required>No Sizes</option></select></div><div class='input-field col s3' id='corediv" + id + "'><select class='validate required materialSelectcore' required='' name='core[]' id='core" + newnum + "'><option value=''>Select Core</option><option value='1'>1 Core</option><option value='2'>2 Core</option><option value='3'>3 Core</option><option value='4'>3.5 Core</option><option value='5'>4 Core</option><option value='6'>5 Core</option><option value='7'>6 Core</option><option value='8'>7 Core</option><option value='9'>8 Core</option><option value='10'>10 Core</option></select></div><div class='input-field col s3' id='typefit" + id + "'><select class='validate required materialSelecttypefit browser-default' required='' name='type[]' id='type" + newnum + "'></select></div> <div class='input-field col s2' id='capacityfit" + id + "'><select class='validate required materialSelectcapacityfit browser-default' required='' name='text[]' id='text" + newnum + "'></select></div><div class='input-field col s3' id='accessoryone" + id + "'><select class='validate required materialSelectaccessoryone browser-default' required='' name='accessoryone[]' id='accone" + newnum + "' onchange='gettypeinner(this.value," + id + "," + newnum + ")'></select></div><div class='input-field col s2' id='accessorytwo" + id + "'><select class='validate required materialSelectaccessorytwo browser-default' required='' name='accessorytwo[]' id='acctwo" + newnum + "' onchange='getsubtypeinner(this.value," + id + "," + newnum + ")'></select></div><div class='input-field col s2' id='accessorythree" + id + "'><select class='validate required materialSelectaccessorythree browser-default' required='' name='accessorythree[]' id='accthree" + newnum + "' ></select></div><div class='input-field col s1'><input id='itemunit" + id + "' type='text' name = 'units[]' onkeypress='return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123)' required='' class='validate required' value=''>" + label + "<!--textarea id='item' name='desc' class='materialize-textarea'></textarea><label for='item'>Item description</label--></div><div class='input-field col s1'><input id='quantity" + id + "' type='number' name = 'quantity[]' min='1' step='1' onkeypress='return event.charCode >= 48 && event.charCode <= 57' required='' class='validate required' value=''><label for='quantity" + id + "'>Quantity</label></div> <div class='input-field col s2'><input id='makeid" + id + "' type='text' name = 'makeid[]' class='validate' value=''><label for='makeid" + id + "'>CatPart Id </label></div> <div class='input-field col s2'><a class='waves-effect waves-light btn blue m-b-xs button' onclick='deletebutton(" + id + "," + newnum + ")'>Delete</a></div></div>";

    var row = "<div id=info" + id + "><div class='col s12'><div class='input-fields col s2 row'><label>Select type of work</label><select class='validate required materialSelect' name='tenderone[]' id='tenderone" + id + "' onchange='getdatasub(this.value," + id + ")'><option value='' disabled selected>Select</option><option value='1'>E/M</option></select></div><div id='second" + id + "' style='display: none;'><div class='input-fields col s2 row'><label>Select Sub Type</label><select class='validate required materialSelect' name='tendertwo[]' id='tendertwo" + id + "' onchange='getseconddatasub(this.value," + id + ")'><option value='' disabled selected>Select</option></select></div></div><div id='third" + id + "' style='display: none;'><div class='input-fields col s2 row'><label>Select Sub Type</label><select class='validate required materialSelect' name='tenderthree[]' id='tenderthree" + id + "' onchange='getthirddatasub(this.value," + id + ")'><option value='' disabled selected>Select</option></select></div></div><div id='fourth" + id + "' style='display: none;'><div class='input-fields col s2 row'><label>Select Sub Type</label><select class='validate required materialSelect' name='tenderfour[]' id='tenderfour" + id + "' onchange='getfourdatasub(this.value," + id + "," + newnum + ")'><option value='' disabled selected>Select</option></select></div></div><div id='fifth" + id + "' style='display: none;'><div class='input-fields col s2 row'><label>Select Sub Type</label><select class='validate required materialSelect' name='tenderfive[]' id='tenderfive" + id + "' onchange='getfivedatasub(this.value," + id + ")'><option value='' disabled selected>Select</option></select></div></div><div id='sixth" + id + "' style='display: none;'><div class='input-fields col s2 row'><label>Select Sub Type</label><select class='validate required materialSelect' name='tendersix[]' id='tendersix" + id + "' onchange='getsixdatasub(this.value," + id + ")'><option value='' disabled selected>Select</option></select></div></div></div>" + rowitems + '</div>';
    $("#itemdata").append(rowitems);
    $("#itemtender" + id + "").focus();

    if (selected == 1) {
        $("#typefit" + id + "").hide();
        $("#type" + newnum + "").removeAttr('required');
        $("#type" + newnum + "").removeClass('required');
        $("#capacityfit" + id + "").hide();
        $("#text" + newnum + "").removeAttr('required');
        $("#text" + newnum + "").removeClass('required');
        $("#accessorytwo" + id + "").hide();
        $("#acctwo" + newnum + "").removeAttr('required');
        $("#acctwo" + newnum + "").removeClass('required');
        $("#accessoryone" + id + "").hide();
        $("#accone" + newnum + "").removeAttr('required');
        $("#accone" + newnum + "").removeClass('required');
        $("#accessorythree" + id + "").hide();
        $("#accthree" + newnum + "").removeAttr('required');
        $("#accthree" + newnum + "").removeClass('required');
        $("#sizesdiv" + id + "").show();
        $("#sizes" + newnum + "").attr('required');
        $("#sizes" + newnum + "").addClass('required');
        $("#itemunit" + id + "").val('');
        $("#corediv" + id + "").show();
        $("#core" + newnum + "").attr('required');
        $("#core" + newnum + "").addClass('required');
    } else if (selected == 2) {
        $("#sizesdiv" + id + "").hide();
        $("#sizesdiv" + id + "").prop('selectedIndex', '');
        $("#sizes" + newnum + "").removeAttr('required');
        $("#sizes" + newnum + "").removeClass('required');
        $("#corediv" + id + "").hide();
        $("#core" + newnum + "").removeAttr('required');
        $("#core" + newnum + "").removeClass('required');
        $("#accessorytwo" + id + "").hide();
        $("#acctwo" + newnum + "").removeAttr('required');
        $("#acctwo" + newnum + "").removeClass('required');
        $("#accessoryone" + id + "").hide();
        $("#accone" + newnum + "").removeAttr('required');
        $("#accone" + newnum + "").removeClass('required');
        $("#accessorythree" + id + "").hide();
        $("#accthree" + newnum + "").removeAttr('required');
        $("#accthree" + newnum + "").removeClass('required');
        $("#typefit" + id + "").show();
        $("#type" + newnum + "").attr('required');
        $("#type" + newnum + "").addClass('required');
        $("#capacityfit" + id + "").show();
        $("#text" + newnum + "").attr('required');
        $("#text" + newnum + "").addClass('required');
        $("#itemunit" + id + "").val('NOS');
    } else if (selected == 4) {
        $("#sizesdiv" + id + "").hide();
        $("#sizesdiv" + id + "").prop('selectedIndex', '');
        $("#sizes" + newnum + "").removeAttr('required');
        $("#sizes" + newnum + "").removeClass('required');
        $("#corediv" + id + "").hide();
        $("#core" + newnum + "").removeAttr('required');
        $("#core" + newnum + "").removeClass('required');
        $("#typefit" + id + "").hide();
        $("#type" + newnum + "").removeAttr('required');
        $("#type" + newnum + "").removeClass('required');
        $("#capacityfit" + id + "").hide();
        $("#text" + newnum + "").removeAttr('required');
        $("#text" + newnum + "").removeClass('required');
        $("#accessorytwo" + id + "").show();
        $("#acctwo" + newnum + "").attr('required');
        $("#acctwo" + newnum + "").addClass('required');
        $("#accessoryone" + id + "").show();
        $("#accone" + newnum + "").attr('required');
        $("#accone" + newnum + "").addClass('required');
        $("#accessorythree" + id + "").show();
        $("#accthree" + newnum + "").attr('required');
        $("#accthree" + newnum + "").addClass('required');
        $("#itemunit" + id + "").val('NOS');
    } else {
        $("#corediv" + id + "").hide();
        $("#core" + newnum + "").removeAttr('required');
        $("#core" + newnum + "").removeClass('required');
        $("#accessorytwo" + id + "").hide();
        $("#acctwo" + newnum + "").removeAttr('required');
        $("#acctwo" + newnum + "").removeClass('required');
        $("#accessoryone" + id + "").hide();
        $("#accone" + newnum + "").removeAttr('required');
        $("#accone" + newnum + "").removeClass('required');
        $("#accessorythree" + id + "").hide();
        $("#accthree" + newnum + "").removeAttr('required');
        $("#accthree" + newnum + "").removeClass('required');
        $("#typefit" + id + "").hide();
        $("#type" + newnum + "").removeAttr('required');
        $("#type" + newnum + "").removeClass('required');
        $("#capacityfit" + id + "").hide();
        $("#text" + newnum + "").removeAttr('required');
        $("#text" + newnum + "").removeClass('required');
        $("#sizesdiv" + id + "").show();
        $("#sizes" + newnum + "").attr('required');
        $("#sizes" + newnum + "").addClass('required');
        $("#itemunit" + id + "").val('');
    }

    /*if (selected == 1) {
     $("#corediv" + id + "").show();
     $("#core" + newnum + "").attr('required');
     $("#core" + newnum + "").addClass('required');
     } else {
     $("#corediv" + id + "").hide();
     $("#core" + newnum + "").removeAttr('required');
     $("#core" + newnum + "").removeClass('required');
     }
     
     if (selected != 2) {
     $("#typefit" + id + "").hide();
     $("#type" + newnum + "").removeAttr('required');
     $("#type" + newnum + "").removeClass('required');
     $("#capacityfit" + id + "").hide();
     $("#text" + newnum + "").removeAttr('required');
     $("#text" + newnum + "").removeClass('required');
     $("#sizesdiv" + id + "").show();
     $("#sizes" + newnum + "").attr('required');
     $("#sizes" + newnum + "").addClass('required');
     } else if (selected == 2) {
     $("#typefit" + id + "").show();
     $("#type" + newnum + "").attr('required');
     $("#type" + newnum + "").addClass('required');
     $("#capacityfit" + id + "").show();
     $("#text" + newnum + "").attr('required');
     $("#text" + newnum + "").addClass('required');
     $("#sizesdiv" + id + "").hide();
     $("#sizesdiv" + id + "").prop('selectedIndex', '');
     $("#sizes" + newnum + "").removeAttr('required');
     $("#sizes" + newnum + "").removeClass('required');
     $("#itemunit" + id + "").val('NOS');
     }*/


    $("#makes" + newnum + "").select2({closeOnSelect: true, placeholder: 'Select Makes'});
    $("#sizes" + newnum + "").select2({closeOnSelect: true, placeholder: 'Select Sizes'});
    $("#core" + newnum + "").material_select();
    $("#type" + newnum + "").select2({closeOnSelect: true, placeholder: 'Select Type'});
    $("#text" + newnum + "").select2({closeOnSelect: true, placeholder: 'Select Capacity'});
    $("#accone" + newnum + "").select2({closeOnSelect: true, placeholder: 'Select Accessory'});
    $("#acctwo" + newnum + "").select2({closeOnSelect: true, placeholder: 'Select Type'});
    $("#accthree" + newnum + "").select2({closeOnSelect: true, placeholder: 'Select Sub Type'});


    /*$("#tenderone" + id + "").material_select();
     $("#tendertwo" + id + "").material_select();
     $("#tenderthree" + id + "").material_select();
     $("#tenderfour" + id + "").material_select();
     $("#tenderfive" + id + "").material_select();
     $("#tendersix" + id + "").material_select();*/
    /*$("#item" + id + "").autocomplete({
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
     $("#item" + id + "").val(ui.item.value);
     return false;
     }
     
     });*/
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/getmakes',
        dataType: "json",
        data: {'value': selected, '_csrf-backend': csrf_token},
        success: function (resultData) {
            // setup listener for custom event to re-initialize on change

            $('.materialSelect' + id + '').on('contentChanged', function () {
                $(this).select2({closeOnSelect: true, placeholder: 'Select Makes'});
            });

            $.each(resultData, function (key, value) {
                if (key != 0) {
                    selects += '<option value="' + key + '">' + value + '</option>';
                } else {
                    selects += '<option value="" disabled required>No Makes</option>';
                }
            }
            );
            $("#makes" + newnum + "").html(selects);
            $("#makes" + newnum + "").trigger('contentChanged');
            $("#addrow").removeAttr('onclick');
            $("#addrow").attr('onclick', 'addrow("' + newnum + '")');
        }
    });

    if (selected != 2 || selected != 4) {
        $.ajax({
            type: 'post',
            url: baseUrl + 'site/getsizes',
            dataType: "json",
            data: {'subparent': selected, 'subone': subone, 'subtwo': subtwo, '_csrf-backend': csrf_token},
            success: function (resultData) {
                // setup listener for custom event to re-initialize on change

                $.each(resultData, function (key, value) {
                    if (key != 0) {
                        sizes += '<option value="' + key + '">' + value + '</option>';
                    } else {
                        sizes += '<option value="" disabled required>No Sizes</option>';
                    }

                }
                );

                $("#sizes" + newnum + "").html(sizes);
                $("#sizes" + newnum + "").select2({closeOnSelect: true, placeholder: 'Select Sizes'});
                $("#addrow").removeAttr('onclick');
                $("#addrow").attr('onclick', 'addrow("' + newnum + '")');
            }
        });
    }

    if (selected == 2) {
        $.ajax({
            type: 'post',
            url: baseUrl + 'site/getfittings',
            dataType: "json",
            data: {'_csrf-backend': csrf_token},
            success: function (resultData) {
                // setup listener for custom event to re-initialize on change

                $.each(resultData.alltypes, function (key, value) {
                    if (key != 0) {
                        types += '<option value="' + key + '">' + value + '</option>';
                    } else {
                        types += '<option value="" disabled required>No Types</option>';
                    }

                }
                );

                $("#type" + newnum + "").html(types);
                $("#type" + newnum + "").select2({closeOnSelect: true, placeholder: 'Select Type'});

                $.each(resultData.allcapacities, function (key, value) {
                    if (key != 0) {
                        cap += '<option value="' + key + '">' + value + '</option>';
                    } else {
                        cap += '<option value="" disabled required>No Capacities</option>';
                    }
                }
                );

                $("#text" + newnum + "").html(cap);
                $("#text" + newnum + "").select2({closeOnSelect: true, placeholder: 'Select Capacity'});
                $("#addrow").removeAttr('onclick');
                $("#addrow").attr('onclick', 'addrow("' + newnum + '")');
            }
        });
    }

    if (selected == 4) {
        var accessories = '';
        $.ajax({
            type: 'post',
            url: baseUrl + 'products/getaccessories',
            dataType: "json",
            data: {'_csrf-backend': csrf_token},
            success: function (resultData) {
                // setup listener for custom event to re-initialize on change

                $.each(resultData.alltypes, function (key, value) {
                    if (key != 0) {
                        accessories += '<option value="' + key + '">' + value + '</option>';
                    } else {
                        accessories += '<option value="" disabled required>No Accessories</option>';
                    }

                }
                );

                $("#accone" + newnum + "").html(accessories);
                $("#accone" + newnum + "").select2({closeOnSelect: true, placeholder: 'Select Accessory'});

            }
        });

        var accessoriestwo = '';
        var accessoriesthree = '';

        $('.materialSelectaccessorytwo').on('contentChanged', function () {
            $(this).select2({closeOnSelect: true, placeholder: 'Select Type'});
        });

        accessoriestwo += '<option value="1" selected>1 Way</option>';
        accessoriestwo += '<option value="2">2 Way</option>';

        $("#acctwo" + newnum + "").html(accessoriestwo);
        $("#acctwo" + newnum + "").trigger('contentChanged');

        $('.materialSelectaccessorythree').on('contentChanged', function () {
            $(this).select2({closeOnSelect: true, placeholder: 'Select Sub Type'});
        });

        accessoriesthree += '<option value="0">Select Sub Type</option>';
        accessoriesthree += '<option value="1" selected>5 A</option>';
        accessoriesthree += '<option value="2">6 A</option>';
        accessoriesthree += '<option value="3">10 A</option>';
        accessoriesthree += '<option value="4">15 A</option>';
        accessoriesthree += '<option value="5">16 A</option>';
        accessoriesthree += '<option value="6">25 A</option>';
        accessoriesthree += '<option value="7">32 A</option>';

        $("#accthree" + newnum + "").html(accessoriesthree);
        $("#accthree" + newnum + "").trigger('contentChanged');

        $("#addrow").removeAttr('onclick');
        $("#addrow").attr('onclick', 'addrow("' + newnum + '")');
    }

}

function getcengineeraddress(value) {

    $("#cengineer").prop('selectedIndex', 0);
    $("#cengineer").material_select();
    $("#ce").hide();

    $("#cwengineer").prop('selectedIndex', 0);
    $("#cwengineer").material_select();
    $("#cwe").hide();

    $("#gengineer").prop('selectedIndex', 0);
    $("#gengineer").material_select();
    $("#ge").hide();

    var arr = ['2', '12', '14'];
    if (arr.indexOf(value) < 0) {
        $("#ce").show();
        $.ajax({
            type: 'post',
            url: baseUrl + 'site/getcengineeraddress',
            dataType: "json",
            data: {'value': value, '_csrf-backend': csrf_token},
            success: function (resultData) {
                var arrz = ['1', '3', '4', '5'];
                if (arrz.indexOf(value) >= 0) {
                    $("#ge").show();
                    $("#gengineer").html(resultData.data);
                    $("#cwe").hide();
                    $("#ce").hide();
                    $("#gengineer").material_select();
                } else {
                    $("#cengineer").html(resultData.data);
                    $("#cwe").hide();
                    $("#ge").hide();
                    $("#cengineer").material_select();
                }
            }
        });
    } else {
        $("#cengineer").material_select('destroy');
        $("#ce").hide();
        $("#cwengineer").material_select('destroy');
        $("#cwe").hide();
        $("#gengineer").material_select('destroy');
        $("#ge").hide();

    }
}

function getcengineer(value) {

    $("#cengineer").prop('selectedIndex', 0);
    $("#cengineer").material_select();
    $("#ce").hide();

    $("#cwengineer").prop('selectedIndex', 0);
    $("#cwengineer").material_select();
    $("#cwe").hide();

    $("#gengineer").prop('selectedIndex', 0);
    $("#gengineer").material_select();
    $("#ge").hide();

    var arr = ['0', '2', '12', '14'];
    if (arr.indexOf(value) < 0) {
        $("#ce").show();
        $.ajax({
            type: 'post',
            url: baseUrl + 'site/getcengineer',
            dataType: "json",
            data: {'value': value, '_csrf-backend': csrf_token},
            success: function (resultData) {
                var arrz = ['1', '3', '4', '5', '13'];
                if (arrz.indexOf(value) >= 0) {
                    $("#ge").show();
                    $("#gengineer").html(resultData.data);
                    $("#cwe").hide();
                    $("#ce").hide();
                    $("#gengineer").material_select();
                } else {
                    $("#cengineer").html(resultData.data);
                    //$("#ce").show();
                    $("#cwe").hide();
                    $("#ge").hide();
                    $("#cengineer").material_select();
                }
            }
        });
    } else {
        $("#cengineer").material_select('destroy');
        $("#ce").hide();
        $("#cwengineer").material_select('destroy');
        $("#cwe").hide();
        $("#gengineer").material_select('destroy');
        $("#ge").hide();

    }
}

function getcwengineer(value) {
    $("#cwengineer").prop('selectedIndex', 0);
    $("#cwengineer").material_select();
    $('#cwe').hide();
    $("#gengineer").prop('selectedIndex', 0);
    $("#gengineer").material_select();
    $("#ge").hide();
    var arr = ['5', '6', '7', '14', '36'];
    if (arr.indexOf(value) < 0) {
        $("#cwe").show();
        $.ajax({
            type: 'post',
            url: baseUrl + 'site/getcwengineer',
            dataType: "json",
            data: {'value': value, '_csrf-backend': csrf_token},
            success: function (resultData) {
                $("#cwengineer").html(resultData.data);
                $("#ge").hide();
                $("#cwengineer").material_select();
            }
        });
    } else {
        $("#cwengineer").material_select('destroy');
        $("#cwe").hide();
        $("#gengineer").material_select('destroy');
        $("#ge").hide();
    }
}

function getgengineer(value) {
    $("#gengineer").prop('selectedIndex', 0);
    $("#gengineer").material_select();
    $("#ge").hide();
    var arr = ['24', '25', '39', '44', '49', '52', '53', '57', '58', '59', '64', '69', '84', '85', '90', '91', '92', '103', '111', '125', '134', '136', '137', '138', '139', '140'];
    if (arr.indexOf(value) < 0) {
        $("#ge").show();
        $.ajax({
            type: 'post',
            url: baseUrl + 'site/getgengineer',
            dataType: "json",
            data: {'value': value, '_csrf-backend': csrf_token},
            success: function (resultData) {
                $("#gengineer").html(resultData.data);
                $("#gengineer").material_select();
            }
        });
    } else {
        $("#gengineer").material_select('destroy');
        $("#ge").hide();

    }
}

function getdata(value) {
    if (value == 1) {
        $("#tenderthree").prop('selectedIndex', '');
        $("#tenderthree").material_select();
        $('#third').show();
        $("#tenderfour").prop('selectedIndex', '');
        $("#tenderfour").material_select();
        $('#fourth').show();
    } else {
        $("#tenderthree").prop('selectedIndex', '');
        $("#tenderthree").material_select();
        $('#third').hide();
        $("#tenderfour").prop('selectedIndex', '');
        $("#tenderfour").material_select();
        $('#fourth').hide();
    }
    $("#tenderfive").prop('selectedIndex', '');
    $("#tenderfive").material_select();
    $('#fifth').hide();
    $("#tendersix").prop('selectedIndex', '');
    $("#tendersix").material_select();
    $('#sixth').hide();
    $("#second").show();
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/getdata',
        dataType: "json",
        data: {'value': value, '_csrf-backend': csrf_token},
        success: function (resultData) {
            $("#tendertwo").html(resultData.data);
            if (value == 1) {
                getseconddata(1);
                $("#third").show();
                $("#fourth").show();
            } else {
                $("#third").hide();
                $("#fourth").hide();
            }
            $("#fifth").hide();
            $("#sixth").hide();
            //$("#itemdata .added").remove();
            $("#itemdata").hide();
            $("#makes").hide();
            $('.browser-default').prop('selectedIndex', 0);
            $("#item-error").remove();
            $("#itembutton").hide();
            $("#tendertwo").material_select();
        }
    });
}

function getdatasub(value, id) {
    $("#tenderthree" + id + "").prop('selectedIndex', '');
    $("#tenderthree" + id + "").material_select();
    $('#third' + id + '').hide();
    $("#tenderfour" + id + "").prop('selectedIndex', '');
    $("#tenderfour" + id + "").material_select();
    $('#fourth' + id + '').hide();
    $("#tenderfive" + id + "").prop('selectedIndex', '');
    $("#tenderfive" + id + "").material_select();
    $('#fifth' + id + '').hide();
    $("#tendersix" + id + "").prop('selectedIndex', '');
    $("#tendersix" + id + "").material_select();
    $('#sixth' + id + '').hide();
    $("#second" + id + "").show();
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/getdata',
        dataType: "json",
        data: {'value': value, '_csrf-backend': csrf_token},
        success: function (resultData) {
            $("#tendertwo" + id + "").html(resultData.data);
            $("#third" + id + "").hide();
            $("#fourth" + id + "").hide();
            $("#fifth" + id + "").hide();
            $("#sixth" + id + "").hide();
            //$("#itemdata" + id + " .added").remove();
            $("#itemdata" + id + "").hide();
            $("#item-error" + id + "").remove();
            $("#itembutton" + id + "").hide();
            $("#tendertwo" + id + "").material_select();
        }
    });
}

function changehold(tid) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/on-hold',
        dataType: "json",
        data: {'value': tid, '_csrf-backend': csrf_token},
        success: function (resultData) {
            if (resultData.status == '1') {
                swal("", "Status successfully changed!", "success");
                if (resultData.hold == '1') {
                    $("#tenderhold" + tid + "").removeClass('green');
                    $("#tenderhold" + tid + "").addClass('red');
                    $("#tenderhold" + tid + "").text('On Hold');
                } else {
                    $("#tenderhold" + tid + "").removeClass('red');
                    $("#tenderhold" + tid + "").addClass('green');
                    $("#tenderhold" + tid + "").text('Ready');
                }

            } else {
                swal("", "Status could not be changed! Please try again.", "error");
            }

        }
    });
}

function movearchive(tid) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/movearchive',
        dataType: "json",
        data: {'value': tid, '_csrf-backend': csrf_token},
        success: function (resultData) {
            if (resultData.status == '1') {
                swal("", "Tender successfully archived!", "success");
                if (resultData.arc == '1') {
                    $("#tenderarc" + tid + "").removeClass('blue');
                    $("#tenderarc" + tid + "").addClass('green');
                    $("#tenderarc" + tid + "").text('Archived');
                } else {
                    $("#tenderhold" + tid + "").removeClass('green');
                    $("#tenderhold" + tid + "").addClass('blue');
                    $("#tenderhold" + tid + "").text('Archive');
                }

            } else {
                swal("", "Tender could not be archived! Please try again.", "error");
            }

        }
    });
}

function resendmail(email, filename, filepath) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'mail/resendmail',
        dataType: "json",
        data: {'email': email, filename: filename, filepath: filepath, '_csrf-backend': csrf_token},
        success: function (resultData) {
            if (resultData.status == '1') {
                swal("", "Mail successfully sent!", "success");
            } else {
                swal("", "Mail could not be changed! Please try again.", "error");
            }

        }
    });
}

function approvetender(value) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/approvetender',
        dataType: "json",
        data: {'value': value, '_csrf-backend': csrf_token},
        success: function (resultData) {
            if (resultData.status == '1') {
                swal("", "Tender successfully approved!", "success");
                window.location.reload();
            } else {
                swal("", "Tender could not be approved!", "error");
            }

        }
    });
}

function approveitem(value) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/approveitem',
        dataType: "json",
        data: {'value': value, '_csrf-backend': csrf_token},
        success: function (resultData) {
            if (resultData.status == '1') {
                swal("", "Item successfully approved!", "success");
                window.location.reload();
            } else {
                swal("", "Item could not be approved!", "error");
            }

        }
    });
}

function getseconddata(value) {
    var selects = '';
    $("#tenderfour").prop('selectedIndex', '');
    $("#tenderfour").material_select();
    $('#fourth').show();
    $("#tenderfive").prop('selectedIndex', '');
    $("#tenderfive").material_select();
    $('#fifth').hide();
    $("#tendersix").prop('selectedIndex', '');
    $("#tendersix").material_select();
    $('#sixth').hide();
    $("#third").show();
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/getseconddata',
        dataType: "json",
        data: {'value': value, '_csrf-backend': csrf_token},
        success: function (resultData) {
            if (resultData.item == '0') {
                $("#tenderthree").html(resultData.data);
                getthirddata(1);
                $("#fourth").show();
                $("#fifth").hide();
                $("#sixth").hide();
                //$("#itemdata .added").remove();
                $("#itemdata").hide();
                $("#makes").hide();
                $('.browser-default').prop('selectedIndex', 0);
                $("#item-error").remove();
                $("#itembutton").hide();
                $("#tenderthree").material_select();
            } else {
                if (resultData.value == 14 || resultData.value == 15 || resultData.value == 16 || resultData.value == 17) {
                    $("#third").hide();
                    $("#fourth").hide();
                    $("#fifth").hide();
                    $("#sixth").hide();
                    $("#itemtender").removeAttr('required');
                    $("#sizes0").removeAttr('required');
                    $("#core0").removeAttr('required');
                    $("#type0").removeAttr('required');
                    $("#text0").removeAttr('required');
                    $("#quantity").removeAttr('required');
                    $("#itemunit").removeAttr('required');
                    $("#accessorytwo").hide();
                    $("#acctwo0").removeAttr('required');
                    $("#acctwo0").removeClass('required');
                    $("#accessoryone").hide();
                    $("#accthree0").removeAttr('required');
                    $("#accthree0").removeClass('required');
                    $("#accessorythree").hide();
                    $("#accone0").removeAttr('required');
                    $("#accone0").removeClass('required');
                    $("#itemunit").val('');
                    //$("#itemdata").show();
                    $("#makes").show();
                    //$("#itembutton").show();
                    if ($("#makes0").data('select2')) {
                        $("#makes0").select2("val", "");
                    }
                    // setup listener for custom event to re-initialize on change
                    $('.materialSelect').on('contentChanged', function () {
                        $(this).select2({closeOnSelect: true, placeholder: 'Select Makes'});
                    });

                    $.each(resultData.select, function (key, value) {
                        if (key != 01) {
                            selects += '<option value="' + key + '">' + value + '</option>';
                        } else {
                            selects += '<option value="" disabled required>No Makes</option>';
                        }
                    }
                    );
                    //$("#makes0").select2("val", "");
                    $("#makes0").html(selects);
                    $("#makes0").trigger('contentChanged');

                } else {
                    $("#third").hide();
                    $("#fourth").hide();
                    $("#fifth").hide();
                    $("#sixth").hide();
                    $("#itemdata").show();
                    $("#makes").show();
                    $("#itembutton").show();
                }

            }
        }
    });
}

function getseconddatasub(value, id) {
    $("#tenderfour" + id + "").prop('selectedIndex', '');
    $("#tenderfour" + id + "").material_select();
    $('#fourth' + id + '').hide();
    $("#tenderfive" + id + "").prop('selectedIndex', '');
    $("#tenderfive" + id + "").material_select();
    $('#fifth' + id + '').hide();
    $("#tendersix" + id + "").prop('selectedIndex', '');
    $("#tendersix" + id + "").material_select();
    $('#sixth' + id + '').hide();
    $("#third" + id + "").show();
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/getseconddata',
        dataType: "json",
        data: {'value': value, '_csrf-backend': csrf_token},
        success: function (resultData) {
            if (resultData.item == '0') {
                $("#tenderthree" + id + "").html(resultData.data);
                $("#fourth" + id + "").hide();
                $("#fifth" + id + "").hide();
                $("#sixth" + id + "").hide();
                //$("#itemdata" + id + " .added").remove();
                $("#itemdata" + id + "").hide();
                $("#item-error" + id + "").remove();
                $("#itembutton" + id + "").hide();
                $("#tenderthree" + id + "").material_select();
            } else {
                $("#third" + id + "").hide();
                $("#fourth" + id + "").hide();
                $("#fifth" + id + "").hide();
                $("#sixth" + id + "").hide();
                $("#itemdata" + id + "").show();
                $("#itembutton" + id + "").show();
            }
        }
    });
}

function getthirddata(value) {
    $("#tenderfive").prop('selectedIndex', '');
    $("#tenderfive").material_select();
    $('#fifth').hide();
    $("#tendersix").prop('selectedIndex', '');
    $("#tendersix").material_select();
    $('#sixth').hide();
    $("#fourth").show();
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/getthirddata',
        dataType: "json",
        data: {'value': value, '_csrf-backend': csrf_token},
        success: function (resultData) {
            if (resultData.item == '0') {
                $("#tenderfour").html(resultData.data);
                $("#fifth").hide();
                $("#sixth").hide();
                //$("#itemdata .added").remove();
                $("#itemdata").hide();
                $("#makes").hide();
                $('.browser-default').prop('selectedIndex', 0);
                $("#item-error").remove();
                $("#itembutton").hide();
                $("#tenderfour").material_select();
            } else {
                $("#fourth").hide();
                $("#fifth").hide();
                $("#sixth").hide();
                if (value == 1 || value == 2) {
                    $("#itemdata").show();
                    $("#makes").show();
                    $("#itembutton").show();
                }
            }
        }
    });
}

function getthirddatasub(value, id) {
    $("#tenderfive" + id + "").prop('selectedIndex', '');
    $("#tenderfive" + id + "").material_select();
    $('#fifth' + id + '').hide();
    $("#tendersix" + id + "").prop('selectedIndex', '');
    $("#tendersix" + id + "").material_select();
    $('#sixth' + id + '').hide();
    $("#fourth" + id + "").show();
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/getthirddata',
        dataType: "json",
        data: {'value': value, '_csrf-backend': csrf_token},
        success: function (resultData) {
            if (resultData.item == '0') {
                $("#tenderfour" + id + "").html(resultData.data);
                $("#fifth" + id + "").hide();
                $("#sixth" + id + "").hide();
                //$("#itemdata" + id + " .added").remove();
                $("#itemdata" + id + "").hide();
                $("#item-error" + id + "").remove();
                $("#itembutton" + id + "").hide();
                $("#tenderfour" + id + "").material_select();
            } else {
                $("#fourth" + id + "").hide();
                $("#fifth" + id + "").hide();
                $("#sixth" + id + "").hide();
                $("#itemdata" + id + "").show();
                $("#itembutton" + id + "").show();
            }
        }
    });
}

function getfourdata(value) {

    $("#itemdata .added").remove();
    //$("#itemdata").remove();
    if (value == 1) {
        var label = document.createElement("label");
        label.id = "itemunit";
        label.innerHTML = "Unit";
        $("#unit").append(label);
        $("#typefit").hide();
        $("#type0").removeAttr('required');
        $("#type0").removeClass('required');
        $("#capacityfit").hide();
        $("#text0").removeAttr('required');
        $("#text0").removeClass('required');
        $("#accessorytwo").hide();
        $("#acctwo0").removeAttr('required');
        $("#acctwo0").removeClass('required');
        $("#accessoryone").hide();
        $("#accone0").removeAttr('required');
        $("#accone0").removeClass('required');
        $("#accessorythree").hide();
        $("#accthree0").removeAttr('required');
        $("#accthree0").removeClass('required');
        $("#sizesdiv").show();
        $("#sizes0").attr('required');
        $("#sizes0").addClass('required');
        $("#itemunit").val('');
        $("#corediv").show();
        $("#core0").attr('required');
        $("#core0").addClass('required');
        $("#itemdata").hide();
        $("#makes").hide();
        $("#itembutton").hide();
    } else if (value == 2) {
        $("#sizesdiv").hide();
        $("#sizesdiv").prop('selectedIndex', '');
        $("#sizes0").removeAttr('required');
        $("#sizes0").removeClass('required');
        $("#corediv").hide();
        $("#core0").removeAttr('required');
        $("#core0").removeClass('required');
        $("#accessorytwo").hide();
        $("#acctwo0").removeAttr('required');
        $("#acctwo0").removeClass('required');
        $("#accessoryone").hide();
        $("#accone0").removeAttr('required');
        $("#accone0").removeClass('required');
        $("#accessorythree").hide();
        $("#accthree0").removeAttr('required');
        $("#accthree0").removeClass('required');
        $("#typefit").show();
        $("#type0").attr('required');
        $("#type0").addClass('required');
        $("#capacityfit").show();
        $("#text0").attr('required');
        $("#text0").addClass('required');
        $("#itemunit").val('NOS');
        $("#unit label").remove();
        $("#itemdata").show();
        $("#makes").show();
        $("#itembutton").show();
    } else if (value == 4) {
        $("#sizesdiv").hide();
        $("#sizesdiv").prop('selectedIndex', '');
        $("#sizes0").removeAttr('required');
        $("#sizes0").removeClass('required');
        $("#corediv").hide();
        $("#core0").removeAttr('required');
        $("#core0").removeClass('required');
        $("#typefit").hide();
        $("#type0").removeAttr('required');
        $("#type0").removeClass('required');
        $("#capacityfit").hide();
        $("#text0").removeAttr('required');
        $("#text0").removeClass('required');
        $("#accessorytwo").show();
        $("#acctwo0").attr('required');
        $("#acctwo0").addClass('required');
        $("#accessoryone").show();
        $("#accone0").attr('required');
        $("#accone0").addClass('required');
        $("#accessorythree").show();
        $("#accthree0").attr('required');
        $("#accthree0").addClass('required');
        $("#itemunit").val('NOS');
        $("#unit label").remove();
        $("#itemdata").show();
        $("#makes").show();
        $("#itembutton").show();
    } else {
        var label = document.createElement("label");
        label.id = "itemunit";
        label.innerHTML = "Unit";
        $("#unit").append(label);
        $("#corediv").hide();
        $("#core0").removeAttr('required');
        $("#core0").removeClass('required');
        $("#accessorytwo").hide();
        $("#acctwo0").removeAttr('required');
        $("#acctwo0").removeClass('required');
        $("#accessoryone").hide();
        $("#accone0").removeAttr('required');
        $("#accone0").removeClass('required');
        $("#accessorythree").hide();
        $("#accthree0").removeAttr('required');
        $("#accthree0").removeClass('required');
        $("#typefit").hide();
        $("#type0").removeAttr('required');
        $("#type0").removeClass('required');
        $("#capacityfit").hide();
        $("#text0").removeAttr('required');
        $("#text0").removeClass('required');
        $("#sizesdiv").show();
        $("#sizes0").attr('required');
        $("#sizes0").addClass('required');
        $("#itemunit").val('');
        $("#itemdata").show();
        $("#makes").show();
        $("#itembutton").show();
    }


    if (value == 2 || value == 5 || value == 4) {
        $("#tenderfive").prop('selectedIndex', '');
        $("#tenderfive").material_select();
        $("#tendersix").prop('selectedIndex', '');
        $("#tendersix").material_select();
        $('#sixth').hide();
        $("#fifth").hide();
    } else {
        $("#tendersix").prop('selectedIndex', '');
        $("#tendersix").material_select();
        $('#sixth').hide();
        $("#fifth").show();
    }
    var subone = $('#tenderfive :selected').val();
    var subtwo = $('#tendersix :selected').val();
    var selects = '';
    var sizes = '';
    var types = '';
    var capacities = '';




    /*if (value != 1) {
     $("#corediv").hide();
     $("#core0").removeAttr('required');
     $("#core0").removeClass('required');
     } else {
     $("#corediv").show();
     $("#core0").attr('required');
     $("#core0").addClass('required');
     }
     if (value != 2) {
     $("#typefit").hide();
     $("#type0").removeAttr('required');
     $("#type0").removeClass('required');
     $("#capacityfit").hide();
     $("#text0").removeAttr('required');
     $("#text0").removeClass('required');
     $("#sizesdiv").show();
     $("#sizes0").attr('required');
     $("#sizes0").addClass('required');
     $("#itemunit").val('RM');
     } else if (value == 2) {
     $("#typefit").show();
     $("#type0").attr('required');
     $("#type0").addClass('required');
     $("#capacityfit").show();
     $("#text0").attr('required');
     $("#text0").addClass('required');
     $("#sizesdiv").hide();
     $("#sizesdiv").prop('selectedIndex', '');
     $("#sizes0").removeAttr('required');
     $("#sizes0").removeClass('required');
     $("#itemunit").val('NOS');
     }
     
     if (value != 4) {
     $("#accessorytwo").hide();
     $("#acctwo0").removeAttr('required');
     $("#acctwo0").removeClass('required');
     $("#accessoryone").hide();
     $("#accone0").removeAttr('required');
     $("#accone0").removeClass('required');
     $("#sizesdiv").show();
     $("#sizes0").attr('required');
     $("#sizes0").addClass('required');
     $("#itemunit").val('RM');
     } else if (value == 4) {
     $("#accessorytwo").show();
     $("#acctwo0").attr('required');
     $("#acctwo0").addClass('required');
     $("#accessoryone").show();
     $("#accone0").attr('required');
     $("#accone0").addClass('required');
     $("#sizesdiv").hide();
     $("#sizesdiv").prop('selectedIndex', '');
     $("#sizes0").removeAttr('required');
     $("#sizes0").removeClass('required');
     $("#itemunit").val('NOS');
     }*/

    $.ajax({
        type: 'post',
        url: baseUrl + 'site/getfourdata',
        dataType: "json",
        data: {'value': value, 'subone': subone, 'subtwo': subtwo, '_csrf-backend': csrf_token},
        beforeSend: function () {
            $("#makes").addClass('makesload');
            $("#typefit").addClass('makesload');
            $("#capacityfit").addClass('makesload');
            $("#sizesdiv").addClass('makesload');
            $("#corediv").addClass('makesload');
            $("#accessoryone").addClass('makesload');
            $("#accessorytwo").addClass('makesload');
            $("#accessorythree").addClass('makesload');
            $(".select2-container").hide();
        },
        success: function (resultData) {
            $("#makes").removeClass('makesload');
            $("#typefit").removeClass('makesload');
            $("#capacityfit").removeClass('makesload');
            $("#sizesdiv").removeClass('makesload');
            $("#corediv").removeClass('makesload');
            $("#accessoryone").removeClass('makesload');
            $("#accessorytwo").removeClass('makesload');
            $("#accessorythree").removeClass('makesload');
            if (resultData.item == '0') {
                $("#tenderfive").html(resultData.data);
                $("#sixth").hide();
                $("#itemdata").hide();
                $("#makes").hide();
                $('.browser-default').prop('selectedIndex', 0);
                $("#itemdata .added").remove();
                $("#item-error").remove();
                $("#itembutton").hide();
                $("#tenderfive").material_select();
            } else {
                $("#fifth").hide();
                $("#sixth").hide();
                $("#itemdata .added").remove();
                $("#itemdata").show();
                $("#makes").show();
                $("#itembutton").show();
            }
            if ($("#makes0").data('select2')) {
                $("#makes0").select2("val", "");
            }
            $('.select2-container').show();
            // setup listener for custom event to re-initialize on change
            $('.materialSelect').on('contentChanged', function () {
                $(this).select2({closeOnSelect: true, placeholder: 'Select Makes'});
            });

            $.each(resultData.select, function (key, value) {
                if (key != 01) {
                    selects += '<option value="' + key + '">' + value + '</option>';
                } else {
                    selects += '<option value="" disabled required>No Makes</option>';
                }
            }
            );
            //$("#makes0").select2("val", "");
            $("#makes0").html(selects);
            $("#makes0").trigger('contentChanged');

            if (value != 2 || value != 4) {
                $('.materialSelectsize').on('contentChanged', function () {
                    $(this).select2({closeOnSelect: true, placeholder: 'Select Sizes'});
                });

                $.each(resultData.sizes, function (key, value) {
                    if (key != 0) {
                        sizes += '<option value="' + key + '">' + value + '</option>';
                    } else {
                        sizes += '<option value="" disabled required>No Sizes</option>';
                    }
                }
                );
                $("#sizes0").html(sizes);
                $("#sizes0").trigger('contentChanged');
            }

            if (value == 2) {
                $('.materialSelecttypefit').on('contentChanged', function () {
                    $(this).select2({closeOnSelect: true, placeholder: 'Select Type'});
                });

                $.each(resultData.types, function (key, value) {
                    if (key != 0) {
                        types += '<option value="' + key + '">' + value + '</option>';
                    } else {
                        types += '<option value="" disabled required>No Types</option>';
                    }
                }
                );
                $("#type0").html(types);
                $("#type0").trigger('contentChanged');

                $('.materialSelectcapacityfit').on('contentChanged', function () {
                    $(this).select2({closeOnSelect: true, placeholder: 'Select Capacity'});
                });

                $.each(resultData.capacities, function (key, value) {
                    if (key != 0) {
                        capacities += '<option value="' + key + '">' + value + '</option>';
                    } else {
                        capacities += '<option value="" disabled required>No Capacities</option>';
                    }
                }
                );
                $("#text0").html(capacities);
                $("#text0").trigger('contentChanged');
            }
            var accessories = '';
            var accessoriestwo = '';
            var accessoriesthree = '';
            if (value == 4) {
                $('.materialSelectaccessoryone').on('contentChanged', function () {
                    $(this).select2({closeOnSelect: true, placeholder: 'Select Accessory'});
                });

                $.each(resultData.accessories, function (key, value) {
                    if (key != 0) {
                        accessories += '<option value="' + key + '">' + value + '</option>';
                    } else {
                        accessories += '<option value="" disabled required>No Accessories</option>';
                    }
                }
                );
                $("#accone0").html(accessories);
                $("#accone0").trigger('contentChanged');

                $('.materialSelectaccessorytwo').on('contentChanged', function () {
                    $(this).select2({closeOnSelect: true, placeholder: 'Select Type'});
                });

                accessoriestwo += '<option value="1" selected>1 Way</option>';
                accessoriestwo += '<option value="2">2 Way</option>';

                $("#acctwo0").html(accessoriestwo);
                $("#acctwo0").trigger('contentChanged');

                $('.materialSelectaccessorythree').on('contentChanged', function () {
                    $(this).select2({closeOnSelect: true, placeholder: 'Select Sub Type'});
                });

                accessoriesthree += '<option value="0">Select Sub Type</option>';
                accessoriesthree += '<option value="1" selected>5 A</option>';
                accessoriesthree += '<option value="2">6 A</option>';
                accessoriesthree += '<option value="3">10 A</option>';
                accessoriesthree += '<option value="4">15 A</option>';
                accessoriesthree += '<option value="5">16 A</option>';
                accessoriesthree += '<option value="6">25 A</option>';
                accessoriesthree += '<option value="7">32 A</option>';

                $("#accthree0").html(accessoriesthree);
                $("#accthree0").trigger('contentChanged');
            }
        }
    });
}

function getfourdatasub(value, id, newnum) {
    //$("#itemdata").show();
    //$("#itembutton").show();
    $("#tendersix" + id + "").prop('selectedIndex', '');
    $("#tendersix" + id + "").material_select();
    $('#sixth' + id + '').hide();
    $("#fifth" + id + "").show();
    var selects = '';
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/getfourdata',
        dataType: "json",
        data: {'value': value, '_csrf-backend': csrf_token},
        success: function (resultData) {
            if (resultData.item == '0') {
                $("#tenderfive" + id + "").html(resultData.data);
                $("#sixth" + id + "").hide();
                $("#itemdata" + id + "").hide();
                //$("#itemdata" + id + " .added").remove();
                $("#item-error" + id + "").remove();
                $("#itembutton" + id + "").hide();
                $("#tenderfive" + id + "").material_select();
            } else {
                $("#fifth" + id + "").hide();
                $("#sixth" + id + "").hide();
                $("#itemdata" + id + "").show();
                $("#itembutton" + id + "").show();
            }

            // setup listener for custom event to re-initialize on change
            $('.materialSelect').on('contentChanged', function () {
                $(this).material_select();
            });

            $.each(resultData.select, function (key, value) {
                if (key != 0) {
                    selects += '<option value="' + key + '">' + value + '</option>';
                } else {
                    selects += '<option value="" disabled required>No Makes</option>';
                }
            }
            );
            $("#makes" + newnum + "").html(selects);
            $("#makes" + newnum + "").material_select();
        }
    });
}

function getfivedata(value) {
    //$("#itemdata").show();
    //$("#itembutton").show();
    $("#sixth").show();
    var parent = $('#tenderfour').val();
    var one = $('#tenderfive').val();
    var selects = '';
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/getfivedata',
        dataType: "json",
        data: {'value': value, 'parent': parent, 'one': one, '_csrf-backend': csrf_token},
        success: function (resultData) {
            if (resultData.item == '0') {
                $("#tendersix").html(resultData.data);
                $("#itemdata").hide();
                $("#makes").hide();
                //$('.browser-default').prop('selectedIndex', 0);
                //$("#itemdata .added").remove();
                $("#item-error").remove();
                $("#itembutton").hide();
                $("#tendersix").material_select();
            } else {
                $("#sixth").hide();
                $("#itemdata").show();
                $("#makes").show();
                $("#itembutton").show();
            }
            $('.materialSelectsize').on('contentChanged', function () {
                $(this).select2({closeOnSelect: true, placeholder: 'Select Sizes'});
            });

            $.each(resultData.sizes, function (key, value) {
                if (key != 0) {
                    selects += '<option value="' + key + '">' + value + '</option>';
                } else {
                    selects += '<option value="" disabled required>No Sizes</option>';
                }
            }
            );
            $("#sizes0").html(selects);
            $("#sizes0").trigger('contentChanged');
        }
    });
}

function getfivedatasub(value, id) {
    //$("#itemdata").show();
    //$("#itembutton").show();
    $("#sixth" + id + "").show();
    var parent = $('#tenderfour').val();
    var one = $('#tenderfive').val();
    var selects = '';
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/getfivedata',
        dataType: "json",
        data: {'value': value, 'parent': parent, 'one': one, '_csrf-backend': csrf_token},
        success: function (resultData) {
            if (resultData.item == '0') {
                $("#tendersix" + id + "").html(resultData.data);
                $("#itemdata" + id + "").hide();
                //$("#itemdata" + id + " .added").remove();
                $("#item-error" + id + "").remove();
                $("#itembutton" + id + "").hide();
                $("#tendersix" + id + "").material_select();
            } else {
                $("#sixth" + id + "").hide();
                $("#itemdata" + id + "").show();
                $("#itembutton" + id + "").show();
            }
            $('.materialSelectsize').on('contentChanged', function () {
                $(this).select2({closeOnSelect: true, placeholder: 'Select Sizes'});
            });

            $.each(resultData.sizes, function (key, value) {
                if (key != 0) {
                    selects += '<option value="' + key + '">' + value + '</option>';
                } else {
                    selects += '<option value="" disabled required>No Sizes</option>';
                }
            }
            );
            $("#sizes0").html(selects);
            $("#sizes0").trigger('contentChanged');
        }
    });
}

function getsixdata(value) {
    $("#itemdata").show();
    $("#makes").show();
    $("#itembutton").show();
    var parent = $('#tenderfour').val();
    var one = $('#tenderfive').val();
    var selects = '';
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/getsixdata',
        dataType: "json",
        data: {'two': value, 'parent': parent, 'one': one, '_csrf-backend': csrf_token},
        success: function (resultData) {
            $("#itemdata .added").remove();
            // setup listener for custom event to re-initialize on change
            $('.materialSelectsize').on('contentChanged', function () {
                $(this).select2({closeOnSelect: true, placeholder: 'Select Sizes'});
            });

            $.each(resultData.sizes, function (key, value) {
                if (key != 0) {
                    selects += '<option value="' + key + '">' + value + '</option>';
                } else {
                    selects += '<option value="" disabled required>No Sizes</option>';
                }
            }
            );
            $("#sizes0").html(selects);
            $("#sizes0").trigger('contentChanged');

        }
    });
}

function getsixdatasub(value, id) {
    $("#itemdata" + id + "").show();
    $("#itembutton" + id + "").show();
}

function deleterate(id, iid, itemid) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/deleterate',
        dataType: "json",
        data: {id: id, 'iid': iid, itemid: itemid, '_csrf-backend': csrf_token},
        success: function (resultData) {

        }
    });
}

function deletebutton(id, num) {
    if (id) {
        $('#inforows' + num + id + '').remove();
        //document.getElementById('itemunit' + id + '').reset();
        //document.getElementById('quantity' + id + '').reset();
        //document.getElementById('makeid' + id + '').reset();
        $('.materialSelect' + id + '').prop('selectedIndex', 0);
        $('.materialSelectsize' + id + '').prop('selectedIndex', 0);
        $('.materialSelectcore' + id + '').prop('selectedIndex', 0);
        $('.materialSelecttypefit' + id + '').prop('selectedIndex', 0);
        $('.materialSelectcapacityfit' + id + '').prop('selectedIndex', 0);
        /* $('#tenderone' + id + '').prop('selectedIndex', 0);
         $('#tendertwo' + id + '').prop('selectedIndex', 0);
         $('#tenderthree' + id + '').prop('selectedIndex', 0);
         $('#tenderfour' + id + '').prop('selectedIndex', 0);
         $('#tenderfive' + id + '').prop('selectedIndex', 0);
         $('#tendersix' + id + '').prop('selectedIndex', 0);*/
    } else {
        /*$('#inforows').remove();
         document.getElementById('itemunit').reset();
         document.getElementById('quantity').reset();
         document.getElementById('makeid').reset();
         $('.materialSelect').prop('selectedIndex', 0);
         $('.materialSelectsize').prop('selectedIndex', 0);
         $('.materialSelectcore' + id + '').prop('selectedIndex', 0);
         $('.materialSelecttypefit' + id + '').prop('selectedIndex', 0);
         $('.materialSelectcapacityfit' + id + '').prop('selectedIndex', 0);*/
        /*$('#tenderone').prop('selectedIndex', 0);
         $('#tendertwo').prop('selectedIndex', 0);
         $('#tenderthree').prop('selectedIndex', 0);
         $('#tenderfour').prop('selectedIndex', 0);
         $('#tenderfive').prop('selectedIndex', 0);
         $('#tendersix').prop('selectedIndex', 0);*/
    }
}

function deleteratebutton(id, num) {
    if (id) {
        $('#inforates' + num + id + '').remove();
        $('.ratelist' + id + '').prop('selectedIndex', 0);
    }
}

var expanded = false;

function showCheckboxesAfter(id) {
    var checkboxes = document.getElementById("checkboxes" + id + "");
    if (!expanded) {
        checkboxes.style.display = "block";
        expanded = true;
    } else {
        checkboxes.style.display = "none";
        expanded = false;
    }
}

function getmakesids() {
    $("#itemsubmit").css('pointer-events', 'none');
    //$("#submitbutton").html('<img src="/assets/images/loading.gif" alt="">');
    var arr = [];
    var num = $('.iteminfo').length;
    var i;
    for (i = 0; i < num; i++) {
        $('#makes' + i + ' > option:selected').each(function (index) {
            arr.push({
                key: i,
                value: $(this).val()
            });
        });
    }
    $('#makeids').val(JSON.stringify(arr));
    $("#create-item").submit();
}


/* Custom editing ends */
