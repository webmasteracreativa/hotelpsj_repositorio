jQuery(function ($) {
    var $staff_list = $('#bookme-pro-all-staff-details'),
        $no_staff = $('#bookme-pro-no-staff'),
        $new_form = $('#bookme-pro-new-staff'),
        $wp_user_select = $('#bookme-pro-new-staff-wpuser'),
        $name_input = $('#bookme-pro-new-staff-fullname'),
        $staff_count = $('#bookme-pro-staff-count');


    $staff_list.on('click', '.bookme-pro-js-handle', function (e) {
        e.stopPropagation();
    });

    $wp_user_select.on('change', function () {
        if (this.value) {
            $name_input.val($(this).find(':selected').text());
        }
    });


    $staff_list.sortable({
        helper: function (e, ui) {
            ui.children().each(function () {
                $(this).width($(this).width());
            });
            return ui;
        },
        axis: 'y',
        handle: '.bookme-pro-js-handle',
        update: function (event, ui) {
            var data = [];
            $staff_list.children('tr').each(function () {
                var $this = $(this);
                var position = $this.data('staff-id');
                data.push(position);
            });
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {action: 'bookme_pro_update_staff_position', position: data, csrf_token: BookmeProL10n.csrf_token}
            });
        }
    });

    $("#bookme-pro-tbs").on('click', '#bookme-pro-delete', function (e) {
        if (confirm(BookmeProL10n.are_you_sure)) {
            var ladda = rangeTools.ladda(this);

            var $for_delete = $('.staff-checker:checked'),
                data = {action: 'bookme_pro_delete_staff', csrf_token: BookmeProL10n.csrf_token},
                staff = [],
                $panels = [];

            $for_delete.each(function () {
                var panel = $(this).parents('tr');
                $panels.push(panel);
                staff.push(this.value);
            });
            data['ids[]'] = staff;
            $.post(ajaxurl, data, function (response) {
                if (response.success) {
                    ladda.stop();
                    $.each($panels.reverse(), function (index) {
                        $(this).delay(500 * index).fadeOut(200, function () {
                            $(this).remove();
                            $staff_count.text($staff_list.children().length);
                            if($staff_list.children().length == 0){
                                $no_staff.show();
                                $staff_list.hide();
                            }
                        });
                    });
                }
            });
        }
    });

    window.newEmployee = function ($edit_form) {
        $edit_form.bookmeProHelp();

        var $staff_full_name = $('#bookme-pro-full-name', $edit_form),
            $staff_wp_user = $('#bookme-pro-wp-user', $edit_form),
            $staff_email = $('#bookme-pro-email', $edit_form),
            $staff_phone = $('#bookme-pro-phone', $edit_form)
            ;

        if (BookmeProL10n.intlTelInput.enabled) {
            $staff_phone.intlTelInput({
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

        $staff_wp_user.on('change', function () {
            if (this.value) {
                $staff_full_name.val($staff_wp_user.find(':selected').text());
                $staff_email.val($staff_wp_user.find(':selected').data('email'));
            }
        });

        // Save staff member details.
        $('#bookme-pro-details-save', $edit_form).on('click', function (e) {
            e.preventDefault();
            var $form = $edit_form.find('form'),
                data = $form.serializeArray(),
                ladda = Ladda.create(this),
                $staff_phone = $('#bookme-pro-phone', $form),
                phone;
            try {
                phone = BookmeProL10n.intlTelInput.enabled ? $staff_phone.intlTelInput('getNumber') : $staff_phone.val();
                if (phone == '') {
                    phone = $staff_phone.val();
                }
            } catch (error) {  // In case when intlTelInput can't return phone number.
                phone = $staff_phone.val();
            }
            data.push({name: 'action', value: 'bookme_pro_create_staff'});
            data.push({name: 'phone', value: phone});
            if (validateForm($form)) {
                ladda.start();
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: data,
                    dataType: 'json',
                    xhrFields: {withCredentials: true},
                    crossDomain: 'withCredentials' in new XMLHttpRequest(),
                    success: function (response) {
                        ladda.stop();
                        if (response.success) {
                            $no_staff.hide();
                            $staff_list.show();
                            $staff_list.append(response.data.html);
                            bookmeProAlert({success: [BookmeProL10n.saved]});
                            if ($form.hasClass('bookme-pro-new-staff')) {
                                $staff_count.text($staff_list.children().length);
                            }
                            $.slidePanel.hide();
                        } else {
                            bookmeProAlert({error: [response.data.error]});
                        }
                    }
                });
            }
        });
    };

    window.bookmeProSidePanelContentFilter = function ($content) {
        if ($content.success) {
            if ($content.data.html.sidepanel) {
                window.bookmeProStaffDetailsData = $content.data.html.details;
                return $content.data.html.sidepanel;
            }
        }
    };

    window.bookmeProSidePanelLoaded = function ($edit_form) {
        var staff_id = $('input[name=staff_id]', $edit_form).val(),
            $details_container = $('#bookme-pro-details-container', $edit_form),
            $loading_indicator = $('.bookme-pro-loading', $edit_form),
            $services_container = $('#bookme-pro-services-container', $edit_form),
            $schedule_container = $('#bookme-pro-schedule-container', $edit_form),
            $holidays_container = $('#bookme-pro-holidays-container', $edit_form)
            ;

        $details_container.html(bookmeProStaffDetailsData).bookmeProHelp();

        $('.bookme-pro-pretty-indicator', $edit_form).on('click', function (e) {
            e.stopPropagation();
            e.preventDefault();
            var frame = wp.media({
                library: {type: 'image'},
                multiple: false
            });
            frame.on('select', function () {
                var selection = frame.state().get('selection').toJSON(),
                    img_src;
                if (selection.length) {
                    if (selection[0].sizes['thumbnail'] !== undefined) {
                        img_src = selection[0].sizes['thumbnail'].url;
                    } else {
                        img_src = selection[0].url;
                    }
                    $edit_form.find('[name=attachment_id]').val(selection[0].id);
                    $('#bookme-pro-js-staff-avatar').find('.bookme-pro-js-image').css({
                        'background-image': 'url(' + img_src + ')',
                        'background-size': 'cover'
                    });
                    $('.bookme-pro-thumb-delete').show();
                    $(this).hide();
                }
            });

            frame.open();
        });

        new BookmeProStaffDetails($details_container, {
            get_details: {},
            intlTelInput: BookmeProL10n.intlTelInput,
            l10n: BookmeProL10n,
            csrf_token: BookmeProL10n.csrf_token,
            renderWpUsers: function (wp_users) {
                $wp_user_select.children(':not(:first)').remove();
                $.each(wp_users, function (index, wp_user) {
                    var $option = $('<option>')
                        .data('email', wp_user.user_email)
                        .val(wp_user.ID)
                        .text(wp_user.display_name);
                    $wp_user_select.append($option);
                });
            }
        });

        // Delete staff member.
        $('#bookme-pro-staff-delete', $edit_form).on('click', function (e) {
            e.preventDefault();
            if (confirm(BookmeProL10n.are_you_sure)) {
                $.post(ajaxurl, {
                    action: 'bookme_pro_delete_staff',
                    'ids[]': staff_id,
                    csrf_token: BookmeProL10n.csrf_token
                }, function (response) {
                    $.slidePanel.hide();
                    $wp_user_select.children(':not(:first)').remove();
                    $.each(response.data.wp_users, function (index, wp_user) {
                        var $option = $('<option>')
                            .data('email', wp_user.user_email)
                            .val(wp_user.ID)
                            .text(wp_user.display_name);
                        $wp_user_select.append($option);
                    });
                    $('#bookme-pro-staff-' + staff_id).remove();
                    $staff_count.text($staff_list.children().length);
                });
            }
        });

        // Delete staff avatar
        $('.bookme-pro-thumb-delete', $edit_form).on('click', function () {
            var $thumb = $(this).parents('.bookme-pro-js-image');
            $.post(ajaxurl, {
                action: 'bookme_pro_delete_staff_avatar',
                id: staff_id,
                csrf_token: BookmeProL10n.csrf_token
            }, function (response) {
                if (response.success) {
                    $thumb.attr('style', '');
                    $edit_form.find('[name=attachment_id]').val('');
                }
            });
        });

        // Open details tab
        $('#bookme-pro-details-tab', $edit_form).on('click', function () {
            $('.tab-pane > div').hide();
            $('.slidePanel-inner', $edit_form).css('margin-bottom', '82px');
            $details_container.show();
        });

        // Open services tab
        $('#bookme-pro-services-tab', $edit_form).on('click', function () {
            $('.tab-pane > div').hide();

            new BookmeProStaffServices($services_container, {
                get_staff_services: {
                    action: 'bookme_pro_get_staff_services',
                    staff_id: staff_id,
                    csrf_token: BookmeProL10n.csrf_token
                },
                l10n: BookmeProL10n
            });

            $services_container.show();
        });

        // Open special days tab
        $('#bookme-pro-special-days-tab', $edit_form).on('click', function () {
            new BookmeProStaffSpecialDays($('.bookme-pro-js-special-days-container'), {
                staff_id: staff_id,
                csrf_token: BookmeProL10n.csrf_token,
                l10n: SpecialDaysL10n
            });
        });

        // Open schedule tab
        $('#bookme-pro-schedule-tab', $edit_form).on('click', function () {
            $('.tab-pane > div').hide();

            new BookmeProStaffSchedule($schedule_container, {
                get_staff_schedule: {
                    action: 'bookme_pro_get_staff_schedule',
                    staff_id: staff_id,
                    csrf_token: BookmeProL10n.csrf_token
                },
                l10n: BookmeProL10n
            });

            $schedule_container.show();
        });

        // Open holiday tab
        $('#bookme-pro-holidays-tab').on('click', function () {
            $('.tab-pane > div').hide();

            new BookmeProStaffDaysOff($holidays_container, {
                staff_id: staff_id,
                csrf_token: BookmeProL10n.csrf_token,
                l10n: BookmeProL10n
            });

            $holidays_container.show();
        });
    };
});