jQuery(function ($) {
    var $form                = $('#business-hours'),
        $final_step_url      = $('input[name=bookme_pro_url_final_step_url]'),
        $final_step_url_mode = $('#bookme_pro_settings_final_step_url_mode'),
        $help_btn            = $('#bookme-pro-help-btn'),
        $participants        = $('#bookme_pro_appointment_participants')
        ;

    bookmeProAlert(BookmeProL10n.alert);

    Ladda.bind('button[type=submit]', {timeout: 2000});

    $('.select_start', $form).on('change', function () {
        var $flexbox = $(this).closest('.bookme-pro-flexbox'),
            $end_select = $('.select_end', $flexbox),
            start_time = this.value;

        if (start_time) {
            $flexbox.find('.bookme-pro-hide-on-off').show();

            // Hides end time options with value less than in the start time.
            var frag      = document.createDocumentFragment();
            var old_value = $end_select.val();
            var new_value = null;
            $('option', $end_select).each(function () {
                if (this.value <= start_time) {
                    var span = document.createElement('span');
                    span.style.display = 'none';
                    span.appendChild(this.cloneNode(true));
                    frag.appendChild(span);
                } else {
                    frag.appendChild(this.cloneNode(true));
                    if (new_value === null || old_value == this.value) {
                        new_value = this.value;
                    }
                }
            });
            $end_select.empty().append(frag).val(new_value);
        } else { // OFF
            $flexbox.find('.bookme-pro-hide-on-off').hide();
        }
    }).each(function () {
        $(this).data('default_value', this.value);
    }).trigger('change');

    // Reset.
    $('#bookme-pro-hours-reset', $form).on('click', function () {
        $('.select_start', $form).each(function () {
            $(this).val($(this).data('default_value')).trigger('change');
        });
    });

    // Customers Tab
    var $default_country      = $('#bookme_pro_cst_phone_default_country'),
        $default_country_code = $('#bookme_pro_cst_default_country_code');

    $.each($.fn.intlTelInput.getCountryData(), function (index, value) {
        $default_country.append('<option value="' + value.iso2 + '" data-code="' + value.dialCode + '">' + value.name + ' +' + value.dialCode + '</option>');
    });
    $default_country.val(BookmeProL10n.default_country);

    $default_country.on('change', function () {
        $default_country_code.val($default_country.find('option:selected').data('code'));
    });

    // Calendar tab
    $participants.on('change', function () {

        $('#bookme_pro_cal_one_participant').hide();
        $('#bookme_pro_cal_many_participants').hide();
        $('#' + $(this).val() ).show();
    }).trigger('change');

    $("#bookme_pro_settings_calendar button[type=reset]").on( 'click', function () {
        setTimeout(function () {
            $participants.trigger('change');
        }, 50 );
    });

    // Company Tab
    $('#bookme-pro-company-reset').on('click', function () {
        var $div = $('#bookme-pro-js-logo .bookme-pro-js-image'),
            $input = $('[name=bookme_pro_co_logo_attachment_id]');
        $div.attr('style', $div.data('style'));
        $input.val($input.data('default'));
    });


    // Payment Tab
    var $currency = $('#bookme_pro_pmt_currency'),
        $formats  = $('#bookme_pro_pmt_price_format')
    ;
    $currency.on('change', function () {
        $formats.find('option').each(function () {
            var decimals = this.value.match(/{price\|(\d)}/)[1],
                price    = BookmeProL10n.sample_price
            ;
            if (decimals < 3) {
                price = price.slice(0, -(decimals == 0 ? 4 : 3 - decimals));
            }
            this.innerHTML = this.value
                .replace('{symbol}', $currency.find('option:selected').data('symbol'))
                .replace(/{price\|\d}/, price)
            ;
        });
    }).trigger('change');



    $('#bookme-pro-payments-reset').on('click', function (event) {
        setTimeout(function () {
            $('#bookme_pro_pmt_currency,#bookme_pro_pmt_paypal,#bookme_pro_pmt_authorize_net,#bookme_pro_pmt_stripe,#bookme_pro_pmt_2checkout,#bookme_pro_pmt_payu_latam,#bookme_pro_pmt_payson,#bookme_pro_pmt_mollie,#bookme_pro_payu_latam_sandbox').change();
        }, 0);
    });

    $('#bookme-pro-customer-reset').on('click', function (event) {
        $default_country.val($default_country.data('country'));
    });

    // Change link to Help page according to activated tab.
    var help_link = $help_btn.attr('href');
    $('.bookme-pro-nav li[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        $help_btn.attr('href', help_link + e.target.getAttribute('data-target').substring(1).replace(/_/g, '-'));
    });

    // Holidays
    var d = new Date();
    $('.bookme-pro-js-annual-calendar').jCal({
        day: new Date(d.getFullYear(), 0, 1),
        days:       1,
        showMonths: 12,
        scrollSpeed: 350,
        events:     BookmeProL10n.holidays,
        action:     'bookme_pro_settings_holiday',
        csrf_token: BookmeProL10n.csrf_token,
        dayOffset:  parseInt(BookmeProL10n.start_of_week),
        loadingImg: BookmeProL10n.loading_img,
        dow:        BookmeProL10n.days,
        ml:         BookmeProL10n.months,
        we_are_not_working: BookmeProL10n.we_are_not_working,
        repeat:     BookmeProL10n.repeat,
        close:      BookmeProL10n.close
    });

    $('.bookme-pro-js-jCalBtn').on('click', function (e) {
        e.preventDefault();
        var trigger = $(this).data('trigger');
        $('.bookme-pro-js-annual-calendar').find($(trigger)).trigger('click');
    });

    // Activate tab.
    $('li[data-target="#bookme_pro_settings_' + BookmeProL10n.current_tab + '"]').tab('show');

    $('#bookme-pro-js-logo .bookme-pro-pretty-indicator').on('click', function(){
        var frame = wp.media({
            library: {type: 'image'},
            multiple: false
        });
        frame.on('select', function () {
            var selection = frame.state().get('selection').toJSON(),
                img_src
                ;
            if (selection.length) {
                if (selection[0].sizes['thumbnail'] !== undefined) {
                    img_src = selection[0].sizes['thumbnail'].url;
                } else {
                    img_src = selection[0].url;
                }
                $('[name=bookme_pro_co_logo_attachment_id]').val(selection[0].id);
                $('#bookme-pro-js-logo .bookme-pro-js-image').css({'background-image': 'url(' + img_src + ')', 'background-size': 'cover'});
                $('#bookme-pro-js-logo .bookme-pro-thumb-delete').show();
                $(this).hide();
            }
        });

        frame.open();
    });

    $('#bookme-pro-js-logo .bookme-pro-thumb-delete').on('click', function () {
        var $thumb = $(this).parents('.bookme-pro-js-image');
        $thumb.attr('style', '');
        $('[name=bookme_pro_co_logo_attachment_id]').val('');
    });
});