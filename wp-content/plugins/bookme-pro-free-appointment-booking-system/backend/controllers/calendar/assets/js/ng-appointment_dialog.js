;(function () {
    var module = angular.module('appointmentDialog', ['ui.date', 'customerDialog', 'paymentDetailsDialog']);

    /**
     * DataSource service.
     */
    module.factory('dataSource', function ($q, $rootScope, $filter) {
        var ds = {
            loaded: false,
            data: {
                staff: [],
                customers: [],
                start_time: [],
                end_time: [],
                time_interval: 900,
                status: {
                    items: [],
                    default: null
                }
            },
            form: {
                screen: null,
                id: null,
                staff: null,
                staff_any: false,
                service: null,
                location: null,
                date: null,
                repeat: {
                    enabled: null,
                    repeat: null,
                    daily: {every: null},
                    weekly: {on: null},
                    biweekly: {on: null},
                    monthly: {on: null, day: null, weekday: null},
                    until: null
                },
                schedule: {
                    items: [],
                    edit: null,
                    page: null,
                    another_time: []
                },
                start_time: null,
                end_time: null,
                customers: [],
                notification: null,
                series_id: null,
                expand_customers_list: false
            },
            l10n: {
                staff_any: BookmeProL10nAppDialog.staff_any
            },
            loadData: function () {
                var deferred = $q.defer();
                if (!ds.loaded) {
                    jQuery.get(
                        ajaxurl,
                        {
                            action: 'bookme_pro_get_data_for_appointment_form',
                            csrf_token: BookmeProL10nAppDialog.csrf_token
                        },
                        function (data) {
                            ds.loaded = true;
                            ds.data = data;

                            if (data.staff.length) {
                                ds.form.staff = data.staff[0];
                            }
                            ds.form.start_time = data.start_time[0];
                            ds.form.end_time = data.end_time[1];
                            deferred.resolve();
                        },
                        'json'
                    );
                } else {
                    deferred.resolve();
                }

                return deferred.promise;
            },
            findStaff: function (id) {
                var result = null;
                jQuery.each(ds.data.staff, function (key, item) {
                    if (item.id == id) {
                        result = item;
                        return false;
                    }
                });
                return result;
            },
            findService: function (staff_id, id) {
                var result = null,
                    staff = ds.findStaff(staff_id);

                if (staff !== null) {
                    jQuery.each(staff.services, function (key, item) {
                        if (item.id == id) {
                            result = item;
                            return false;
                        }
                    });
                }
                return result;
            },
            findLocation: function (staff_id, id) {
                var result = null,
                    staff = ds.findStaff(staff_id);

                if (staff !== null) {
                    jQuery.each(staff.locations, function (key, item) {
                        if (item.id == id) {
                            result = item;
                            return false;
                        }
                    });
                }
                return result;
            },
            findTime: function (source, date) {
                var result = null,
                    value_to_find = $filter('date')(date, 'HH:mm'),
                    time = source == 'start' ? ds.data.start_time : ds.data.end_time;

                jQuery.each(time, function (key, item) {
                    if (item.value >= value_to_find) {
                        result = item;
                        return false;
                    }
                });
                return result;
            },
            findCustomer: function (id) {
                var result = null;
                jQuery.each(ds.data.customers, function (key, item) {
                    if (item.id == id) {
                        result = item;
                        return false;
                    }
                });
                return result;
            },
            resetCustomers: function () {
                ds.data.customers.forEach(function (customer) {
                    customer.custom_fields = [];
                    customer.extras = [];
                    customer.status = ds.data.status.default;
                    customer.number_of_persons = 1;
                    customer.compound_token = null;
                    customer.payment_id = null;
                    customer.payment_type = null;
                    customer.payment_title = null;
                    customer.package_id = null;
                });
            },
            getDataForEndTime: function () {
                var result = [];
                if (ds.form.start_time) {
                    var start_time = ds.form.start_time.value.split(':'),
                        end = (24 + parseInt(start_time[0])) + ':' + start_time[1];
                    jQuery.each(ds.data.end_time, function (key, item) {
                        if (item.value > end) {
                            return false;
                        }
                        if (item.value > ds.form.start_time.value) {
                            result.push(item);
                        }
                    });
                }
                return result;
            },
            setEndTimeBasedOnService: function () {
                var i = jQuery.inArray(ds.form.start_time, ds.data.start_time),
                    d = ds.form.service ? ds.form.service.duration : ds.data.time_interval;
                if (d < 86400) {
                    if (i !== -1) {
                        for (; i < ds.data.end_time.length; ++i) {
                            d -= ds.data.time_interval;
                            if (d < 0) {
                                break;
                            }
                        }
                        ds.form.end_time = ds.data.end_time[i];
                    }
                }
            },
            getStartAndEndDates: function () {
                var start_date = moment(ds.form.date.getTime()),
                    end_date = moment(ds.form.date.getTime()),
                    start_time = [0, 0],
                    end_time = [0, 0]
                    ;
                if (ds.form.service && ds.form.service.duration >= 86400) {
                    end_date.add(ds.form.service.duration, 'seconds');
                } else {
                    start_time = ds.form.start_time.value.split(':');
                    end_time = ds.form.end_time.value.split(':');
                }
                start_date.hours(start_time[0]);
                start_date.minutes(start_time[1]);
                end_date.hours(end_time[0]);
                end_date.minutes(end_time[1]);

                return {
                    start_date: start_date.format('YYYY-MM-DD HH:mm:00'),
                    end_date: end_date.format('YYYY-MM-DD HH:mm:00')
                };
            },
            getTotalNumberOfPersons: function () {
                var result = 0;
                ds.form.customers.forEach(function (item) {
                    result += parseInt(item.number_of_persons);
                });

                return result;
            },
            getTotalNumberOfNotCancelledPersons: function (exceptCustomer) {
                var result = 0;
                ds.form.customers.forEach(function (item) {
                    if ((!exceptCustomer || item.id != exceptCustomer.id) && item.status != 'cancelled' && item.status != 'rejected' && item.status != 'waitlisted') {
                        result += parseInt(item.number_of_persons);
                    }
                });

                return result;
            },
            getTotalNumberOfCancelledPersons: function () {
                var result = 0;
                ds.form.customers.forEach(function (item) {
                    if (item.status == 'cancelled' || item.status == 'rejected' || item.status == 'waitlisted') {
                        result += parseInt(item.number_of_persons);
                    }
                });

                return result;
            }
        };

        return ds;
    });

    /**
     * Controller for 'create/edit appointment' dialog form.
     */
    module.controller('appointmentDialogCtrl', function ($scope, $element, dataSource, $filter) {
        // Set up initial data.
        $scope.$calendar = null;
        // Set up data source.
        $scope.dataSource = dataSource;
        $scope.form = dataSource.form;  // shortcut
        // Error messages.
        $scope.errors = {};
        // Callback to be called after editing appointment.
        var callback = null;

        /**
         * Prepare the form for new event.
         *
         * @param int staff_id
         * @param moment start_date
         * @param function _callback
         */
        $scope.configureNewForm = function (staff_id, start_date, _callback) {
            var weekday = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'][start_date.format('d')],
                staff = dataSource.findStaff(staff_id),
                service = staff && staff.services.length == 1 ? staff.services[0] : null,
                location = staff && staff.locations.length == 1 ? staff.locations[0] : null
                ;
            jQuery.extend($scope.form, {
                screen: 'main',
                id: null,
                staff: staff,
                staff_any: false,
                service: service,
                location: location,
                date: start_date.clone().local().toDate(),
                start_time: dataSource.findTime('start', start_date.format('HH:mm')),
                end_time: null,
                series_id: null,
                repeat: {
                    enabled: 0,
                    repeat: 'daily',
                    daily: {every: 1},
                    weekly: {on: [weekday]},
                    biweekly: {on: [weekday]},
                    monthly: {on: 'day', day: start_date.format('D'), weekday: weekday},
                    until: start_date.clone().add(1, 'month').format('YYYY-MM-DD')
                },
                schedule: {
                    items: [],
                    edit: 0,
                    page: 0,
                    another_time: []
                },
                customers: [],
                internal_note: null,
                expand_customers_list: false
            });
            $scope.errors = {};
            dataSource.setEndTimeBasedOnService();
            callback = _callback;

            $scope.prepareExtras();
            $scope.prepareCustomFields();
            $scope.dataSource.resetCustomers();
        };

        /**
         * Prepare the form for editing an event.
         */
        $scope.configureEditForm = function (appointment_id, _callback) {
            $scope.loading = true;
            jQuery.post(
                ajaxurl,
                {
                    action: 'bookme_pro_get_data_for_appointment',
                    id: appointment_id,
                    csrf_token: BookmeProL10nAppDialog.csrf_token
                },
                function (response) {
                    $scope.$apply(function ($scope) {
                        if (response.success) {
                            var start_date = moment(response.data.start_date),
                                end_date = moment(response.data.end_date),
                                staff = $scope.dataSource.findStaff(response.data.staff_id);
                            jQuery.extend($scope.form, {
                                screen: 'main',
                                id: appointment_id,
                                staff: staff,
                                staff_any: response.data.staff_any ? staff : false,
                                service: $scope.dataSource.findService(response.data.staff_id, response.data.service_id),
                                location: $scope.dataSource.findLocation(response.data.staff_id, response.data.location_id),
                                date: start_date.clone().local().toDate(),
                                start_time: $scope.dataSource.findTime('start', start_date.format('HH:mm')),
                                end_time: start_date.format('YYYY-MM-DD') == end_date.format('YYYY-MM-DD')
                                    ? $scope.dataSource.findTime('end', end_date.format('HH:mm'))
                                    : $scope.dataSource.findTime('end', (24 + end_date.hour()) + end_date.format(':mm')),
                                repeat: {
                                    enabled: 0,
                                    repeat: 'daily',
                                    daily: {every: 1},
                                    weekly: {on: []},
                                    biweekly: {on: []},
                                    monthly: {on: 'day', day: '1', weekday: 'mon'},
                                    until: start_date.clone().add(1, 'month').format('YYYY-MM-DD')
                                },
                                schedule: {
                                    items: [],
                                    edit: 0,
                                    page: 0,
                                    another_time: []
                                },
                                customers: [],
                                internal_note: response.data.internal_note,
                                series_id: response.data.series_id,
                                expand_customers_list: false
                            });

                            $scope.prepareExtras();
                            $scope.prepareCustomFields();
                            $scope.dataSource.resetCustomers();

                            var customers_ids = [];
                            response.data.customers.forEach(function (item, i, arr) {
                                var customer = $scope.dataSource.findCustomer(item.id),
                                    clone = {};
                                if (customers_ids.indexOf(item.id) === -1) {
                                    customers_ids.push(item.id);
                                    clone = customer;
                                } else {
                                    // For Error: ngRepeat:dupes & chosen directive
                                    angular.copy(customer, clone);
                                }
                                clone.ca_id = item.ca_id;
                                clone.package_id = item.package_id;
                                clone.extras = item.extras;
                                clone.status = item.status;
                                clone.custom_fields = item.custom_fields;
                                clone.number_of_persons = item.number_of_persons;
                                clone.payment_id = item.payment_id;
                                clone.payment_type = item.payment_type;
                                clone.payment_title = item.payment_title;
                                clone.compound_token = item.compound_token;
                                clone.compound_service = item.compound_service;
                                $scope.form.customers.push(clone);
                            });
                        }
                        $scope.loading = false;
                    });
                },
                'json'
            );
            $scope.errors = {};
            callback = _callback;
        };

        var checkAppointmentErrors = function () {
            if ($scope.form.staff) {
                var dates = $scope.dataSource.getStartAndEndDates(),
                    customers = [];

                $scope.form.customers.forEach(function (item, i, arr) {
                    var customer_extras = {};
                    if ($scope.form.service) {
                        jQuery('#bookme-pro-extras .service_' + $scope.form.service.id + ' input.extras-count').each(function () {
                            var extra_id = jQuery(this).data('id');
                            if (item.extras[extra_id] !== undefined) {
                                customer_extras[extra_id] = item.extras[extra_id];
                            }
                        });
                    }
                    customers.push({
                        id: item.id,
                        ca_id: item.ca_id,
                        custom_fields: item.custom_fields,
                        extras: customer_extras,
                        number_of_persons: item.number_of_persons,
                        status: item.status
                    });
                });

                jQuery.post(
                    ajaxurl,
                    {
                        action: 'bookme_pro_check_appointment_errors',
                        csrf_token: BookmeProL10nAppDialog.csrf_token,
                        start_date: dates.start_date,
                        end_date: dates.end_date,
                        appointment_id: $scope.form.id,
                        customers: JSON.stringify(customers),
                        staff_id: $scope.form.staff.id,
                        service_id: $scope.form.service ? $scope.form.service.id : null
                    },
                    function (response) {
                        $scope.$apply(function ($scope) {
                            angular.forEach(response, function (value, error) {
                                $scope.errors[error] = value;
                            });
                        });
                    },
                    'json'
                );
            }
        };

        $scope.onServiceChange = function () {
            $scope.dataSource.setEndTimeBasedOnService();
            $scope.prepareExtras();
            $scope.prepareCustomFields();
            checkAppointmentErrors();
        };

        $scope.onStaffChange = function () {
            if ($scope.form.staff.services.length == 1) {
                $scope.form.service = $scope.form.staff.services[0];
                $scope.onServiceChange();
            } else {
                $scope.form.service = null;
            }
            $scope.form.location = $scope.form.staff.locations.length == 1 ? $scope.form.staff.locations[0] : null;
        };

        $scope.onStartTimeChange = function () {
            $scope.dataSource.setEndTimeBasedOnService();
            checkAppointmentErrors();
        };

        $scope.onEndTimeChange = function () {
            checkAppointmentErrors();
        };

        $scope.onDateChange = function () {
            checkAppointmentErrors();
        };

        $scope.onCustomersChange = function (old_length, old_nop) {
            if (dataSource.form.service && dataSource.form.customers.length > old_length) {
                var nop = dataSource.form.service.capacity_min - old_nop;
                dataSource.form.customers[dataSource.form.customers.length - 1].number_of_persons = nop > 0 ? nop : 1;
            }
            $scope.errors.customers_appointments_limit = [];
            checkAppointmentErrors();
        };

        $scope.processForm = function () {
            $scope.loading = true;

            $scope.errors = {};

            var dates = $scope.dataSource.getStartAndEndDates(),
                schedule = [],
                customers = []
                ;

            angular.forEach($scope.form.schedule.items, function (item) {
                if (!item.deleted) {
                    schedule.push(item.slots);
                }
            });

            $scope.form.customers.forEach(function (item, i, arr) {
                var customer_extras = {};
                if ($scope.form.service) {
                    jQuery('#bookme-pro-extras .service_' + $scope.form.service.id + ' input.extras-count').each(function () {
                        var extra_id = jQuery(this).data('id');
                        if (item.extras[extra_id] !== undefined) {
                            customer_extras[extra_id] = item.extras[extra_id];
                        }
                    });
                }
                customers.push({
                    id: item.id,
                    ca_id: item.ca_id,
                    custom_fields: item.custom_fields,
                    extras: customer_extras,
                    number_of_persons: item.number_of_persons,
                    status: item.status
                });
            });
            jQuery.post(
                ajaxurl,
                {
                    action: 'bookme_pro_save_appointment_form',
                    csrf_token: BookmeProL10nAppDialog.csrf_token,
                    id: $scope.form.id,
                    staff_id: $scope.form.staff ? $scope.form.staff.id : null,
                    service_id: $scope.form.service ? $scope.form.service.id : null,
                    location_id: $scope.form.location ? $scope.form.location.id : null,
                    start_date: dates.start_date,
                    end_date: dates.end_date,
                    repeat: JSON.stringify($scope.form.repeat),
                    schedule: schedule,
                    customers: JSON.stringify(customers),
                    notification: $scope.form.notification,
                    internal_note: $scope.form.internal_note,
                    created_from: typeof BookmeProSCCalendarL10n !== 'undefined' ? 'staff-cabinet' : 'backend'
                },
                function (response) {
                    $scope.$apply(function ($scope) {
                        if (response.success) {
                            if (callback) {
                                // Call callback.
                                callback(response.data);
                            }
                            // Close the dialog.
                            $element.children().modal('hide');
                        } else {
                            $scope.errors = response.errors;
                        }
                        $scope.loading = false;
                    });
                },
                'json'
            );
        };

        // On 'Cancel' button click.
        $scope.closeDialog = function () {
            // Close the dialog.
            $element.children().modal('hide');
        };

        $scope.statusToString = function (status) {
            return dataSource.data.status.items[status];
        };

        /**************************************************************************************************************
         * New customer                                                                                               *
         **************************************************************************************************************/

        /**
         * Create new customer.
         * @param customer
         */
        $scope.createCustomer = function (customer) {
            // Add new customer to the list.
            var nop = 1;
            if (dataSource.form.service) {
                nop = dataSource.form.service.capacity_min - dataSource.getTotalNumberOfNotCancelledPersons();
                if (nop < 1) {
                    nop = 1;
                }
            }
            var new_customer = {
                id: customer.id.toString(),
                name: customer.full_name,
                custom_fields: customer.custom_fields,
                extras: customer.extras,
                status: customer.status,
                number_of_persons: nop,
                compound_token: null,
                payment_id: null,
                payment_type: null,
                payment_title: null
            };

            if (customer.email || customer.phone) {
                new_customer.name += ' (' + [customer.email, customer.phone].filter(Boolean).join(', ') + ')';
            }

            dataSource.data.customers.push(new_customer);

            // Make it selected.
            if (!dataSource.form.service || dataSource.form.customers.length < dataSource.form.service.capacity_max) {
                dataSource.form.customers.push(new_customer);
            }
        };

        $scope.removeCustomer = function (customer) {
            $scope.form.customers.splice($scope.form.customers.indexOf(customer), 1);
            checkAppointmentErrors();
        };

        $scope.openNewCustomerDialog = function () {
            showSidepanel(jQuery('#bookme-pro-customer-dialog'));
        };

        /**************************************************************************************************************
         * Customer Details                                                                                           *
         **************************************************************************************************************/

        $scope.editCustomerDetails = function (customer) {
            var $dialog = jQuery('#bookme-pro-customer-details-dialog');
            $dialog.find('input.bookme-pro-custom-field:text, textarea.bookme-pro-custom-field, select.bookme-pro-custom-field').val('');
            $dialog.find('input.bookme-pro-custom-field:checkbox, input.bookme-pro-custom-field:radio').prop('checked', false);
            $dialog.find('#bookme-pro-extras :checkbox').prop('checked', false);

            customer.custom_fields.forEach(function (field) {
                var $custom_field = $dialog.find('#bookme-pro-js-custom-fields > *[data-id="' + field.id + '"]');
                switch ($custom_field.data('type')) {
                    case 'checkboxes':
                        field.value.forEach(function (value) {
                            $custom_field.find('.bookme-pro-custom-field').filter(function () {
                                return this.value == value;
                            }).prop('checked', true);
                        });
                        break;
                    case 'radio-buttons':
                        $custom_field.find('.bookme-pro-custom-field').filter(function () {
                            return this.value == field.value;
                        }).prop('checked', true);
                        break;
                    default:
                        $custom_field.find('.bookme-pro-custom-field').val(field.value);
                        break;
                }
            });

            $dialog.find('#bookme-pro-extras .extras-count').val(0);
            angular.forEach(customer.extras, function (extra_count, extra_id) {
                $dialog.find('#bookme-pro-extras .extras-count[data-id="' + extra_id + '"]').val(extra_count);
            });

            // Prepare select for number of persons.
            var $number_of_persons = $dialog.find('#bookme-pro-edit-number-of-persons');

            var max = $scope.form.service
                ? parseInt($scope.form.service.capacity_max) - $scope.dataSource.getTotalNumberOfNotCancelledPersons(customer)
                : 1;
            $number_of_persons.empty();
            for (var i = 1; i <= max; ++i) {
                $number_of_persons.append('<option value="' + i + '">' + i + '</option>');
            }
            if (customer.number_of_persons > max) {
                $number_of_persons.append('<option value="' + customer.number_of_persons + '">' + customer.number_of_persons + '</option>');
            }
            $number_of_persons.val(customer.number_of_persons);
            $dialog.find('#bookme-pro-appointment-status').val(customer.status);
            $dialog.find('#bookme-pro-deposit-due').val(customer.due);
            $scope.edit_customer = customer;

            showSidepanel($dialog);
            $dialog.on('sidePanel.hide', function () {
                    jQuery('body').css('overflow-y', 'hidden');
                });
        };

        $scope.prepareExtras = function () {
            if ($scope.form.service) {
                jQuery('#bookme-pro-extras > *').hide();
                var $service_extras = jQuery('#bookme-pro-extras .service_' + $scope.form.service.id);
                if ($service_extras.length) {
                    $service_extras.show();
                    jQuery('#bookme-pro-extras').show();
                } else {
                    jQuery('#bookme-pro-extras').hide();
                }
            } else {
                jQuery('#bookme-pro-extras').hide();
            }
        };

        // Hide or unhide custom fields for current service
        $scope.prepareCustomFields = function () {
            if (BookmeProL10nAppDialog.cf_per_service == 1) {
                var show = false;
                jQuery('#bookme-pro-js-custom-fields div[data-services]').each(function () {
                    var $this = jQuery(this);
                    if (dataSource.form.service !== null) {
                        var services = $this.data('services');
                        if (services && jQuery.inArray(dataSource.form.service.id, services) > -1) {
                            $this.show();
                            show = true;
                        } else {
                            $this.hide();
                        }
                    } else {
                        $this.hide();
                    }
                });
                if (show) {
                    jQuery('#bookme-pro-js-custom-fields').show();
                } else {
                    jQuery('#bookme-pro-js-custom-fields').hide();
                }
            }
        };

        $scope.saveCustomFields = function () {
            var result = [],
                extras = {},
                $fields = jQuery('#bookme-pro-js-custom-fields > *'),
                $number_of_persons = jQuery('#bookme-pro-customer-details-dialog #bookme-pro-edit-number-of-persons'),
                $extras = jQuery('#bookme-pro-extras')
                ;

            $fields.each(function () {
                var $this = jQuery(this),
                    value;
                if ($this.is(':visible')) {
                    switch ($this.data('type')) {
                        case 'checkboxes':
                            value = [];
                            $this.find('.bookme-pro-custom-field:checked').each(function () {
                                value.push(this.value);
                            });
                            break;
                        case 'radio-buttons':
                            value = $this.find('.bookme-pro-custom-field:checked').val();
                            break;
                        default:
                            value = $this.find('.bookme-pro-custom-field').val();
                            break;
                    }
                    result.push({id: $this.data('id'), value: value});
                }
            });

            if ($scope.form.service) {
                $extras.find(' .service_' + $scope.form.service.id + ' input.extras-count').each(function () {
                    if (this.value > 0) {
                        extras[jQuery(this).data('id')] = this.value;
                    }
                });
            }

            $scope.edit_customer.custom_fields = result;
            $scope.edit_customer.number_of_persons = $number_of_persons.val();
            $scope.edit_customer.extras = extras;
            $scope.edit_customer.status = jQuery('#bookme-pro-customer-details-dialog #bookme-pro-appointment-status').val();

            hideSidepanel(jQuery('#bookme-pro-customer-details-dialog'));
            if ($extras.length > 0) {
                // Check if intersection with another appointment exists.
                checkTimeInterval();
            }
        };

        /**************************************************************************************************************
         * Payment Details                                                                                            *
         **************************************************************************************************************/

        $scope.completePayment = function (payment_id, payment_title) {
            jQuery.each($scope.dataSource.data.customers, function (key, item) {
                if (item.payment_id == payment_id) {
                    item.payment_type = 'full';
                    item.payment_title = payment_title;
                }
            });
        };

        /**************************************************************************************************************
         * Package Schedule                                                                                           *
         **************************************************************************************************************/

        $scope.editPackageSchedule = function (customer) {
            jQuery(document.body).trigger('bookme_pro_packages.schedule_dialog', [customer.package_id, function (deleted) {
                if (jQuery.inArray(Number(customer.ca_id), deleted) != -1) {
                    $scope.removeCustomer(customer);
                }
                if (callback) {
                    // Call callback.
                    callback('refresh');
                }
            }, true]);
        }

        /**************************************************************************************************************
         * Schedule of Recurring Appointments                                                                         *
         **************************************************************************************************************/

        $scope.schSchedule = function ($event) {
            var extras = [];
            $scope.form.customers.forEach(function (item, i, arr) {
                extras.push(item.extras);
            });

            if (
                ($scope.form.repeat.repeat == 'weekly' || $scope.form.repeat.repeat == 'biweekly') &&
                $scope.form.repeat[$scope.form.repeat.repeat].on.length == 0
            ) {
                $scope.errors.repeat_weekdays_empty = true;
            } else {
                delete $scope.errors.repeat_weekdays_empty;
                var ladda = Ladda.create($event.currentTarget);
                ladda.start();
                var dates = $scope.dataSource.getStartAndEndDates();
                jQuery.post(
                    ajaxurl,
                    {
                        action: 'bookme_pro_recurring_appointments_get_schedule',
                        csrf_token: BookmeProL10nAppDialog.csrf_token,
                        staff_id: $scope.form.staff.id,
                        service_id: $scope.form.service.id,
                        location_id: $scope.form.location ? $scope.form.location.id : null,
                        datetime: dates.start_date,
                        until: $scope.form.repeat.until,
                        repeat: $scope.form.repeat.repeat,
                        params: $scope.form.repeat[$scope.form.repeat.repeat],
                        extras: extras
                    },
                    function (response) {
                        $scope.$apply(function ($scope) {
                            $scope.form.schedule.items = response.data;
                            $scope.form.schedule.page = 0;
                            $scope.form.schedule.another_time = [];
                            angular.forEach($scope.form.schedule.items, function (item) {
                                if (item.another_time) {
                                    var page = parseInt(( item.index - 1 ) / 10) + 1;
                                    if ($scope.form.schedule.another_time.indexOf(page) < 0) {
                                        $scope.form.schedule.another_time.push(page);
                                    }
                                }
                            });
                            $scope.form.screen = 'schedule';
                            ladda.stop();
                        });
                    },
                    'json'
                );
            }
        };
        $scope.schFormatDate = function (date) {
            var m = moment(date),
                weekday = m.format('d'),
                month = m.format('M'),
                day = m.format('DD');

            return BookmeProL10nAppDialog.dateOptions.dayNamesMin[weekday] + ', ' + BookmeProL10nAppDialog.dateOptions.monthNamesShort[month - 1] + ' ' + day;
        };
        $scope.schFormatTime = function (slots, options) {
            for (var i = 0; i < options.length; ++i) {
                if (slots == options[i].value) {
                    return options[i].title;
                }
            }
        };
        $scope.schFirstPage = function () {
            return $scope.form.schedule.page == 0;
        };
        $scope.schLastPage = function () {
            var lastPageNum = Math.ceil($scope.form.schedule.items.length / 10 - 1);
            return $scope.form.schedule.page == lastPageNum;
        };
        $scope.schNumberOfPages = function () {
            return Math.ceil($scope.form.schedule.items.length / 10);
        };
        $scope.schStartingItem = function () {
            return $scope.form.schedule.page * 10;
        };
        $scope.schPageBack = function () {
            $scope.form.schedule.page = $scope.form.schedule.page - 1;
        };
        $scope.schPageForward = function () {
            $scope.form.schedule.page = $scope.form.schedule.page + 1;
        };
        $scope.schOnWeekdayClick = function (weekday) {
            var idx = $scope.form.repeat.weekly.on.indexOf(weekday);

            // is currently selected
            if (idx > -1) {
                $scope.form.repeat.weekly.on.splice(idx, 1);
            }
            // is newly selected
            else {
                $scope.form.repeat.weekly.on.push(weekday);
            }
            // copy weekly to biweekly
            $scope.form.repeat.biweekly.on = $scope.form.repeat.weekly.on.slice();
        };
        $scope.schOnDateChange = function (item) {
            var extras = [];
            $scope.form.customers.forEach(function (item, i, arr) {
                extras.push(item.extras);
            });

            var exclude = [];
            angular.forEach($scope.form.schedule.items, function (_item) {
                if (item.slots != _item.slots && !_item.deleted) {
                    exclude.push(_item.slots);
                }
            });
            jQuery.post(
                ajaxurl,
                {
                    action: 'bookme_pro_recurring_appointments_get_schedule',
                    csrf_token: BookmeProL10nAppDialog.csrf_token,
                    staff_id: $scope.form.staff.id,
                    service_id: $scope.form.service.id,
                    datetime: item.date + ' 00:00',
                    until: item.date,
                    repeat: 'daily',
                    params: {every: 1},
                    with_options: 1,
                    exclude: exclude,
                    extras: extras
                },
                function (response) {
                    $scope.$apply(function ($scope) {
                        if (response.data.length) {
                            item.options = response.data[0].options;
                            var found = false;
                            jQuery.each(item.options, function (key, option) {
                                if (option.value == item.slots) {
                                    found = true;
                                    return false;
                                }
                            });
                            if (!found) {
                                item.slots = item.options[0].value;
                            }
                        } else {
                            item.options = [];
                        }
                    });
                },
                'json'
            );
        };
        $scope.schIsScheduleEmpty = function () {
            return $scope.form.schedule.items.every(function (item) {
                return item.deleted;
            });
        };
        $scope.schDateOptions = jQuery.extend({}, BookmeProL10nAppDialog.dateOptions, {dateFormat: 'D, M dd, yy'});
        $scope.schViewSeries = function () {
            jQuery(document.body).trigger('recurring_appointments.series_dialog', [$scope.form.series_id, function (event) {
                // Switch to the event owner tab.
                jQuery('li[data-staff_id=' + event.staffId + ']').click();
            }]);
        };

        /**
         * Datepicker options.
         */
        $scope.dateOptions = BookmeProL10nAppDialog.dateOptions;

    });

    /**
     * Directive for slide up/down.
     */
    module.directive('mySlideUp', function () {
        return function (scope, element, attrs) {
            element.hide();
            // watch the expression, and update the UI on change.
            scope.$watch(attrs.mySlideUp, function (value) {
                if (value) {
                    element.delay(0).slideDown();
                } else {
                    element.slideUp();
                }
            });
        };
    });

    /**
     * Directive for Popover jQuery plugin.
     */
    module.directive('popover', function () {
        return function (scope, element, attrs) {
            element.popover({
                trigger: 'hover',
                content: function () {
                    return this.getAttribute('popover');
                },
                html: true,
                placement: 'top',
                template: '<div class="popover bookme-pro-font-xs" style="width: 220px" role="tooltip"><div class="popover-arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
            });
        };
    });

    /**
     * Filters for pagination in Schedule.
     */
    module.filter('startFrom', function () {
        return function (input, start) {
            start = +start;
            return input.slice(start);
        }
    });
    module.filter('range', function () {
        return function (input, total) {
            total = parseInt(total);

            for (var i = 1; i <= total; ++i) {
                input.push(i);
            }

            return input;
        };
    });


    jQuery('#bookme-pro-select2').select2({
        width: '100%',
        theme: 'bootstrap',
        allowClear: false,
        language: {
            noResults: function () {
                return BookmeProL10nAppDialog.no_result_found;
            }
        }
    });
})();

/**
 * @param int appointment_id
 * @param int staff_id
 * @param moment start_date
 * @param function callback
 */
var showAppointmentDialog = function (appointment_id, staff_id, start_date, callback) {

    var $dialog = jQuery('#bookme-pro-appointment-dialog');
    var $scope = angular.element($dialog[0]).scope();
    $scope.$apply(function ($scope) {
        $scope.loading = true;
        $dialog.parents('.slidePanel').find('.slidePanel-heading h2')
            .text(appointment_id ? BookmeProL10nAppDialog.title.edit_appointment : BookmeProL10nAppDialog.title.new_appointment);
        // Populate data source.
        $scope.dataSource.loadData().then(function () {
            $scope.loading = false;
            if (appointment_id) {
                $scope.configureEditForm(appointment_id, callback);
            } else {
                $scope.configureNewForm(staff_id, start_date, callback);
            }
        });
    });

    // hide customer details dialog, if it remained opened.
    if (jQuery('#bookme-pro-customer-details-dialog').hasClass('slidePanel-show')) {
        jQuery('#bookme-pro-customer-details-dialog').removeClass('slidePanel-show');
    }

    // hide new customer dialog, if it remained opened.
    if (jQuery('#bookme-pro-customer-dialog').hasClass('slidePanel-show')) {
        jQuery('#bookme-pro-customer-dialog').removeClass('slidePanel-show');
    }

    //$dialog.modal('show');
    showSidepanel(jQuery('#appointmentPanel'));

};