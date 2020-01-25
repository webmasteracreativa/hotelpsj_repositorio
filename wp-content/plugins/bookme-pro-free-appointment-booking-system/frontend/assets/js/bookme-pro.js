(function ($) {
    window.bookmePro = function (Options) {
        var $container = $('#bookme-pro-form-' + Options.form_id),
            timeZone = typeof Intl === 'object' ? Intl.DateTimeFormat().resolvedOptions().timeZone : undefined,
            timeZoneOffset = new Date().getTimezoneOffset();
        Options.skip_steps.service = Options.skip_steps.service_part1 && Options.skip_steps.service_part2;

        // initialize
        if (Options.status.booking == 'finished') {
            stepComplete();
        } else if (Options.status.booking == 'cancelled') {
            stepDetails();
        } else {
            stepService({new_chain: true});
        }


        /**
         * Service step.
         */
        function stepService(params) {
            if (Options.skip_steps.service) {
                stepTime(params);
                return;
            }
            var data = $.extend({
                action: 'bookme_pro_render_service',
                csrf_token: BookmeProL10n.csrf_token,
                form_id: Options.form_id,
                time_zone: timeZone,
                time_zone_offset: timeZoneOffset
            }, params);
            $.ajax({
                url: Options.ajaxurl,
                data: data,
                dataType: 'json',
                xhrFields: {withCredentials: true},
                crossDomain: 'withCredentials' in new XMLHttpRequest(),
                success: function (response) {
                    if (response.success) {

                        BookmeProL10n.csrf_token = response.csrf_token;
                        $container.html(response.html);
                        if (params === undefined) { // Scroll when returning to the step Service. default value {new_chain : true}
                            scrollTo($container);
                        }

                        var $chain_item_draft = $('.bookme-pro-js-chain-item.bookme-pro-js-draft', $container),
                            $select_location = $('.bookme-pro-js-select-location', $container),
                            $select_category = $('.bookme-pro-js-select-category', $container),
                            $select_service = $('.bookme-pro-js-select-service', $container),
                            $select_employee = $('.bookme-pro-js-select-employee', $container),
                            $select_nop = $('.bookme-pro-js-select-number-of-persons', $container),
                            $select_quantity = $('.bookme-pro-js-select-quantity', $container),
                            $date_from = $('.bookme-pro-js-date-from', $container),
                            $select_time_from = $('.bookme-pro-js-select-time-from', $container),
                            $select_time_to = $('.bookme-pro-js-select-time-to', $container),
                            $next_step = $('.bookme-pro-js-next-step', $container),
                            locations = response.locations,
                            categories = response.categories,
                            services = response.services,
                            staff = response.staff,
                            chain = response.chain,
                            last_chain_key = 0,
                            category_selected = false,
                            slots = null,
                            calendar_loaded = false
                            ;

                        // insert data into select
                        function setSelect($select, data, value) {
                            // reset select
                            $('option:not([value=""])', $select).remove();
                            // and fill the new data
                            var docFragment = document.createDocumentFragment();

                            function valuesToArray(obj) {
                                return Object.keys(obj).map(function (key) {
                                    return obj[key];
                                });
                            }

                            function compare(a, b) {
                                if (parseInt(a.pos) < parseInt(b.pos))
                                    return -1;
                                if (parseInt(a.pos) > parseInt(b.pos))
                                    return 1;
                                return 0;
                            }

                            // sort select by position
                            data = valuesToArray(data).sort(compare);

                            $.each(data, function (key, object) {
                                var option = document.createElement('option');
                                option.value = object.id;
                                option.text = object.name;
                                docFragment.appendChild(option);
                            });
                            $select.append(docFragment);
                            // set default value of select
                            $select.val(value);
                        }

                        function setSelects($chain_item, location_id, category_id, service_id, staff_id) {
                            var _staff = {}, _services = {}, _categories = {}, _nop = {};
                            $.each(staff, function (id, staff_member) {
                                if (!location_id || locations[location_id].staff.hasOwnProperty(id)) {
                                    if (!service_id) {
                                        if (!category_id) {
                                            _staff[id] = staff_member;
                                        } else {
                                            $.each(staff_member.services, function (s_id) {
                                                if (services[s_id].category_id == category_id) {
                                                    _staff[id] = staff_member;
                                                    return false;
                                                }
                                            });
                                        }
                                    } else if (staff_member.services.hasOwnProperty(service_id)) {
                                        if (staff_member.services[service_id].price != null) {
                                            _staff[id] = {
                                                id: id,
                                                name: staff_member.name + ' (' + staff_member.services[service_id].price + ')',
                                                pos: staff_member.pos
                                            };
                                        } else {
                                            _staff[id] = staff_member;
                                        }
                                    }
                                }
                            });
                            if (!location_id) {
                                _categories = categories;
                                $.each(services, function (id, service) {
                                    if (!category_id || service.category_id == category_id) {
                                        if (!staff_id || staff[staff_id].services.hasOwnProperty(id)) {
                                            _services[id] = service;
                                        }
                                    }
                                });
                            } else {
                                var category_ids = [],
                                    service_ids = [];
                                $.each(locations[location_id].staff, function (st_id) {
                                    $.each(staff[st_id].services, function (s_id) {
                                        category_ids.push(services[s_id].category_id);
                                        service_ids.push(s_id);
                                    });
                                });
                                $.each(categories, function (id, category) {
                                    if ($.inArray(parseInt(id), category_ids) > -1) {
                                        _categories[id] = category;
                                    }
                                });
                                $.each(services, function (id, service) {
                                    if ($.inArray(id, service_ids) > -1) {
                                        if (!category_id || service.category_id == category_id) {
                                            if (!staff_id || staff[staff_id].services.hasOwnProperty(id)) {
                                                _services[id] = service;
                                            }
                                        }
                                    }
                                });
                            }
                            var nop = $chain_item.find('.bookme-pro-js-select-number-of-persons').val();
                            var max_capacity = service_id
                                ? (staff_id
                                    ? staff[staff_id].services[service_id].max_capacity
                                    : services[service_id].max_capacity)
                                : 1;
                            var min_capacity = service_id
                                ? (staff_id
                                    ? staff[staff_id].services[service_id].min_capacity
                                    : services[service_id].min_capacity)
                                : 1;
                            for (var i = min_capacity; i <= max_capacity; ++i) {
                                _nop[i] = {id: i, name: i, pos: i};
                            }
                            if (nop > max_capacity) {
                                nop = max_capacity;
                            }
                            if (nop < min_capacity || !Options.attributes.show_number_of_persons) {
                                nop = min_capacity;
                            }
                            setSelect($chain_item.find('.bookme-pro-js-select-category'), _categories, category_id);
                            setSelect($chain_item.find('.bookme-pro-js-select-service'), _services, service_id);
                            setSelect($chain_item.find('.bookme-pro-js-select-employee'), _staff, staff_id);
                            setSelect($chain_item.find('.bookme-pro-js-select-number-of-persons'), _nop, nop);
                        }

                        function initTooltip() {
                            $('.bookme-pro-calendar', $container).find('.picker__day').each(function () {
                                if (!$(this).hasClass("picker__day--disabled") && !$(this).hasClass("picker__day--outfocus")) {
                                    var date = new Date($(this).data('pick')),
                                        month = date.getMonth() + 1,
                                        day = date.getDate();
                                    if (month < 10) month = '0' + month;
                                    if (day < 10) day = '0' + day;
                                    var caldate = date.getFullYear() + '-' + month + '-' + day;
                                    if (slots[caldate] != undefined) {
                                        $(this).attr("title", slots[caldate]);
                                        $(this).bookmeProTooltip();
                                        $(this).removeClass('bookme-pro-hide');
                                    } else {
                                        $(this).addClass('bookme-pro-hide');
                                    }
                                }
                            });
                        }

                        function getAvailability() {
                            if (Options.skip_steps.service_part2 || !Options.show_calendar_availability.enabled)
                                return;

                            var $calendar = $('.bookme-pro-calendar', $container),
                                $cal_holder = $calendar.find('.picker__holder');
                            // Prepare chain data.
                            // Prepare chain data.
                            var chain = {};
                            $('.bookme-pro-js-chain-item:not(.bookme-pro-js-draft)', $container).each(function () {
                                var $chain_item = $(this);
                                var staff_ids = [];
                                if (!$('.bookme-pro-js-select-service', $chain_item).val()) {
                                    return;
                                }
                                if ($('.bookme-pro-js-select-employee', $chain_item).val()) {
                                    staff_ids.push($('.bookme-pro-js-select-employee', $chain_item).val());
                                } else {
                                    $('.bookme-pro-js-select-employee', $chain_item).find('option').each(function () {
                                        if (this.value) {
                                            staff_ids.push(this.value);
                                        }
                                    });
                                }
                                chain[$chain_item.data('chain_key')] = {
                                    location_id: $('.bookme-pro-js-select-location', $chain_item).val(),
                                    service_id: $('.bookme-pro-js-select-service', $chain_item).val(),
                                    staff_ids: staff_ids,
                                    number_of_persons: $('.bookme-pro-js-select-number-of-persons', $chain_item).val(),
                                    quantity: $('.bookme-pro-js-select-quantity', $chain_item).val() ? $('.bookme-pro-js-select-quantity', $chain_item).val() : 1
                                };
                            });
                            if (!$.isEmptyObject(chain)) {
                                $calendar.css('opacity', 0.5);
                                $cal_holder.addClass('bookme-pro-loader');
                                $.ajax({
                                    type: 'POST',
                                    url: Options.ajaxurl,
                                    data: {
                                        action: 'bookme_pro_render_availability',
                                        csrf_token: BookmeProL10n.csrf_token,
                                        form_id: Options.form_id,
                                        chain: chain,
                                        date_from: $date_from.pickadate('picker').get('view', 'yyyy-mm-dd'),
                                        time_from: $select_time_from.val(),
                                        time_to: $select_time_to.val()
                                    },
                                    dataType: 'json',
                                    xhrFields: {withCredentials: true},
                                    crossDomain: 'withCredentials' in new XMLHttpRequest(),
                                    success: function (response) {
                                        if (response.success) {
                                            BookmeProL10n.csrf_token = response.csrf_token;
                                            slots = response.slots;
                                            initTooltip();
                                        }
                                        $calendar.css('opacity', 1);
                                        $cal_holder.removeClass('bookme-pro-loader');
                                    }
                                });
                            }

                        }

                        // Function to adjust calendar sizing
                        function adjust_calendar() {
                            jQuery('.bookme-pro-calendar .picker__table').each(function () {
                                var boxesWidth = jQuery(this).find('tbody tr td').width();
                                var boxesHeight = boxesWidth * 1;
                                if (Options.auto_calendar_size.enabled) {
                                    jQuery(this).find('tbody tr td').height(boxesHeight);
                                    jQuery(this).find('tbody tr td div').css('line-height', boxesHeight + 'px');
                                }
                                if (boxesWidth < 50) {
                                    jQuery(this).find('tbody tr td div span').css({
                                        width: boxesWidth + 'px',
                                        height: boxesHeight + 'px',
                                        'line-height': boxesHeight - 1 + 'px'
                                    });
                                } else if (boxesWidth < 65) {
                                    jQuery(this).find('tbody tr td div span').css({
                                        width: '40px',
                                        height: '40px',
                                        'line-height': '39px'
                                    });
                                } else {
                                    jQuery(this).find('tbody tr td div span').css({
                                        width: '50px',
                                        height: '50px',
                                        'line-height': '49px'
                                    });
                                }

                            });
                        }

                        // Init Pickadate.
                        $date_from.pickadate({
                            formatSubmit: 'yyyy-mm-dd',
                            format: Options.date_format,
                            min: response.date_min || true,
                            max: response.date_max || true,
                            clear: false,
                            close: false,
                            today: false,
                            monthsFull: BookmeProL10n.months,
                            weekdaysFull: BookmeProL10n.days,
                            weekdaysShort: BookmeProL10n.daysShort,
                            labelMonthNext: BookmeProL10n.nextMonth,
                            labelMonthPrev: BookmeProL10n.prevMonth,
                            firstDay: Options.start_of_week,
                            klass: {
                                picker: 'picker picker--opened picker--focused bookme-pro-calendar'
                            },
                            onSet: function (timestamp) {
                                if (slots) {
                                    if (!$.isNumeric(timestamp.select)) {
                                        getAvailability();
                                    } else {
                                        initTooltip();
                                    }
                                }
                                this.open();
                            },
                            onClose: function () {
                                this.open(false);
                            },
                            onRender: function () {
                                $('.bookme-pro-calendar', $container).find('.picker__day').each(function () {
                                    var $text = $(this).text(),
                                        $span = $('<span class="bookme-pro-calendar-date" />');
                                    $span.html($text);
                                    $(this).html($span);
                                });
                                if (!calendar_loaded) {
                                    setTimeout(function () {
                                        // for adjust perfectly
                                        adjust_calendar();
                                    }, 10);
                                    calendar_loaded = true;
                                } else {
                                    adjust_calendar();
                                }

                            }
                        });

                        $('.bookme-pro-js-go-to-cart', $container).on('click', function (e) {
                            e.preventDefault();
                            ladda_start(this);
                            stepCart({from_step: 'service'});
                        });

                        $(window).on('resize', function () {
                            adjust_calendar();
                        });

                        $container.off('click').off('change');

                        // Location select change
                        $container.on('change', '.bookme-pro-js-select-location', function () {
                            var $chain_item = $(this).closest('.bookme-pro-js-chain-item'),
                                location_id = this.value,
                                category_id = $chain_item.find('.bookme-pro-js-select-category').val(),
                                service_id = $chain_item.find('.bookme-pro-js-select-service').val(),
                                staff_id = $chain_item.find('.bookme-pro-js-select-employee').val()
                                ;

                            // Validate selected values.
                            if (location_id) {
                                if (staff_id && !locations[location_id].staff.hasOwnProperty(staff_id)) {
                                    staff_id = '';
                                }
                                if (service_id) {
                                    var valid = false;
                                    $.each(locations[location_id].staff, function (id) {
                                        if (staff[id].services.hasOwnProperty(service_id)) {
                                            valid = true;
                                            return false;
                                        }
                                    });
                                    if (!valid) {
                                        service_id = '';
                                    }
                                }
                                if (category_id) {
                                    var valid = false;
                                    $.each(locations[location_id].staff, function (id) {
                                        $.each(staff[id].services, function (s_id) {
                                            if (services[s_id].category_id == category_id) {
                                                valid = true;
                                                return false;
                                            }
                                        });
                                        if (valid) {
                                            return false;
                                        }
                                    });
                                    if (!valid) {
                                        category_id = '';
                                    }
                                }
                            }
                            setSelects($chain_item, location_id, category_id, service_id, staff_id);
                        });

                        // Category select change
                        $container.on('change', '.bookme-pro-js-select-category', function () {
                            var $chain_item = $(this).closest('.bookme-pro-js-chain-item'),
                                location_id = $chain_item.find('.bookme-pro-js-select-location').val(),
                                category_id = this.value,
                                service_id = $chain_item.find('.bookme-pro-js-select-service').val(),
                                staff_id = $chain_item.find('.bookme-pro-js-select-employee').val()
                                ;

                            // Validate selected values.
                            if (category_id) {
                                category_selected = true;
                                if (service_id) {
                                    if (services[service_id].category_id != category_id) {
                                        service_id = '';
                                    }
                                }
                                if (staff_id) {
                                    var valid = false;
                                    $.each(staff[staff_id].services, function (id) {
                                        if (services[id].category_id == category_id) {
                                            valid = true;
                                            return false;
                                        }
                                    });
                                    if (!valid) {
                                        staff_id = '';
                                    }
                                }
                            } else {
                                category_selected = false;
                            }
                            setSelects($chain_item, location_id, category_id, service_id, staff_id);
                        });

                        // Service select change
                        $container.on('change', '.bookme-pro-js-select-service', function () {
                            var $chain_item = $(this).closest('.bookme-pro-js-chain-item'),
                                location_id = $chain_item.find('.bookme-pro-js-select-location').val(),
                                category_id = category_selected
                                    ? $chain_item.find('.bookme-pro-js-select-category').val()
                                    : '',
                                service_id = this.value,
                                staff_id = $chain_item.find('.bookme-pro-js-select-employee').val()
                                ;

                            // Validate selected values.
                            if (service_id) {
                                if (staff_id && !staff[staff_id].services.hasOwnProperty(service_id)) {
                                    staff_id = '';
                                }
                            }
                            setSelects($chain_item, location_id, category_id, service_id, staff_id);
                            if (service_id) {
                                $chain_item.find('.bookme-pro-js-select-category').val(services[service_id].category_id);
                                getAvailability();
                            }
                        });

                        // Staff select change
                        $container.on('change', '.bookme-pro-js-select-employee', function () {
                            var $chain_item = $(this).closest('.bookme-pro-js-chain-item'),
                                location_id = $chain_item.find('.bookme-pro-js-select-location').val(),
                                category_id = $('.bookme-pro-js-select-category', $chain_item).val(),
                                service_id = $chain_item.find('.bookme-pro-js-select-service').val(),
                                staff_id = this.value
                                ;

                            setSelects($chain_item, location_id, category_id, service_id, staff_id);
                            getAvailability();
                        });

                        // Set up draft selects.
                        if (Options.attributes.show_service_duration) {
                            $.each(services, function (id, service) {
                                service.name = service.name + ' ( ' + service.duration + ' )';
                            });
                        }

                        setSelect($select_location, locations);
                        setSelect($select_category, categories);
                        setSelect($select_service, services);
                        setSelect($select_employee, staff);
                        $select_location.closest('.bookme-pro-form-group').toggle(!Options.attributes.hide_locations);
                        $select_category.closest('.bookme-pro-form-group').toggle(!Options.attributes.hide_categories);
                        $select_service.closest('.bookme-pro-form-group').toggle(!(Options.attributes.hide_services && Options.attributes.service_id));
                        $select_employee.closest('.bookme-pro-form-group').toggle(!Options.attributes.hide_staff_members);
                        $select_nop.closest('.bookme-pro-form-group').toggle(Options.attributes.show_number_of_persons);
                        $('#bookme-pro-service-part1', $container).toggle(!Options.skip_steps.service_part1);
                        $('#bookme-pro-service-part2', $container).toggle(!Options.skip_steps.service_part2);
                        if (Options.attributes.location_id) {
                            $select_location.val(Options.attributes.location_id).trigger('change');
                        }
                        if (Options.attributes.category_id) {
                            $select_category.val(Options.attributes.category_id).trigger('change');
                        }
                        if (Options.attributes.service_id) {
                            $select_service.val(Options.attributes.service_id).trigger('change');
                        }
                        if (Options.attributes.staff_member_id) {
                            $select_employee.val(Options.attributes.staff_member_id).trigger('change');
                        }

                        // Create chain items.
                        $.each(chain, function (key, chain_item) {
                            var $chain_item = $chain_item_draft
                                .clone()
                                .data('chain_key', key)
                                .removeClass('bookme-pro-js-draft')
                                .css('display', 'block');
                            $chain_item_draft.find('select').each(function (i, select) {
                                $chain_item.find('select:eq(' + i + ')').val(select.value);
                            });
                            last_chain_key = key;
                            if (key == 0) {
                                $chain_item.find('.bookme-pro-js-actions button[data-action="drop"]').remove();
                            }
                            $('.bookme-pro-js-chain-item:last', $container).after($chain_item);
                            if (!Options.attributes.hide_locations && chain_item.location_id) {
                                $('.bookme-pro-js-select-location', $chain_item).val(chain_item.location_id).trigger('change');
                            }
                            if (chain_item.service_id) {
                                $('.bookme-pro-js-select-service', $chain_item).val(chain_item.service_id).trigger('change');
                                if (Options.attributes.hide_categories) {
                                    // Deselect category to keep full list of services.
                                    $('.bookme-pro-js-select-category', $chain_item).val('');
                                }
                            } else {
                                getAvailability();
                            }
                            if (!Options.attributes.hide_staff_members && chain_item.staff_ids.length == 1 && chain_item.staff_ids[0]) {
                                $('.bookme-pro-js-select-employee', $chain_item).val(chain_item.staff_ids[0]).trigger('change');
                            }
                            if (chain_item.number_of_persons > 1) {
                                $('.bookme-pro-js-select-number-of-persons', $chain_item).val(chain_item.number_of_persons);
                            }
                            if (chain_item.quantity > 1) {
                                $('.bookme-pro-js-select-quantity', $chain_item).val(chain_item.quantity);
                            }
                        });

                        $container.on('click', '.bookme-pro-js-mobile-step-1 .bookme-pro-js-actions button', function () {
                            switch ($(this).data('action')) {
                                case 'plus':
                                    var $new_chain = $chain_item_draft.clone();
                                    $chain_item_draft.find('select').each(function (i, select) {
                                        $new_chain.find('select:eq(' + i + ')').val(select.value);
                                    });
                                    $('.bookme-pro-js-chain-item:last', $container)
                                        .after(
                                            $new_chain
                                                .data('chain_key', ++last_chain_key)
                                                .removeClass('bookme-pro-js-draft')
                                                .css('display', 'table')
                                        );
                                    break;
                                case 'drop':
                                    $(this).closest('.bookme-pro-js-chain-item').remove();
                                    break;
                            }
                        });


                        var stepServiceValidator = function () {
                            $('.bookme-pro-js-select-service-error', $container).hide();
                            $('.bookme-pro-js-select-employee-error', $container).hide();
                            $('.bookme-pro-js-select-location-error', $container).hide();

                            var valid = true,
                                $select_service = null,
                                $select_employee = null,
                                $select_location = null,
                                $scroll_to = null;

                            $('.bookme-pro-js-chain-item:not(.bookme-pro-js-draft)', $container).each(function () {
                                var $chain = $(this);
                                $select_service = $('.bookme-pro-js-select-service', $chain);
                                $select_employee = $('.bookme-pro-js-select-employee', $chain);
                                $select_location = $('.bookme-pro-js-select-location', $chain);

                                $select_service.removeClass('bookme-pro-error');
                                $select_employee.removeClass('bookme-pro-error');
                                $select_location.removeClass('bookme-pro-error');

                                // service validation
                                if (!$select_service.val()) {
                                    valid = false;
                                    $select_service.addClass('bookme-pro-error');
                                    $('.bookme-pro-js-select-service-error', $chain).show();
                                    $scroll_to = $select_service;
                                }
                                if (Options.required.hasOwnProperty('location') && Options.required.location && !$select_location.val()) {
                                    valid = false;
                                    $select_location.addClass('bookme-pro-error');
                                    $('.bookme-pro-js-select-location-error', $chain).show();
                                    $scroll_to = $select_location;
                                }
                                if (Options.required.staff && !$select_employee.val()) {
                                    valid = false;
                                    $select_employee.addClass('bookme-pro-error');
                                    $('.bookme-pro-js-select-employee-error', $chain).show();
                                    $scroll_to = $select_employee;
                                }
                            });

                            $date_from.removeClass('bookme-pro-error');
                            // date validation
                            if (!$date_from.val()) {
                                valid = false;
                                $date_from.addClass('bookme-pro-error');
                                if ($scroll_to === null) {
                                    $scroll_to = $date_from;
                                }
                            }


                            if ($scroll_to !== null) {
                                scrollTo($scroll_to);
                            }

                            return valid;
                        };

                        // "Next" click
                        $next_step.on('click', function (e) {
                            e.preventDefault();

                            if (stepServiceValidator()) {

                                ladda_start(this);

                                // Prepare chain data.
                                var chain = {};
                                $('.bookme-pro-js-chain-item:not(.bookme-pro-js-draft)', $container).each(function () {
                                    var $chain_item = $(this);
                                    var staff_ids = [];
                                    if ($('.bookme-pro-js-select-employee', $chain_item).val()) {
                                        staff_ids.push($('.bookme-pro-js-select-employee', $chain_item).val());
                                    } else {
                                        $('.bookme-pro-js-select-employee', $chain_item).find('option').each(function () {
                                            if (this.value) {
                                                staff_ids.push(this.value);
                                            }
                                        });
                                    }
                                    chain[$chain_item.data('chain_key')] = {
                                        location_id: $('.bookme-pro-js-select-location', $chain_item).val(),
                                        service_id: $('.bookme-pro-js-select-service', $chain_item).val(),
                                        staff_ids: staff_ids,
                                        number_of_persons: $('.bookme-pro-js-select-number-of-persons', $chain_item).val(),
                                        quantity: $('.bookme-pro-js-select-quantity', $chain_item).val() ? $('.bookme-pro-js-select-quantity', $chain_item).val() : 1
                                    };
                                });

                                $.ajax({
                                    type: 'POST',
                                    url: Options.ajaxurl,
                                    data: {
                                        action: 'bookme_pro_session_save',
                                        csrf_token: BookmeProL10n.csrf_token,
                                        form_id: Options.form_id,
                                        chain: chain,
                                        date_from: $date_from.pickadate('picker').get('select', 'yyyy-mm-dd'),
                                        time_from: $select_time_from.val(),
                                        time_to: $select_time_to.val()
                                    },
                                    dataType: 'json',
                                    xhrFields: {withCredentials: true},
                                    crossDomain: 'withCredentials' in new XMLHttpRequest(),
                                    success: function (response) {
                                        stepTime();
                                    }
                                });
                            }
                        });
                    }
                } // ajax success
            }); // ajax
        }

        /**
         * Time step.
         */
        var xhr_render_time = null;

        function stepTime(params, error_message) {
            if (xhr_render_time != null) {
                xhr_render_time.abort();
                xhr_render_time = null;
            }
            var data = $.extend({
                action: 'bookme_pro_render_time',
                csrf_token: BookmeProL10n.csrf_token,
                form_id: Options.form_id,
                skip_extras: false
            }, params);
            if (Options.skip_steps.service) {
                // If Service step is skipped then we need to send time zone offset.
                data.time_zone = timeZone;
                data.time_zone_offset = timeZoneOffset;
            }
            xhr_render_time = $.ajax({
                url: Options.ajaxurl,
                data: data,
                dataType: 'json',
                xhrFields: {withCredentials: true},
                crossDomain: 'withCredentials' in new XMLHttpRequest(),
                success: function (response) {
                    if (response.success == false) {
                        // The session doesn't contain data.
                        stepService();
                        return;
                    }
                    BookmeProL10n.csrf_token = response.csrf_token;
                    $container.html(response.html);
                    // hide tooltip for not showing
                    $('#bookme-pro-tooltip').css({opacity: 0, visibility: 'hidden'});

                    var $columnizer_wrap = $('.bookme-pro-columnizer-wrap', $container),
                        $columnizer = $('.bookme-pro-columnizer', $columnizer_wrap),
                        $time_next_button = $('.bookme-pro-time-next', $container),
                        $time_prev_button = $('.bookme-pro-time-prev', $container),
                        $current_screen = null,
                        slot_height = 36,
                        column_width = $columnizer_wrap.width(),
                        columns = 0,
                        screen_index = 0,
                        has_more_slots = response.has_more_slots,
                        form_hidden = false,
                        $screens,
                        slots_per_column,
                        columns_per_screen
                        ;
                    // 'BACK' button.
                    $('.bookme-pro-js-back-step', $container).on('click', function (e) {
                        e.preventDefault();
                        ladda_start(this);
                        stepService();
                    }).toggle(!Options.skip_steps.service);

                    $('.bookme-pro-js-go-to-cart', $container).on('click', function (e) {
                        e.preventDefault();
                        ladda_start(this);
                        stepCart({from_step: 'time'});
                    });

                    // Insert all slots.
                    var slots = '';
                    $.each(response.slots, function (group, group_slots) {
                        slots += group_slots;
                    });
                    $columnizer.html(slots);


                    if (response.has_slots) {
                        if (error_message) {
                            $container.find('.bookme-pro-holder.bookme-pro-label-error').html(error_message);
                        } else {
                            $container.find('.bookme-pro-holder.bookme-pro-label-error').hide();
                        }

                        // Calculate number of slots per column.
                        slots_per_column = parseInt($(window).height() / slot_height, 10);
                        if (slots_per_column < 4) {
                            slots_per_column = 4;
                        } else if (slots_per_column > 10) {
                            slots_per_column = 10;
                        }

                        columns_per_screen = parseInt($columnizer_wrap.width() / column_width, 10);

                        if (columns_per_screen > 10) {
                            columns_per_screen = 10;
                        } else if (columns_per_screen == 0) {
                            // BookmePro form display hidden.
                            form_hidden = true;
                            columns_per_screen = 4;
                        }

                        initSlots();


                        if (!has_more_slots && $screens.length == 1) {
                            $time_next_button.hide();
                        }

                        var hammertime = $('.bookme-pro-time-step', $container).hammer({swipe_velocity: 0.1});

                        hammertime.on('swipeleft', function () {
                            if ($time_next_button.is(':visible')) {
                                $time_next_button.trigger('click');
                            }
                        });

                        hammertime.on('swiperight', function () {
                            if ($time_prev_button.is(':visible')) {
                                $time_prev_button.trigger('click');
                            }
                        });

                        $time_next_button.on('click', function (e) {
                            $time_prev_button.show();
                            if ($screens.eq(screen_index + 1).length) {
                                $columnizer.animate(
                                    {left: (Options.is_rtl ? '+' : '-') + ( screen_index + 1 ) * $current_screen.width()},
                                    {duration: 800}
                                );

                                $current_screen = $screens.eq(++screen_index);
                                $columnizer_wrap.animate(
                                    {duration: 800}
                                );

                                if (screen_index + 1 == $screens.length && !has_more_slots) {
                                    $time_next_button.hide();
                                }
                            } else if (has_more_slots) {
                                // Do ajax request when there are more slots.
                                var $button = $('> button:last', $columnizer);
                                if ($button.length == 0) {
                                    $button = $('.tse-content:hidden:last > button:last', $columnizer);
                                    if ($button.length == 0) {
                                        $button = $('.tse-content:last > button:last', $columnizer);
                                    }
                                }

                                // Render Next Time
                                var data = {
                                        action: 'bookme_pro_render_next_time',
                                        csrf_token: BookmeProL10n.csrf_token,
                                        form_id: Options.form_id,
                                        last_slot: $button.val()
                                    },
                                    ladda = ladda_start(this);

                                $.ajax({
                                    type: 'POST',
                                    url: Options.ajaxurl,
                                    data: data,
                                    dataType: 'json',
                                    xhrFields: {withCredentials: true},
                                    crossDomain: 'withCredentials' in new XMLHttpRequest(),
                                    success: function (response) {
                                        if (response.success) {
                                            if (response.has_slots) { // if there are available time
                                                has_more_slots = response.has_more_slots;
                                                var $html = $(response.html);
                                                // The first slot is always a day slot.
                                                // Check if such day slot already exists (this can happen
                                                // because of time zone offset) and then remove the first slot.
                                                var $first_day = $html.eq(0);
                                                if ($('button.bookme-pro-day[value="' + $first_day.attr('value') + '"]', $container).length) {
                                                    $html = $html.not(':first');
                                                }
                                                $columnizer.append($html);
                                                initSlots();
                                                $time_next_button.trigger('click');
                                            } else { // no available time
                                                $time_next_button.hide();
                                            }
                                        } else { // no available time
                                            $time_next_button.hide();
                                        }
                                        ladda.stop();
                                    }
                                });
                            }
                        });

                        $time_prev_button.on('click', function () {
                            $time_next_button.show();
                            $current_screen = $screens.eq(--screen_index);
                            $columnizer.animate(
                                {left: (Options.is_rtl ? '+' : '-') + screen_index * $current_screen.width()},
                                {duration: 800}
                            );
                            $columnizer_wrap.animate(
                                {duration: 800}
                            );
                            if (screen_index === 0) {
                                $time_prev_button.hide();
                            }
                        });
                    }
                    if (params === undefined) {     // Scroll when returning to the step Time.
                        scrollTo($container);
                    }

                    function initSlots() {
                        var $buttons = $('> button', $columnizer),
                            slots_count = 0,
                            max_slots = 0,
                            $button,
                            $column,
                            $screen;

                        /**
                         * Create columns for 'Show each day in one column' mode.
                         */
                        while ($buttons.length > 0) {
                            // Create column.
                            if ($buttons.eq(0).hasClass('bookme-pro-day')) {
                                slots_count = 1;
                                $column = $('<div class="tse-content" />');
                                $button = $($buttons.splice(0, 1));
                                $button.addClass('bookme-pro-js-first-child');
                                $column.append($button);
                            } else {
                                slots_count++;
                                $button = $($buttons.splice(0, 1));
                                // If it is last slot in the column.
                                if (!$buttons.length || $buttons.eq(0).hasClass('bookme-pro-day')) {
                                    $button.addClass('bookme-pro-last-child');
                                    $column.append($button);
                                    $columnizer.append($('<div class="bookme-pro-column tse-scrollable" />').append($column));
                                } else {
                                    $column.append($button);
                                }
                            }
                            // Calculate max number of slots.
                            if (slots_count > max_slots) {
                                max_slots = slots_count;
                            }
                        }

                        /**
                         * Create screens.
                         */
                        var $columns = $('> .bookme-pro-column', $columnizer);

                        while (has_more_slots ? $columns.length >= columns_per_screen : $columns.length) {
                            $screen = $('<div class="bookme-pro-time-screen"/>');
                            for (var i = 0; i < columns_per_screen; ++i) {
                                $column = $($columns.splice(0, 1));
                                if (i == 0) {
                                    $column.addClass('bookme-pro-js-first-column');
                                    var $first_slot = $column.find('.bookme-pro-js-first-child');
                                    // In the first column the first slot is time.
                                    if (!$first_slot.hasClass('bookme-pro-day')) {
                                        var group = $first_slot.data('group'),
                                            $group_slot = $('button.bookme-pro-day[value="' + group + '"]:last', $container);
                                        // Copy group slot to the first column.
                                        $column.prepend($group_slot.clone());
                                    }
                                }
                                $screen.append($column);
                            }
                            $columnizer.append($screen);
                        }
                        $screens = $('.bookme-pro-time-screen', $columnizer);
                        if ($current_screen === null) {
                            $current_screen = $screens.eq(0);
                        }

                        // On click on a slot.
                        $('button.bookme-pro-hour', $container).off('click').on('click', function (e) {
                            e.preventDefault();
                            var $this = $(this),
                                data = {
                                    action: 'bookme_pro_session_save',
                                    csrf_token: BookmeProL10n.csrf_token,
                                    form_id: Options.form_id,
                                    slots: this.value
                                };
                            $this.attr({
                                'data-style': 'zoom-in',
                                'data-spinner-color': '#333',
                                'data-spinner-size': '40'
                            });
                            ladda_start(this);
                            $.ajax({
                                type: 'POST',
                                url: Options.ajaxurl,
                                data: data,
                                dataType: 'json',
                                xhrFields: {withCredentials: true},
                                crossDomain: 'withCredentials' in new XMLHttpRequest(),
                                success: function (response) {
                                    if (Options.cart.enabled) {
                                        stepCart({add_to_cart: true, from_step: 'time'});
                                    } else {
                                        stepDetails({add_to_cart: true});
                                    }
                                }
                            });
                        });

                        // Columnizer width & height.
                        $('.bookme-pro-time-step', $container).width(columns_per_screen * column_width);
                        $columnizer_wrap.height(form_hidden
                            ? $('.bookme-pro-column.bookme-pro-js-first-column button', $current_screen).length * (slot_height + 3)
                            : $current_screen.height());
                        form_hidden = false;

                        // Initialize scrollers.
                        $('.bookme-pro-column', $container).TrackpadScrollEmulator();
                    }
                }
            });
        }

        /**
         * Cart step.
         */
        function stepCart(params, error) {
            if (!Options.cart.enabled) {
                stepDetails(params);
            } else {
                if (params && params.from_step) {
                    // Record previous step if it was given in params.
                    Options.cart.prev_step = params.from_step;
                }
                var data = $.extend({
                    action: 'bookme_pro_render_cart',
                    csrf_token: BookmeProL10n.csrf_token,
                    form_id: Options.form_id
                }, params);
                $.ajax({
                    url: Options.ajaxurl,
                    data: data,
                    dataType: 'json',
                    xhrFields: {withCredentials: true},
                    crossDomain: 'withCredentials' in new XMLHttpRequest(),
                    success: function (response) {
                        if (response.success) {
                            $container.html(response.html);
                            if (error) {
                                $('.bookme-pro-holder.bookme-pro-label-error', $container).html(error.message);
                                $('tr[data-cart-key="' + error.failed_key + '"]', $container).addClass('bookme-pro-label-error');
                            } else {
                                $('.bookme-pro-holder.bookme-pro-label-error', $container).hide();
                            }
                            scrollTo($container);
                            $('.bookme-pro-js-next-step', $container).on('click', function () {
                                ladda_start(this);
                                stepDetails();
                            });
                            $('.bookme-pro-add-item', $container).on('click', function () {
                                ladda_start(this);
                                stepService({new_chain: true});
                            });
                            // 'BACK' button.
                            $('.bookme-pro-js-back-step', $container).on('click', function (e) {
                                e.preventDefault();
                                ladda_start(this);
                                switch (Options.cart.prev_step) {
                                    case 'service':
                                        stepService();
                                        break;
                                    case 'time':
                                        stepTime();
                                        break;
                                    default:
                                        stepService();
                                }
                            });
                            $('.bookme-pro-js-actions button', $container).on('click', function () {
                                ladda_start(this);
                                var $this = $(this),
                                    $cart_item = $this.closest('tr');
                                switch ($this.data('action')) {
                                    case 'drop':
                                        $.ajax({
                                            url: Options.ajaxurl,
                                            data: {
                                                action: 'bookme_pro_cart_drop_item',
                                                csrf_token: BookmeProL10n.csrf_token,
                                                form_id: Options.form_id,
                                                cart_key: $cart_item.data('cart-key')
                                            },
                                            dataType: 'json',
                                            xhrFields: {withCredentials: true},
                                            crossDomain: 'withCredentials' in new XMLHttpRequest(),
                                            success: function (response) {
                                                if (response.success) {
                                                    var remove_cart_key = $cart_item.data('cart-key'),
                                                        $trs_to_remove = $('tr[data-cart-key="' + remove_cart_key + '"]', $container)
                                                        ;
                                                    $cart_item.delay(300).fadeOut(200, function () {
                                                        $('.bookme-pro-js-total-price', $container).html(response.data.total_price);
                                                        $('.bookme-pro-js-total-deposit-price', $container).html(response.data.total_deposit_price);
                                                        $trs_to_remove.remove();
                                                        if ($('tr[data-cart-key]').length == 0) {
                                                            $('.bookme-pro-js-back-step', $container).hide();
                                                            $('.bookme-pro-js-next-step', $container).hide();
                                                        }
                                                    });
                                                }
                                            }
                                        });
                                        break;
                                    case 'edit':
                                        stepService({edit_cart_item: $cart_item.data('cart-key')});
                                        break;
                                }
                            });
                        }
                    }
                });
            }
        }

        /**
         * Details step.
         */
        function stepDetails(params) {
            var data = $.extend({
                action: 'bookme_pro_render_details',
                csrf_token: BookmeProL10n.csrf_token,
                form_id: Options.form_id,
                page_url: document.URL.split('#')[0]
            }, params);
            $.ajax({
                url: Options.ajaxurl,
                data: data,
                dataType: 'json',
                xhrFields: {withCredentials: true},
                crossDomain: 'withCredentials' in new XMLHttpRequest(),
                success: function (response) {
                    if (response.success) {
                        $container.html(response.html);
                        scrollTo($container);
                        // Init.
                        var phone_number = '',
                            $guest_info = $('.bookme-pro-js-guest', $container),
                            $phone_field = $('.bookme-pro-js-user-phone-input', $container),
                            $email_field = $('.bookme-pro-js-user-email', $container),
                            $full_name_field = $('.bookme-pro-js-full-name', $container),
                            $first_name_field = $('.bookme-pro-js-first-name', $container),
                            $last_name_field = $('.bookme-pro-js-last-name', $container),
                            $phone_error = $('.bookme-pro-js-user-phone-error', $container),
                            $email_error = $('.bookme-pro-js-user-email-error', $container),
                            $name_error = $('.bookme-pro-js-full-name-error', $container),
                            $first_name_error = $('.bookme-pro-js-first-name-error', $container),
                            $last_name_error = $('.bookme-pro-js-last-name-error', $container),
                            $captcha = $('.bookme-pro-js-captcha-img', $container),
                            $refresh = $('.bookme-pro-js-captcha-refresh', $container),
                            $errors = $('.bookme-pro-js-full-name-error, .bookme-pro-js-first-name-error, .bookme-pro-js-last-name-error, .bookme-pro-js-user-phone-error, .bookme-pro-js-user-email-error, .bookme-pro-custom-field-error', $container),
                            $fields = $('.bookme-pro-js-full-name, .bookme-pro-js-first-name, .bookme-pro-js-last-name, .bookme-pro-js-user-phone-input, .bookme-pro-js-user-email, .bookme-pro-custom-field', $container),
                            $modals = $('.bookme-pro-js-modal', $container),
                            $login_modal = $('.bookme-pro-js-login', $container),
                            $cst_modal = $('.bookme-pro-js-cst-duplicate', $container),
                            $next_btn = $('.bookme-pro-js-next-step', $container)
                            ;

                        if (Options.status.booking == 'cancelled') {
                            Options.status.booking = 'ok';
                        }

                        var $payments = $('.bookme-pro-payment', $container),
                            $apply_coupon_button = $('.bookme-pro-js-apply-coupon', $container),
                            $coupon_input = $('input.bookme-pro-user-coupon', $container),
                            $coupon_discount = $('#bookme-pro-js-coupon-discount', $container),
                            $coupon_total = $('#bookme-pro-js-coupon-total', $container),
                            $coupon_error = $('.bookme-pro-js-coupon-error', $container),
                            $bookme_pro_payment_nav = $('.bookme-pro-payment-nav', $container),
                            $buttons = $('form.bookme-pro-authorize-net,form.bookme-pro-stripe', $container)
                            ;
                        $payments.on('click', function () {
                            $buttons.hide();
                            $('.bookme-pro-gateway-buttons.pay-' + $(this).val(), $container).show();
                            if ($(this).val() == 'card') {
                                $('form.bookme-pro-' + $(this).data('form'), $container).show();
                            }
                        });
                        $payments.eq(0).trigger('click');

                        if (Options.intlTelInput.enabled) {
                            $phone_field.intlTelInput({
                                preferredCountries: [Options.intlTelInput.country],
                                initialCountry: Options.intlTelInput.country,
                                geoIpLookup: function (callback) {
                                    $.get('https://ipinfo.io', function () {
                                    }, 'jsonp').always(function (resp) {
                                        var countryCode = (resp && resp.country) ? resp.country : '';
                                        callback(countryCode);
                                    });
                                },
                                utilsScript: Options.intlTelInput.utils
                            });
                        }
                        // Init modals.
                        $('body > .bookme-pro-js-modal.' + Options.form_id).remove();
                        $modals
                            .addClass(Options.form_id).appendTo('body')
                            .on('click', '.bookme-pro-js-close', function (e) {
                                e.preventDefault();
                                $(e.delegateTarget).removeClass('bookme-pro-in')
                                    .find('form').trigger('reset').end()
                                    .find('input').removeClass('bookme-pro-error').end()
                                    .find('.bookme-pro-label-error').html('')
                                ;
                            })
                        ;
                        // Login modal.
                        $('.bookme-pro-js-login-show', $container).on('click', function (e) {
                            e.preventDefault();
                            $login_modal.addClass('bookme-pro-in');
                        });
                        $('button:submit', $login_modal).on('click', function (e) {
                            e.preventDefault();
                            var ladda = Ladda.create(this);
                            ladda.start();
                            $.ajax({
                                type: 'POST',
                                url: Options.ajaxurl,
                                data: {
                                    action: 'bookme_pro_wp_user_login',
                                    csrf_token: BookmeProL10n.csrf_token,
                                    form_id: Options.form_id,
                                    log: $login_modal.find('[name="log"]').val(),
                                    pwd: $login_modal.find('[name="pwd"]').val(),
                                    rememberme: $login_modal.find('[name="rememberme"]').prop('checked') ? 1 : 0
                                },
                                dataType: 'json',
                                xhrFields: {withCredentials: true},
                                crossDomain: 'withCredentials' in new XMLHttpRequest(),
                                success: function (response) {
                                    if (response.success) {
                                        BookmeProL10n.csrf_token = response.data.csrf_token;
                                        $guest_info.fadeOut('slow');
                                        $full_name_field.val(response.data.full_name).removeClass('bookme-pro-error');
                                        $first_name_field.val(response.data.first_name).removeClass('bookme-pro-error');
                                        $last_name_field.val(response.data.last_name).removeClass('bookme-pro-error');
                                        if (response.data.phone) {
                                            $phone_field.removeClass('bookme-pro-error');
                                            if (Options.intlTelInput.enabled) {
                                                $phone_field.intlTelInput('setNumber', response.data.phone);
                                            } else {
                                                $phone_field.val(response.data.phone);
                                            }
                                        }
                                        $email_field.val(response.data.email).removeClass('bookme-pro-error');
                                        $errors.filter(':not(.bookme-pro-custom-field-error)').html('');
                                        $login_modal.removeClass('bookme-pro-in');
                                    } else if (response.error_code == 8) {
                                        $login_modal.find('input').addClass('bookme-pro-error');
                                        $login_modal.find('.bookme-pro-label-error').html(response.error);
                                    }
                                    ladda.stop();
                                }
                            })
                        });
                        // Customer duplicate modal.
                        $('button:submit', $cst_modal).on('click', function (e) {
                            e.preventDefault();
                            $cst_modal.removeClass('bookme-pro-in');
                            $next_btn.trigger('click', [1]);
                        });

                        $apply_coupon_button.on('click', function (e) {
                            var ladda = ladda_start(this);
                            $coupon_error.text('');
                            $coupon_input.removeClass('bookme-pro-error');

                            var data = {
                                action: 'bookme_pro_apply_coupon',
                                csrf_token: BookmeProL10n.csrf_token,
                                form_id: Options.form_id,
                                coupon: $coupon_input.val()
                            };

                            $.ajax({
                                type: 'POST',
                                url: Options.ajaxurl,
                                data: data,
                                dataType: 'json',
                                xhrFields: {withCredentials: true},
                                crossDomain: 'withCredentials' in new XMLHttpRequest(),
                                success: function (response) {
                                    if (response.success) {
                                        $coupon_input.replaceWith(data.coupon);
                                        $apply_coupon_button.replaceWith('<strong>✓</strong>');
                                        $coupon_discount.text(response.discount);
                                        $coupon_total.text(response.total);
                                        if (response.total_simple <= 0) {
                                            $bookme_pro_payment_nav.hide();
                                            $('.bookme-pro-js-coupon-free', $container).attr('checked', 'checked').val(data.coupon);
                                        } else {
                                            // Set new price for payment request
                                            $('input.bookme-pro-payment-amount', $container).val(response.total_simple);
                                            var $payu_latam_form = $('.bookme-pro-payu_latam-form', $container);
                                            if ($payu_latam_form.length) {
                                                $.post(Options.ajaxurl, {
                                                    action: 'bookme_pro_payu_latam_refresh_tokens',
                                                    csrf_token: BookmeProL10n.csrf_token,
                                                    form_id: Options.form_id
                                                })
                                                    .done(function (response) {
                                                        if (response.success) {
                                                            $payu_latam_form.find('input[name=referenceCode]').val(response.data.referenceCode);
                                                            $payu_latam_form.find('input[name=signature]').val(response.data.signature);
                                                        }
                                                    }, 'json');
                                            }
                                        }
                                    } else if (response.error_code == 6) {
                                        $coupon_error.html(response.error);
                                        $coupon_input.addClass('bookme-pro-error');
                                        scrollTo($coupon_error);
                                    }
                                    ladda.stop();
                                },
                                error: function () {
                                    ladda.stop();
                                }
                            });
                        });

                        $next_btn.on('click', function (e, force_update_customer) {
                            e.preventDefault();
                            var custom_fields = [],
                                checkbox_values,
                                captcha_ids = [],
                                ladda = ladda_start(this)
                                ;
                            $('.bookme-pro-custom-fields-container', $container).each(function () {
                                var $cf_container = $(this),
                                    key = $cf_container.data('key'),
                                    custom_fields_data = [];
                                $('div.bookme-pro-custom-field-row', $cf_container).each(function () {
                                    var $this = $(this);
                                    switch ($this.data('type')) {
                                        case 'text-field':
                                            custom_fields_data.push({
                                                id: $this.data('id'),
                                                value: $this.find('input.bookme-pro-custom-field').val()
                                            });
                                            break;
                                        case 'textarea':
                                            custom_fields_data.push({
                                                id: $this.data('id'),
                                                value: $this.find('textarea.bookme-pro-custom-field').val()
                                            });
                                            break;
                                        case 'checkboxes':
                                            checkbox_values = [];
                                            $this.find('input.bookme-pro-custom-field:checked').each(function () {
                                                checkbox_values.push(this.value);
                                            });
                                            custom_fields_data.push({
                                                id: $this.data('id'),
                                                value: checkbox_values
                                            });
                                            break;
                                        case 'radio-buttons':
                                            custom_fields_data.push({
                                                id: $this.data('id'),
                                                value: $this.find('input.bookme-pro-custom-field:checked').val() || null
                                            });
                                            break;
                                        case 'drop-down':
                                            custom_fields_data.push({
                                                id: $this.data('id'),
                                                value: $this.find('select.bookme-pro-custom-field').val()
                                            });
                                            break;
                                        case 'captcha':
                                            custom_fields_data.push({
                                                id: $this.data('id'),
                                                value: $this.find('input.bookme-pro-custom-field').val()
                                            });
                                            captcha_ids.push($this.data('id'));
                                            break;
                                    }
                                });
                                custom_fields[key] = {custom_fields: JSON.stringify(custom_fields_data)};
                            });

                            try {
                                phone_number = Options.intlTelInput.enabled ? $phone_field.intlTelInput('getNumber') : $phone_field.val();
                                if (phone_number == '') {
                                    phone_number = $phone_field.val();
                                }
                            } catch (error) {  // In case when intlTelInput can't return phone number.
                                phone_number = $phone_field.val();
                            }
                            var data = {
                                action: 'bookme_pro_session_save',
                                csrf_token: BookmeProL10n.csrf_token,
                                form_id: Options.form_id,
                                full_name: $full_name_field.val(),
                                first_name: $first_name_field.val(),
                                last_name: $last_name_field.val(),
                                phone: phone_number,
                                email: $email_field.val(),
                                cart: custom_fields,
                                captcha_ids: JSON.stringify(captcha_ids),
                                force_update_customer: !Options.update_details_dialog || force_update_customer
                            };
                            $.ajax({
                                type: 'POST',
                                url: Options.ajaxurl,
                                data: data,
                                dataType: 'json',
                                xhrFields: {withCredentials: true},
                                crossDomain: 'withCredentials' in new XMLHttpRequest(),
                                success: function (response) {
                                    // Error messages
                                    $errors.empty();
                                    $fields.removeClass('bookme-pro-error');

                                    if (response.success) {
                                        if (Options.woocommerce.enabled) {
                                            var data = {
                                                action: 'bookme_pro_add_to_woocommerce_cart',
                                                csrf_token: BookmeProL10n.csrf_token,
                                                form_id: Options.form_id
                                            };
                                            $.ajax({
                                                type: 'POST',
                                                url: Options.ajaxurl,
                                                data: data,
                                                dataType: 'json',
                                                xhrFields: {withCredentials: true},
                                                crossDomain: 'withCredentials' in new XMLHttpRequest(),
                                                success: function (response) {
                                                    if (response.success) {
                                                        window.location.href = Options.woocommerce.cart_url;
                                                    } else {
                                                        ladda.stop();
                                                        stepTime(undefined, response.error);
                                                    }
                                                }
                                            });
                                        } else {
                                            if ($('.bookme-pro-payment[value=local]', $container).is(':checked') || $('.bookme-pro-js-coupon-free', $container).is(':checked') || $('.bookme-pro-payment:checked', $container).val() == undefined) {
                                                // handle only if was selected local payment !
                                                e.preventDefault();
                                                save();

                                            } else if ($('.bookme-pro-payment[value=card]', $container).is(':checked')) {
                                                var stripe = $('.bookme-pro-payment[data-form=stripe]', $container).is(':checked');
                                                var card_action = stripe ? 'bookme_pro_stripe' : 'bookme_pro_authorize_net_aim';
                                                $form = $container.find(stripe ? '.bookme-pro-stripe' : '.bookme-pro-authorize-net');
                                                e.preventDefault();

                                                var data = {
                                                    action: card_action,
                                                    csrf_token: BookmeProL10n.csrf_token,
                                                    card: {
                                                        number: $form.find('input[name="card_number"]').val(),
                                                        cvc: $form.find('input[name="card_cvc"]').val(),
                                                        exp_month: $form.find('select[name="card_exp_month"]').val(),
                                                        exp_year: $form.find('select[name="card_exp_year"]').val()
                                                    },
                                                    form_id: Options.form_id
                                                };

                                                var card_payment = function (data) {
                                                    $.ajax({
                                                        type: 'POST',
                                                        url: Options.ajaxurl,
                                                        data: data,
                                                        dataType: 'json',
                                                        xhrFields: {withCredentials: true},
                                                        crossDomain: 'withCredentials' in new XMLHttpRequest(),
                                                        success: function (response) {
                                                            if (response.success) {
                                                                stepComplete();
                                                            } else if (response.error_code == 3) {
                                                                handle_error_3(response);
                                                            } else if (response.error_code == 7) {
                                                                ladda.stop();
                                                                $form.find('.bookme-pro-js-card-error').text(response.error);
                                                            }
                                                        }
                                                    });
                                                };
                                                if (stripe && $form.find('#publishable_key').val()) {
                                                    try {
                                                        Stripe.setPublishableKey($form.find('#publishable_key').val());
                                                        Stripe.createToken(data.card, function (status, response) {
                                                            if (response.error) {
                                                                $form.find('.bookme-pro-js-card-error').text(response.error.message);
                                                                ladda.stop();
                                                            } else {
                                                                // Token from stripe.js
                                                                data['card'] = response['id'];
                                                                card_payment(data);
                                                            }
                                                        });
                                                    } catch (e) {
                                                        $form.find('.bookme-pro-js-card-error').text(e.message);
                                                        ladda.stop();
                                                    }
                                                } else {
                                                    card_payment(data);
                                                }
                                            } else if ($('.bookme-pro-payment[value=paypal]', $container).is(':checked')
                                                || $('.bookme-pro-payment[value=2checkout]', $container).is(':checked')
                                                || $('.bookme-pro-payment[value=payu_latam]', $container).is(':checked')
                                                || $('.bookme-pro-payment[value=payson]', $container).is(':checked')
                                                || $('.bookme-pro-payment[value=mollie]', $container).is(':checked')
                                            ) {
                                                e.preventDefault();
                                                var $pay = $('.bookme-pro-payment:checked', $container).val();
                                                $form = $('form.bookme-pro-' + $pay + '-form', $container);
                                                if ($form.find('input.bookme-pro-payment-id').length > 0) {
                                                    $.ajax({
                                                        type: 'POST',
                                                        url: Options.ajaxurl,
                                                        xhrFields: {withCredentials: true},
                                                        crossDomain: 'withCredentials' in new XMLHttpRequest(),
                                                        data: {
                                                            action: 'bookme_pro_save_pending_appointment',
                                                            csrf_token: BookmeProL10n.csrf_token,
                                                            form_id: Options.form_id,
                                                            payment_type: $form.data('gateway')
                                                        },
                                                        dataType: 'json',
                                                        success: function (response) {
                                                            if (response.success) {
                                                                $form.find('input.bookme-pro-payment-id').val(response.payment_id);
                                                                $form.submit();
                                                            } else if (response.error_code == 3) {
                                                                handle_error_3(response);
                                                            }
                                                        }
                                                    });
                                                } else {
                                                    $.ajax({
                                                        type: 'POST',
                                                        url: Options.ajaxurl,
                                                        xhrFields: {withCredentials: true},
                                                        crossDomain: 'withCredentials' in new XMLHttpRequest(),
                                                        data: {
                                                            action: 'bookme_pro_check_cart',
                                                            csrf_token: BookmeProL10n.csrf_token,
                                                            form_id: Options.form_id
                                                        },
                                                        dataType: 'json',
                                                        success: function (response) {
                                                            if (response.success) {
                                                                $form.submit();
                                                            } else if (response.error_code == 3) {
                                                                handle_error_3(response);
                                                            }
                                                        }
                                                    });
                                                }
                                            }
                                        }
                                    } else {
                                        ladda.stop();
                                        var $scroll_to = null;
                                        if (response.appointments_limit) {
                                            stepComplete(['appointment_limit']);
                                        } else {

                                            if (response.full_name) {
                                                $name_error.html(response.full_name);
                                                $full_name_field.addClass('bookme-pro-error');
                                                $scroll_to = $full_name_field;
                                            }
                                            if (response.first_name) {
                                                $first_name_error.html(response.first_name);
                                                $first_name_field.addClass('bookme-pro-error');
                                                if ($scroll_to === null) {
                                                    $scroll_to = $first_name_field;
                                                }
                                            }
                                            if (response.last_name) {
                                                $last_name_error.html(response.last_name);
                                                $last_name_field.addClass('bookme-pro-error');
                                                if ($scroll_to === null) {
                                                    $scroll_to = $last_name_field;
                                                }
                                            }
                                            if (response.phone) {
                                                $phone_error.html(response.phone);
                                                $phone_field.addClass('bookme-pro-error');
                                                if ($scroll_to === null) {
                                                    $scroll_to = $phone_field;
                                                }
                                            }
                                            if (response.email) {
                                                $email_error.html(response.email);
                                                $email_field.addClass('bookme-pro-error');
                                                if ($scroll_to === null) {
                                                    $scroll_to = $email_field;
                                                }
                                            }
                                            if (response.custom_fields) {
                                                $.each(response.custom_fields, function (key, fields) {
                                                    $.each(fields, function (field_id, message) {
                                                        var $custom_fields_collector = $('.bookme-pro-custom-fields-container[data-key="' + key + '"]', $container);
                                                        var $div = $('[data-id="' + field_id + '"]', $custom_fields_collector);
                                                        $div.find('.bookme-pro-custom-field-error').html(message);
                                                        $div.find('.bookme-pro-custom-field').addClass('bookme-pro-error');
                                                        if ($scroll_to === null) {
                                                            $scroll_to = $div.find('.bookme-pro-custom-field');
                                                        }
                                                    });
                                                });
                                            }
                                            if (response.customer) {
                                                $cst_modal
                                                    .find('.bookme-pro-js-modal-body').html(response.customer).end()
                                                    .addClass('bookme-pro-in')
                                                ;
                                            }
                                        }
                                        if ($scroll_to !== null) {
                                            scrollTo($scroll_to);
                                        }
                                    }
                                }
                            });
                        });

                        $('.bookme-pro-js-back-step', $container).on('click', function (e) {
                            e.preventDefault();
                            ladda_start(this);
                            if (Options.cart.enabled) {
                                stepCart();
                            } else {
                                stepTime();
                            }
                        });

                        $refresh.on('click', function () {
                            $captcha.css('opacity', '0.5');
                            $.ajax({
                                type: 'POST',
                                url: Options.ajaxurl,
                                data: {
                                    action: 'bookme_pro_captcha_refresh',
                                    form_id: Options.form_id,
                                    csrf_token: BookmeProL10n.csrf_token
                                },
                                dataType: 'json',
                                xhrFields: {withCredentials: true},
                                crossDomain: 'withCredentials' in new XMLHttpRequest(),
                                success: function (response) {
                                    if (response.success) {
                                        $captcha.attr('src', response.data.captcha_url).on('load', function () {
                                            $captcha.css('opacity', '1');
                                        });
                                    }
                                }
                            });
                        });
                    }
                }
            });
        }


        /**
         * Complete step.
         */
        function stepComplete(errors) {
            if ((typeof errors === 'undefined' || errors.length == 0) && Options.final_step_url) {
                document.location.href = Options.final_step_url;
            } else {
                $.ajax({
                    url: Options.ajaxurl,
                    data: {
                        action: 'bookme_pro_render_complete',
                        csrf_token: BookmeProL10n.csrf_token,
                        form_id: Options.form_id,
                        errors: errors
                    },
                    dataType: 'json',
                    xhrFields: {withCredentials: true},
                    crossDomain: 'withCredentials' in new XMLHttpRequest(),
                    success: function (response) {
                        if (response.success) {
                            $container.html(response.html);
                            scrollTo($container);
                        }
                    }
                });
            }
        }

        // =========== helpers ===================

        //
        function save() {
            $.ajax({
                type: 'POST',
                url: Options.ajaxurl,
                xhrFields: {withCredentials: true},
                crossDomain: 'withCredentials' in new XMLHttpRequest(),
                data: {action: 'bookme_pro_save_appointment', csrf_token: BookmeProL10n.csrf_token, form_id: Options.form_id},
                dataType: 'json'
            }).done(function (response) {
                if (response.success) {
                    stepComplete();
                } else if (response.error_code == 3) {
                    handle_error_3(response);
                }
            });
        }

        function ladda_start(elem) {
            var ladda = Ladda.create(elem);
            ladda.start();
            return ladda;
        }

        /**
         * Handle error with code 3 which means one of the cart item is not available anymore.
         *
         * @param response
         */
        function handle_error_3(response) {
            if (Options.cart.enabled) {
                stepCart(undefined, {
                    failed_key: response.failed_cart_key,
                    message: response.error
                });
            } else {
                stepTime(undefined, response.error);
            }
        }

        /**
         * Scroll to element if it is not visible.
         *
         * @param $elem
         */
        function scrollTo($elem) {
            var elemTop = $elem.offset().top;
            var scrollTop = $(window).scrollTop();
            if (elemTop < $(window).scrollTop() || elemTop > scrollTop + window.innerHeight) {
                $('html,body').animate({scrollTop: (elemTop - 24)}, 500);
            }
        }

    };

})(jQuery);
