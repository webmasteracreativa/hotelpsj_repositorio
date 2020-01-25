jQuery(function ($) {
    var $custom_notifications = $('#bookme-pro-js-custom-notifications');

    bookmeProAlert(BookmeProL10n.alert);

    $('.bookme-pro-admin-notify').on('change', function () {
        var $checkbox = $(this);
        $checkbox.hide().prev('img').show();
        $.get(ajaxurl, {
            action: 'bookme_pro_admin_notify',
            csrf_token: BookmeProL10n.csrf_token,
            option_name: $checkbox.attr('name'),
            value: $checkbox.is(':checked') ? 1 : 0
        }, function () {
        }, 'json').always(function () {
            $checkbox.show().prev('img').hide();
        });
    });


    $custom_notifications
        .on('change', "select[name$='[type]']", function () {
            var $panel = $(this).closest('.panel'),
                $settings = $panel.find('.bookme-pro-js-settings'),
                set = $(this).find(':selected').data('set'),
                value = $(this).find(':selected').val();

            $panel.find("[name$='[to_staff]']").closest('.check-inline').toggle(value != 'customer_birthday');
            $panel.find("[name$='[to_admin]']").closest('.check-inline').toggle(value != 'customer_birthday');

            $settings.each(function () {
                $(this).toggle($(this).hasClass('bookme-pro-js-' + set));
            });
        })
        .on('click', '.bookme-pro-js-delete', function () {
            if (confirm(BookmeProL10n.are_you_sure)) {
                var $button = $(this),
                    id = $button.data('notification_id');
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'bookme_pro_delete_custom_notification',
                        id: id,
                        csrf_token: BookmeProL10n.csrf_token
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            $button.closest('.panel').remove();
                        }
                    }
                });
            }
        })
        .find("select[name$='[type]']").trigger('change');

    $('#notifications button[type=reset]').on('click', function () {
        setTimeout(function () {
            $("select[name$='[type]']", $custom_notifications).trigger('change');
        }, 0);
    });

    var $admin_phone = $('#bookme_pro_sms_admin_phone'),
        $twilio_phone = $('#bookme_pro_twillio_phone_number'),
        $test_phone = $('#bookme_pro_test_sms_phone');
    if (BookmeProL10n.intlTelInput.enabled) {
        $('#bookme_pro_sms_admin_phone, #bookme_pro_twillio_phone_number, #bookme_pro_test_sms_phone').intlTelInput({
            preferredCountries: [BookmeProL10n.intlTelInput.country],
            initialCountry: BookmeProL10n.intlTelInput.country,
            geoIpLookup: function (callback) {
                $.get('https://ipinfo.io', function () {
                }, 'jsonp').always(function (resp) {
                    var countryCode = (resp && resp.country) ? resp.country : '';
                    callback(countryCode);
                });
            },
            utilsScript: BookmeProL10n.intlTelInput.utils
        });
    }
    $('#bookme-pro-js-submit-notifications').on('click', function (e) {
        e.preventDefault();
        var ladda = Ladda.create(this);
        ladda.start();
        var $form = $(this).parents('form'),
            data = $form.serializeArray(),
            bookme_pro_sms_admin_phone, bookme_pro_twillio_phone_number;

        try {
            bookme_pro_sms_admin_phone = BookmeProL10n.intlTelInput.enabled ? $admin_phone.intlTelInput('getNumber') : $admin_phone.val();
            bookme_pro_twillio_phone_number = BookmeProL10n.intlTelInput.enabled ? $twilio_phone.intlTelInput('getNumber') : $twilio_phone.val();
            if (bookme_pro_sms_admin_phone == '') {
                bookme_pro_sms_admin_phone = $admin_phone.val();
            }
            if (bookme_pro_twillio_phone_number == '') {
                bookme_pro_twillio_phone_number = $twilio_phone.val();
            }
        } catch (error) {  // In case when intlTelInput can't return phone number.
            bookme_pro_sms_admin_phone = $admin_phone.val();
            bookme_pro_twillio_phone_number = $twilio_phone.val();
        }
        data.push({name: 'action', value: 'bookme_pro_save_sms'});
        data.push({name: 'csrf_token', value: BookmeProL10n.csrf_token});
        data.push({name: 'bookme_pro_sms_admin_phone', value: bookme_pro_sms_admin_phone});
        data.push({name: 'bookme_pro_twillio_phone_number', value: bookme_pro_twillio_phone_number});

        $.ajax({
            url: ajaxurl,
            method: 'post',
            data: data,
            dataType: 'json',
            xhrFields: {withCredentials: true},
            crossDomain: 'withCredentials' in new XMLHttpRequest(),
            success: function (response) {
                if (response.success) {
                    bookmeProAlert({success: [response.data.message]});
                }
                Ladda.stopAll();
            }
        });
    });

    $('#send_test_sms').on('click', function (e) {
        e.preventDefault();
        var ladda = Ladda.create(this);
        ladda.start();
        $.ajax({
            url: ajaxurl,
            data: {
                action: 'bookme_pro_send_test_sms',
                csrf_token: BookmeProL10n.csrf_token,
                phone_number: BookmeProL10n.intlTelInput.enabled ? $test_phone.intlTelInput('getNumber') : $test_phone.val()
            },
            dataType: 'json',
            xhrFields: {withCredentials: true},
            crossDomain: 'withCredentials' in new XMLHttpRequest(),
            success: function (response) {
                Ladda.stopAll();
                if (response.success) {
                    bookmeProAlert({success: [response.message]});
                } else {
                    bookmeProAlert({error: [response.message]});
                }
            }
        });
    });
});