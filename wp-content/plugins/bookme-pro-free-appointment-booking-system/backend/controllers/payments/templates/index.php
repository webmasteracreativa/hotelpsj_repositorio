<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div id="bookme-pro-tbs" class="wrap">
    <div class="bookme-pro-tbs-body">
        <?php \BookmePro\Lib\Utils\Common::pageHeader(esc_html__('Payments', 'bookme_pro')); ?>
        <div class="panel panel-default bookme-pro-main">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="bookme-pro-margin-bottom-lg bookme-pro-relative">
                            <button type="button" class="btn btn-block btn-default" id="bookme-pro-filter-date" data-date="<?php echo date( 'Y-m-d', strtotime( '-30 day' ) ) ?> - <?php echo date( 'Y-m-d' ) ?>">
                                <i class="dashicons dashicons-calendar-alt"></i>
                                <span>
                                    <?php echo \BookmePro\Lib\Utils\DateTime::formatDate( '-30 days' ) ?> - <?php echo \BookmePro\Lib\Utils\DateTime::formatDate( 'today' ) ?>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <div class="form-group">
                            <select id="bookme-pro-filter-type" class="form-control bookme-pro-js-select" data-placeholder="<?php esc_attr_e( 'Type', 'bookme_pro' ) ?>">
                                <?php foreach ( $types as $type ) : ?>
                                    <option value="<?php echo esc_attr( $type ) ?>">
                                        <?php echo \BookmePro\Lib\Entities\Payment::typeToString( $type ) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <div class="form-group">
                            <select id="bookme-pro-filter-staff" class="form-control bookme-pro-js-select" data-placeholder="<?php esc_attr_e( 'Provider', 'bookme_pro' ) ?>">
                                <?php foreach ( $providers as $provider ) : ?>
                                    <option value="<?php echo $provider['id'] ?>"><?php echo esc_html( $provider['full_name'] ) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <div class="form-group">
                            <select id="bookme-pro-filter-service" class="form-control bookme-pro-js-select" data-placeholder="<?php esc_attr_e( 'Service', 'bookme_pro' ) ?>">
                                <?php foreach ( $services as $service ) : ?>
                                    <option value="<?php echo $service['id'] ?>"><?php echo esc_html( $service['title'] ) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>

                <table id="bookme-pro-payments-list" class="table table-striped" width="100%">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Date', 'bookme_pro' ) ?></th>
                            <th><?php esc_html_e( 'Type', 'bookme_pro' ) ?></th>
                            <th><?php esc_html_e( 'Customer', 'bookme_pro' ) ?></th>
                            <th><?php esc_html_e( 'Provider', 'bookme_pro' ) ?></th>
                            <th><?php esc_html_e( 'Service', 'bookme_pro' ) ?></th>
                            <th><?php esc_html_e( 'Appointment Date', 'bookme_pro' ) ?></th>
                            <th><?php esc_html_e( 'Amount', 'bookme_pro' ) ?></th>
                            <th><?php esc_html_e( 'Status', 'bookme_pro' ) ?></th>
                            <th width="16"></th>
                            <th width="16"><input type="checkbox" id="bookme-pro-check-all"></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="6"><div class="pull-right"><?php esc_html_e( 'Total', 'bookme_pro' ) ?>:</div></th>
                            <th colspan="4"><span id="bookme-pro-payment-total"></span></th>
                        </tr>
                    </tfoot>
                </table>
                <div class="text-right bookme-pro-margin-top-lg">
                    <?php \BookmePro\Lib\Utils\Common::deleteButton() ?>
                </div>
            </div>
        </div>

        <div ng-app="paymentDetails" ng-controller="paymentDetailsCtrl">
            <div payment-details-dialog></div>
            <?php \BookmePro\Backend\Controllers\Payments\Components::getInstance()->renderPaymentDetailsDialog() ?>
        </div>
    </div>
</div>
