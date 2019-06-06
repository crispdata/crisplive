var baseUrl = $('#base_url').val();
var csrf_token = $('meta[name="csrf-token"]').attr('content');
$(document).ready(function () {
    $('select.materialSelectmake').select2({closeOnSelect: true, placeholder: 'Select Makes'});
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

    $('select.materialSelectdepart').select2({
        closeOnSelect: true,
        placeholder: 'Select Department',
        allowClear: true,
        ajax: {
            headers: {
                "Authorization": "Bearer " + csrf_token,
                "Content-Type": "application/json",
            },
            type: 'get',
            url: baseUrl + 'contractor/getdepartments',
            dataType: 'json',
            data: function (params) {
                $("#conextrad").remove();
                $("#showcontd").remove();
                return {
                    term: params.term || '',
                    page: params.page || 1
                }
            },
            cache: true,
        }
    });

    $('select.materialSelectcon').select2({
        closeOnSelect: true,
        placeholder: 'All Contractors',
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
                $("#conextra").remove();
                $("#showcont").remove();
                return {
                    term: params.term || '',
                    page: params.page || 1
                }
            },
            cache: true,
        }
    }
    );
    $('select.cmakes').select2({closeOnSelect: true, placeholder: 'Select Cables Makes'});
    $('select.lmakes').select2({closeOnSelect: true, placeholder: 'Select Lighting Makes'});
    $('select.wmakes').select2({closeOnSelect: true, placeholder: 'Select Wire Makes'});
    $('select.cementmakes').select2({closeOnSelect: true, placeholder: 'Select Cement Makes'});
    $('select.rmakes').select2({closeOnSelect: true, placeholder: 'Select Reinforcement Steel Makes'});
    $('select.smakes').select2({closeOnSelect: true, placeholder: 'Select Structural Steel Makes'});
    $('select.nmakes').select2({closeOnSelect: true, placeholder: 'Select Non Structural Steel Makes'});
    $('#dashmake').select2({closeOnSelect: true, placeholder: 'Select Make'});
    $('.materialSelectsize').select2({closeOnSelect: true, placeholder: 'Select Size'});
    $('.materialSelectdivision').select2({closeOnSelect: true, placeholder: 'Select Division'});
    $('.materialSelecttypefit').select2({closeOnSelect: true, placeholder: 'Select Type'});
    $('.materialSelectorg').select2({closeOnSelect: true, placeholder: 'Select Organisation'});
    $('.materialSelectdepart').select2({closeOnSelect: true, placeholder: 'Select Department'});
    $('.materialSelectcapacityfit').select2({closeOnSelect: true, placeholder: 'Select Capacity'});
    $('.materialSelectaccessoryone').select2({closeOnSelect: true, placeholder: 'Select Accessory'});
    $('.materialSelectaccessorytwo').select2({closeOnSelect: true, placeholder: 'Select Type'});
    $('.materialSelectaccessorythree').select2({closeOnSelect: true, placeholder: 'Select Sub Type'});
    $('select.ddfavour').select2({closeOnSelect: true});
    $('select.contact-authtypes').select2();
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