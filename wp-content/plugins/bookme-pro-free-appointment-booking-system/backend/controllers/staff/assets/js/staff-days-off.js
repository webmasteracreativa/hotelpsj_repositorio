jQuery(function ($) {
    var DaysOff = function($container, options) {
        var obj  = this;
        jQuery.extend(obj.options, options);
        $container.parents('.slidePanel-inner').css('margin-bottom','0');

        if (!$container.children().length) {
            $container.html('<div class="bookme-pro-loading"></div>');
            $.ajax({
                url         : ajaxurl,
                data        : {action: 'bookme_pro_staff_holidays', id: obj.options.staff_id, csrf_token : obj.options.csrf_token},
                xhrFields   : { withCredentials: true },
                crossDomain : 'withCredentials' in new XMLHttpRequest(),
                success     : function (response) {
                    $container.html(response.data.html);
                    var d = new Date();
                    $('.bookme-pro-js-holidays').jCal({
                        day: new Date(d.getFullYear(), 0, 1),
                        days: 1,
                        showMonths: 12,
                        scrollSpeed: 350,
                        events: response.data.holidays,
                        action: 'bookme_pro_staff_holidays_update',
                        csrf_token: obj.options.csrf_token,
                        staff_id: obj.options.staff_id,
                        dayOffset: response.data.start_of_week,
                        loadingImg: response.data.loading_img,
                        dow: response.data.days,
                        ml: response.data.months,
                        we_are_not_working: response.data.we_are_not_working,
                        repeat: response.data.repeat,
                        close: response.data.close
                    });

                    $('.bookme-pro-js-jCalBtn', $container).on('click', function (e) {
                        e.preventDefault();
                        var trigger = $(this).data('trigger');
                        $('.bookme-pro-js-holidays',$container).find($(trigger)).trigger('click');
                    })
                }
            });
        }
    };

    DaysOff.prototype.options = {
        staff_id  : -1,
        csrf_token: '',
        l10n: {}
    };

    window.BookmeProStaffDaysOff = DaysOff;
});