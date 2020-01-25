jQuery(function ($) {
    var Services = function($container, options) {
        var obj  = this;
        jQuery.extend(obj.options, options);
        $container.parents('.slidePanel-inner').css('margin-bottom','82px');

        // Load services form
        if (!$container.children().length) {
            $container.html('<div class="bookme-pro-loading"></div>');
            $.ajax({
                type        : 'POST',
                url         : ajaxurl,
                data        : obj.options.get_staff_services,
                dataType    : 'json',
                xhrFields   : { withCredentials: true },
                crossDomain : 'withCredentials' in new XMLHttpRequest(),
                success     : function (response) {
                    $container.html(response.data.html);

                    var $services_form = $('form', $container);
                    $(document.body).trigger('special_hours.tab_init', [$container, obj.options.get_staff_services.staff_id, obj.options.bookmeProAlert]);
                    var autoTickCheckboxes = function () {
                        // Handle 'select category' checkbox.
                        $('.bookme-pro-services-category .bookme-pro-category-checkbox').each(function () {
                            $(this).prop(
                                'checked',
                                $('.bookme-pro-category-services .bookme-pro-service-checkbox.bookme-pro-category-' + $(this).data('category-id') + ':not(:checked)').length == 0
                            );
                        });
                        // Handle 'select all services' checkbox.
                        $('#bookme-pro-check-all-entities').prop(
                            'checked',
                            $('.bookme-pro-service-checkbox:not(:checked)').length == 0
                        );
                    };
                    var checkCapacityError = function ($form_group) {
                        if (parseInt($form_group.find('.bookme-pro-js-capacity-min').val()) > parseInt($form_group.find('.bookme-pro-js-capacity-max').val())) {
                            $form_group.addClass('has-error');
                        } else {
                            $form_group.removeClass('has-error');
                        }
                        var has_errors = false;
                        $services_form.find('.bookme-pro-js-capacity').closest('.form-group').each(function () {
                            if ($(this).hasClass('has-error')) {
                                has_errors = true;
                            }
                        });
                        if (has_errors) {
                            $services_form.find('.bookme-pro-js-services-error').html(obj.options.l10n.capacity_error);
                            $services_form.find('#bookme-pro-services-save').prop('disabled', true);
                        } else {
                            $services_form.find('.bookme-pro-js-services-error').html('');
                            $services_form.find('#bookme-pro-services-save').prop('disabled', false);
                        }
                    };

                    $services_form
                    // Select all services related to chosen category
                        .on('click', '.bookme-pro-category-checkbox', function () {
                            $('.bookme-pro-category-services .bookme-pro-category-' + $(this).data('category-id')).prop('checked', $(this).is(':checked')).change();
                            autoTickCheckboxes();
                        })
                        // Check and uncheck all services
                        .on('click', '#bookme-pro-check-all-entities', function () {
                            $('.bookme-pro-service-checkbox', $services_form).prop('checked', $(this).is(':checked')).change();
                            $('.bookme-pro-category-checkbox').prop('checked', $(this).is(':checked'));
                        })
                        // Select service
                        .on('click', '.bookme-pro-service-checkbox', function () {
                            autoTickCheckboxes();
                        })
                        // Save services
                        .on('click', '#bookme-pro-services-save', function (e) {
                            e.preventDefault();
                            var ladda = Ladda.create(this);
                            ladda.start();
                            $.ajax({
                                type       : 'POST',
                                url        : ajaxurl,
                                data       : $services_form.serialize(),
                                dataType   : 'json',
                                xhrFields  : {withCredentials: true},
                                crossDomain: 'withCredentials' in new XMLHttpRequest(),
                                success    : function (response) {
                                    ladda.stop();
                                    if (response.success) {
                                        obj.options.bookmeProAlert({success: [obj.options.l10n.saved]});
                                    }
                                }
                            });
                        })
                        // After reset auto tick group checkboxes.
                        .on('click', '#bookme-pro-services-reset', function () {
                            setTimeout(function () {
                                autoTickCheckboxes();
                                $('.bookme-pro-js-capacity-form-group', $services_form).each(function () {
                                    checkCapacityError($(this));
                                });
                                $('.bookme-pro-service-checkbox', $services_form).trigger('change');
                            }, 0);
                        });

                    $('.bookme-pro-service-checkbox').on('change', function () {
                        var $this    = $(this),
                            $service = $this.closest('li'),
                            $inputs  = $service.find('input:not(:checkbox)');

                        $inputs.attr('disabled', !$this.is(':checked'));

                        // Handle package-service connections
                        if ($(this).is(':checked') && $service.data('service-type') == 'package') {
                            $('li[data-service-type="simple"][data-service-id="' + $service.data('sub-service') + '"] .bookme-pro-service-checkbox', $services_form).prop('checked', true).trigger('change');
                            $('.bookme-pro-js-capacity-min', $service).val($('li[data-service-type="simple"][data-service-id="' + $service.data('sub-service') + '"] .bookme-pro-js-capacity-min', $services_form).val());
                            $('.bookme-pro-js-capacity-max', $service).val($('li[data-service-type="simple"][data-service-id="' + $service.data('sub-service') + '"] .bookme-pro-js-capacity-max', $services_form).val());
                        }
                        if (!$(this).is(':checked') && $service.data('service-type') == 'simple') {
                            $('li[data-service-type="package"][data-sub-service="' + $service.data('service-id') + '"] .bookme-pro-service-checkbox', $services_form).prop('checked', false).trigger('change');
                        }
                    });

                    $('.bookme-pro-js-capacity').on('keyup change', function () {
                        var $service = $(this).closest('li');
                        if ($service.data('service-type') == 'simple') {
                            if ($(this).hasClass('bookme-pro-js-capacity-min')) {
                                $('li[data-service-type="package"][data-sub-service="' + $service.data('service-id') + '"] .bookme-pro-js-capacity-min', $services_form).val($(this).val());
                            } else {
                                $('li[data-service-type="package"][data-sub-service="' + $service.data('service-id') + '"] .bookme-pro-js-capacity-max', $services_form).val($(this).val());
                            }
                        }
                        checkCapacityError($(this).closest('.form-group'));
                    });
                    autoTickCheckboxes();
                }
            });
        }

    };

    Services.prototype.options = {
        get_staff_services: {
            action  : 'bookme_pro_get_staff_services',
            staff_id: -1,
            csrf_token: ''
        },
        bookmeProAlert: window.bookmeProAlert,
        l10n: {}
    };

    window.BookmeProStaffServices = Services;
});