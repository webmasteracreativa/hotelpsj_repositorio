jQuery(function ($) {
    var Details = function($container, options) {
        var obj  = this;
        jQuery.extend(obj.options, options);
        $container.parents('.slidePanel-inner').css('margin-bottom','82px');

        if (Object.keys(obj.options.get_details).length === 0) {
            // backend united edit & details in one request.
            initDetails($container);
        } else {
            // get details content.
            $container.html('<div class="bookme-pro-loading"></div>');
            $.ajax({
                url         : ajaxurl,
                data        : obj.options.get_details,
                dataType    : 'json',
                xhrFields   : { withCredentials: true },
                crossDomain : 'withCredentials' in new XMLHttpRequest(),
                success     : function (response) {
                    $container.html(response.data.html);
                    initDetails($container);
                }
            });
        }

        function initDetails($container) {
            var $staff_full_name = $('#bookme-pro-full-name', $container),
                $staff_wp_user = $('#bookme-pro-wp-user', $container),
                $staff_email   = $('#bookme-pro-email', $container),
                $staff_phone   = $('#bookme-pro-phone', $container),
                $location_row  = $('.locations-row', $container)
            ;

            if (obj.options.intlTelInput.enabled) {
                $staff_phone.intlTelInput({
                    preferredCountries: [obj.options.intlTelInput.country],
                    initialCountry: obj.options.intlTelInput.country,
                    geoIpLookup: function (callback) {
                        $.get('https://ipinfo.io', function() {}, 'jsonp').always(function(resp) {
                            var countryCode = (resp && resp.country) ? resp.country : '';
                            callback(countryCode);
                        });
                    },
                    utilsScript: obj.options.intlTelInput.utils
                });
            }

            $staff_wp_user.on('change', function () {
                if (this.value) {
                    $staff_full_name.val($staff_wp_user.find(':selected').text());
                    $staff_email.val($staff_wp_user.find(':selected').data('email'));
                }
            });

            $('input.bookme-pro-js-all-locations, input.bookme-pro-location', $container).on('change', function () {
                if ($(this).hasClass('bookme-pro-js-all-locations')) {
                    $location_row.find('.bookme-pro-location').prop('checked', $(this).prop('checked'));
                } else {
                    $location_row.find('.bookme-pro-js-all-locations').prop('checked', $location_row.find('.bookme-pro-location:not(:checked)').length == 0);
                }
                updateLocationsButton();
            });

            $('button:reset', $container).on('click', function () {
                setTimeout(updateLocationsButton, 0);
            });

            function updateLocationsButton() {
                var locations_checked = $location_row.find('.bookme-pro-location:checked').length;
                if (locations_checked == 0) {
                    $location_row.find('.bookme-pro-locations-count').text(obj.options.l10n.selector.nothing_selected);
                } else if (locations_checked == 1) {
                    $location_row.find('.bookme-pro-locations-count').text($location_row.find('.bookme-pro-location:checked').data('location_name'));
                } else {
                    if (locations_checked == $location_row.find('.bookme-pro-location').length) {
                        $location_row.find('.bookme-pro-locations-count').text(obj.options.l10n.selector.all_selected);
                    } else {
                        $location_row.find('.bookme-pro-locations-count').text(locations_checked + '/' + $location_row.find('.bookme-pro-location').length);
                    }
                }
            }

            updateLocationsButton();

            function updateStaffTable(){
                $.get(ajaxurl, {
                    action: 'bookme_pro_get_staff',
                    csrf_token: obj.options.csrf_token
                }, function (response) {
                    if (response.success) {
                        $('#bookme-pro-all-staff-details').html(response.data.html);
                    }
                });
            }

            // Save staff member details.
            $('#bookme-pro-details-save', $container).on('click',function(e){
                e.preventDefault();
                var $form = $(this).closest('form'),
                    data  = $form.serializeArray(),
                    ladda = Ladda.create(this),
                    $staff_phone = $('#bookme-pro-phone',$form),
                    phone;
                try {
                    phone = BookmeProL10n.intlTelInput.enabled ? $staff_phone.intlTelInput('getNumber') : $staff_phone.val();
                    if (phone == '') {
                        phone = $staff_phone.val();
                    }
                } catch (error) {  // In case when intlTelInput can't return phone number.
                    phone = $staff_phone.val();
                }
                data.push({name: 'action', value: 'bookme_pro_update_staff'});
                data.push({name: 'phone',  value: phone});
                ladda.start();

                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: data,
                    dataType: 'json',
                    xhrFields: {withCredentials: true},
                    crossDomain: 'withCredentials' in new XMLHttpRequest(),
                    success: function (response) {
                        if (response.success) {
                            updateStaffTable();
                            obj.options.bookmeProAlert({success: [obj.options.l10n.saved]});
                            $('[bookme-pro-js-staff-name-' + obj.options.get_details.id + ']').text($('#bookme-pro-full-name', $form).val());
                            if (typeof obj.options.renderWpUsers === 'function') {
                                obj.options.renderWpUsers(response.data.wp_users);
                            }
                        } else {
                            obj.options.bookmeProAlert({error: [response.data.error]});
                        }
                        ladda.stop();
                    }
                });
            });
        }

    };

    Details.prototype.options = {
        intlTelInput: {},
        get_details : {
            action: 'bookme_pro_get_staff_details',
            id    : -1,
            csrf_token: ''
        },
        csrf_token:'',
        l10n        : {},
        bookmeProAlert : window.bookmeProAlert
    };

    window.BookmeProStaffDetails = Details;
});