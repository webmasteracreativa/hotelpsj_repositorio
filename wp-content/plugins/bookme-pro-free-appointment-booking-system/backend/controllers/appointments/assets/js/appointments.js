jQuery(function ($) {
    var
        $appointments_list = $('#bookme-pro-appointments-list'),
        $check_all_button = $('#bookme-pro-check-all'),
        $id_filter = $('#bookme-pro-filter-id'),
        $date_filter = $('#bookme-pro-filter-date'),
        $staff_filter = $('#bookme-pro-filter-staff'),
        $customer_filter = $('#bookme-pro-filter-customer'),
        $service_filter = $('#bookme-pro-filter-service'),
        $status_filter = $('#bookme-pro-filter-status'),
        $add_button = $('#bookme-pro-add'),
        $delete_button = $('#bookme-pro-delete'),
        isMobile = false
        ;

    try {
        document.createEvent("TouchEvent");
        isMobile = true;
    } catch (e) {

    }

    $('.bookme-pro-js-select').val(null);
    $.each(BookmeProL10n.filter, function (field, value) {
        if (value != '') {
            $('#bookme-pro-filter-' + field).val(value);
        }
        // check if select has correct values
        if ($('#bookme-pro-filter-' + field).prop('type') == 'select-one') {
            if ($('#bookme-pro-filter-' + field + ' option[value="' + value + '"]').length == 0) {
                $('#bookme-pro-filter-' + field).val(null);
            }
        }
    });

    /**
     * Init DataTables.
     */
    var columns = [
        {data: 'id', responsivePriority: 2},
        {data: 'start_date', responsivePriority: 2},
        {data: 'staff.name', responsivePriority: 2},
        {data: 'customer.full_name', render: $.fn.dataTable.render.text(), responsivePriority: 2},
        {
            data: 'customer.phone',
            responsivePriority: 3,
            render: function (data, type, row, meta) {
                if (isMobile) {
                    return '<a href="tel:' + data + '">' + $.fn.dataTable.render.text().display(data) + '</a>';
                } else {
                    return $.fn.dataTable.render.text().display(data);
                }
            }
        },
        {data: 'customer.email', render: $.fn.dataTable.render.text(), responsivePriority: 3},
        {
            data: 'service.title',
            responsivePriority: 2,
            render: function (data, type, row, meta) {
                if (row.service.extras.length) {
                    var extras = '<ul class="bookme-pro-list list-dots">';
                    $.each(row.service.extras, function (key, item) {
                        extras += '<li><nobr>' + item.title + '</nobr></li>';
                    });
                    extras += '</ul>';
                    return data + extras;
                }
                else {
                    return data;
                }
            }
        },
        {data: 'service.duration', responsivePriority: 2},
        {data: 'status', responsivePriority: 2},
        {
            data: 'payment',
            responsivePriority: 2,
            render: function (data, type, row, meta) {
                return '<a href="#bookme-pro-payment-details-modal" id="bookme-pro-payment-dialog-show" data-payment_id="' + row.payment_id + '">' + data + '</a>';
            }
        }
    ];
    $.each(BookmeProL10n.cf_columns, function (i, cf_id) {
        columns.push({
            data: 'custom_fields.' + cf_id,
            render: $.fn.dataTable.render.text(),
            responsivePriority: 4,
            orderable: false
        });
    });
    var dt = $appointments_list.DataTable({
        order: [[1, 'desc']],
        info: false,
        paging: false,
        searching: false,
        processing: true,
        responsive: true,
        serverSide: true,
        ajax: {
            url: ajaxurl,
            type: 'POST',
            data: function (d) {
                return $.extend({action: 'bookme_pro_get_appointments', csrf_token: BookmeProL10n.csrf_token}, {
                    filter: {
                        id: $id_filter.val(),
                        date: $date_filter.data('date'),
                        staff: $staff_filter.val(),
                        customer: $customer_filter.val(),
                        service: $service_filter.val(),
                        status: $status_filter.val()
                    }
                }, d);
            }
        },
        columns: columns.concat([
            {
                responsivePriority: 1,
                orderable: false,
                render: function (data, type, row, meta) {
                    return '<button type="button" class="btn btn-sm btn-default"><i class="glyphicon glyphicon-pencil"></i></a>';
                }
            },
            {
                responsivePriority: 1,
                orderable: false,
                render: function (data, type, row, meta) {
                    return '<input type="checkbox" value="' + row.ca_id + '" />';
                }
            }
        ]),
        language: {
            zeroRecords: BookmeProL10n.zeroRecords,
            processing: BookmeProL10n.processing
        }
    });

    /**
     * Add appointment.
     */
    $add_button.on('click', function () {
        showAppointmentDialog(
            null,
            null,
            moment(),
            function (event) {
                dt.ajax.reload();
            }
        )
    });

    /**
     * Edit appointment.
     */
    $appointments_list.on('click', 'button', function (e) {
        e.preventDefault();
        var data = dt.row($(this).closest('td')).data();
        showAppointmentDialog(
            data.id,
            null,
            null,
            function (event) {
                dt.ajax.reload();
            }
        )
    });

    /**
     * Select all appointments.
     */
    $check_all_button.on('change', function () {
        $appointments_list.find('tbody input:checkbox').prop('checked', this.checked);
    });

    /**
     * On appointment select.
     */
    $appointments_list.on('change', 'tbody input:checkbox', function () {
        $check_all_button.prop('checked', $appointments_list.find('tbody input:not(:checked)').length == 0);
    });

    /**
     * Delete appointments.
     */
    $delete_button.on('click', function () {
        var ladda = Ladda.create(this);
        ladda.start();

        var data = [];
        var $checkboxes = $appointments_list.find('tbody input:checked');
        $checkboxes.each(function () {
            data.push(this.value);
        });

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bookme_pro_delete_customer_appointments',
                csrf_token: BookmeProL10n.csrf_token,
                data: data,
                notify: $('#bookme-pro-delete-notify').prop('checked') ? 1 : 0,
                reason: $('#bookme-pro-delete-reason').val()
            },
            dataType: 'json',
            success: function (response) {
                ladda.stop();
                $('#bookme-pro-delete-dialog').modal('hide');
                if (response.success) {
                    dt.draw(false);
                } else {
                    alert(response.data.message);
                }
            }
        });
    });

    /**
     * Init date range picker.
     */
    moment.locale('en', {
        months: BookmeProL10n.calendar.longMonths,
        monthsShort: BookmeProL10n.calendar.shortMonths,
        weekdays: BookmeProL10n.calendar.longDays,
        weekdaysShort: BookmeProL10n.calendar.shortDays,
        weekdaysMin: BookmeProL10n.calendar.shortDays
    });

    var picker_ranges = {};
    picker_ranges[BookmeProL10n.yesterday] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
    picker_ranges[BookmeProL10n.today] = [moment(), moment()];
    picker_ranges[BookmeProL10n.tomorrow] = [moment().add(1, 'days'), moment().add(1, 'days')];
    picker_ranges[BookmeProL10n.last_7] = [moment().subtract(7, 'days'), moment()];
    picker_ranges[BookmeProL10n.last_30] = [moment().subtract(30, 'days'), moment()];
    picker_ranges[BookmeProL10n.this_month] = [moment().startOf('month'), moment().endOf('month')];
    picker_ranges[BookmeProL10n.next_month] = [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')];

    $date_filter.daterangepicker(
        {
            parentEl: $date_filter.parent(),
            startDate: moment().startOf('month'),
            endDate: moment().endOf('month'),
            ranges: picker_ranges,
            locale: {
                applyLabel: BookmeProL10n.apply,
                cancelLabel: BookmeProL10n.cancel,
                fromLabel: BookmeProL10n.from,
                toLabel: BookmeProL10n.to,
                customRangeLabel: BookmeProL10n.custom_range,
                daysOfWeek: BookmeProL10n.calendar.shortDays,
                monthNames: BookmeProL10n.calendar.longMonths,
                firstDay: parseInt(BookmeProL10n.startOfWeek),
                format: BookmeProL10n.mjsDateFormat
            }
        },
        function (start, end) {
            var format = 'YYYY-MM-DD';
            $date_filter
                .data('date', start.format(format) + ' - ' + end.format(format))
                .find('span')
                .html(start.format(BookmeProL10n.mjsDateFormat) + ' - ' + end.format(BookmeProL10n.mjsDateFormat));
        }
    );

    /**
     * On filters change.
     */
    $('.bookme-pro-js-select')
        .on('select2:unselecting', function (e) {
            e.preventDefault();
            $(this).val(null).trigger('change');
        })
        .select2({
            width: '100%',
            theme: 'bootstrap',
            allowClear: true,
            language: {
                noResults: function () {
                    return BookmeProL10n.no_result_found;
                }
            }
        });

    $id_filter.on('keyup', function () {
        dt.ajax.reload();
    });
    $date_filter.on('apply.daterangepicker', function () {
        dt.ajax.reload();
    });
    $staff_filter.on('change', function () {
        dt.ajax.reload();
    });
    $customer_filter.on('change', function () {
        dt.ajax.reload();
    });
    $service_filter.on('change', function () {
        dt.ajax.reload();
    });
    $status_filter.on('change', function () {
        dt.ajax.reload();
    });

    window.bookmeProSidePanelLoaded = function ($edit_form) {

    };
});