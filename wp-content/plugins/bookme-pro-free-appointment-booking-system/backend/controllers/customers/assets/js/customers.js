jQuery(function($) {
    var
        $customers_list     = $('#bookme-pro-customers-list'),
        $filter             = $('#bookme-pro-filter'),
        $check_all_button   = $('#bookme-pro-check-all'),
        $customer_dialog    = $('#bookme-pro-customer-dialog'),
        $add_button         = $('#bookme-pro-add'),
        $delete_button      = $('#bookme-pro-delete'),
        $delete_dialog      = $('#bookme-pro-delete-dialog'),
        $delete_button_no   = $('#bookme-pro-delete-no'),
        $delete_button_yes  = $('#bookme-pro-delete-yes'),
        $remember_choice    = $('#bookme-pro-delete-remember-choice'),
        remembered_choice,
        row
        ;

    /**
     * Init DataTables.
     */
    var dt = $customers_list.DataTable({
        order: [[ 0, 'asc' ]],
        info: false,
        searching: false,
        lengthChange: false,
        pageLength: 25,
        pagingType: 'numbers',
        processing: true,
        responsive: true,
        serverSide: true,
        ajax: {
            url : ajaxurl,
            type: 'POST',
            data: function (d) {
                return $.extend({}, d, {
                    action: 'bookme_pro_get_customers',
                    csrf_token : BookmeProL10n.csrf_token,
                    filter: $filter.val()
                });
            }
        },
        columns: [
            { data: 'full_name', render: $.fn.dataTable.render.text(), visible: BookmeProL10n.first_last_name == 0 },
            { data: 'first_name', render: $.fn.dataTable.render.text(), visible: BookmeProL10n.first_last_name == 1 },
            { data: 'last_name', render: $.fn.dataTable.render.text(), visible: BookmeProL10n.first_last_name == 1 },
            { data: 'wp_user', render: $.fn.dataTable.render.text() },
            { data: 'phone', render: $.fn.dataTable.render.text() },
            { data: 'email', render: $.fn.dataTable.render.text() },
            { data: 'notes', render: $.fn.dataTable.render.text() },
            { data: 'last_appointment' },
            { data: 'total_appointments' },
            { data: 'payments' },
            {
                responsivePriority: 1,
                orderable: false,
                searchable: false,
                render: function ( data, type, row, meta ) {
                    return '<button type="button" class="btn btn-sm btn-default"><i class="glyphicon glyphicon-pencil"></i></button>';
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
        dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row pull-left'<'col-sm-12 bookme-pro-margin-top-lg'p>>",
        language: {
            zeroRecords: BookmeProL10n.zeroRecords,
            processing:  BookmeProL10n.processing
        }
    });

    /**
     * Select all customers.
     */
    $check_all_button.on('change', function () {
        $customers_list.find('tbody input:checkbox').prop('checked', this.checked);
    });

    /**
     * On customer select.
     */
    $customers_list.on('change', 'tbody input:checkbox', function () {
        $check_all_button.prop('checked', $customers_list.find('tbody input:not(:checked)').length == 0);
    });

    /**
     * Edit customer.
     */
    $customers_list.on('click', 'button', function () {
        row = dt.row($(this).closest('td'));
        show_customer_dialog();
    });

    /**
     * New customer.
     */
    $add_button.on('click', function () {
        row = null;
        show_customer_dialog();
    });

    /**
     * On show modal.
     */
    show_customer_dialog = function () {
        var $title = $customer_dialog.find('.modal-title');
        var $button = $customer_dialog.find('.modal-footer button:first');
        var customer;
        if (row) {
            customer = $.extend({}, row.data());
            $title.text(BookmeProL10n.edit_customer);
            $button.text(BookmeProL10n.save);
        } else {
            customer = {
                id         : '',
                wp_user_id : '',
                full_name  : '',
                first_name : '',
                last_name  : '',
                phone      : '',
                email      : '',
                notes      : '',
                birthday   : ''
            };
            $title.text(BookmeProL10n.new_customer);
            $button.text(BookmeProL10n.create_customer);
        }

        var $scope = angular.element($customer_dialog).scope();
        $scope.$apply(function ($scope) {
            $scope.customer = customer;
            setTimeout(function() {
                $customer_dialog.find('#phone').intlTelInput('setNumber', customer.phone);
            }, 0);
        });
        showSidepanel($customer_dialog);
    };

    /**
     * Delete customers.
     */
    $delete_button.on('click', function () {
        if (remembered_choice === undefined) {
            $delete_dialog.modal('show');
        } else {
            deleteCustomers(this, remembered_choice);
        }}
    );

    $delete_button_no.on('click', function () {
        if ($remember_choice.prop('checked')) {
            remembered_choice = false;
        }
        deleteCustomers(this, false);
    });

    $delete_button_yes.on('click', function () {
        if ($remember_choice.prop('checked')) {
            remembered_choice = true;
        }
        deleteCustomers(this, true);
    });

    function deleteCustomers(button, with_wp_user) {
        var ladda = Ladda.create(button);
        ladda.start();

        var data = [];
        var $checkboxes = $customers_list.find('tbody input:checked');
        $checkboxes.each(function () {
            data.push(this.value);
        });

        $.ajax({
            url  : ajaxurl,
            type : 'POST',
            data : {
                action       : 'bookme_pro_delete_customers',
                csrf_token   : BookmeProL10n.csrf_token,
                data         : data,
                with_wp_user : with_wp_user ? 1 : 0
            },
            dataType : 'json',
            success  : function(response) {
                ladda.stop();
                $delete_dialog.modal('hide');
                if (response.success) {
                    dt.ajax.reload(null, false);
                } else {
                    alert(response.data.message);
                }
            }
        });
    }

    /**
     * On filters change.
     */
    $filter.on('keyup', function () { dt.ajax.reload(); });
});

(function() {
    var module = angular.module('customer', ['customerDialog']);
    module.controller('customerCtrl', function($scope) {
        $scope.customer = {
            id         : '',
            wp_user_id : '',
            full_name  : '',
            first_name : '',
            last_name  : '',
            phone      : '',
            email      : '',
            notes      : '',
            birthday   : ''
        };
        $scope.saveCustomer = function(customer) {
            jQuery('#bookme-pro-customers-list').DataTable().ajax.reload(null, false);
        };
    });
})();