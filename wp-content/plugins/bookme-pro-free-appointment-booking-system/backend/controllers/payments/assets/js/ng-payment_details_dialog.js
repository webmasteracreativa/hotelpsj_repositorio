;(function () {
    angular.module('paymentDetailsDialog', []).directive('paymentDetailsDialog', function () {
        return {
            restrict: 'A',
            replace: true,
            scope: {
                callback: '&paymentDetailsDialog'
            },
            templateUrl: 'bookme-pro-payment-details-dialog.tpl',
            // The linking function will add behavior to the template.
            link: function (scope, element, attrs) {
                var $body = element.find('.modal-body'),
                    spinner = $body.html();

                jQuery(document).on('click', '#bookme-pro-payment-dialog-show', function (e) {
                    e.preventDefault();
                    showSidepanel(jQuery('#bookme-pro-payment-details-modal'));
                    loadPaymentDialog(this);
                });

                var loadPaymentDialog = function ($btn, payment_id) {
                    if (payment_id === undefined) {
                        payment_id = jQuery($btn).attr('data-payment_id');
                    }
                    jQuery.ajax({
                        url: ajaxurl,
                        data: {
                            action: 'bookme_pro_get_payment_details',
                            payment_id: payment_id,
                            csrf_token: BookmeProL10n.csrf_token
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                $body.html(response.data.html);
                                $body.find('#bookme-pro-complete-payment').on('click', function () {
                                    var ladda = Ladda.create(this);
                                    ladda.start();
                                    jQuery.ajax({
                                        url: ajaxurl,
                                        data: {
                                            action: 'bookme_pro_complete_payment',
                                            payment_id: payment_id,
                                            csrf_token: BookmeProL10n.csrf_token
                                        },
                                        dataType: 'json',
                                        type: 'POST',
                                        success: function (response) {
                                            if (response.success) {
                                                loadPaymentDialog($btn,payment_id);
                                                if (scope.callback) {
                                                    scope.$apply(function ($scope) {
                                                        $scope.callback({
                                                            payment_id: payment_id,
                                                            payment_title: response.data.payment_title
                                                        });
                                                    });
                                                }
                                                // Reload DataTable.
                                                var $table = jQuery($btn).closest('table.dataTable');
                                                if ($table.length) {
                                                    $table.DataTable().ajax.reload();
                                                }
                                            }
                                        }
                                    });
                                });
                            }
                        }
                    });
                };

                element.on('sidePanel.hide', function () {
                    if (jQuery('.slidePanel-wrapper').length > 1) {
                        jQuery('body').css('overflow-y', 'hidden');
                    }
                    setTimeout(function() {
                        $body.html(spinner);
                    }, 600);

                });
            }
        }
    });
})();