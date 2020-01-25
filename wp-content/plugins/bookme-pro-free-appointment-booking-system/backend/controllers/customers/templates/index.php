<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div id="bookme-pro-tbs" class="wrap">
    <div class="bookme-pro-tbs-body">
        <?php \BookmePro\Lib\Utils\Common::pageHeader(esc_html__('Customers', 'bookme_pro')); ?>
        <div class="panel panel-default bookme-pro-main">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <input class="form-control" type="text" id="bookme-pro-filter" placeholder="<?php esc_attr_e( 'Quick search customer', 'bookme_pro' ) ?>" />
                        </div>
                    </div>
                    <div class="col-md-8 form-inline bookme-pro-margin-bottom-lg  text-right">
                        <div class="form-group">
                            <button type="button" class="btn btn-primary bookme-pro-btn-block-xs" id="bookme-pro-add"><i class="glyphicon glyphicon-plus"></i> <?php esc_html_e( 'New customer', 'bookme_pro' ) ?></button>
                        </div>
                    </div>
                </div>

                <table id="bookme-pro-customers-list" class="table table-striped" width="100%">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( get_option( 'bookme_pro_l10n_label_name' ) ) ?></th>
                            <th><?php esc_html_e( get_option( 'bookme_pro_l10n_label_first_name' ) ) ?></th>
                            <th><?php esc_html_e( get_option( 'bookme_pro_l10n_label_last_name' ) ) ?></th>
                            <th><?php esc_html_e( 'User', 'bookme_pro' ) ?></th>
                            <th><?php esc_html_e( get_option( 'bookme_pro_l10n_label_phone' ) ) ?></th>
                            <th><?php esc_html_e( get_option( 'bookme_pro_l10n_label_email' ) ) ?></th>
                            <th><?php esc_html_e( 'Notes', 'bookme_pro' ) ?></th>
                            <th><?php esc_html_e( 'Last appointment', 'bookme_pro' ) ?></th>
                            <th><?php esc_html_e( 'Total appointments', 'bookme_pro' ) ?></th>
                            <th><?php esc_html_e( 'Payments', 'bookme_pro' ) ?></th>
                            <th width="16"></th>
                            <th width="16"><input type="checkbox" id="bookme-pro-check-all"></th>
                        </tr>
                    </thead>
                </table>

                <div class="text-right bookme-pro-margin-top-lg">
                    <?php \BookmePro\Lib\Utils\Common::deleteButton() ?>
                </div>
            </div>
        </div>

        <div id="bookme-pro-delete-dialog" class="modal fade" tabindex=-1 role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        <div class="modal-title h2"><?php esc_html_e( 'Delete customers', 'bookme_pro' ) ?></div>
                    </div>
                    <div class="modal-body">
                        <?php esc_html_e( 'You are about to delete customers which may have WordPress accounts associated to them. Do you want to delete those accounts too (if there are any)?', 'bookme_pro' ) ?>
                        <div class="checkbox">
                            <label>
                                <input id="bookme-pro-delete-remember-choice" type="checkbox" /><?php esc_html_e( 'Remember my choice', 'bookme_pro' ) ?>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger ladda-button" id="bookme-pro-delete-no" data-spinner-size="40" data-style="zoom-in">
                            <span class="ladda-label"><i class="glyphicon glyphicon-trash"></i> <?php esc_html_e( 'No, delete just customers', 'bookme_pro' ) ?></span>
                        </button>
                        <button type="button" class="btn btn-danger ladda-button" id="bookme-pro-delete-yes" data-spinner-size="40" data-style="zoom-in">
                            <span class="ladda-label"><i class="glyphicon glyphicon-trash"></i> <?php esc_html_e( 'Yes', 'bookme_pro' ) ?></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div ng-app="customer" ng-controller="customerCtrl">
            <div customer-dialog=saveCustomer(customer) customer="customer"></div>
            <?php \BookmePro\Backend\Controllers\Customers\Components::getInstance()->renderCustomerDialog() ?>
        </div>
    </div>
</div>