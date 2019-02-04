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

});

function getdata(value) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/getdata',
        dataType: "json",
        data: {'value': value, '_csrf-backend': csrf_token},
        success: function (resultData) {
            $("#tendertwo").html(resultData.data);
            
            $("#second").show();
            alert('asa');
            $("#third").show();
        }
    });
}

function getseconddata(value) {
    $.ajax({
        type: 'post',
        url: baseUrl + 'site/getseconddata',
        dataType: "json",
        data: {'value': value, '_csrf-backend': csrf_token},
        success: function (resultData) {
            $("#tenderthree").html(resultData.data);
        }
    });
}


/* Custom editing ends */
