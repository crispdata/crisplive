var baseUrl = $('#base_url').val();
var clients = {};
var csrf_token = $('meta[name="csrf-token"]').attr('content');
var user_email = $('meta[name="user_email"]').attr('content');
var isTimeline = $('input[name="GenerateReport[IsTimeline]"]:checked').val();
if (isTimeline == "0" || isTimeline == "1") {
    setTimeout(function () {
        $('#steps-uid-0-t-3').trigger('click');
    }, 1000);
}

$(document).ready(function () {

    // updating the view with notifications using ajax

    function load_unseen_notification(view = '')
    {
         $.ajax({
              method: "post",
            url: baseUrl + 'message/getnotifications',
            dataType: "json",
              data: {'view': view, '_csrf-backend': csrf_token},
              success: function (data)
              {
                   $('#message-dropdown').html(data.notification);
                   if (data.unseen_notification > 0)
                   {
                        $('.badge').html(data.unseen_notification);
                   }
              }
         });
    }

    if (user_email != '') {
        load_unseen_notification();
    }

// load new notifications

    $(document).on('click', '#messagebutton', function () {

         $('.badge').html('');

         load_unseen_notification('yes');

    });

    if (user_email != '') {
        setInterval(function () {

            load_unseen_notification();


        }, 5000);
    }

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

    $('.datepicktime').daterangepicker({
        opens: 'up'
    }, function (start, end, label) {
        $('#StartDate').val(start.format('YYYY-MM-DD'));
        $('#EndDate').val(end.format('YYYY-MM-DD'));
    });




});

function gotoroles(pid) {
    window.location = baseUrl + "site/update-project?project=" + pid + "&role=1";
}

function nl2br(str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

function changestatus(uid) {
    var x = document.getElementById("statususer" + uid + "").checked;
    if (x === true) {
        var val = '1';
    } else {
        var val = '0';
    }
    $.ajax({
          method: "post",
        url: baseUrl + 'site/changestatus',
        dataType: "json",
          data: {'val': val, 'uid': uid, '_csrf-backend': csrf_token},
          success: function (data)
          {
              
          }
     });
}