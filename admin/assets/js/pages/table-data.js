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
    });
    $('.quantities').DataTable({
        language: {
            searchPlaceholder: 'Search records',
            sSearch: '',
            sLengthMenu: 'Show _MENU_',
            sLength: 'dataTables_length',
            "decimal": ".",
            oPaginate: {
                sFirst: '<i class="material-icons">chevron_left</i>',
                sPrevious: '<i class="material-icons">chevron_left</i>',
                sNext: '<i class="material-icons">chevron_right</i>',
                sLast: '<i class="material-icons">chevron_right</i>'
            }
        },
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

    $('#current-project-contractors').DataTable({
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
        "bFilter": false,
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

    $('#product').on('change', function () {
        $("#product-types").submit();
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

    $('#mtypesorttwo').on('change', function () {
        var selectedValue = $(this).val();
        if (selectedValue == 3) {
            $("#make-types").submit();
        }
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
    if ($('input[name="selected_id[]"]:checked').length <= 0) {
        swal("", "Please select any tender", "warning");
        return false;
    } else {
        var result = confirm("Do you really want to perform this action?");
        if (result) {
            return true;
        } else {
            return false;
        }
    }
}