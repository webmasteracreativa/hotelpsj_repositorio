<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>

<div ng-controller=testEmailNotificationsDialogCtrl>
    <div id="bookme-pro-test-email-notifications-dialog" class="slidePanel <?php echo is_rtl() ? 'slidePanel-left' : 'slidePanel-right'; ?>">
        <div class="slidePanel-scrollable">
            <div>
                <div class="slidePanel-content">
                    <header class="slidePanel-header">
                        <div class="slidePanel-overlay-panel">
                            <div class="slidePanel-heading">
                                <h2><?php esc_html_e('Test Email Notifications', 'bookme_pro') ?></h2>
                            </div>
                            <div class="slidePanel-actions">
                                <div class="btn-group-flat">
                                    <button type="button" id="bookmeProSendTestEmail"
                                            class="btn btn-primary btn-sm" data-spinner-size="40" data-style="zoom-in"
                                            ng-click=testEmailNotifications()><i class="glyphicon glyphicon-ok"></i>
                                    </button>
                                    <button type="button" class="btn btn-default btn-sm slidePanel-close"><i
                                                class="glyphicon glyphicon-remove"></i></button>
                                </div>
                            </div>
                        </div>
                    </header>
                    <div class="slidePanel-inner">
                        <div class="panel-body">
                            <div ng-show=loading class="bookme-pro-loading"></div>
                            <div ng-hide=loading>
                                <form>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="bookme_pro_test_to_email"><?php esc_html_e('To email', 'bookme_pro') ?></label>
                                            <input id="bookme_pro_test_to_email" class="form-control"
                                                   type="text" ng-model="toEmail"/>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="bookme_pro_test_sender_name"><?php esc_html_e('Sender name', 'bookme_pro') ?></label>
                                                    <input id="bookme_pro_test_sender_name" class="form-control"
                                                           type="text" ng-model="dataSource.sender_name"/>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="bookme_pro_test_sender_email"><?php esc_html_e('Sender email', 'bookme_pro') ?></label>
                                                    <input id="bookme_pro_test_sender_email"
                                                           class="form-control" type="text"
                                                           ng-model="dataSource.sender_email"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="bookme_pro_test_reply_to_customers"><?php esc_html_e('Reply directly to customers', 'bookme_pro') ?></label>
                                                    <select id="bookme_pro_test_reply_to_customers"
                                                            class="form-control"
                                                            ng-model="dataSource.reply_to_customers">
                                                        <option value="0"><?php esc_html_e('Disabled', 'bookme_pro') ?></option>
                                                        <option value="1"><?php esc_html_e('Enabled', 'bookme_pro') ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="bookme_pro_test_send_as"><?php esc_html_e('Send emails as', 'bookme_pro') ?></label>
                                                    <select id="bookme_pro_test_send_as" class="form-control"
                                                            ng-model="dataSource.send_as">
                                                        <option value="html"><?php esc_html_e('HTML', 'bookme_pro') ?></option>
                                                        <option value="text"><?php esc_html_e('Text', 'bookme_pro') ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label><?php esc_html_e('Notifications', 'bookme_pro') ?></label>
                                                <select name="notification[]" id="bookme-pro-js-notification-selector" class="form-control" multiple data-placeholder="<?php esc_attr_e('Select Notifications', 'bookme_pro') ?>" data-nothing="<?php esc_attr_e('No selected', 'bookme_pro') ?>" data-selected="<?php esc_attr_e('selected', 'bookme_pro') ?>" data-selectall="<?php esc_attr_e('Select All', 'bookme_pro') ?>" data-unselectall="<?php esc_attr_e('Unselect All', 'bookme_pro') ?>" data-allselected="<?php esc_attr_e('All Notifications Selected', 'bookme_pro') ?>" ng-model="notification_to_send">
                                                        <option ng-repeat="notification in dataSource.notifications" value="{{notification.type}}">{{notification.name}}</option>
                                                </select>
                                                </div>
                                            </div>
                                        </div>
                                        <?php \BookmePro\Lib\Utils\Common::csrf() ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bookme-pro-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="<?php esc_attr_e('Close', 'bookme_pro') ?>"><span
                                aria-hidden="true">&times;</span></button>
                    <div class="modal-title h2"></div>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php esc_html_e('Close', 'bookme_pro') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>