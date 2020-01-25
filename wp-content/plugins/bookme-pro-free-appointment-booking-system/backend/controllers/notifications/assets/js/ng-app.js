;(function () {
    var module = angular.module('notifications', []);

    module.factory('dataSource', function ($q, $rootScope) {
        var ds = {
            sender_name: '',
            sender_email: '',
            reply_to_customers: false,
            send_as: 'html',
            notifications: [],
            loadData: function (params) {
                var deferred = $q.defer();
                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: jQuery.extend({
                        action: 'bookme_pro_get_email_notifications_data',
                        csrf_token: BookmeProL10n.csrf_token
                    }, params),
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            ds.sender_name = response.data.sender_name;
                            ds.sender_email = response.data.sender_email;
                            ds.reply_to_customers = response.data.reply_to_customers;
                            ds.send_as = response.data.send_as;
                            ds.notifications = response.data.notifications;
                        }
                        $rootScope.$apply(deferred.resolve);
                    },
                    error: function () {
                        $rootScope.$apply(deferred.resolve);
                    }
                });

                return deferred.promise;
            }
        };

        return ds;
    });

    module.controller('emailNotifications', function ($scope, dataSource) {
        $scope.showTestEmailNotificationDialog = function () {
            showTestEmailNotificationDialog();
        }
    });

    module.controller('testEmailNotificationsDialogCtrl', function ($scope, dataSource, $timeout) {
        $scope.loading = true;
        $scope.mailSentAlert = false;
        $scope.allNotifications = false;
        $scope.toEmail = 'admin@example.com';
        $scope.dataSource = dataSource;
        $scope.notification_to_send = [];

        dataSource.loadData().then(function () {
            $scope.loading = false;

            angular.forEach($scope.dataSource.notifications, function (notification) {
                if (notification.active == '1') {
                    $scope.notification_to_send.push(notification.type);
                }
            });

            setTimeout(function () {
                var $notifications = jQuery("#bookme-pro-js-notification-selector");
                $notifications.multiselect({
                    texts: {
                        placeholder: $notifications.data('placeholder'), // text to use in dummy input
                        selectedOptions: ' ' + $notifications.data('selected'),      // selected suffix text
                        selectAll: $notifications.data('selectall'),     // select all text
                        unselectAll: $notifications.data('unselectall'),   // unselect all text
                        noneSelected: $notifications.data('nothing'),   // None selected text
                        allSelected: $notifications.data('allselected')
                    },
                    showCheckbox: false,  // display the checkbox to the user
                    selectAll: true, // add select all option
                    minHeight: 20,
                    maxPlaceholderOpts: 1
                });
            });
        });

        $scope.testEmailNotifications = function () {
            var data = {
                action: 'bookme_pro_test_email_notifications',
                csrf_token: BookmeProL10n.csrf_token,
                notifications: $scope.notification_to_send,
                to_email: $scope.toEmail,
                sender_name: $scope.dataSource.sender_name,
                sender_email: $scope.dataSource.sender_email,
                reply_to_customers: $scope.dataSource.reply_to_customers,
                send_as: $scope.dataSource.send_as
            };
            var ladda = Ladda.create(document.getElementById('bookmeProSendTestEmail'));
            ladda.start();
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function (response) {
                    bookmeProAlert({success: [BookmeProL10n.sent_successfully]});
                    Ladda.stopAll();
                }
            });
        };
    });

})();

var showTestEmailNotificationDialog = function () {
    showSidepanel(jQuery('#bookme-pro-test-email-notifications-dialog'));
};