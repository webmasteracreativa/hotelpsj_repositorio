jQuery(function($) {
    var $custom_notifications = $('#bookme-pro-js-custom-notifications');

    Ladda.bind( 'button[type=submit]' );

    // menu fix for WP 3.8.1
    $('#toplevel_page_ab-system > ul').css('margin-left', '0px');

    /* exclude checkboxes in form */
    var $checkboxes = $('.bookme-pro-js-collapse .panel-title > input:checkbox');

    $checkboxes.change(function () {
        $(this).parents('.panel-heading').next().collapse(this.checked ? 'show' : 'hide');
    });

    $('[data-toggle="popover"]').popover({
        html: true,
        placement: 'top',
        trigger: 'hover',
        template: '<div class="popover bookme-pro-font-xs" style="width: 220px" role="tooltip"><div class="popover-arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    });

    $custom_notifications
        .on('change', "select[name$='[type]']", function () {
            var $panel    = $(this).closest('.panel'),
                $settings = $panel.find('.bookme-pro-js-settings'),
                set       = $(this).find(':selected').data('set'),
                value     = $(this).find(':selected').val();

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

    $('button[type=reset]').on('click', function () {
        setTimeout(function () {
            $("select[name$='[type]']", $custom_notifications).trigger('change');
        }, 0);
    });

    bookmeProAlert(BookmeProL10n.alert);
});