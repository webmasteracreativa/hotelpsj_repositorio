<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/** @var \BookmePro\Lib\Entities\Staff[] $staff_members */
?>
<style>
    .fc-slats tr { height: <?php echo max( 21, (int) ( 0.43 * get_option( 'bookme_pro_gen_time_slot_length' ) ) ) ?>px; }
    .fc-time-grid-event.fc-short .fc-time::after { content: '' !important; }
</style>
<div id="bookme-pro-tbs" class="wrap">
    <div class="bookme-pro-tbs-body">
        <?php \BookmePro\Lib\Utils\Common::pageHeader(esc_html__('Calendar', 'bookme_pro')); ?>
        <div class="panel panel-default bookme-pro-main bookme-pro-fc-inner">
            <div class="panel-body">
                <ul class="bookme-pro-nav bookme-pro-nav-tabs">
                    <?php if ( \BookmePro\Lib\Utils\Common::isCurrentUserAdmin() ) : ?>
                        <li class="bookme-pro-nav-item bookme-pro-js-calendar-tab" data-staff_id="0">
                            <?php esc_html_e( 'All', 'bookme_pro' ) ?>
                        </li>
                    <?php endif ?>
                    <?php foreach ( $staff_members as $staff ) : ?>
                        <li class="bookme-pro-nav-item bookme-pro-js-calendar-tab" data-staff_id="<?php echo $staff->getId() ?>" style="display: none">
                            <?php echo $staff->getFullName() ?>
                        </li>
                    <?php endforeach ?>
                    <?php if ( \BookmePro\Lib\Utils\Common::isCurrentUserAdmin() ) : ?>
                        <div class="col-sm-1 pull-right" style="margin-top: 5px;">
                            <select name="staff_ids[]" id="bookme-pro-js-staff-selector" class="form-control" multiple data-placeholder="<?php esc_attr_e('Select Staff Members', 'bookme_pro') ?>" data-nothing="<?php esc_attr_e('No staff selected', 'bookme_pro') ?>" data-selected="<?php esc_attr_e('selected', 'bookme_pro') ?>" data-selectall="<?php esc_attr_e('Select All', 'bookme_pro') ?>" data-unselectall="<?php esc_attr_e('Unselect All', 'bookme_pro') ?>" data-allselected="<?php esc_attr_e('All Staffs Selected', 'bookme_pro') ?>">
                                <?php foreach ( $staff_members as $staff ) : ?>
                                    <option value="<?php echo $staff->getId() ?>" data-staff_name="<?php echo esc_attr( $staff->getFullName() ) ?>"><?php echo $staff->getFullName() ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    <?php endif ?>
                </ul>
                <div class="bookme-pro-margin-top-xlg">
                        <div class="fc-loading-inner" style="display: none">
                            <div class="fc-loading"></div>
                        </div>
                        <div id="bookme-pro-fc-wrapper" class="bookme-pro-calendar">
                            <div class="bookme-pro-js-calendar-element"></div>
                        </div>
                        <?php \BookmePro\Backend\Controllers\Calendar\Components::getInstance()->renderAppointmentDialog() ?>
                        <?php \BookmePro\Lib\Proxy\Shared::renderComponentCalendar() ?>
                </div>
            </div>
        </div>

        <?php \BookmePro\Backend\Controllers\Calendar\Components::getInstance()->renderDeleteDialog(); ?>
    </div>
</div>