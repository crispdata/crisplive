var baseUrl = $('#base_url').val();
var create_project_response = '';

$(document).ready(function () {

    var form = $("#create-project-form");
    var validator = $("#create-project-form").validate({
        errorPlacement: function errorPlacement(error, element) {
            element.after(error);
        },
        rules: {
            confirm: {
                equalTo: "#password"
            }
        }
    });

    form.children("div").steps({
        headerTag: "h3",
        bodyTag: "section",
        transitionEffect: "fade",
        onStepChanging: function (event, currentIndex, newIndex)
        {
            if (form.valid()) {
                if (newIndex == 0) {
                    $('[role=menu] li').first().css("display", "none");
                } else {
                    $('[role=menu] li').first().removeAttr("style");
                }
            }
            form.validate().settings.ignore = ":disabled,:hidden";
            return form.valid();
        },
        onFinishing: function (event, currentIndex)
        {
            form.validate().settings.ignore = ":disabled";
            return form.valid();
        },
        onFinished: function (event, currentIndex)
        {
            var project_form_data = $(form).serialize();

            $.ajax({
                type: "POST",
                url: baseUrl + 'site/create-new-project',
                data: project_form_data,
                success: function (result) {

                    if (result === "success")
                    {
                        $(form).trigger("reset");
                        //$(form).replaceWith("<p>Congratulations! You have successfully created new project</p>");
                        $(".project-success-sweetalert").trigger("click");
                        window.setTimeout(function () {
                            window.location = baseUrl + 'site/current-projects';
                        }, 2000);

                    } else if (result == "warning") {
                        $(".project-warning-sweetalert").trigger("click");
                    }
                }
            });
        }
    });

    var update_form = $("#update-project-form");
    var update_validator = $("#update-project-form").validate({
        errorPlacement: function errorPlacement(error, element) {
            element.after(error);
        },
        rules: {
            confirm: {
                equalTo: "#password"
            }
        }
    });

    update_form.children("div").steps({
        headerTag: "h3",
        bodyTag: "section",
        transitionEffect: "fade",
        onStepChanging: function (event, currentIndex, newIndex)
        {
            if (update_form.valid()) {
                if (newIndex == 0) {
                    $('[role=menu] li').first().css("display", "none");
                } else {
                    $('[role=menu] li').first().removeAttr("style");
                }
            }
            update_form.validate().settings.ignore = ":disabled,:hidden";
            return update_form.valid();
        },
        onFinishing: function (event, currentIndex)
        {
            update_form.validate().settings.ignore = ":disabled";
            return update_form.valid();
        },
        onFinished: function (event, currentIndex)
        {
            var project_form_data = $(update_form).serialize();
            var project_form = $(update_form).serializeArray();

            $.ajax({
                type: "POST",
                url: baseUrl + 'site/update-project',
                data: project_form_data,
                success: function (result) {

                    if (result == "success")
                    {
                        // $(update_form).trigger("reset");
                        $(".project-update-sweetalert").trigger("click");

                        window.setTimeout(function () {
                            window.location = baseUrl + 'site/project-details?project=' + project_form[1].value + '';
                        }, 2000);

                    } else if (result == "warning") {
                        $(".project-warning-sweetalert").trigger("click");
                    }
                }
            });
        }
    });

    var template_form = $("#create-template-form");
    var template_validator = $("#create-template-form").validate({
        errorPlacement: function errorPlacement(error, element) {
            element.after(error);
        },
        rules: {
            confirm: {
                equalTo: "#password"
            }
        }
    });

    template_form.children("div").steps({
        headerTag: "h3",
        bodyTag: "section",
        transitionEffect: "fade",
        onStepChanging: function (event, currentIndex, newIndex)
        {
            if (template_form.valid()) {
                if (newIndex == 0) {
                    $('[role=menu] li').first().css("display", "none");
                } else {
                    $('[role=menu] li').first().removeAttr("style");
                }
            }
            template_form.validate().settings.ignore = ":disabled,:hidden";
            return template_form.valid();
        },
        onFinishing: function (event, currentIndex)
        {
            template_form.validate().settings.ignore = ":disabled";
            return template_form.valid();
        },
        onFinished: function (event, currentIndex)
        {
            var template_form_data = $(template_form).serialize();

            $.ajax({
                type: "POST",
                url: baseUrl + 'site/create-template',
                data: template_form_data,
                success: function (result) {

                    if (result == "success")
                    {
                        // $(update_form).trigger("reset");
                        $(".template-success-sweetalert").trigger("click");
                        window.setTimeout(function () {
                            window.location = baseUrl + 'site/manage-templates';
                        }, 2000);

                    }
                }
            });
        }
    });


    var update_template_form = $("#update-template-form");
    var update_template_validator = $("#update-template-form").validate({
        errorPlacement: function errorPlacement(error, element) {
            element.after(error);
        },
        rules: {
            confirm: {
                equalTo: "#password"
            }
        }
    });

    update_template_form.children("div").steps({
        headerTag: "h3",
        bodyTag: "section",
        transitionEffect: "fade",
        onStepChanging: function (event, currentIndex, newIndex)
        {
            if (update_template_form.valid()) {
                if (newIndex == 0) {
                    $('[role=menu] li').first().css("display", "none");
                } else {
                    $('[role=menu] li').first().removeAttr("style");
                }
            }
            update_template_form.validate().settings.ignore = ":disabled,:hidden";
            return update_template_form.valid();
        },
        onFinishing: function (event, currentIndex)
        {
            update_template_form.validate().settings.ignore = ":disabled";
            return update_template_form.valid();
        },
        onFinished: function (event, currentIndex)
        {
            var project_form_data = $(update_template_form).serialize();

            $.ajax({
                type: "POST",
                url: baseUrl + 'site/update-template',
                data: project_form_data,
                success: function (result) {

                    if (result == "success")
                    {
                        // $(update_form).trigger("reset");
                        $(".template-update-sweetalert").trigger("click");
                        window.setTimeout(function () {
                            window.location = baseUrl + 'site/manage-templates';
                        }, 2000);

                    }
                }
            });
        }
    });


    var report = $("#generate-report-form");
    var report_validator = $("#generate-report-form").validate({
        errorPlacement: function errorPlacement(error, element) {
            element.after(error);
        },
        rules: {
            confirm: {
                equalTo: "#password"
            }
        }
    });

    report.children("div").steps({
        headerTag: "h3",
        bodyTag: "section",
        transitionEffect: "fade",
        onStepChanging: function (event, currentIndex, newIndex)
        {
            if (report.valid()) {
                /* console.log('currentIndex: '+ currentIndex);
                 console.log('newIndex: '+ newIndex);
                 console.log('style: '+$('#report-div-3').css( "display")); */


                if (newIndex == 0) {
                    $('[role=menu] li').first().css("display", "none");
                } else {
                    $('[role=menu] li').first().removeAttr("style");
                }

                if (newIndex == 6 || newIndex == 4) {
                    if (newIndex == 6) {
                        if ($('input[name="GenerateReport[IsTimeline]"]:checked').val() == "1") {

                            if ($('#report-div-3').css("display") == 'block' || $('#show-email-me').css("display") == 'block') {
                                $('.actions').show();
                            } else {
                                $('.actions').hide();
                            }
                        }
                    }
                    if (newIndex == 4) {
                        console.log($('input[name="GenerateReport[IsTimeline]"]:checked').val());
                        if ($('input[name="GenerateReport[IsTimeline]"]:checked').val() == "0" || $('input[name="GenerateReport[IsTimeline]"]:checked').val() == undefined) {

                            if ($('#report-div-3').css("display") == 'block' || $('#show-email-me').css("display") == 'block') {
                                $('.actions').show();
                            } else {
                                $('.actions').hide();
                            }
                        }
                    }
                } else {
                    $('.actions').show();
                }
            }
            report.validate().settings.ignore = ":disabled,:hidden";
            return report.valid();
        },
        onFinishing: function (event, currentIndex)
        {
            report.validate().settings.ignore = ":disabled";
            return report.valid();
        },
        onFinished: function (event, currentIndex)
        {
            event.preventDefault();
            //var report_data = $(report).serialize();
            var report_data = new FormData(report.get(0));

            $('body').removeClass('loaded');
            $('#materialPreloader').show();
            $('.loader').show();

            $.ajax({
                type: "POST",
                url: baseUrl + 'report/generate-report',
                data: report_data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (result) {

                    var resp = JSON.parse(result);

                    if (resp.status == "success") {

                        $('body').addClass('loaded');
                        $('#materialPreloader').hide();
                        $('.loader').hide();

                        window.location.href = baseUrl + 'report/download?f=pdf/' + resp.project_name + '-Summary-' + resp.version + '.pdf';
                        $(report).trigger("reset");
                        $(".report-success-sweetalert").trigger("click");
                        window.setTimeout(function () {
                            window.location = baseUrl + 'site/project-details?project=' + resp.project_id + '';
                        }, 2000);
                    }
                    if (resp.stat == "success")
                    {
                        $('body').addClass('loaded');
                        $('#materialPreloader').hide();
                        $('.loader').hide();

                        $(report).trigger("reset");
                        $(".report-success-sweetalert").trigger("click");
                        window.setTimeout(function () {
                            window.location = baseUrl + 'site/project-details?project=' + resp.project_id + '';
                        }, 2000);
                    }
                }
            });
        }
    });

    $(".wizard .actions ul li a").addClass("waves-effect waves-blue btn-flat");
    $(".wizard .steps ul").addClass("tabs z-depth-1");
    $(".wizard .steps ul li").addClass("tab");
    $('ul.tabs').tabs();
    $('select').material_select();
    $('.select-wrapper.initialized').prev("ul").remove();
    $('.select-wrapper.initialized').prev("input").remove();
    $('.select-wrapper.initialized').prev("span").remove();

    $('.pdatepicker').pickadate({
        format: 'dd-mm-yyyy',
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 15, // Creates a dropdown of 15 years to control year
        onSet: function (ele) {
            if (ele.select) {
                var chosen_date = $('.pdatepicker').val();
                $('.ddatepicker').pickadate('picker').set('min', chosen_date);
                this.close();
            }
        }
    });


    $('.ddatepicker').pickadate({
        format: 'dd-mm-yyyy',
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 15, // Creates a dropdown of 15 years to control year
        onSet: function (ele) {
            if (ele.select) {
                var chosen_date = $('.ddatepicker').val();
                $('.bsdatepicker').pickadate('picker').set('min', chosen_date);
                $('.pdatepicker').pickadate('picker').set('max', chosen_date);
                this.close();
            }
        },
        onStart: function () {
            var chosen_date = $('.pdatepicker').val();
            $('.ddatepicker').pickadate('picker').set('min', chosen_date);
            //return false;
        }
    });

    $('.bsdatepicker').pickadate({
        format: 'dd-mm-yyyy',
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 15, // Creates a dropdown of 15 years to control year
        onSet: function (ele) {
            if (ele.select) {
                var chosen_date = $('.bsdatepicker').val();
                $('.bedatepicker').pickadate('picker').set('min', chosen_date);
                $('.ddatepicker').pickadate('picker').set('max', chosen_date);
                this.close();
            }
        },
        onStart: function () {
            var chosen_date = $('.ddatepicker').val();
            $('.bsdatepicker').pickadate('picker').set('min', chosen_date);
            //return false;
        }
    });
    $('.bedatepicker').pickadate({
        format: 'dd-mm-yyyy',
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 15, // Creates a dropdown of 15 years to control year
        onSet: function (ele) {
            if (ele.select) {
                var chosen_date = $('.bedatepicker').val();
                $('.bodatepicker').pickadate('picker').set('min', chosen_date);
                $('.bsdatepicker').pickadate('picker').set('max', chosen_date);
                this.close();
            }
        },
        onStart: function () {
            var chosen_date = $('.bsdatepicker').val();
            $('.bedatepicker').pickadate('picker').set('min', chosen_date);
            //return false;
        }
    });
    $('.bodatepicker').pickadate({
        format: 'dd-mm-yyyy',
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 15, // Creates a dropdown of 15 years to control year
        onSet: function (ele) {
            if (ele.select) {
                var chosen_date = $('.bodatepicker').val();
                $('.bedatepicker').pickadate('picker').set('max', chosen_date);
                this.close();
            }
        },
        onStart: function () {
            var chosen_date = $('.bedatepicker').val();
            $('.bodatepicker').pickadate('picker').set('min', chosen_date);
            //return false;
        }
    });
});


