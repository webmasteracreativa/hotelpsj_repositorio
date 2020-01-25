
<style type="text/css">
    #bookme-pro-shortcode-form {
        margin-top: 15px;
    }

    #bookme-pro-shortcode-form table {
        width: 100%;
    }

    #bookme-pro-shortcode-form table td {
        padding: 5px;
        vertical-align: 0;
    }

    #bookme-pro-shortcode-form table th.bookme-pro-title-col {
        width: 80px;
    }

    #bookme-pro-shortcode-form table td select {
        width: 100%;
        margin-bottom: 5px;
    }

    #add-bookme-pro-form {
        margin-bottom: 10px;
    }

    .bookme-pro-media-icon {
        display: inline-block;
        width: 16px;
        height: 16px;
        vertical-align: text-top;
        margin: 0 2px;
        background: url("<?php echo plugins_url( 'assets/images/logo-16.png', __DIR__ ) ?>") 0 0 no-repeat;
    }

    .components-button .bookme-pro-media-icon {
        margin: 4px 6px 4px 0;
    }

    #TB_overlay {
        z-index: 100001 !important;
    }

    #TB_window {
        z-index: 100002 !important;
    }
</style>

<script type="text/javascript">
    jQuery(function ($) {
        var $select_category = $('#bookme-pro-select-category'),
            $select_service = $('#bookme-pro-select-service'),
            $select_employee = $('#bookme-pro-select-employee'),
            $hide_categories = $('#bookme-pro-hide-categories'),
            $hide_services = $('#bookme-pro-hide-services'),
            $hide_staff = $('#bookme-pro-hide-employee'),
            $hide_number_of_persons = $('#bookme-pro-hide-number-of-persons'),
            $hide_calendar = $('#bookme-pro-hide-calendar'),
            $add_form_button = $('#add-bookme-pro-form'),
            $insert_form_shortcode = $('#bookme-pro-insert-shortcode'),
            categories = <?php echo json_encode($casest['categories']) ?>,
            services = <?php echo json_encode($casest['services']) ?>,
            staff = <?php echo json_encode($casest['staff']) ?>,
            $add_button_appointment = $('#add-bookme-pro-appointment'),
            $insert_appointment_shortcode = $('#bookme-pro-insert-ap-shortcode')
            ;

        function openFormModal() {
            window.parent.tb_show(<?php echo json_encode(__('Insert Booking Form', 'bookme_pro')) ?>, '#TB_inline?width=640&inlineId=bookme-pro-tinymce-popup&height=650');
            window.setTimeout(function () {
                $('#TB_window').css({
                    'overflow-x': 'auto',
                    'overflow-y': 'hidden'
                });
            }, 100);
        }

        function openAppointmentModal() {
            window.parent.tb_show(<?php echo json_encode(__('Add Bookme Pro appointments list', 'bookme_pro')) ?>, '#TB_inline?width=640&amp;inlineId=bookme-pro-tinymce-appointment-popup&amp;height=650');
            window.setTimeout(function () {
                $('#TB_window').css({
                    'overflow-x': 'auto',
                    'overflow-y': 'hidden'
                });
            }, 100);
        }

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

        function setSelects(category_id, service_id, staff_id) {
            var _staff = {}, _services = {}, _categories = {}, _nop = {};
            $.each(staff, function (id, staff_member) {
                if (service_id == '') {
                    if (category_id == '') {
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
            });
            _categories = categories;
            $.each(services, function (id, service) {
                if (category_id == '' || service.category_id == category_id) {
                    if (staff_id == '' || staff[staff_id].services.hasOwnProperty(id)) {
                        _services[id] = service;
                    }
                }
            });
            setSelect($select_category, _categories, category_id);
            setSelect($select_service, _services, service_id);
            setSelect($select_employee, _staff, staff_id);
        }

        function getFormShortcode() {
            var insert = '[bookme-pro-form';
            var hide = [];
            if ($select_category.val()) {
                insert += ' category_id="' + $select_category.val() + '"';
            }
            if ($hide_categories.is(':checked')) {
                hide.push('categories');
            }
            if ($select_service.val()) {
                insert += ' service_id="' + $select_service.val() + '"';
            }
            if ($hide_services.is(':checked')) {
                hide.push('services');
            }
            if ($select_employee.val()) {
                insert += ' staff_member_id="' + $select_employee.val() + '"';
            }
            if ($hide_number_of_persons.is(':not(:checked)')) {
                insert += ' show_number_of_persons="1"';
            }

            if ($hide_calendar.is(':checked')) {
                hide.push('calendar');
            }

            if ($hide_staff.is(':checked')) {
                hide.push('staff_members');
            }
            if (hide.length > 0) {
                insert += ' hide="' + hide.join() + '"';
            }
            return insert += ']';
        }

        function getAppoinmentShortcode() {
            var shortcode = '[bookme-pro-appointments-list',
                column;

            // columns
            var columns = $('[data-column]:checked');
            if (columns.length) {
                column = [];
                $.each(columns, function () {
                    column.push($(this).data('column'));
                });
                shortcode += ' columns="' + column.join(',') + '"';
            }
            // custom_fields
            var custom_fields = $('[data-custom_field]:checked');
            if (custom_fields.length) {
                column = [];
                $.each(custom_fields, function () {
                    column.push($(this).data('custom_field'));
                });
                shortcode += ' custom_fields="' + column.join(',') + '"';
            }


            if ($('#bookme-pro-show-column-titles:checked').length) {
                shortcode += ' show_column_titles="1"';
            }
            return shortcode + ']';
        }

        function clearFields() {
            $select_category.val('');
            $select_service.val('');
            $select_employee.val('');
            $hide_categories.prop('checked', false);
            $hide_services.prop('checked', false);
            $hide_staff.prop('checked', false);
            $hide_calendar.prop('checked', false);
            $hide_number_of_persons.prop('checked', true);
        }

        $add_form_button.on('click', function () {
            openFormModal();
        });

        $add_button_appointment.on('click', function () {
            openAppointmentModal();
        });

        // Category select change
        $select_category.on('change', function () {
            var
                category_id = this.value,
                service_id = $select_service.val(),
                staff_id = $select_employee.val()
                ;

            // Validate selected values.
            if (category_id != '') {
                if (service_id != '') {
                    if (services[service_id].category_id != category_id) {
                        service_id = '';
                    }
                }
                if (staff_id != '') {
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
            }
            setSelects(category_id, service_id, staff_id);
        });

        // Service select change
        $select_service.on('change', function () {
            var
                category_id = '',
                service_id = this.value,
                staff_id = $select_employee.val()
                ;

            // Validate selected values.
            if (service_id != '') {
                if (staff_id != '' && !staff[staff_id].services.hasOwnProperty(service_id)) {
                    staff_id = '';
                }
            }
            setSelects(category_id, service_id, staff_id);
            if (service_id) {
                $select_category.val(services[service_id].category_id);
            }
        });

        // Staff select change
        $select_employee.on('change', function () {
            var
                category_id = $select_category.val(),
                service_id = $select_service.val(),
                staff_id = this.value
                ;

            setSelects(category_id, service_id, staff_id);
        });

        // Set up draft selects.
        setSelect($select_category, categories);
        setSelect($select_service, services);
        setSelect($select_employee, staff);

        <?php if (\BookmePro\Lib\Utils\Common::is_gutenberg_page()){ ?>
        var properties = null,
            el = wp.element.createElement;
        var withInspectorControls = wp.compose.createHigherOrderComponent(function (BlockEdit) {
            return function (props) {
                properties = props;
                if (props.name != 'core/shortcode')
                    return el(
                        wp.element.Fragment,
                        null,
                        el(
                            BlockEdit,
                            props
                        )
                    );

                return el(
                    wp.element.Fragment,
                    null,
                    el(
                        BlockEdit,
                        props
                    ),
                    el(
                        wp.editor.InspectorControls,
                        null,
                        el(
                            wp.components.PanelBody,
                            {
                                title: '<?php _e('Bookme Pro Shortcode', 'bookme_pro') ?>',
                                className: 'block-social-links',
                                initialOpen: true
                            },
                            el(
                                wp.components.Button,
                                {
                                    id: 'add-bookme-pro-form',
                                    className: 'is-button is-default bookme-pro-media-button',
                                    onClick: function () {
                                        openFormModal();
                                    }
                                },
                                el(
                                    'span',
                                    {
                                        className: 'bookme-pro-media-icon'
                                    }
                                ),
                                '<?php _e('Add Bookme Pro booking form', 'bookme_pro') ?>'
                            ),
                            el(
                                wp.components.Button,
                                {
                                    id: 'add-ap-appointment',
                                    className: 'is-button is-default bookme-pro-media-button',
                                    onClick: function () {
                                        openAppointmentModal();
                                    }
                                },
                                el(
                                    'span',
                                    {
                                        className: 'bookme-pro-media-icon'
                                    }
                                ),
                                '<?php _e('Add Bookme Pro appointments list', 'bookme_pro') ?>'
                            )
                        )
                    )
                );
            };
        }, 'withInspectorControls');
        wp.hooks.addFilter('editor.BlockEdit', 'BookmePro', withInspectorControls);

        $insert_form_shortcode.on('click', function (e) {
            e.preventDefault();
            properties.setAttributes({text: getFormShortcode()});
            clearFields();
            window.parent.tb_remove();
            return false;
        });

        $insert_appointment_shortcode.on('click', function (e) {
            e.preventDefault();
            properties.setAttributes({text: getAppoinmentShortcode()});
            window.parent.tb_remove();
            return false;
        });
        <?php } ?>

        $insert_form_shortcode.on('click', function (e) {
            e.preventDefault();
            window.send_to_editor(getFormShortcode());
            clearFields();
            window.parent.tb_remove();
            return false;
        });

        $insert_appointment_shortcode.on('click', function (e) {
            e.preventDefault();
            window.send_to_editor(getAppoinmentShortcode());
            window.parent.tb_remove();
            return false;
        });
    });
</script>