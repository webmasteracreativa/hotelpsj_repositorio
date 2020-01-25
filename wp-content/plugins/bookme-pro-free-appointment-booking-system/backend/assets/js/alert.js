function bookmeProAlert(alert) {

    var types = {
        success: 'alert-success',
        error: 'alert-danger'
    };

    // Check if there are messages in alert.
    var not_empty = false;
    for (var type in alert) {
        if (types.hasOwnProperty(type) && alert[type].length) {
            not_empty = true;
            break;
        }
    }

    if (not_empty) {
        var $container = jQuery('#bookme-pro-alert');
        if ($container.length == 0) {
            $container = jQuery('<div id="bookme-pro-alert" class="bookme-pro-alert"></div>').appendTo('#bookme-pro-tbs');
        }
        for (var type in alert) {
            var class_name;
            if (types.hasOwnProperty(type)) {
                class_name = types[type];
            } else {
                continue;
            }
            alert[type].forEach(function (message) {
                var $alert = jQuery('<div class="alert"><i class="alert-icon"></i><button type="button" class="close">&times;</button></div>');
                $alert
                    .addClass(class_name)
                    .append('<b class="bookme-pro-margin-left-sm bookme-pro-vertical-middle">' + message + '</b>')
                    .appendTo($container).fadeIn().css('transform', 'translate3d(0%, 0px, 0px)');

                if (type == 'success') {
                    setTimeout(function () {
                        $alert.css('transform', jQuery('html').attr('dir') == 'rtl' ? 'translate3d(-100%, 0px, 0px)' : 'translate3d(100%, 0px, 0px)').fadeOut();
                    }, 5000);
                }
                $alert.find('.close').on('click', function (e) {
                    e.preventDefault();
                    $alert.css('transform', jQuery('html').attr('dir') == 'rtl' ? 'translate3d(-100%, 0px, 0px)' : 'translate3d(100%, 0px, 0px)').fadeOut();
                });
            });
        }
    }
}