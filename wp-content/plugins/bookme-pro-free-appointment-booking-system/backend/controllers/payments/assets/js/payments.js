jQuery(function($) {
    var
        $payments_list    = $('#bookme-pro-payments-list'),
        $check_all_button = $('#bookme-pro-check-all'),
        $date_filter      = $('#bookme-pro-filter-date'),
        $type_filter      = $('#bookme-pro-filter-type'),
        $staff_filter     = $('#bookme-pro-filter-staff'),
        $service_filter   = $('#bookme-pro-filter-service'),
        $payment_total    = $('#bookme-pro-payment-total'),
        $delete_button    = $('#bookme-pro-delete')
        ;
    $('.bookme-pro-js-select')
        .val(null)
        .on('select2:unselecting', function(e) {
            e.preventDefault();
            $(this).val(null).trigger('change');
        })
        .select2({
            allowClear: true,
            theme: 'bootstrap',
            language: {
                noResults: function() { return BookmeProL10n.no_result_found; }
            }
        });
    /**
     * Init DataTables.
     */
    var dt = $payments_list.DataTable({
        order: [[ 0, 'asc' ]],
        paging: false,
        info: false,
        searching: false,
        processing: true,
        responsive: true,
        serverSide: true,
        ajax: {
            url: ajaxurl,
            type: 'POST',
            data: function ( d ) {
                return $.extend( {}, d, {
                    action: 'bookme_pro_get_payments',
                    csrf_token: BookmeProL10n.csrf_token,
                    filter: {
                        created: $date_filter.data('date'),
                        type:    $type_filter.val(),
                        staff:   $staff_filter.val(),
                        service: $service_filter.val()
                    }
                } );
            },
            dataSrc: function (json) {
                $payment_total.html(json.total);

                return json.data;
            }
        },
        columns: [
            { data: 'created' },
            { data: 'type' },
            { data: 'customer', render: $.fn.dataTable.render.text() },
            { data: 'provider' },
            { data: 'service' },
            { data: 'start_date' },
            { data: 'paid' },
            { data: 'status' },
            {
                responsivePriority: 1,
                orderable: false,
                searchable: false,
                render: function ( data, type, row, meta ) {
                    return '<button type="button" class="btn btn-default" id="bookme-pro-payment-dialog-show" data-payment_id="' + row.id + '"><i class="glyphicon glyphicon-list-alt"></i> ' + BookmeProL10n.details + '</a>';
                }
            },
            {
                responsivePriority: 1,
                orderable: false,
                searchable: false,
                render: function ( data, type, row, meta ) {
                    return '<input type="checkbox" value="' + row.id + '">';
                }
            }
        ],
        language: {
            zeroRecords: BookmeProL10n.zeroRecords,
            processing:  BookmeProL10n.processing
        }
    });

    /**
     * Select all coupons.
     */
    $check_all_button.on('change', function () {
        $payments_list.find('tbody input:checkbox').prop('checked', this.checked);
    });

    /**
     * On coupon select.
     */
    $payments_list.on('change', 'tbody input:checkbox', function () {
        $check_all_button.prop('checked', $payments_list.find('tbody input:not(:checked)').length == 0);
    });
    /**
     * Init date range picker.
     */
    moment.locale('en', {
        months:        BookmeProL10n.calendar.longMonths,
        monthsShort:   BookmeProL10n.calendar.shortMonths,
        weekdays:      BookmeProL10n.calendar.longDays,
        weekdaysShort: BookmeProL10n.calendar.shortDays,
        weekdaysMin:   BookmeProL10n.calendar.shortDays
    });

    var picker_ranges = {};
    picker_ranges[BookmeProL10n.yesterday]  = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
    picker_ranges[BookmeProL10n.today]      = [moment(), moment()];
    picker_ranges[BookmeProL10n.last_7]     = [moment().subtract(7, 'days'), moment()];
    picker_ranges[BookmeProL10n.last_30]    = [moment().subtract(30, 'days'), moment()];
    picker_ranges[BookmeProL10n.this_month] = [moment().startOf('month'), moment().endOf('month')];
    picker_ranges[BookmeProL10n.last_month] = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')];

    $date_filter.daterangepicker(
        {
            parentEl: $date_filter.parent(),
            startDate: moment().subtract(30, 'days'), // by default selected is "Last 30 days"
            ranges: picker_ranges,
            locale: {
                applyLabel:  BookmeProL10n.apply,
                cancelLabel: BookmeProL10n.cancel,
                fromLabel:   BookmeProL10n.from,
                toLabel:     BookmeProL10n.to,
                customRangeLabel: BookmeProL10n.custom_range,
                daysOfWeek:  BookmeProL10n.calendar.shortDays,
                monthNames:  BookmeProL10n.calendar.longMonths,
                firstDay:    parseInt(BookmeProL10n.startOfWeek),
                format:      BookmeProL10n.mjsDateFormat
            }
        },
        function(start, end) {
            var format = 'YYYY-MM-DD';
            $date_filter
                .data('date', start.format(format) + ' - ' + end.format(format))
                .find('span')
                .html(start.format(BookmeProL10n.mjsDateFormat) + ' - ' + end.format(BookmeProL10n.mjsDateFormat));
        }
    );


    $date_filter.on('apply.daterangepicker', function () { dt.ajax.reload(); });
    $type_filter.on('change', function () { dt.ajax.reload(); });
    $staff_filter.on('change', function () { dt.ajax.reload(); });
    $service_filter.on('change', function () { dt.ajax.reload(); });

    /**
     * Delete payments.
     */
    $delete_button.on('click', function () {
        if (confirm(BookmeProL10n.are_you_sure)) {
            var ladda = Ladda.create(this);
            ladda.start();

            var data = [];
            var $checkboxes = $payments_list.find('tbody input:checked');
            $checkboxes.each(function () {
                data.push(this.value);
            });

            $.ajax({
                url  : ajaxurl,
                type : 'POST',
                data : {
                    action : 'bookme_pro_delete_payments',
                    csrf_token : BookmeProL10n.csrf_token,
                    data : data
                },
                dataType : 'json',
                success  : function(response) {
                    if (response.success) {
                        dt.rows($checkboxes.closest('td')).remove().draw();
                    } else {
                        alert(response.data.message);
                    }
                    ladda.stop();
                }
            });
        }
    });
});

(function() {
    var module = angular.module('paymentDetails', ['paymentDetailsDialog']);
    module.controller('paymentDetailsCtrl', function($scope) {});
})();