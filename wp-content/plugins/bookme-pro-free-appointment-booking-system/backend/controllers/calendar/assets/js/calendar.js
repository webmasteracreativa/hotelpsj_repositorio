jQuery(function ($) {
    var $fullCalendar = $('#bookme-pro-fc-wrapper .bookme-pro-js-calendar-element'),
        $tabs = $('.bookme-pro-js-calendar-tab'),
        $staff = $('#bookme-pro-js-staff-selector'),
        firstHour = new Date().getHours(),
        staffMembers = [],
        staffIds = getCookie('bookme_pro_cal_st_ids'),
        tabId = getCookie('bookme_pro_cal_tab_id'),
        lastView = getCookie('bookme_pro_cal_view'),
        views = 'month agendaWeek agendaDay multiStaffDay';

    if (views.indexOf(lastView) == -1) {
        lastView = 'multiStaffDay';
    }
    // Init tabs and staff member filters.
    if (staffIds === null) {
        $staff.find('option').each(function (index, value) {
            this.selected = true;
            $tabs.filter('[data-staff_id=' + this.value + ']').show();
        });
    } else if (staffIds != '') {
        $.each(staffIds.split(','), function (index, value) {
            $staff.find('option').filter('[value=' + value + ']').prop('selected', true);
            $tabs.filter('[data-staff_id=' + value + ']').show();
        });
    }

    $tabs.filter('[data-staff_id=' + tabId + ']').addClass('active');
    if ($tabs.filter('li.active').length == 0) {
        $tabs.eq(0).addClass('active').show();
        $staff.filter('[value=' + $tabs.eq(0).data('staff_id') + ']').prop('checked', true);
    }

    $staff.multiselect({
        texts: {
            placeholder: $staff.data('placeholder'), // text to use in dummy input
            selectedOptions: ' ' + $staff.data('selected'),      // selected suffix text
            selectAll: $staff.data('selectall'),     // select all text
            unselectAll: $staff.data('unselectall'),   // unselect all text
            noneSelected: $staff.data('nothing'),   // None selected text
            allSelected: $staff.data('allselected')
        },
        showCheckbox: false,  // display the checkbox to the user
        selectAll: true, // add select all option
        minHeight: 20,
        maxPlaceholderOpts: 1
    });


    updateStaffButton();

    /**
     * Calculate height of FullCalendar.
     *
     * @return {number}
     */
    function heightFC() {
        var window_height = $(window).height(),
            wp_admin_bar_height = $('#wpadminbar').height(),
            bookme_pro_calendar_tabs_height = $('#bookme-pro-fc-wrapper .tabbable').outerHeight(true),
            height_to_reduce = wp_admin_bar_height + bookme_pro_calendar_tabs_height,
            $wrap = $('#wpbody-content .wrap');

        if ($wrap.css('margin-top')) {
            height_to_reduce += parseInt($wrap.css('margin-top').replace('px', ''), 10);
        }

        if ($wrap.css('margin-bottom')) {
            height_to_reduce += parseInt($wrap.css('margin-bottom').replace('px', ''), 10);
        }

        var res = window_height - height_to_reduce - 130;

        return res > 620 ? res : 620;
    }

    var options = {
        fullcalendar: {
            // General Display.
            header: {
                left: views,
                center: 'title',
                right: 'prev today next'
            },
            height: heightFC(),
            // Views.
            defaultView: lastView,
            scrollTime: firstHour + ':00:00',
            views: {
                agendaWeek: {
                    columnFormat: 'ddd, D'
                },
                multiStaffDay: {
                    staffMembers: staffMembers
                }
            },
            viewRender: function (view, element) {
                setCookie('bookme_pro_cal_view', view.type);
            }
        },
        getCurrentStaffId: function () {
            return $tabs.filter('.active').data('staff_id');
        },
        getStaffMembers: function () {
            return staffMembers;
        },
        l10n: BookmeProL10n
    };

    var calendar = new BookmeProCalendar($fullCalendar, options);

    $('.fc-agendaDay-button').addClass('fc-corner-right');
    if ($tabs.filter('.active').data('staff_id') == 0) {
        $('.fc-agendaDay-button').hide();
    } else {
        $('.fc-multiStaffDay-button').hide();
    }

    $(window).on('resize', function () {
        $fullCalendar.fullCalendar('option', 'height', heightFC());
    });

    // Click on tabs.
    $tabs.on('click', function (e) {
        e.preventDefault();
        $tabs.removeClass('active');
        $(this).addClass('active');
        var staff_id = $(this).data('staff_id');
        setCookie('bookme_pro_cal_tab_id', staff_id);

        if (staff_id == 0) {
            $('.fc-agendaDay-button').hide();
            $('.fc-multiStaffDay-button').show();
            $fullCalendar.fullCalendar('refetchEvents');
        } else {
            $('.fc-multiStaffDay-button').hide();
            $('.fc-agendaDay-button').show();
            var view = $fullCalendar.fullCalendar('getView');
            if (view.type == 'multiStaffDay') {
                $fullCalendar.fullCalendar('changeView', 'agendaDay');
            }
            $fullCalendar.fullCalendar('refetchEvents');
        }
    });

    $('.dropdown-menu').on('click', function (e) {
        e.stopPropagation();
    });

    /**
     * On staff checkbox click.
     */
    $staff.on('change', function (e) {
        updateStaffButton();

        $staff.find('option').each(function (index, value) {
            $tabs.filter('[data-staff_id=' + this.value + ']').toggle(this.selected);
        });

        if ($tabs.filter(':visible.active').length == 0) {
            $tabs.filter(':visible:first').triggerHandler('click');
        } else if ($tabs.filter('.active').data('staff_id') == 0) {
            var view = $fullCalendar.fullCalendar('getView');
            if (view.type == 'multiStaffDay') {
                view.displayView($fullCalendar.fullCalendar('getDate'));
            }
            $fullCalendar.fullCalendar('refetchEvents');
        }
    });

    function updateStaffButton() {

        // Update staffMembers array.
        var ids = [];
        staffMembers.length = 0;
        $staff.find('option:selected').each(function () {
            staffMembers.push({id: this.value, name: this.getAttribute('data-staff_name')});
            ids.push(this.value);
        });
        setCookie('bookme_pro_cal_st_ids', ids);
    }

    /**
     * Set cookie.
     *
     * @param key
     * @param value
     */
    function setCookie(key, value) {
        var expires = new Date();
        expires.setTime(expires.getTime() + 86400000); // 60 × 60 × 24 × 1000
        document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
    }

    /**
     * Get cookie.
     *
     * @param key
     * @return {*}
     */
    function getCookie(key) {
        var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
        return keyValue ? keyValue[2] : null;
    }

});