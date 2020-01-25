jQuery(function ($) {
    var $no_result = $('#bookme-pro-services-wrapper .no-result');
    // Remember user choice in the modal dialog.
    var update_staff_choice = null,
        $add_new_category = $('#bookme-pro-add-new-category'),
        $new_category_modal = $('#bookme-pro-new-category'),
        $new_category_form = $('#new-category-form'),
        $new_category_name = $('#bookme-pro-category-name');

    $new_category_modal.on('click', function () {
        $add_new_category.modal('show');
    });

    // Save new category.
    $new_category_form.on('submit', function () {
        var data = $(this).serialize();
        var ladda = rangeTools.ladda($new_category_form.find('button[type="submit"]').get(0));
        $.post(ajaxurl, data, function (response) {
            $('#bookme-pro-category-item-list').append(response.data.html);
            var $new_category = $('.bookme-pro-category-item:last');
            // add created category to services
            $('select[name="category_id"]').append('<option value="' + $new_category.data('category-id') + '">' + $new_category.find('input').val() + '</option>');
            $add_new_category.modal('hide');
            $new_category_name.val('');
            ladda.stop();
        });
        return false;
    });

    // Cancel button.
    $new_category_form.on('click', 'button[type="button"]', function (e) {
        $add_new_category.modal('hide');
        $new_category_name.val('');
    });

    // Save category.
    function saveCategory() {
        var $this = $(this),
            $item = $this.closest('.bookme-pro-category-item'),
            field = $this.attr('name'),
            value = $this.val(),
            id = $item.data('category-id'),
            data = {action: 'bookme_pro_update_category', id: id, csrf_token: BookmeProL10n.csrf_token};
        data[field] = value;
        $.post(ajaxurl, data, function (response) {
            // Hide input field.
            $item.find('input').hide();
            $item.find('.displayed-value').show();
            // Show modified category name.
            $item.find('.displayed-value').text(value);
            // update edited category's name for services
            $('select[name="category_id"] option[value="' + id + '"]').text(value);
        });
    }

    // Categories list delegated events.
    $('#bookme-pro-categories-list')

    // On category item click.
        .on('click', '.bookme-pro-category-item', function (e) {
            if ($(e.target).is('.bookme-pro-js-handle')) return;
            $('#bookme-pro-js-services-list').html('<div class="bookme-pro-loading"></div>');
            var $clicked = $(this);

            $.get(ajaxurl, {
                action: 'bookme_pro_get_category_services',
                category_id: $clicked.data('category-id'),
                csrf_token: BookmeProL10n.csrf_token
            }, function (response) {
                if (response.success) {
                    $('.bookme-pro-category-item').not($clicked).removeClass('active');
                    $clicked.addClass('active');
                    $('.bookme-pro-category-title').text($clicked.text());
                    refreshList(response.data);
                }
            });
        })

        // On edit category click.
        .on('click', '.bookme-pro-js-edit', function (e) {
            // Keep category item click from being executed.
            e.stopPropagation();
            // Prevent navigating to '#'.
            e.preventDefault();
            var $this = $(this).closest('.bookme-pro-category-item');
            $this.find('.displayed-value').hide();
            $this.find('input').show().focus();
        })

        // On blur save changes.
        .on('blur', 'input', saveCategory)

        // On press Enter save changes.
        .on('keypress', 'input', function (e) {
            var code = e.keyCode || e.which;
            if (code == 13) {
                saveCategory.apply(this);
            }
        })

        // On delete category click.
        .on('click', '.bookme-pro-js-delete', function (e) {
            // Keep category item click from being executed.
            e.stopPropagation();
            // Prevent navigating to '#'.
            e.preventDefault();
            // Ask user if he is sure.
            if (confirm(BookmeProL10n.are_you_sure)) {
                var $item = $(this).closest('.bookme-pro-category-item');
                var data = {
                    action: 'bookme_pro_delete_category',
                    id: $item.data('category-id'),
                    csrf_token: BookmeProL10n.csrf_token
                };
                $.post(ajaxurl, data, function (response) {
                    // Remove category item from Services
                    $('select[name="category_id"] option[value="' + $item.data('category-id') + '"]').remove();
                    // Remove category item from DOM.
                    $item.remove();
                    if ($item.is('.active')) {
                        $('.bookme-pro-js-all-services').click();
                    }
                });
            }
        })

        .on('click', 'input', function (e) {
            e.stopPropagation();
        });

    // Services list delegated events.
    $('#bookme-pro-services-wrapper')
    // On click on 'Delete' button.
        .on('click', '#bookme-pro-delete', function (e) {
            if (confirm(BookmeProL10n.are_you_sure)) {
                var ladda = rangeTools.ladda(this);

                var $for_delete = $('.service-checker:checked'),
                    data = {action: 'bookme_pro_remove_services', csrf_token: BookmeProL10n.csrf_token},
                    services = [],
                    $panels = [];

                $for_delete.each(function () {
                    var panel = $(this).parents('.bookme-pro-js-collapse');
                    $panels.push(panel);
                    services.push(this.value);
                    if (panel.find('.bookme-pro-js-service-type input[name="type"]:checked').val() == 'simple') {
                        $('#services_list .bookme-pro-js-collapse').each(function () {
                            if ($(this).find('.bookme-pro-js-service-type input[name="type"]:checked').val() == 'package' && $(this).find('.bookme-pro-js-package-sub-service option:selected').val() == panel.data('service-id')) {
                                $panels.push($(this));
                            }
                        });
                    }
                });
                data['service_ids[]'] = services;
                $.post(ajaxurl, data, function (response) {
                    if (response.success) {
                        ladda.stop();
                        $.each($panels.reverse(), function (index) {
                            $(this).delay(500 * index).fadeOut(200, function () {
                                $(this).remove();
                            });
                        });
                        $(document.body).trigger('service.deleted', [services]);
                    }
                });
            }
        });

    // Modal window events.
    var $modal = $('#bookme-pro-update-service-settings');
    $modal
        .on('click', '.bookme-pro-yes', function () {
            $modal.modal('hide');
            if ($('#bookme-pro-remember-my-choice').prop('checked')) {
                update_staff_choice = true;
            }
            submitServiceFrom($modal.data('input'), true);
        })
        .on('click', '.bookme-pro-no', function () {
            if ($('#bookme-pro-remember-my-choice').prop('checked')) {
                update_staff_choice = false;
            }
            submitServiceFrom($modal.data('input'), false);
        });

    function refreshList(response, service_id) {
        var $list = $('#bookme-pro-js-services-list');
        $list.html(response);
        if (response.indexOf('panel') >= 0) {
            $no_result.hide();
            $list.bookmeProHelp();
        } else {
            $no_result.show();
        }
        makeSortable();
    }

    function initColorPicker($jquery_collection) {
        $jquery_collection.each(function () {
            $(this).data('last-color', $(this).val());
        });
        $jquery_collection.wpColorPicker({
            width: 250
        });
    }

    function submitServiceFrom($form, update_staff) {
        $form.find('input[name=update_staff]').val(update_staff ? 1 : 0);
        $form.find('input[name=package_service_changed]').val($form.find('[name=package_service]').data('last_value') != $form.find('[name=package_service]').val() ? 1 : 0);
        var ladda = rangeTools.ladda($('button.ajax-service-send').get(0)),
            data = $form.serializeArray();
        $(document.body).trigger('service.submitForm', [$form, data]);
        $.post(ajaxurl, data, function (response) {
            if (response.success) {
                var $panel = $form.parents('.bookme-pro-js-collapse'),
                    $price = $form.find('[name=price]'),
                    $capacity_min = $form.find('[name=capacity_min]'),
                    $capacity_max = $form.find('[name=capacity_max]'),
                    $package_service = $form.find('[name=package_service]'),
                    $id = $form.find('[name=id]');
                $price.data('last_value', $price.val());
                $capacity_min.data('last_value', $capacity_min.val());
                $capacity_max.data('last_value', $capacity_max.val());
                $package_service.data('last_value', $package_service.val());
                $id.val(response.data.service_id);
                bookmeProAlert(response.data.alert);
                $('.bookme-pro-category-item.active').trigger('click');
            } else {
                bookmeProAlert({error: [response.data.message]});
            }
        }, 'json').always(function () {
            ladda.stop();
        });
    }

    function checkCapacityError($panel) {
        if (parseInt($panel.find('[name="capacity_min"]').val()) > parseInt($panel.find('[name="capacity_max"]').val())) {
            $panel.find('form .bookme-pro-js-services-error').html(BookmeProL10n.capacity_error);
            $panel.find('[name="capacity_min"]').closest('.form-group').addClass('has-error');
            $panel.find('form .ajax-service-send').prop('disabled', true);
        } else {
            $panel.find('form .bookme-pro-js-services-error').html('');
            $panel.find('[name="capacity_min"]').closest('.form-group').removeClass('has-error');
            $panel.find('form .ajax-service-send').prop('disabled', false);
        }
    }

    var $category = $('#bookme-pro-category-item-list');
    $category.sortable({
        axis: 'y',
        handle: '.bookme-pro-js-handle',
        update: function (event, ui) {
            var data = [];
            $category.children('li').each(function () {
                var $this = $(this);
                var position = $this.data('category-id');
                data.push(position);
            });
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {action: 'bookme_pro_update_category_position', position: data, csrf_token: BookmeProL10n.csrf_token}
            });
        }
    });

    function makeSortable() {
        if ($('.bookme-pro-js-all-services').hasClass('active')) {
            var $services = $('#services_list');
            $services.sortable({
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
                    $services.children('tr').each(function () {
                        data.push($(this).data('service-id'));
                    });
                    $.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: {
                            action: 'bookme_pro_update_services_position',
                            position: data,
                            csrf_token: BookmeProL10n.csrf_token
                        }
                    });
                }
            });
        } else {
            $('#services_list .bookme-pro-js-handle').hide();
        }
    }


    makeSortable();

    // call when sidepanel is loaded
    window.bookmeProSidePanelLoaded = function ($panel) {
        $panel.bookmeProHelp();
        initColorPicker($('.bookme-pro-js-color-picker', $panel));

        var $staff_preference = $panel.find('[name=staff_preference]'),
            $staff_list = $panel.find('.bookme-pro-staff-list'),
            $staff_member = $panel.find('#bookme-pro-js-staff-selector'),
            $staff_box = $panel.find('.bookme-pro-preference-box');

        $staff_member.multiselect({
            texts: {
                placeholder: $staff_member.data('placeholder'), // text to use in dummy input
                selectedOptions: ' ' + $staff_member.data('selected'),      // selected suffix text
                selectAll: $staff_member.data('selectall'),     // select all text
                unselectAll: $staff_member.data('unselectall'),   // unselect all text
                noneSelected: $staff_member.data('nothing'),   // None selected text
                allSelected: $staff_member.data('allselected')
            },
            showCheckbox: false,  // display the checkbox to the user
            selectAll: true, // add select all option
            minHeight: 20,
            maxPlaceholderOpts: 1
        });

        $staff_preference.on('change', function () {
            /** @see Service::PREFERRED_ORDER */
            if ($(this).val() == 'order' && $staff_list.html() == '') {
                var $staff_ids = $staff_preference.data('default'),
                    $draggable = $('<div class="bookme-pro-flex-cell"><i class="bookme-pro-js-handle bookme-pro-margin-right-sm bookme-pro-icon bookme-pro-icon-draghandle bookme-pro-cursor-move" title="' + BookmeProL10n.reorder + '"></i><input type="hidden" name="positions[]"></div>');

                $staff_ids.forEach(function (staff_id) {
                    $staff_list.append($draggable.clone().find('input').val(staff_id).end().append(BookmeProL10n.staff[staff_id]));
                });
                Object.keys(BookmeProL10n.staff).forEach(function (staff_id) {
                    staff_id = parseInt(staff_id);
                    if ($staff_ids.indexOf(staff_id) == -1) {
                        $staff_list.append($draggable.clone().find('input').val(staff_id).end().append(BookmeProL10n.staff[staff_id]));
                    }
                });
            }
            $staff_box.toggle($(this).val() == 'order');
        }).trigger('change');

        $('[data-toggle="popover"]').popover({
            html: true,
            placement: 'top',
            trigger: 'hover',
            template: '<div class="popover bookme-pro-font-xs" style="width: 220px" role="tooltip"><div class="popover-arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
        });

        $panel
            .find('[name=duration]').on('change', function () {
            $panel.find('[name=start_time_info]').closest('.form-group').toggle($(this).val() >= 86400);
        }).trigger('change');

        $panel
            .find('.bookme-pro-js-capacity').on('keyup change', function () {
            checkCapacityError($(this).parents('.bookme-pro-js-collapse'));
        });

        $panel
            .find('.ajax-service-send').on('click', function (e) {
            e.preventDefault();
            var $form = $panel.find('form'),
                show_modal = false;
            if ($form.find('input[name=id]').val()) {
                if (update_staff_choice === null) {
                    $('.bookme-pro-question', $form).each(function () {
                        if ($(this).data('last_value') != this.value) {
                            show_modal = true;
                        }
                    });
                }
            }
            if (show_modal) {
                $modal.data('input', $form).modal('show');
            } else {
                submitServiceFrom($form, update_staff_choice);
            }
        });

        $panel
            .find('#bookme-pro-delete').on('click', function (e) {
            e.preventDefault();
            if (confirm(BookmeProL10n.are_you_sure)) {
                var ladda = rangeTools.ladda(this),
                    id = $panel.find('input[name=id]').val(),
                    data = {action: 'bookme_pro_remove_services', csrf_token: BookmeProL10n.csrf_token};

                data['service_ids[]'] = id;
                $.post(ajaxurl, data, function (response) {
                    if (response.success) {
                        ladda.stop();
                        $.slidePanel.hide();
                        $('[data-service-id=' + id + ']').fadeOut(200, function () {
                            $(this).remove();
                        });
                        $(document.body).trigger('service.deleted', [id]);
                    }
                });
            }
        });

        $panel
            .find('.bookme-pro-question').each(function () {
            $(this).data('last_value', this.value);
        });

        $panel.on('change', 'input.bookme-pro-check-all-entities, input.bookme-pro-js-check-entity', function () {
            var $container = $(this).parents('.form-group');
            if ($(this).hasClass('bookme-pro-check-all-entities')) {
                $container.find('.bookme-pro-js-check-entity').prop('checked', $(this).prop('checked'));
            } else {
                $container.find('.bookme-pro-check-all-entities').prop('checked', $container.find('.bookme-pro-js-check-entity:not(:checked)').length == 0);
            }
            var $form = $(this).closest('.bookme-pro-js-collapse'),
                service_id = $form.data('service-id'),
                service_type = $form.find('.bookme-pro-js-service-type input[name="type"]:checked').val(),
                staff_index = $(this).closest('li').index() + 1;
            if (service_type == 'simple' && !$(this).is(':checked')) {
                $('#services_list .bookme-pro-js-collapse').each(function () {
                    if ($(this).find('.bookme-pro-js-service-type input[name="type"]:checked').val() == 'package' && $(this).find('.bookme-pro-js-package-sub-service option:selected').val() == service_id) {
                        $(this).find('.bookme-pro-entity-selector li:nth-child(' + staff_index + ') input').prop('checked', false).trigger('change');
                    }
                });
            } else if (service_type == 'package' && $(this).is(':checked')) {
                var sub_service_id = $form.find('.bookme-pro-js-package-sub-service option:selected').val();
                $('#services_list .bookme-pro-js-collapse').each(function () {
                    if ($(this).find('.bookme-pro-js-service-type input[name="type"]:checked').val() == 'simple' && $(this).data('service-id') == sub_service_id) {
                        $(this).find('.bookme-pro-entity-selector li:nth-child(' + staff_index + ') input').prop('checked', true).trigger('change');
                    }
                });
            }
        });

        $staff_list.sortable({
            axis: 'y',
            handle: '.bookme-pro-js-handle',
            update: function () {
                var positions = [];
                $('[name="positions[]"]', $(this)).each(function () {
                    positions.push(this.value);
                });

                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: 'bookme_pro_update_service_staff_preference_orders',
                        service_id: $(this).data('service_id'),
                        positions: positions,
                        csrf_token: BookmeProL10n.csrf_token
                    }
                });
            }
        });

        if ($('input[name=type]', $panel).length > 1) {
            $('.bookme-pro-js-service-type', $panel).show();
            $('input[name=type]', $panel).on('change', function () {
                $panel.closest('.panel').find('.bookme-pro-js-service').hide();
                $panel.closest('.panel').find('.bookme-pro-js-service-' + this.value).show();
            });
            $('input[name=type]:checked', $panel).trigger('change');
        }
        $(document.body).trigger('service.initForm', [$panel, $panel.closest('.panel').data('service-id')]);
    };
});