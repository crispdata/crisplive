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

    // Write your custom Javascript codes here...

    $('.project-success-sweetalert').on('click', function () {
        swal("Success !!!", "Project has created successfully!", "success");
    });
    $('.contact-success-sweetalert').on('click', function () {
        swal("Success !!!", "contact has created successfully!", "success");
    });

    $('.project-update-sweetalert').on('click', function () {
        swal("Success !!!", "Project has updated successfully!", "success");
    });

    $('.template-update-sweetalert').on('click', function () {
        swal("Success !!!", "Template has updated successfully!", "success");
    });

    $('.template-success-sweetalert').on('click', function () {
        swal("Success !!!", "Template has created successfully!", "success");
    });

    $('.report-success-sweetalert').on('click', function () {
        swal("Success !!!", "Report has generated successfully!", "success");
    });

    $(document).on('keypress', '#ClientAuto', function () {
        $("#ClientAuto").autocomplete({
            source: function (request, response) {
                $.ajax({
                    type: 'post',
                    url: baseUrl + 'client/get',
                    dataType: "json",
                    data: {'client': request.term, '_csrf-backend': csrf_token},
                    success: function (resultData) {
                        response(resultData);
                    }
                });
            },
            select: function (event, ui) {
                $('#ClientAuto').val(ui.item.label);
                $('#ClientId').val(ui.item.value);
                return false;
            }
        });
    });

    $(document).on('keypress', '.contact_name', function () {

        var form_clients = $(".contact_id");
        var c_form = {};
        var cl = [];
        var inc = 0;

        for (var n = 0; n < form_clients.length; n++) {
            if ($(form_clients[n]).val()) {
                c_form[n] = $(form_clients[n]).val();
            }
        }

        $(".contact_name").autocomplete({

            source: function (request, response) {
                $.ajax({
                    type: 'post',
                    url: baseUrl + 'contact/get',
                    dataType: "json",
                    data: {'contact': request.term, '_csrf-backend': csrf_token},
                    success: function (resultData) {
                        $.each(resultData, function (i, e) {
                            $.each(c_form, function (index, element) {

                                if (element == e.value) {
                                    /* console.log('data: '+e.value);
                                     console.log('input: '+element); */

                                    delete resultData[i]
                                    return true;
                                }
                            });

                        });
                        $.each(resultData, function (i, e) {
                            if (e) {
                                cl[inc] = e;
                                inc++;
                            }
                        });
                        response(cl);
                    }
                });
            },
            select: function (event, ui) {
                $(this).val(ui.item.label);
                $(this).prev().val(ui.item.value);

                return false;
            }
        });
    });

    $(document).on('keypress', '#search-contact', function () {

        var form_clients = $(".contact_id");
        var c_form = {};
        var cl = [];
        var inc = 0;

        for (var n = 0; n < form_clients.length; n++) {
            if ($(form_clients[n]).val()) {
                c_form[n] = $(form_clients[n]).val();
            }
        }

        $("#search-contact").autocomplete({

            source: function (request, response) {
                $.ajax({
                    type: 'post',
                    url: baseUrl + 'contact/get',
                    dataType: "json",
                    data: {'contact': request.term, '_csrf-backend': csrf_token},
                    success: function (resultData) {
                        $.each(resultData, function (i, e) {
                            $.each(c_form, function (index, element) {

                                if (element == e.value) {
                                    /* console.log('data: '+e.value);
                                     console.log('input: '+element); */

                                    delete resultData[i]
                                    return true;
                                }
                            });

                        });
                        $.each(resultData, function (i, e) {
                            if (e) {
                                cl[inc] = e;
                                inc++;
                            }
                        });
                        response(cl);
                    }
                });
            },
            select: function (event, ui) {

                var sel_contact = '<div class="collection-item">' + ui.item.label + '<span class="badge rmv-contact">x</span><input type = "hidden" value = "' + ui.item.label + '" name = "GenerateReport[ContactName][]"><input type = "hidden" class = "contact_id" value = "' + ui.item.value + '" name = "GenerateReport[ContactValue][]"></div>';

                $("#selected-contact").append(sel_contact);
                $("#selected-contact").show();
                $("#search-contact").val('');

                return false;
            }
        });
    });

    $(document).on('click', '.rmv-contact', function () {
        $(this).parent().remove();
        if ($('#selected-contact').children().length == 0) {
            $("#selected-contact").hide();
        }
    });

    $("#search-contact").on('focusout', function () {
        $("#search-contact").val('');
    });

    $('#report-other-email').on("keypress", function (e) {
        $('#other-email-error').hide();
        if (e.keyCode == 13) {

            var email_val = $('#report-other-email').val();
            var email_filter = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;

            if ($.trim(email_val) == "") {
                $('#other-email-error').show();
                $('#other-email-error').text('Email cannot be blank.');
                return false;
            }
            if (!email_filter.test($.trim(email_val))) {
                $('#other-email-error').show();
                $('#other-email-error').text('Please enter a valid email address.');
                return false;
            }

            var entered_email = $('.others-email');

            for (var n = 0; n < entered_email.length; n++) {
                if ($(entered_email[n]).val()) {
                    if ($(entered_email[n]).val() == $.trim(email_val)) {
                        $('#other-email-error').show();
                        $('#other-email-error').text('Email exists already.');
                        return false;
                    }
                }
            }

            var sel_email = '<div class="collection-item">' + email_val + '<span class="badge rmv-email">x</span><input class = "others-email" type = "hidden" value = "' + email_val + '" name = "GenerateReport[OtherEmail][]"></div>';

            $("#selected-other-emails").append(sel_email);
            $("#selected-other-emails").show();
            $("#report-other-email").val('');
        }
    });

    $(document).on('click', '.rmv-email', function () {
        $(this).parent().remove();
        if ($('#selected-other-emails').children().length == 0) {
            $("#selected-other-emails").hide();
        }
    });

    $(document).on('focusout', '.contact_name', function () {
        var contact_name = $(this).parent().find('.contact_name').val();
        var contact_id = $(this).parent().find('.contact_id').val();
        var contact_stat_div = $(this);

        var stat = false;
        $.ajax({
            type: 'post',
            url: baseUrl + 'contact/is-exist',
            dataType: "json",
            data: {'id': contact_id, 'contact_name': contact_name, '_csrf-backend': csrf_token},
            success: function (resultData) {
                if (resultData == 'success') {
                    console.log('exist');
                    stat = true;
                    contact_stat_div.parent().parent().find('.contact_stat').empty();
                    contact_stat_div.parent().parent().find('.contact_stat').append('<a class="btn-floating btn-small waves-effect waves-light green href = "#"><i class="material-icons">done</i></a>');
                } else {
                    console.log('not exist');
                    stat = false;
                    contact_stat_div.prev().val('');
                    contact_stat_div.parent().parent().find('.contact_stat').empty();
                    contact_stat_div.parent().parent().find('.contact_stat').append('<a id = "c_contact_modal" class="btn-floating btn-small waves-effect waves-light blue modal-trigger c_contact_modal" href = "#add-contact"><i class="material-icons">add</i></a>');
                    if ($.trim(contact_stat_div.parent().parent().find('.contact_name').val()) != "") {
                        $('#c_contact_modal').trigger('click');
                        $('.contact_stat').removeClass('current-modal');
                        contact_stat_div.parent().parent().find('.contact_stat').addClass('current-modal');
                    }
                    contact_stat_div.parent().parent().find('.contact_name').val("");
                }
            }
        });

        /* setTimeout(function(){
         if(stat){
         contact_stat_div.parent().parent().find('.contact_stat').empty();
         contact_stat_div.parent().parent().find('.contact_stat').append('<a class="btn-floating btn-small waves-effect waves-light green href = "#"><i class="material-icons">done</i></a>');
         }else{
         contact_stat_div.prev().val('');
         contact_stat_div.parent().parent().find('.contact_stat').empty();
         contact_stat_div.parent().parent().find('.contact_stat').append('<a id = "c_contact_modal" class="btn-floating btn-small waves-effect waves-light blue modal-trigger c_contact_modal" href = "#add-contact"><i class="material-icons">add</i></a>');
         if($.trim(contact_stat_div.parent().parent().find('.contact_name').val()) != ""){
         $('#c_contact_modal').trigger('click');
         $('.contact_stat').removeClass('current-modal');
         contact_stat_div.parent().parent().find('.contact_stat').addClass('current-modal');
         }
         contact_stat_div.parent().parent().find('.contact_name').val("");
         }
         }, 300); */

    });

    $('#add-contact').on('hidden', function () {
        console.log('closed');
    });



    $('.picker__box').on('change', function () {
        $('.picker__day--infocus').on('click', function () {
            $('.picker__close').trigger("click");
        });
    });
    $('.picker__day--infocus').on('click', function () {
        $('.picker__close').trigger("click");
    });

    $("#add-page-project").on("click", function () {

        $("#add-page-html").append('<div class="row"><div class="input-field col s12"><input type="text" name = "CreateProject[PageName][]" class="validate"><label>Page Name</label></div></div>');
    });

    $("#add-role-project").on("click", function () {

        var role_html_content = '<div class="row"><div class="input-field col s3 m3"><input type="hidden" class = "contact_id validate" name = "CreateProject[ContactId][]"><input type="text" class = "contact_name validate" name = "ContactId[]"><label>Contact Name</label></div><a style = "display:none" id = "c_contact_modal" class="modal-trigger" href = "#add-contact"></a><div class="input-field col s2 m1 contact_stat"></div><div class="input-field col s3 m2"><input type="text" name = "CreateProject[RoleName][]" class="validate"><label>Assign Role</label></div>';

        var role_delete_check = pathname.split('/');

        if (role_delete_check[role_delete_check.length - 1] == 'update-project') {
            role_html_content += '<div class="input-field col s3 m3 deleterole"><a class="waves-effect waves-light btn blue delete-project-role">Delete</a></div>';
        }
        role_html_content += '</div>';


        $("#add-role-html").append(role_html_content);
    });

    $(document).on('click', '.delete-project-role', function () {
        $(this).parent().parent().remove();
    });

    $('#header_project').on('change', function () {
        var pid = $(this).val();
        $.ajax({
            type: 'post',
            url: baseUrl + 'site/set-session',
            dataType: "json",
            data: {'key': 'header_project', 'value': $(this).val(), '_csrf-backend': csrf_token},
            success: function (resultData) {
                if (resultData) {
                    window.location.href = baseUrl + "site/project-details?project=" + pid + "";
                }
            }
        });
    });


    $('#current_project_sort').on('change', function () {
        $('#current-project-sort-form').submit();
    });

    $('.confirm-delete').on('click', function () {
        var id = $(this).attr("data-id");

        if (id) {
            $('#' + id).submit();
        }
    });

    $('.proj-delete').on('click', function () {
        var id = $(this).parent().attr("id");
        $('.confirm-delete').attr('data-id', id);

    });

    $('.confirm-delete-page').on('click', function () {
        var id = $(this).attr("data-id");
        if (id) {
            $('#del-page' + id).submit();
        }
    });

    $('.page-delete').on('click', function () {
        var id = $(this).parent().attr("data-id");
        $('.confirm-delete-page').attr('data-id', id);

    });


    $('.confirm-dismiss').on('click', function () {

        var id = $(this).attr("data-id");

        if (id) {
            $.ajax({
                type: 'post',
                url: baseUrl + 'recentchanges/change-isdismiss',
                dataType: "json",
                data: {'id': id, '_csrf-backend': csrf_token},
                success: function (resultData) {
                    if (resultData)
                        ;
                    $('#tr-' + id).removeClass('activity');
                    $('#tr-' + id).addClass('dismiss');
                }
            });
        }

    });
    $('.confirm-dismiss-todo').on('click', function () {

        var id = $(this).attr("data-id");

        if (id) {
            $.ajax({
                type: 'post',
                url: baseUrl + 'site/change-isdismiss',
                dataType: "json",
                data: {'id': id, '_csrf-backend': csrf_token},
                success: function (resultData) {
                    if (resultData)
                        ;
                    $('#tr-' + id).removeClass('activity');
                    $('#tr-' + id).addClass('dismiss');
                }
            });
        }
    });
    $('#Email').on('keypress click change select', function () {
        $('#email-error').text('');
    });

    $('#submit-add-contact-modal').on('click', function () {

        $("#add-contact-modal").validate().settings.ignore = ":disabled,:hidden";

        if ($("#add-contact-modal").valid()) {
            var data = $("#add-contact-modal").serialize();
            $.ajax({
                type: 'post',
                url: baseUrl + 'site/create-contact-modal',
                data: data,
                success: function (resultData) {

                    var contact_resp = JSON.parse(resultData);

                    if (contact_resp.status == "success") {
                        $("#add-contact-modal")[0].reset();
                        $('#add-contact-modal-cancel').trigger('click');
                        $('.contact-success-sweetalert').trigger('click');

                        $('.current-modal').parent().find('label').addClass('active');
                        $('.current-modal').parent().find('.contact_name').val('');
                        $('.current-modal').parent().find('.contact_id').val('');
                        $('.current-modal').parent().find('.contact_name').val(contact_resp.name);
                        $('.current-modal').parent().find('.contact_id').val(contact_resp.id);

                        $('.current-modal').parent().find('.contact_stat').empty();
                        $('.current-modal').parent().find('.contact_stat').append('<a class="btn-floating btn-small waves-effect waves-light green href = "#"><i class="material-icons">done</i></a>');

                    } else if (contact_resp.status == "email") {
                        if (resultData.Email != "") {
                            $('#email-error').text('');

                            var e_data = 'E-mail already exist.';

                            $('#email-error').text(e_data);
                            $('#email-error').removeAttr('style');
                        }
                        /* swal({
                         title: "Email Already Registered!",
                         timer: 2000,
                         type: "success",
                         showConfirmButton: false
                         }); */
                    }
                }
            });
        }
    });
    $(document).on('click', '.go-to-project', function () {
        var ProjectId = $(this).closest("tr").attr("data-id");
        window.location.href = baseUrl + "site/project-details?project=" + ProjectId;
    });
    $('.go-to-page').on('click', function () {
        var ProjectId = $(this).closest("tr").attr("data-id");
        window.location.href = baseUrl + "page/page-details?page=" + ProjectId;
    });
    $('.go-to-contact').on('click', function () {
        var ContactId = $(this).closest("tr").attr("data-id");
        window.location.href = baseUrl + "site/contact-details?contact=" + ContactId;
    });

    $(document).on('click', '.c_contact_modal', function () {
        $('#c_contact_modal').trigger('click');
        $('.contact_stat').removeClass('current-modal');
        $(this).parent().parent().find('.contact_stat').addClass('current-modal');
    });

    $('.tabs').find('li').each(function () {
        $(this).removeClass("disabled");
    });


    $('.confirm-template-delete').on('click', function () {
        var id = $(this).attr("data-id");
        if (id) {
            $('#' + id).submit();
        }
    });

    $(document).on('click', '.template-delete', function () {
        var id = $(this).parent().attr("id");
        $('.confirm-template-delete').attr('data-id', id);

    });

    $('.project-template-name').on('click', function () {
        var template_id = $(this).prev().val();
        $('#empty').val('true');
        $.ajax({
            type: 'post',
            url: baseUrl + 'template/get-template-pages',
            dataType: "json",
            data: {'id': template_id, '_csrf-backend': csrf_token},
            success: function (resultData) {
                $('#add-page-html').empty();
                $('#add-page-html').append(resultData);
            }
        });
        $.ajax({
            type: 'post',
            url: baseUrl + 'template/get-template-roles',
            dataType: "json",
            data: {'id': template_id, '_csrf-backend': csrf_token},
            success: function (resultData) {
                $('#add-role-html').empty();
                $('#add-role-html').append(resultData);
            }
        });
    });
    $('[data-field=action]').removeClass("sorting");
    $('[data-field=action]').on('click', function () {
        $('[data-field=action]').removeClass("sorting_desc");
        $('[data-field=action]').removeClass("sorting_asc");
    });
    if ($('#steps-uid-0-t-0').attr('class') == 'active') {
        $('[role=menu] li').first().css("display", "none");
    }

    $('.datepick').daterangepicker({
        opens: 'up'
    }, function (start, end, label) {
        $('#EventStartDate1').val(start.format('YYYY-MM-DD'));
        $('#EventStartDate').val(start.format('MM-DD-YYYY'));
        $('#EventEndDate1').val(end.format('YYYY-MM-DD'));
        $('#EventEndDate').val(end.format('MM-DD-YYYY'));
        $('#EventStartDate').css('color', 'black');
        $('#EventEndDate').css('color', 'black');
    });
    $(document).on('focus', ".datepick1", function () {
        $('.datepick1').daterangepicker({
            opens: 'up'
        }, function (start, end, label) {
            $('#EventStartDate1-1').val(start.format('YYYY-MM-DD'));
            $('#EventStartDate-1').val(start.format('MM-DD-YYYY'));
            $('#EventEndDate1-1').val(end.format('YYYY-MM-DD'));
            $('#EventEndDate-1').val(end.format('MM-DD-YYYY'));
        });
    });

    $('.project-name').on('click', function () {
        var project_id = $(this).prev().val();
        $('#empty').val('true');
        $.ajax({
            type: 'post',
            url: baseUrl + 'project/get-project-pages',
            dataType: "json",
            data: {'id': project_id, '_csrf-backend': csrf_token},
            success: function (resultData) {
                $('#add-page-html').empty();
                $('#add-page-html').append(resultData);
            }
        });
        $.ajax({
            type: 'post',
            url: baseUrl + 'project/get-project-roles',
            dataType: "json",
            data: {'id': project_id, '_csrf-backend': csrf_token},
            success: function (resultData) {
                $('#add-role-html').empty();
                $('#add-role-html').append(resultData);
            }
        });
        $.ajax({
            type: 'post',
            url: baseUrl + 'project/get-project-client',
            dataType: "json",
            data: {'id': project_id, '_csrf-backend': csrf_token},
            success: function (resultData) {
                $('#client-name').empty();
                $('#client-name').append(resultData);
            }
        });
        $.ajax({
            type: 'post',
            url: baseUrl + 'project/get-project-event-date',
            dataType: "json",
            data: {'id': project_id, '_csrf-backend': csrf_token},
            success: function (resultData) {
                $('#datepick').empty();
                $('#datepick').append(resultData);
            }
        });
        $.ajax({
            type: 'post',
            url: baseUrl + 'project/get-project-map',
            dataType: "json",
            data: {'id': project_id, '_csrf-backend': csrf_token},
            success: function (resultData) {
                $('#EventLocation').val(resultData.EventLocation);
                $('#EventLatitude').val(resultData.EventLatitude);
                $('#EventLongtitude').val(resultData.EventLongtitude);

            }
        });
    });

    $('.peoplebox input.all:checkbox').click(function () {
        var itemId = $(this).attr('data-item');
        var $inputs = $('.people' + itemId + ' input:checkbox')
        if ($(this).is(':checked')) {
            $inputs.not(this).prop('disabled', true); // <-- disable all but checked one
            $inputs.not(this).next().css("background-color", "gray");
            $inputs.not(this).next().css("color", "#9e9e9e");
        } else {
            $inputs.prop('disabled', false); // <--
            $inputs.not(this).next().removeAttr("style");
        }
    })

    $('.peoplebox input.none:checkbox').click(function () {
        var itemId = $(this).attr('data-item');
        var $inputs = $('.people' + itemId + ' input:checkbox')
        if ($(this).is(':checked')) {
            $inputs.not(this).prop('disabled', true); // <-- disable all but checked one
            $inputs.not(this).next().css("background-color", "gray");
            $inputs.not(this).next().css("color", "#9e9e9e");
        } else {
            $inputs.prop('disabled', false); // <--
            $inputs.not(this).next().removeAttr("style");
        }
    })
    $('.pagesbox input.all:checkbox').click(function () {
        var itemId = $(this).attr('data-item');
        var $inputs = $('.pages' + itemId + ' input:checkbox')
        if ($(this).is(':checked')) {
            $inputs.not(this).prop('disabled', true); // <-- disable all but checked one
            $inputs.not(this).next().css("background-color", "gray");
            $inputs.not(this).next().css("color", "#9e9e9e");
        } else {
            $inputs.prop('disabled', false); // <--
            $inputs.not(this).next().removeAttr("style");
        }
    })

    $('.pagesbox input.none:checkbox').click(function () {
        var itemId = $(this).attr('data-item');
        var $inputs = $('.pages' + itemId + ' input:checkbox')
        if ($(this).is(':checked')) {
            $inputs.not(this).prop('disabled', true); // <-- disable all but checked one
            $inputs.not(this).next().css("background-color", "gray");
            $inputs.not(this).next().css("color", "#9e9e9e");
        } else {
            $inputs.prop('disabled', false); // <--
            $inputs.not(this).next().removeAttr("style");
        }
    })

    $("#current-project").on("click", ".showSingle", function () {
        var $inputs = $('tbody tr .showSingle');
        $inputs.attr('class', 'showSingle btn blue');
        $inputs.not(this).attr('class', 'showSingle');
        $('.targetDiv').hide();
        $('#div' + $(this).attr('target')).show();
    });

    //   $('.timepicker').wickedpicker();

    if (screen.width >= 1024) {
        $("#post_list").sortable({
            placeholder: "ui-state-highlight",
            update: function (event, ui)
            {
                var post_order_ids = new Array();
                $('#post_list tr').each(function () {
                    post_order_ids.push($(this).data("post-id"));
                });
                $.ajax({
                    url: baseUrl + 'page/saveitemorder',
                    method: "POST",
                    data: {post_order_ids: post_order_ids, '_csrf-backend': csrf_token},
                    success: function (data)
                    {

                    }
                });
            }
        });
    }
    if (screen.width >= 1024) {
        $("#documents_list").sortable({
            placeholder: "ui-state-highlight",
            update: function (event, ui)
            {
                var post_order_ids = new Array();
                $('#documents_list tr').each(function () {
                    post_order_ids.push($(this).data("post-id"));
                });
                $.ajax({
                    url: baseUrl + 'documents/saveitemorder',
                    method: "POST",
                    data: {post_order_ids: post_order_ids, '_csrf-backend': csrf_token},
                    success: function (data)
                    {

                    }
                });
            }
        });
    }
    if (screen.width >= 1024) {
        $("#timeline_list").sortable({
            placeholder: "ui-state-highlight",
            update: function (event, ui)
            {
                var post_order_ids = new Array();
                $('#timeline_list tr').each(function () {
                    post_order_ids.push($(this).data("post-id"));
                });
                $.ajax({
                    url: baseUrl + 'timeline/saveitemorder',
                    method: "POST",
                    data: {post_order_ids: post_order_ids, '_csrf-backend': csrf_token},
                    success: function (data)
                    {

                    }
                });
            }
        });
    }

    $("#tool").click(function () {

        //$("#current-project").load(" #current-project > *");
        $("#listing").toggle();
        $("#drawing").toggle();

    });

    $('.image-popup-no-margins').magnificPopup({
        type: 'image',
        closeOnContentClick: true,
        closeBtnInside: false,
        fixedContentPos: true,
        mainClass: 'mfp-no-margins mfp-with-zoom', // class to remove default margin from left and right side
        image: {
            verticalFit: true
        },
        zoom: {
            enabled: true,
            duration: 300 // don't foget to change the duration also in CSS
        }
    });


    $("#current-project").on("click", ".showthreaddiv", function () {
        var $inputs = $('tbody tr button.showthreaddiv');
        $inputs.attr('class', 'showthreaddiv btn blue');
        $inputs.not(this).attr('class', 'showthreaddiv btn-flat');
        $('.targetthreaddiv').hide();
        $('#divthread' + $(this).attr('target')).show();

    });

    $('.report-dates').on('click', function () {

        if ($(this).prev().val() == 0) {
            $('.datepick').hide();
        } else {
            $('.datepick').show();
        }
    });

    $('#report-tab-1').on('click', function () {
        $('.actions').hide();
        $(this).removeAttr('class');
        $(this).addClass('waves-effect waves-light blue btn m-b-xs');
        $('#report-tab-2').removeAttr('class');
        $('#report-tab-2').addClass('waves-effect waves-grey btn white m-b-xs');
        $('#report-tab-3').removeAttr('class');
        $('#report-tab-3').addClass('waves-effect waves-grey btn white m-b-xs');
        $('#report-div-2').hide();
        $('#report-div-3').hide();
        $('#report-div-1').show();
    });
    $('#report-tab-2').on('click', function () {
        $('.actions').hide();
        $(this).removeAttr('class');
        $(this).addClass('waves-effect waves-light blue btn m-b-xs');
        $('#report-tab-3').removeAttr('class');
        $('#report-tab-3').addClass('waves-effect waves-grey btn white m-b-xs');
        $('#report-tab-1').removeAttr('class');
        $('#report-tab-1').addClass('waves-effect waves-grey btn white m-b-xs');
        $('#report-div-3').hide();
        $('#report-div-1').hide();
        $('#report-div-2').show();
    });
    $('#report-tab-3').on('click', function () {
        $('.actions').show();
        $(this).removeAttr('class');
        $(this).addClass('waves-effect waves-light blue btn m-b-xs');
        $('#report-tab-1').removeAttr('class');
        $('#report-tab-1').addClass('waves-effect waves-grey btn white m-b-xs');
        $('#report-tab-2').removeAttr('class');
        $('#report-tab-2').addClass('waves-effect waves-grey btn white m-b-xs');
        $('#report-div-1').hide();
        $('#report-div-2').hide();
        $('#report-div-3').show();
    });

    $('.go_to_from_contact').on('click', function () {
        $('#report-tab-2').trigger('click');
    });
    $('.go_to_other').on('click', function () {
        $('#report-tab-3').trigger('click');
    });
    $('.go_to_from_project').on('click', function () {
        $('#report-tab-1').trigger('click');
    });

    $('#email-me').on('click', function () {
        $('#hide-email-me').hide();
        $('#show-email-me').empty();
        $('#final_step_heading').empty();
        $('#final_step_heading').text('Who Would You Like This Report Emailed To?');
        $('#selected-other-emails').empty();
        $('#show-email-me').append('<div class="input-field col s12 m4"></div><div class="input-field col s12 m4 collection" style = "padding:0px;"><div class="collection-item text-center">' + user_email + '<input class = "others-email" type = "hidden" value = "' + user_email + '" name = "GenerateReport[OtherEmail][]"></div></div><div class="input-field col s12 m4"></div>');
        $('#show-email-me').show();
    });
    $('#download-pdf').on('click', function () {
        $('#hide-email-me').hide();
        $('#show-email-me').empty();
        $('#final_step_heading').empty();
        $('#final_step_heading').text('Please fill company details');
        //var report_html = "";
        var project_id = $("input[name='GenerateReport[project_id]']").val();
        $.ajax({
            type: 'post',
            url: baseUrl + 'report/report-content',
            dataType: "json",
            data: {project_id: project_id, '_csrf-backend': csrf_token},
            success: function (resultData) {
                //report_html = resultData;
                $('#show-email-me').append(resultData);
                $('#show-email-me').show();
            }
        });

        /* setTimeout(function(){ 
         //console.log(report_html);
         $('#show-email-me').append(report_html);
         $('#show-email-me').show();
         }, 600); */




    });
    $('#email-others').on('click', function () {
        $('#hide-email-me').show();
        $('#final_step_heading').empty();
        $('#final_step_heading').text('Who Would You Like This Report Emailed To?');
        $('#show-email-me').empty();
        $('#show-email-me').hide();
    });

    $("#edit-event-details").on("click", function () {
        $(this).hide();
        $('.details-edit').removeAttr("style");
        $('.map').removeAttr("style");
        $('.project-value').css("display", "none");
    });
    $("#details-update-cancel").on("click", function () {
        window.location.href = window.location.href;
        /* var map_val = $('.project-map').text();
         if(map_val == ''){
         $('.map').css( "display", "none" );
         }
         $('.details-edit').css( "display", "none" );
         $('.project-value').removeAttr("style"); */
    });

    $("#update-event-details").on("click", function () {

        if ($('#update-project-details').valid()) {

            var project_details = $('#update-project-details').serialize();

            $.ajax({
                type: 'post',
                url: baseUrl + 'project/update-project-details',
                dataType: "json",
                data: project_details,
                success: function (resultData) {
                    if (resultData == 'success') {
                        window.location.href = window.location.href;
                    }
                }
            });
        }
    });
    $("#project-search").on("keyup", function () {
        var project_name = $('#project-search').val();
        $.ajax({
            type: 'post',
            url: baseUrl + 'project/search-duplicate-project',
            dataType: "json",
            data: {'project_name': project_name, '_csrf-backend': csrf_token},
            success: function (resultData) {
                if (resultData) {
                    $('#empty').val('');
                    $('#project-list').empty();
                    $('#project-list').append(resultData);
                }
            }
        });
    });
    $('input[name="GenerateReport[IsTimeline]"]').change(function () {
        $('#submitTimeline').val('true');
        var report_form = $('#generate-report-form');
        report_form.submit();
    });
    $('#check-all-pages').on('click', function () {
        console.log($(this).prev().is(':checked'));

        if (!$(this).prev().is(':checked')) {
            $('.all_check').each(function (e) {
                $(this).removeAttr("checked");
            });
            $('.all_check').each(function () {
                $(this).next().trigger('click');
            });
        } else {
            $('.all_check').each(function (e) {
                $(this).removeAttr("checked");
            });
        }
    });

    $('.report-project-page').on('click', function () {
        if ($('.report-project-page').is(':checked')) {
            $('#empty').val('');
            $('#empty').val('true');
            if ($('#yes').is(':checked')) {
                $("#no").removeAttr('checked');
                $('#yes').attr('checked', 'checked');
            } else {
                $("#yes").removeAttr('checked');
                $('#no').attr('checked', 'checked');
            }
        } else {
            $('#empty').val('');
        }
    });


});

/* new functions starts */

function editPojectPages() {
    $('#editPojectPages').submit();
}

/* new functions ends */


function isView(id) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'recentchanges/change-isview',
        dataType: "json",
        data: {'id': id, '_csrf-backend': csrf_token},
        success: function (resultData) {
            if (resultData) {
                $('#tr-' + id).removeClass('activity');
            }
        }
    });
}

function isDismiss(id) {
    if ($('#dismis-' + id).prop("checked") == true) {
        $('.confirm-dismiss').attr('data-id', id);
        $('#dismiss-modal').trigger('click');
    }
}

function isDismissTodo(id) {
    if ($('#dismis-' + id).prop("checked") == true) {
        $('.confirm-dismiss-todo').attr('data-id', id);
        $('#dismiss-modal').trigger('click');
    }
}

/** Pages **/

function savecolor(val, id) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'page/savecolor',
        dataType: "json",
        data: {'val': val, 'id': id, '_csrf-backend': csrf_token},
        success: function (resultData) {
            $("#colorshow" + id + "").css({
                'background-color': val,
            });

        }
    });
}

function savedocumentcolor(val, id) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'documents/savecolor',
        dataType: "json",
        data: {'val': val, 'id': id, '_csrf-backend': csrf_token},
        success: function (resultData) {
            $("#colorshow" + id + "").css({
                'background-color': val,
            });

        }
    });
}

function savepeople(val, attr, id) {
    var checked = $('#all' + id + ':checkbox:checked').length > 0;
    $.ajax({
        type: 'post',
        url: baseUrl + 'page/savepeople',
        dataType: "json",
        data: {'val': val, 'id': id, 'attr': attr, 'checked': checked, '_csrf-backend': csrf_token},
        success: function (resultData) {

        }
    });
}

function savedocumentpeople(val, attr, id) {
    var checked = $('#all' + id + ':checkbox:checked').length > 0;
    $.ajax({
        type: 'post',
        url: baseUrl + 'documents/savepeople',
        dataType: "json",
        data: {'val': val, 'id': id, 'attr': attr, 'checked': checked, '_csrf-backend': csrf_token},
        success: function (resultData) {

        }
    });
}

function saveflag(id) {
    var x = document.getElementById("flagtext" + id);
    $.ajax({
        type: 'post',
        url: baseUrl + 'page/saveflag',
        dataType: "json",
        data: {'txt': x.value, 'id': id, '_csrf-backend': csrf_token},
        success: function (resultData) {

        }
    });
}

function savedocumentflag(id) {
    var x = document.getElementById("flagtext" + id);
    $.ajax({
        type: 'post',
        url: baseUrl + 'documents/saveflag',
        dataType: "json",
        data: {'txt': x.value, 'id': id, '_csrf-backend': csrf_token},
        success: function (resultData) {

        }
    });
}

function savetimeline(attr, id, pid) {
    var x = document.getElementById(attr + id);
    $.ajax({
        type: 'post',
        url: baseUrl + 'page/savetimeline',
        dataType: "json",
        data: {'txt': x.value, 'id': id, 'attr': attr, 'pid': pid, '_csrf-backend': csrf_token},
        success: function (resultData) {

        }
    });
}

function savetodo(attr, id) {
    var x = document.getElementById(attr + id);
    $.ajax({
        type: 'post',
        url: baseUrl + 'page/savetodo',
        dataType: "json",
        data: {'txt': x.value, 'id': id, 'attr': attr, '_csrf-backend': csrf_token},
        success: function (resultData) {

        }
    });
}

function savedocumenttodo(attr, id) {
    var x = document.getElementById(attr + id);
    $.ajax({
        type: 'post',
        url: baseUrl + 'documents/savetodo',
        dataType: "json",
        data: {'txt': x.value, 'id': id, 'attr': attr, '_csrf-backend': csrf_token},
        success: function (resultData) {

        }
    });
}

function savepages(val, attr, id) {
    var checked = $('#pageall' + id + ':checkbox:checked').length > 0;
    $.ajax({
        type: 'post',
        url: baseUrl + 'page/savepages',
        dataType: "json",
        data: {'val': val, 'id': id, 'attr': attr, 'checked': checked, '_csrf-backend': csrf_token},
        success: function (resultData) {

        }
    });
}

function savedetails(attr, pid, id) {
    var x = document.getElementById(attr + id);
    $.ajax({
        type: 'post',
        url: baseUrl + 'page/savedetails',
        dataType: "json",
        data: {'txt': x.value, 'id': id, 'pid': pid, 'attr': attr, '_csrf-backend': csrf_token},
        success: function (resultData) {

        }
    });
}

function savedocumentdetails(attr, pid, id) {
    var x = document.getElementById(attr + id);
    $.ajax({
        type: 'post',
        url: baseUrl + 'documents/savedetails',
        dataType: "json",
        data: {'txt': x.value, 'id': id, 'pid': pid, 'attr': attr, '_csrf-backend': csrf_token},
        success: function (resultData) {

        }
    });
}

function savedoc(val, id, did) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'page/savedoc',
        dataType: "json",
        data: {'val': did, 'id': id, '_csrf-backend': csrf_token},
        success: function (resultData) {

        }
    });
}

function sendmessage(tid) {
    var message = document.getElementById('message' + tid + '').value;
    var pid = $('#pid' + tid + '').val();
    var rid = $('#rid' + tid + '').val();
    var tid = $('#threadid' + tid + '').val();
    $.ajax({
        type: 'post',
        url: baseUrl + 'message/acceptmessages',
        dataType: "json",
        data: {'message': message, 'pid': pid, 'rid': rid, 'tid': tid, '_csrf-backend': csrf_token},
        success: function (resultData) {
            $("#message" + tid + "").val('');
            $("#chat_main" + tid + "").html(resultData.messages);


        }
    });
}

function getmessages(rid, pid, tid) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'message/getmessages',
        dataType: "json",
        data: {'rid': rid, 'pid': pid, 'tid': tid, '_csrf-backend': csrf_token},
        success: function (resultData) {
            $("#message" + tid + "").val('');
            $("#message" + tid + "").attr('disabled', false);
            $(".roleslist" + tid + " a").not("#role" + rid + "-" + tid + "").css({'color': '#0277bb;', 'background-color': 'transparent'});
            $("#role" + rid + "-" + tid + "").css({'color': '#fff;', 'background-color': 'lightskyblue'});
            document.getElementById("rid" + tid + "").value = resultData.rid;
            $("#chat_main" + tid + "").html(resultData.messages);


        }
    });
}

function refresh() {
    location.reload();
}

$('#searchz').on('input', function () {
    var x = document.getElementById("searchz");
    $.ajax({
        type: 'post',
        url: baseUrl + 'search/search',
        dataType: "json",
        data: {'val': x.value, '_csrf-backend': csrf_token},
        success: function (resultData) {
            $("#searchdata").html(resultData.data);
            $(".search-results").css('display', 'block');
            if (screen.width >= 768) {
                var maxHeight = -1;
                $('.search-result-container').each(function () {
                    maxHeight = maxHeight > $(this).height() ? maxHeight : $(this).height();
                });
                $('.search-result-container').each(function () {
                    $(this).height(maxHeight);
                });
            }
        }
    });
});
/** end **/

/* Custom editing stats */

function changePeopleStatus(id) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'page/change-people-status',
        dataType: "json",
        data: {'PageItemId': id, '_csrf-backend': csrf_token},
        success: function (resultData) {
            if (resultData.status == 'active' || resultData.status == 'inactive') {
                window.location.href = window.location.href;
            }
        }
    });
}
function changeFlagStatus(id) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'page/change-flag-status',
        dataType: "json",
        data: {'PageItemId': id, '_csrf-backend': csrf_token},
        success: function (resultData) {
            if (resultData.status == 'active' || resultData.status == 'inactive') {
                window.location.href = window.location.href;
            }
        }
    });
}
function changeTimelineStatus(id) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'page/change-timeline-status',
        dataType: "json",
        data: {'PageItemId': id, '_csrf-backend': csrf_token},
        success: function (resultData) {
            if (resultData.status == 'active' || resultData.status == 'inactive') {
                window.location.href = window.location.href;
            }
        }
    });
}
function changeToDoStatus(id) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'page/change-to-do-status',
        dataType: "json",
        data: {'PageItemId': id, '_csrf-backend': csrf_token},
        success: function (resultData) {
            if (resultData.status == 'active' || resultData.status == 'inactive') {
                window.location.href = window.location.href;
            }
        }
    });
}
function changePageStatus(id) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'page/change-page-status',
        dataType: "json",
        data: {'PageItemId': id, '_csrf-backend': csrf_token},
        success: function (resultData) {
            if (resultData.status == 'active' || resultData.status == 'inactive') {
                window.location.href = window.location.href;
            }
        }
    });
}
function changeDocumentStatus(id) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'page/change-document-status',
        dataType: "json",
        data: {'PageItemId': id, '_csrf-backend': csrf_token},
        success: function (resultData) {
            if (resultData.status == 'active' || resultData.status == 'inactive') {
                window.location.href = window.location.href;
            }
        }
    });
}
function changeDocumentPeopleStatus(id) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'documents/change-people-status',
        dataType: "json",
        data: {'DocumentItemId': id, '_csrf-backend': csrf_token},
        success: function (resultData) {
            if (resultData.status == 'active' || resultData.status == 'inactive') {
                window.location.href = window.location.href;
            }
        }
    });
}
function changeDocumentFlagStatus(id) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'documents/change-flag-status',
        dataType: "json",
        data: {'DocumentItemId': id, '_csrf-backend': csrf_token},
        success: function (resultData) {
            if (resultData.status == 'active' || resultData.status == 'inactive') {
                window.location.href = window.location.href;
            }
        }
    });
}
function changeDocumentToDoStatus(id) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'documents/change-to-do-status',
        dataType: "json",
        data: {'DocumentItemId': id, '_csrf-backend': csrf_token},
        success: function (resultData) {
            if (resultData.status == 'active' || resultData.status == 'inactive') {
                window.location.href = window.location.href;
            }
        }
    });
}

/* Custom editing ends */
