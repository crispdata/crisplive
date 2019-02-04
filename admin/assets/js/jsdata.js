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

    $("#searchform").submit(function (e) {
        e.preventDefault();
        var val = $("#searchdata").val() // get the current value of the input field.
        if (val) {
            $.ajax({
                url: baseUrl + 'site/searchtender',
                type: "post",
                data: 'val=' + val + '&_csrf-backend=' + csrf_token,
                beforeSend: function () {
                    $(".mn-inner .col .page-title").html('');
                    $(".mn-inner #sort-data").css('display', 'none');
                    $(".mn-inner .add-contact").css('display', 'none');
                    $(".mn-inner .card-content").html('<span class="fetchmess"><p>Fetching Tenders.....</p></span>');
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


    $('#sbutton').on('click', function () {
        var val = $("#searchdata").val() // get the current value of the input field.
        if (val) {
            $.ajax({
                url: baseUrl + 'site/searchtender',
                type: "post",
                data: 'val=' + val + '&_csrf-backend=' + csrf_token,
                beforeSend: function () {
                    $(".mn-inner .col .page-title").html('');
                    $(".mn-inner #sort-data").css('display', 'none');
                    $(".mn-inner .add-contact").css('display', 'none');
                    $(".mn-inner .card-content").html('<span class="fetchmess"><p>Fetching Tenders.....</p></span>');
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

function showdivs(val) {
    if (val == 1) {
        $("#cablesdiv").show();
        $("#cables").attr('required', 'true');
        $("#lightdiv").hide();
        $("#lighting").removeAttr('required');
    } else if (val == 2) {
        $("#lightdiv").show();
        $("#lighting").attr('required', 'true');
        $("#cablesdiv").hide();
        $("#cables").removeAttr('required');
    } else {
        $("#cablesdiv").hide();
        $("#lightdiv").hide();
        $("#lighting").removeAttr('required');
        $("#cables").removeAttr('required');
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
    var rowitems = "<div class='row added iteminfo' id='inforows" + id + "' ><div class='input-field col s1'><input id='itemtender" + id + "' type='text' name = 'itemtender[]' required='' class='validate required' value=''><label for='itemtender'>Sr. no</label></div><div class='input-field col s2' id='sizesdiv" + id + "'><select class='validate required materialSelectsize" + id + " browser-default' required='' name='desc[]' id='sizes" + newnum + "' style='display: inline; height: 0px; padding: 0px; width: 0px;'><option value='' disabled required>No Sizes</option></select></div><div class='input-field col s3' id='corediv" + id + "'><select class='validate required materialSelectcore' required='' name='core[]' id='core" + newnum + "'><option value=''>Select Core</option><option value='1'>1 Core</option><option value='2'>2 Core</option><option value='3'>3 Core</option><option value='4'>3.5 Core</option><option value='5'>4 Core</option></select></div><div class='input-field col s2' id='typefit" + id + "'><select class='validate required materialSelecttypefit' required='' name='type[]' id='type" + newnum + "'></select></div> <div class='input-field col s3' id='capacityfit" + id + "'><select class='validate required materialSelectcapacityfit browser-default' required='' name='text[]' id='text" + newnum + "'></select></div><div class='input-field col s1'><input id='itemunit" + id + "' type='text' name = 'units[]' required='' class='validate required' value='RM'><label for='itemunit" + id + "'>Units</label><!--textarea id='item' name='desc' class='materialize-textarea'></textarea><label for='item'>Item description</label--></div><div class='input-field col s1'><input id='quantity" + id + "' type='text' name = 'quantity[]' required='' class='validate required' value=''><label for='quantity" + id + "'>Quantity</label></div> <div class='input-field col s2'><input id='makeid" + id + "' type='text' name = 'makeid[]' class='validate' value=''><label for='makeid" + id + "'>CatPart Id </label></div> <div class='input-field col s2'><a class='waves-effect waves-light btn blue m-b-xs button' onclick='deletebutton(" + id + ")'>Delete</a></div></div>";
    var row = "<div id=info" + id + "><div class='col s12'><div class='input-fields col s2 row'><label>Select type of work</label><select class='validate required materialSelect' name='tenderone[]' id='tenderone" + id + "' onchange='getdatasub(this.value," + id + ")'><option value='' disabled selected>Select</option><option value='1'>E/M</option></select></div><div id='second" + id + "' style='display: none;'><div class='input-fields col s2 row'><label>Select Sub Type</label><select class='validate required materialSelect' name='tendertwo[]' id='tendertwo" + id + "' onchange='getseconddatasub(this.value," + id + ")'><option value='' disabled selected>Select</option></select></div></div><div id='third" + id + "' style='display: none;'><div class='input-fields col s2 row'><label>Select Sub Type</label><select class='validate required materialSelect' name='tenderthree[]' id='tenderthree" + id + "' onchange='getthirddatasub(this.value," + id + ")'><option value='' disabled selected>Select</option></select></div></div><div id='fourth" + id + "' style='display: none;'><div class='input-fields col s2 row'><label>Select Sub Type</label><select class='validate required materialSelect' name='tenderfour[]' id='tenderfour" + id + "' onchange='getfourdatasub(this.value," + id + "," + newnum + ")'><option value='' disabled selected>Select</option></select></div></div><div id='fifth" + id + "' style='display: none;'><div class='input-fields col s2 row'><label>Select Sub Type</label><select class='validate required materialSelect' name='tenderfive[]' id='tenderfive" + id + "' onchange='getfivedatasub(this.value," + id + ")'><option value='' disabled selected>Select</option></select></div></div><div id='sixth" + id + "' style='display: none;'><div class='input-fields col s2 row'><label>Select Sub Type</label><select class='validate required materialSelect' name='tendersix[]' id='tendersix" + id + "' onchange='getsixdatasub(this.value," + id + ")'><option value='' disabled selected>Select</option></select></div></div></div>" + rowitems + '</div>';
    $("#itemdata").append(rowitems);
    $("#itemunit" + id + "").focus();
    if (selected == 1) {
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
    }


    $("#makes" + newnum + "").select2({closeOnSelect: true, placeholder: 'Select Makes'});
    $("#sizes" + newnum + "").select2({closeOnSelect: true, placeholder: 'Select Sizes'});
    $("#core" + newnum + "").material_select();
    $("#type" + newnum + "").material_select();
    $("#text" + newnum + "").select2({closeOnSelect: true, placeholder: 'Select Capacity'});
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

    if (selected != 2) {
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
                $("#type" + newnum + "").material_select();

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

    var arr = ['2', '12'];
    if (arr.indexOf(value) < 0) {
        $("#ce").show();
        $.ajax({
            type: 'post',
            url: baseUrl + 'site/getcengineer',
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

function getcwengineer(value) {
    $("#cwengineer").prop('selectedIndex', 0);
    $("#cwengineer").material_select();
    $('#cwe').hide();
    $("#gengineer").prop('selectedIndex', 0);
    $("#gengineer").material_select();
    $("#ge").hide();
    var arr = ['5', '6', '7', '8', '14', '36'];
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
    var arr = ['24', '25', '39', '44', '46', '49', '52', '53', '57', '58', '59', '64', '69', '84', '85', '90', '91', '92', '103', '111', '125', '126'];
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
    $("#tenderthree").prop('selectedIndex', '');
    $("#tenderthree").material_select();
    $('#third').hide();
    $("#tenderfour").prop('selectedIndex', '');
    $("#tenderfour").material_select();
    $('#fourth').hide();
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
            $("#third").hide();
            $("#fourth").hide();
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
    $("#tenderfour").prop('selectedIndex', '');
    $("#tenderfour").material_select();
    $('#fourth').hide();
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
                $("#fourth").hide();
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
                $("#third").hide();
                $("#fourth").hide();
                $("#fifth").hide();
                $("#sixth").hide();
                $("#itemdata").show();
                $("#makes").show();
                $("#itembutton").show();
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
                $("#itemdata").show();
                $("#makes").show();
                $("#itembutton").show();
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
    //$("#itemdata").show();
    //$("#itembutton").show();
    $("#itemdata .added").remove();
    $("#tendersix").prop('selectedIndex', '');
    $("#tendersix").material_select();
    $('#sixth').hide();
    $("#fifth").show();
    var subone = $('#tenderfive :selected').val();
    var subtwo = $('#tendersix :selected').val();
    var selects = '';
    var sizes = '';
    var types = '';
    var capacities = '';
    if (value != 1) {
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


    $.ajax({
        type: 'post',
        url: baseUrl + 'site/getfourdata',
        dataType: "json",
        data: {'value': value, 'subone': subone, 'subtwo': subtwo, '_csrf-backend': csrf_token},
        success: function (resultData) {
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

            // setup listener for custom event to re-initialize on change
            $('.materialSelect').on('contentChanged', function () {
                $(this).select2({closeOnSelect: true, placeholder: 'Select Makes'});
            });

            $.each(resultData.select, function (key, value) {
                if (key != 0) {
                    selects += '<option value="' + key + '">' + value + '</option>';
                } else {
                    selects += '<option value="" disabled required>No Makes</option>';
                }
            }
            );
            //$("#makes0").select2("val", "");
            $("#makes0").html(selects);
            $("#makes0").trigger('contentChanged');

            if (value != 2) {
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
                    $(this).material_select();
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
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/getfivedata',
        dataType: "json",
        data: {'value': value, '_csrf-backend': csrf_token},
        success: function (resultData) {
            if (resultData.item == '0') {
                $("#tendersix").html(resultData.data);
                $("#itemdata").hide();
                $("#makes").hide();
                $('.browser-default').prop('selectedIndex', 0);
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
        }
    });
}

function getfivedatasub(value, id) {
    //$("#itemdata").show();
    //$("#itembutton").show();
    $("#sixth" + id + "").show();
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/getfivedata',
        dataType: "json",
        data: {'value': value, '_csrf-backend': csrf_token},
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
                $(this).material_select();
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
            $("#sizes0").material_select();
        }
    });
}

function getsixdatasub(value, id) {
    $("#itemdata" + id + "").show();
    $("#itembutton" + id + "").show();
}



function deletebutton(id) {
    if (id) {
        $('#inforows' + id + '').remove();
        document.getElementById('itemunit' + id + '').reset();
        document.getElementById('quantity' + id + '').reset();
        document.getElementById('makeid' + id + '').reset();
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
        $('#inforows').remove();
        document.getElementById('itemunit').reset();
        document.getElementById('quantity').reset();
        document.getElementById('makeid').reset();
        $('.materialSelect').prop('selectedIndex', 0);
        $('.materialSelectsize').prop('selectedIndex', 0);
        $('.materialSelectcore' + id + '').prop('selectedIndex', 0);
        $('.materialSelecttypefit' + id + '').prop('selectedIndex', 0);
        $('.materialSelectcapacityfit' + id + '').prop('selectedIndex', 0);
        /*$('#tenderone').prop('selectedIndex', 0);
         $('#tendertwo').prop('selectedIndex', 0);
         $('#tenderthree').prop('selectedIndex', 0);
         $('#tenderfour').prop('selectedIndex', 0);
         $('#tenderfive').prop('selectedIndex', 0);
         $('#tendersix').prop('selectedIndex', 0);*/
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
