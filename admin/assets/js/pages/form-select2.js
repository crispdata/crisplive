$(document).ready(function () {
    $('select.materialSelectmake').select2({closeOnSelect: true, placeholder: 'Select Makes'});
    $('select.materialSelectcontractor').select2({closeOnSelect: true, placeholder: 'Select Contractor'});
    $('select.materialSelectcon').select2({closeOnSelect: true, placeholder: 'All Contractors'});
    $('select.cmakes').select2({closeOnSelect: true, placeholder: 'Select Cables Makes'});
    $('select.lmakes').select2({closeOnSelect: true, placeholder: 'Select Lighting Makes'});
    $('select.cementmakes').select2({closeOnSelect: true, placeholder: 'Select Cement Makes'});
    $('select.rmakes').select2({closeOnSelect: true, placeholder: 'Select Reinforcement Steel Makes'});
    $('select.smakes').select2({closeOnSelect: true, placeholder: 'Select Structural Steel Makes'});
    $('select.nmakes').select2({closeOnSelect: true, placeholder: 'Select Non Structural Steel Makes'});
    $('#dashmake').select2({closeOnSelect: true, placeholder: 'Select Make'});
    $('.materialSelectsize').select2({closeOnSelect: true, placeholder: 'Select Size'});
    $('.materialSelecttypefit').select2({closeOnSelect: true, placeholder: 'Select Type'});
    $('.materialSelectcapacityfit').select2({closeOnSelect: true, placeholder: 'Select Capacity'});
    $('.materialSelectaccessoryone').select2({closeOnSelect: true, placeholder: 'Select Accessory'});
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