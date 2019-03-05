var csrf_token = $('meta[name="csrf-token"]').attr('content');
var baseUrl = $('#base_url').val();
(function ($) {

    'use strict';
    /**
     * =====================================
     * Function for windows height and width      
     * =====================================
     */
    function windowSize(el) {
        var result = 0;
        if ("height" == el)
            result = window.innerHeight ? window.innerHeight : $(window).height();
        if ("width" == el)
            result = window.innerWidth ? window.innerWidth : $(window).width();
        return result;
    }


    /**
     * =====================================
     * Function for email address validation         
     * =====================================
     */
    function isValidEmail(emailAddress) {
        var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
        return pattern.test(emailAddress);
    }
    ;
    /**
     * =====================================
     * Function for windows height and width      
     * =====================================
     */
    function deviceControll() {
        if (windowSize('width') < 768) {
            $('body').removeClass('desktop').removeClass('tablet').addClass('mobile');
        } else if (windowSize('width') < 992) {
            $('body').removeClass('mobile').removeClass('desktop').addClass('tablet');
        } else {
            $('body').removeClass('mobile').removeClass('tablet').addClass('desktop');
        }
    }




    $(window).on('resize', function () {

        deviceControll();
    });
    $(document).on('ready', function () {

        deviceControll();
        /**
         * =======================================
         * Top Navigaion Init
         * =======================================
         */
        var navigation = $('#js-navbar-menu').okayNav({
            toggle_icon_class: "okayNav__menu-toggle",
            toggle_icon_content: "<span /><span /><span /><span /><span />"
        });
        /**
         * =======================================
         * Top Fixed Navbar
         * =======================================
         */
        $(document).on('scroll', function () {
            var activeClass = 'navbar-home',
                    ActiveID = '.main-navbar-top',
                    scrollPos = $(this).scrollTop();
            if (scrollPos > 10) {
                $(ActiveID).addClass(activeClass);
            } else {
                $(ActiveID).removeClass(activeClass);
            }
        });
        /**
         * =======================================
         * NAVIGATION SCROLL
         * =======================================
         */
        var TopOffsetId = '.navbar-brand';
        $('#js-navbar-menu').onePageNav({
            currentClass: 'active',
            scrollThreshold: 0.2, // Adjust if Navigation highlights too early or too late
            scrollSpeed: 1000,
            scrollOffset: Math.abs($(TopOffsetId).outerHeight() - 1)
        });
        $('.btn-scroll a, a.btn-scroll').on('click', function (e) {
            e.preventDefault();
            var target = this.hash,
                    scrollOffset = Math.abs($(TopOffsetId).outerHeight()),
                    $target = ($(target).offset() || {"top": NaN}).top;
            if (e.target.hash !== '') {
                $('html, body').animate({scrollTop: $(e.target.hash).offset().top - 90}, 900);
                return false;
            }

        });
        $('#register').on('submit', function (e) {

            e.preventDefault();
            $.ajax({
                type: 'post',
                url: baseUrl + 'site/register',
                data: $('#register').serialize(),
                beforeSend: function () {
                    $("#signbutton").html('<img src="frontend/web/assets/images/loading.gif" alt="">');
                    $("#signbutton").attr('disabled', 'true');
                },
                success: function (response) {
                    if (response == 1) {
                        var message = 'Thank you for registering with Crispdata. We shall start sharing the desired information on your registered email address.';
                        swal({
                            title: "Success!",
                            text: message,
                            type: "success",
                            showCancelButton: false,
                            showConfirmButton: false,
                            timer: 6000,
                        }
                        , function () {
                            window.location.reload();
                        })
                                ;
                    } else if (response == 2) {
                        swal("", 'Please check the Terms of Service', "warning");
                    } else if (response == 3) {
                        swal("", 'Please select any product types', "warning");
                    } else {
                        swal("Sorry", 'Your registeration process has been failed. Please try again', "error");
                    }
                    $("#signbutton").text('Register');
                    $("#signbutton").removeAttr('disabled');
                }
            });
        });
        $('#emailotp').on('click', function (e) {
            var email = $("#email").val();
            $.ajax({
                type: 'post',
                url: baseUrl + 'site/sendotp',
                dataType: "json",
                data: {'email': email, 'type': '2', '_csrf-frontend': csrf_token},
                beforeSend: function () {
                    $("#emailotp").html('<img src="frontend/web/assets/images/loading.gif" alt="">');
                },
                success: function (data) {
                    if (data.response == 3) {
                        swal("", data.mess, "error");
                        $("#emailotp").text('Click here to generate OTP');
                    } else if (data.response == 0) {
                        swal("", "Please enter email address", "warning");
                        $("#emailotp").text('Click here to generate OTP');
                    } else {
                        swal("", "OTP sent to your email address", "success");
                        $("#otpemail").show();
                        $("#otpemail").val('');
                        $("#vemailotp").show();
                        $("#emailotp").text('Regenerate OTP');
                        $("#otpemail").removeAttr('disabled');
                        $("#vemailotp").css('background-color', 'red');
                        $("#vemailotp").text('Validate OTP');
                    }
                }
            });
        });
        $('#mobileotp').on('click', function (e) {
            var mobile = $("#mobile").val();
            $.ajax({
                type: 'post',
                url: baseUrl + 'site/sendotp',
                dataType: "json",
                data: {'mobile': mobile, 'type': '1', '_csrf-frontend': csrf_token},
                beforeSend: function () {
                    $("#mobileotp").html('<img src="frontend/web/assets/images/loading.gif" alt="">');
                },
                success: function (data) {
                    if (data.response == 0) {
                        swal("", "Please enter mobile number", "warning");
                        $("#mobileotp").text('Click here to generate OTP');
                    } else if (data.response == 1) {
                        swal("", "Please enter valid mobile number", "warning");
                        $("#mobileotp").text('Click here to generate OTP');
                    } else if (data.response == 2) {
                        swal("", "OTP sent to your mobile number", "success");
                        $("#otpmobile").show();
                        $("#otpmobile").val('');
                        $("#vmobileotp").show();
                        $("#mobileotp").text('Regenerate OTP');
                        $("#otpmobile").removeAttr('disabled');
                        $("#vmobileotp").css('background-color', 'red');
                        $("#vmobileotp").text('Validate OTP');
                    } else {
                        swal("", data.mess, "error");
                        $("#mobileotp").text('Click here to generate OTP');
                    }
                }
            });
        });
        $('#vemailotp').on('click', function (e) {
            var otp = $("#otpemail").val();
            var email = $("#email").val();
            var valone = $("#vmobileotp").attr('data-val');
            $.ajax({
                type: 'post',
                url: baseUrl + 'site/verifyotp',
                dataType: "json",
                data: {'email': email, 'otp': otp, 'type': '2', '_csrf-frontend': csrf_token},
                beforeSend: function () {
                    $("#vemailotp").html('<img src="frontend/web/assets/images/loading.gif" alt="">');
                },
                success: function (data) {
                    if (data.response == 1) {
                        swal("", 'Otp not verified', "error");
                        $("#vemailotp").text('Validate OTP');
                    } else if (data.response == 0) {
                        swal("", "Please enter OTP to validate", "warning");
                        $("#vemailotp").text('Validate OTP');
                    } else {
                        swal("", "OTP Successfully Validated", "success");
                        $("#otpemail").attr('disabled', 'true');
                        $("#vemailotp").css('background-color', 'green');
                        $("#vemailotp").text('Validated');
                        $("#vemailotp").attr('data-val', '1');
                        if (valone == 1) {
                            $("#signbutton").removeAttr('disabled');
                        }
                    }
                }
            });
        });
        $('#vmobileotp').on('click', function (e) {
            var otp = $("#otpmobile").val();
            var mobile = $("#mobile").val();
            var valtwo = $("#vemailotp").attr('data-val');
            $.ajax({
                type: 'post',
                url: baseUrl + 'site/verifyotp',
                dataType: "json",
                data: {'mobile': mobile, 'otp': otp, 'type': '1', '_csrf-frontend': csrf_token},
                beforeSend: function () {
                    $("#vmobileotp").html('<img src="frontend/web/assets/images/loading.gif" alt="">');
                },
                success: function (data) {
                    if (data.response == 3) {
                        swal("", data.mess, "error");
                        $("#vmobileotp").text('Validate OTP');
                    } else if (data.response == 0) {
                        swal("", "Please enter OTP to validate", "warning");
                        $("#vmobileotp").text('Validate OTP');
                    } else {
                        swal("", "OTP Successfully Validated", "success");
                        $("#otpmobile").attr('disabled', 'true');
                        $("#vmobileotp").css('background-color', 'green');
                        $("#vmobileotp").text('Validated');
                        $("#vmobileotp").attr('data-val', '1');
                        if (valtwo == 1) {
                            $("#signbutton").removeAttr('disabled');
                        }
                    }
                }
            });
        });
        $('#state').on('change', function (e) {
            var val = $("#state option:selected").val();
            var selects = '';
            $.ajax({
                type: 'post',
                url: baseUrl + 'site/getcities',
                dataType: "json",
                data: {'val': val, '_csrf-frontend': csrf_token},
                success: function (data) {
                    // setup listener for custom event to re-initialize on change
                    $('.contact-city').on('contentChanged', function () {
                        $(this).select2({closeOnSelect: true, placeholder: 'Select City'});
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
        });

        $('#authtype').on('change', function (e) {
            var val = $("#authtype option:selected").val();
            if (val == 1) {
                $("#cablesdiv").show();
                //$("#cables").attr('required', 'true');
                $("#lightdiv").hide();
                $("#cementdiv").hide();
                $("#rsteeldiv").hide();
                $("#ssteeldiv").hide();
                $("#nsteeldiv").hide();
                //$("#lighting").removeAttr('required');
            } else if (val == 2) {
                $("#lightdiv").show();
                //$("#lighting").attr('required', 'true');
                $("#cablesdiv").hide();
                $("#cementdiv").hide();
                $("#rsteeldiv").hide();
                $("#ssteeldiv").hide();
                $("#nsteeldiv").hide();
                //$("#cables").removeAttr('required');
            } else if (val == 3) {
                $("#lightdiv").hide();
                //$("#lighting").attr('required', 'true');
                $("#cablesdiv").hide();
                $("#cementdiv").show();
                $("#rsteeldiv").hide();
                $("#ssteeldiv").hide();
                $("#nsteeldiv").hide();
                //$("#cables").removeAttr('required');
            } else if (val == 4) {
                $("#lightdiv").hide();
                //$("#lighting").attr('required', 'true');
                $("#cablesdiv").hide();
                $("#cementdiv").hide();
                $("#rsteeldiv").show();
                $("#ssteeldiv").hide();
                $("#nsteeldiv").hide();
                //$("#cables").removeAttr('required');
            } else if (val == 5) {
                $("#lightdiv").hide();
                //$("#lighting").attr('required', 'true');
                $("#cablesdiv").hide();
                $("#cementdiv").hide();
                $("#rsteeldiv").hide();
                $("#ssteeldiv").show();
                $("#nsteeldiv").hide();
                //$("#cables").removeAttr('required');
            } else if (val == 6) {
                $("#lightdiv").hide();
                //$("#lighting").attr('required', 'true');
                $("#cablesdiv").hide();
                $("#cementdiv").hide();
                $("#rsteeldiv").hide();
                $("#ssteeldiv").hide();
                $("#nsteeldiv").show();
                //$("#cables").removeAttr('required');
            } else {
                $("#cablesdiv").hide();
                $("#lightdiv").hide();
                $("#cementdiv").hide();
                $("#rsteeldiv").hide();
                $("#ssteeldiv").hide();
                $("#nsteeldiv").hide();
                //$("#lighting").removeAttr('required');
                //$("#cables").removeAttr('required');
            }
        });



        $('#rtype').on('change', function (e) {
            var val = $("#rtype option:selected").val();
            localStorage.setItem("type", val);
            document.getElementById("register").reset();
            $("#hiddentype").val(val);
            $("#mobileotp").text('Click here to generate OTP');
            $("#emailotp").text('Click here to generate OTP');
            $("#otpmobile").hide();
            $("#vmobileotp").removeAttr('data-val');
            $("#vmobileotp").hide();
            $("#otpemail").hide();
            $("#vemailotp").removeAttr('data-val');
            $("#vemailotp").hide();
            $("#signbutton").attr('disabled', 'true');
            $("#state").select2("val", "");
            $("#cables").select2("val", "");
            $("#lighting").select2("val", "");
            $("#cement").select2("val", "");
            $("#rsteel").select2("val", "");
            $("#ssteel").select2("val", "");
            $("#nsteel").select2("val", "");
            $("#authtype").select2("val", "");
            $("#city").select2({closeOnSelect: true, placeholder: 'Select City'});
            if (val == 2) {
                $("#contractor").show();
                $("#commondiv").show();
                $("#dealer").hide();
                $("#cables").select2("val", "");
                $("#lighting").select2("val", "");
                $("#cables").removeAttr('required');
                $("#lighting").removeAttr('required');
                $("#cement").select2("val", "");
                $("#rsteel").select2("val", "");
                $("#cement").removeAttr('required');
                $("#rsteel").removeAttr('required');
                $("#ssteel").select2("val", "");
                $("#nsteel").select2("val", "");
                $("#ssteel").removeAttr('required');
                $("#nsteel").removeAttr('required');
                $("#authtype").removeAttr('required');
                $("#contracttype").attr('required');
                $("#supplier").hide();
                $("#manufacturer").hide();
            } else if (val == 4) {
                $("#contractor").hide();
                $("#commondiv").hide();
                $("#dealer").hide();
                $("#supplier").show();
                $("#manufacturer").hide();
            } else if (val == 1) {
                $("#contractor").hide();
                $("#contracttype").select2("val", "");
                $("#contracttype").removeAttr('required');
                $("#cables").attr('required');
                $("#lighting").attr('required');
                $("#cement").attr('required');
                $("#rsteel").attr('required');
                $("#ssteel").attr('required');
                $("#nsteel").attr('required');
                $("#authtype").attr('required');
                $("#commondiv").show();
                $("#dealer").show();
                $("#supplier").hide();
                $("#manufacturer").hide();
            } else {
                $("#contractor").hide();
                $("#dealer").show();
                $("#contracttype").select2("val", "");
                $("#contracttype").removeAttr('required');
                $("#cables").attr('required');
                $("#lighting").attr('required');
                $("#cement").attr('required');
                $("#rsteel").attr('required');
                $("#ssteel").attr('required');
                $("#nsteel").attr('required');
                $("#authtype").attr('required');
                $("#commondiv").show();
                $("#supplier").hide();
                $("#manufacturer").hide();
            }
        });
        /**
         * =======================================
         * PopUp Item Script
         * =======================================
         */
        $('.popup-video').magnificPopup({
            //disableOn: 700,
            type: 'iframe',
            mainClass: 'mfp-fade',
            removalDelay: 160,
            preloader: true,
            fixedContentPos: true
        });
        /**
         * =======================================
         * TESTIMONIAL SYNC WITH CLIENTS
         * =======================================
         */
        var testimonialSlider = $(".testimonial-wrapper"); // client's message
        testimonialSlider.owlCarousel({
            singleItem: true,
            autoPlay: 3000,
            slideSpeed: 500,
            paginationSpeed: 500,
            autoHeight: false,
            navigation: false,
            pagination: true,
            // transitionStyle:    "fade"
        });
        /**
         * ============================
         * CONTACT FORM 2
         * ============================
         */
        $("#contact-form").on('submit', function (e) {
            e.preventDefault();
            var success = $(this).find('.email-success'),
                    failed = $(this).find('.email-failed'),
                    loader = $(this).find('.email-loading'),
                    postUrl = $(this).attr('action');
            var data = {
                name: $(this).find('.contact-name').val(),
                email: $(this).find('.contact-email').val(),
                mobile: $(this).find('.contact-mobile').val(),
                subject: $(this).find('.contact-subject').val(),
                message: $(this).find('.contact-message').val(),
                '_csrf-frontend': csrf_token
            };
            if (isValidEmail(data['email']) && (data['message'].length > 1) && (data['name'].length > 1)) {
                $.ajax({
                    type: "POST",
                    url: postUrl,
                    data: data,
                    beforeSend: function () {
                        loader.fadeIn(1000);
                    },
                    success: function (data) {
                        loader.fadeOut(1000);
                        success.delay(500).fadeIn(1000);
                        failed.fadeOut(500);
                    },
                    error: function (xhr) { // if error occured
                        loader.fadeOut(1000);
                        failed.delay(500).fadeIn(1000);
                        success.fadeOut(500);
                    },
                    complete: function () {
                        loader.fadeOut(1000);
                        $('#contact-name').val('');
                        $('#contact-email').val('');
                        $('#contact-mobile').val('');
                        $('#contact-subject').val('');
                        $('#contact-message').val('');
                    }
                });
            } else {
                loader.fadeOut(1000);
                failed.delay(500).fadeIn(1000);
                success.fadeOut(500);
            }

            return false;
        });
    });
}(jQuery));
$(document).ready(function () {
    $('select.cmakes').select2({closeOnSelect: false, placeholder: 'Select Cables Makes'});
    $('select.lmakes').select2({closeOnSelect: false, placeholder: 'Select Lighting Makes'});
    $('select.cementmakes').select2({closeOnSelect: false, placeholder: 'Select Cement Makes'});
    $('select.rmakes').select2({closeOnSelect: false, placeholder: 'Select Reinforcement Steel Makes'});
    $('select.smakes').select2({closeOnSelect: false, placeholder: 'Select Structural Steel Makes'});
    $('select.nmakes').select2({closeOnSelect: false, placeholder: 'Select Non Structural Steel Makes'});
    $('select.contact-city').select2({closeOnSelect: true, placeholder: 'Select City'});
    $('select.contact-state').select2({closeOnSelect: true, placeholder: 'Select State'});
    $('select.contact-type').select2({width: '100%'});
    $('select.contact-ctype').select2();
    $('select.contact-contracttype').select2();
    $('select.contact-authtype').select2();
    $('#multiple').select2({
        placeholder: 'Select Multiple States'
    });
    $(".js-example-basic-multiple-limit").select2({
        maximumSelectionLength: 2,
        placeholder: 'Limited Selection'
    });
    $(".js-example-tokenizer").select2({
        tags: true,
        tokenSeparators: [',', ' '],
        placeholder: 'With Tokenization'
    });
    var data = [{id: 0, text: 'enhancement'}, {id: 1, text: 'bug'}, {id: 2, text: 'duplicate'}, {id: 3, text: 'invalid'}, {id: 4, text: 'wontfix'}];
    $(".js-example-data-array").select2({
        data: data
    });
});

