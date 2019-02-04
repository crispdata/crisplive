var baseUrl = $('#base_url').val();
var csrf_token = $('meta[name="csrf-token"]').attr('content');

function submitform() {
    $("#sort-data").submit();
}

$(document).ready(function () {
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
       
        /*"bProcessing": true,
         "serverSide": true,
         "ajax": {
         url: baseUrl + 'site/gettenders',
         type: "post", // type of method  ,GET/POST/DELETE
         data: {
         '_csrf-backend': csrf_token
         },
         error: function () {
         $("#employee_grid_processing").css("display", "none");
         }
         }*/

    });

    $('#current-project-tenders').DataTable({
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
        'bPaginate': false,
        "bInfo": false,
        "bLengthChange": true,

    });

    $('#view-items').DataTable({
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
        "pageLength": 100
    });

    $('#check_all').on('click', function () {
        if (this.checked) {
            $('.checkbox').each(function () {
                this.checked = true;
            });
        } else {
            $('.checkbox').each(function () {
                this.checked = false;
            });
        }
    });

    $('.checkbox').on('click', function () {
        if ($('.checkbox:checked').length == $('.checkbox').length) {
            $('#check_all').prop('checked', true);
        } else {
            $('#check_all').prop('checked', false);
        }
    });


    var result = $("#current-project-makes").DataTable({
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
        }
    });

    $('#statusFilter').on('change', function () {
        $("#make-types").submit();
    });

    $('#statusFiltersizes').on('change', function () {
        if (this.value == 1) {
            $("#second").show();
            $("#third").show();
            $("#tables").hide();
        } else {
            $("#second").hide();
            $("#third").hide();
            $("#mtypesorttwo").prop('selectedIndex', 0);
            $("#mtypesorttwo").material_select();
            $("#mtypesortthree").prop('selectedIndex', 0);
            $("#mtypesortthree").material_select();
            $("#make-types").submit();
        }
        //$("#make-types").submit();
    });

    $('#statusFilterfit').on('change', function () {
        $("#make-types").submit();
    });

    $('#mtypesortthree').on('change', function () {
        $("#make-types").submit();
    });

    $('#commanddropdown').on('change', function () {
        $("#command-types").submit();
    });

    $('.dataTables_length select').addClass('browser-default');

    $('#data-table').DataTable({
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
        }
    });
    $('.dataTables_length select').addClass('browser-default');

    $('#recent-table').DataTable({
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
        }
    });
    $('.dataTables_length select').addClass('browser-default');
});

function deleteConfirm() {
    var result = confirm("Do you really want to delete items?");
    if (result) {
        return true;
    } else {
        return false;
    }
}