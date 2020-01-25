jQuery(function($) {
    $('.collapse').collapse('hide');

    $('#bookme_pro_import_file').change(function() {
        if($(this).val()) {
            $('#bookme_pro_import').submit();
        }
    });
});