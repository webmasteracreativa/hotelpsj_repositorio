<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BookmePro\Lib\Entities\CustomerAppointment;
use BookmePro\Lib\Config;
?>
<div id="bookme-pro-tbs" class="wrap">
    <div class="bookme-pro-tbs-body">
        <?php \BookmePro\Lib\Utils\Common::pageHeader(esc_html__('All Bookings', 'bookme_pro')); ?>
        <div class="panel panel-default bookme-pro-main">
            <div class="panel-body">
                <div>
                    <div class="form-inline bookme-pro-margin-bottom-lg text-right">
                        <div class="form-group">
                            <button type="button" class="btn btn-primary bookme-pro-btn-block-xs" id="bookme-pro-add"><i class="glyphicon glyphicon-plus"></i> <?php esc_html_e( 'New Booking', 'bookme_pro' ) ?></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-lg-1">
                        <div class="form-group">
                            <input class="form-control" type="text" id="bookme-pro-filter-id" placeholder="<?php esc_attr_e( 'No.', 'bookme_pro' ) ?>" />
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="bookme-pro-margin-bottom-lg bookme-pro-relative">
                            <button type="button" class="btn btn-block btn-default" id="bookme-pro-filter-date" data-date="<?php echo date( 'Y-m-d', strtotime( 'first day of' ) ) ?> - <?php echo date( 'Y-m-d', strtotime( 'last day of' ) ) ?>">
                                <i class="dashicons dashicons-calendar-alt"></i>
                                <span>
                                    <?php echo \BookmePro\Lib\Utils\DateTime::formatDate( 'first day of this month' ) ?> - <?php echo \BookmePro\Lib\Utils\DateTime::formatDate( 'last day of this month' ) ?>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <div class="form-group">
                            <select class="form-control bookme-pro-js-select" id="bookme-pro-filter-staff" data-placeholder="<?php echo esc_attr( \BookmePro\Lib\Utils\Common::getTranslatedOption( 'bookme_pro_l10n_label_employee' ) ) ?>">
                                <?php foreach ( $staff_members as $staff ) : ?>
                                    <option value="<?php echo $staff['id'] ?>"><?php esc_html_e( $staff['full_name'] ) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="clearfix visible-md-block"></div>
                    <div class="col-md-4 col-lg-2">
                        <div class="form-group">
                            <select class="form-control bookme-pro-js-select" id="bookme-pro-filter-customer" data-placeholder="<?php esc_attr_e( 'Customer', 'bookme_pro' ) ?>">
                                <?php foreach ( $customers as $customer ) : ?>
                                    <option value="<?php echo $customer['id'] ?>"><?php esc_html_e( $customer['full_name'] ) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <div class="form-group">
                            <select class="form-control bookme-pro-js-select" id="bookme-pro-filter-service" data-placeholder="<?php echo esc_attr( \BookmePro\Lib\Utils\Common::getTranslatedOption( 'bookme_pro_l10n_label_service' ) ) ?>">
                                <?php foreach ( $services as $service ) : ?>
                                    <option value="<?php echo $service['id'] ?>"><?php esc_html_e( $service['title'] ) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <div class="form-group">
                            <select class="form-control bookme-pro-js-select" id="bookme-pro-filter-status" data-placeholder="<?php esc_attr_e( 'Status', 'bookme_pro' ) ?>">
                                <option value="<?php echo CustomerAppointment::STATUS_PENDING ?>"><?php echo CustomerAppointment::statusToString( CustomerAppointment::STATUS_PENDING ) ?></option>
                                <option value="<?php echo CustomerAppointment::STATUS_APPROVED ?>"><?php echo CustomerAppointment::statusToString( CustomerAppointment::STATUS_APPROVED ) ?></option>
                                <option value="<?php echo CustomerAppointment::STATUS_CANCELLED ?>"><?php echo CustomerAppointment::statusToString( CustomerAppointment::STATUS_CANCELLED ) ?></option>
                                <option value="<?php echo CustomerAppointment::STATUS_REJECTED ?>"><?php echo CustomerAppointment::statusToString( CustomerAppointment::STATUS_REJECTED ) ?></option>
                                <?php if ( Config::waitingListActive() ): ?>
                                    <option value="<?php echo CustomerAppointment::STATUS_WAITLISTED ?>"><?php echo CustomerAppointment::statusToString( CustomerAppointment::STATUS_WAITLISTED ) ?></option>
                                <?php endif ?>
                            </select>
                        </div>
                    </div>
                </div>

                <table id="bookme-pro-appointments-list" class="table table-striped" width="100%">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'No.', 'bookme_pro' ) ?></th>
                            <th><?php esc_html_e( 'Booking Date', 'bookme_pro' ) ?></th>
                            <th><?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption( 'bookme_pro_l10n_label_employee' ) ?></th>
                            <th><?php esc_html_e( 'Customer Name', 'bookme_pro' ) ?></th>
                            <th><?php esc_html_e( 'Customer Phone', 'bookme_pro' ) ?></th>
                            <th><?php esc_html_e( 'Customer Email', 'bookme_pro' ) ?></th>
                            <th><?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption( 'bookme_pro_l10n_label_service' ) ?></th>
                            <th><?php esc_html_e( 'Duration', 'bookme_pro' ) ?></th>
                            <th><?php esc_html_e( 'Status', 'bookme_pro' ) ?></th>
                            <th><?php esc_html_e( 'Payment', 'bookme_pro' ) ?></th>
                            <?php foreach ( $custom_fields as $custom_field ) : ?>
                                <th><?php esc_html_e($custom_field->label) ?></th>
                            <?php endforeach ?>
                            <th></th>
                            <th width="16"><input type="checkbox" id="bookme-pro-check-all" /></th>
                        </tr>
                    </thead>
                </table>

                <div class="text-right bookme-pro-margin-top-lg">
                    <?php \BookmePro\Lib\Utils\Common::deleteButton( '', '', '#bookme-pro-delete-dialog' ) ?>
                </div>
            </div>
        </div>

        <?php \BookmePro\Backend\Controllers\Calendar\Components::getInstance()->renderDeleteDialog(); ?>

        <?php \BookmePro\Backend\Controllers\Calendar\Components::getInstance()->renderAppointmentDialog() ?>
        <?php \BookmePro\Lib\Proxy\Shared::renderComponentAppointments() ?>
    </div>
</div>
