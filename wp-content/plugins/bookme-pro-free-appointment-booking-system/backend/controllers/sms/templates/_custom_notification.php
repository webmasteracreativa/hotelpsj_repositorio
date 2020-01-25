<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/** @var BookmePro\Backend\Controllers\Notifications\Forms\Notifications $form */
use BookmePro\Lib\Entities\CustomerAppointment;
use BookmePro\Lib\DataHolders\Notification\Settings;
use BookmePro\Lib\Entities\Notification;

$id = $notification['id'];
$notification_settings = (array) json_decode( $notification['settings'], true );
?>
<div class="panel panel-default bookme-pro-js-collapse">
    <div class="panel-heading" role="tab">
        <div class="checkbox bookme-pro-margin-remove">
            <label>
                <input name="notification[<?php echo $id ?>][active]" value="0" type="checkbox" checked="checked" class="hidden">
                <input id="<?php echo $id ?>_active" name="notification[<?php echo $id ?>][active]" value="1" type="checkbox" <?php checked( $notification['active'] ) ?>>
                <a href="#collapse_<?php echo $id ?>" class="collapsed panel-title" role="button" data-toggle="collapse" data-parent="#bookme-pro-js-custom-notifications">
                    <?php echo $notification['subject'] ?: esc_html__( 'Custom notification', 'bookme_pro' ) ?>
                </a>
            </label>
            <button type="button" class="pull-right btn btn-link bookme-pro-js-delete" style="margin-top: -5px" data-notification_id="<?php echo $id ?>" title="<?php esc_attr_e( 'Delete',  'bookme_pro' ) ?>">
                <span class="ladda-label"><i class="glyphicon glyphicon-trash text-danger"></i></span>
            </button>
        </div>
    </div>
    <div id="collapse_<?php echo $id ?>" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="notification_<?php echo $id ?>_type"><?php esc_html_e( 'Type', 'bookme_pro' ) ?></label>
                        <select class="form-control" name="notification[<?php echo $id ?>][type]" id="notification_<?php echo $id ?>_type">
                            <optgroup label="<?php esc_attr_e( 'Reminder notification', 'bookme_pro' ) ?>">
                                <option value="<?php echo Notification::TYPE_APPOINTMENT_START_TIME ?>" data-set="<?php echo Settings::SET_EXISTING_EVENT_WITH_DATE_AND_TIME ?>" <?php selected( $notification['type'], Notification::TYPE_APPOINTMENT_START_TIME ) ?>><?php esc_html_e( 'Appointment date and time', 'bookme_pro' ) ?></option>
                                <option value="<?php echo Notification::TYPE_CUSTOMER_BIRTHDAY ?>" data-set="<?php echo Settings::SET_EXISTING_EVENT_WITH_DATE ?>" <?php selected( $notification['type'], Notification::TYPE_CUSTOMER_BIRTHDAY ) ?>><?php esc_html_e( 'Customer\'s birthday', 'bookme_pro' ) ?></option>
                                <option value="<?php echo Notification::TYPE_LAST_CUSTOMER_APPOINTMENT ?>" data-set="<?php echo Settings::SET_EXISTING_EVENT_WITH_DATE_AND_TIME ?>" <?php selected( $notification['type'], Notification::TYPE_LAST_CUSTOMER_APPOINTMENT ) ?>><?php esc_html_e( 'Last client appointment', 'bookme_pro' ) ?></option>
                            </optgroup>
                        </select>
                    </div>
                </div>
                <div class="bookme-pro-js-settings-holder">
                    <?php
                        $set      = Settings::SET_EXISTING_EVENT_WITH_DATE_AND_TIME;
                        $settings = @$notification_settings[ $set ];
                    ?>
                    <div class="bookme-pro-js-settings bookme-pro-js-<?php echo $set ?>">
                        <?php $name = 'notification[' . $id . '][settings][' . $set . ']' ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <labeL for="notification_<?php echo $id ?>_status_1"><?php esc_html_e( 'With status', 'bookme_pro' ) ?></labeL>
                                <select class="form-control" name="<?php echo $name ?>[status]" id="notification_<?php echo $id ?>_status_1">
                                    <option value=""><?php esc_html_e( 'Any', 'bookme_pro' ) ?></option>
                                    <?php foreach ( $statuses as $status ) : ?>
                                        <option value="<?php echo $status ?>" <?php selected( $settings['status'] == $status ) ?>><?php echo CustomerAppointment::statusToString( $status ) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <labeL for="notification_<?php echo $id ?>_send_1"><?php esc_html_e( 'Send', 'bookme_pro' ) ?></labeL>
                            <div class="form-inline bookme-pro-margin-bottom-sm">
                                <div class="form-group">
                                    <label><input type="radio" name="<?php echo $name ?>[option]" value="1" checked id="notification_<?php echo $id ?>_send_1"></label>
                                    <select class="form-control" name="<?php echo $name ?>[offset_hours]">
                                        <?php foreach ( array_merge( range( 1, 24 ), range( 48, 336, 24 ), array( 504, 672 ) ) as $hour ) : ?>
                                            <option value="<?php echo $hour ?>" <?php selected( @$settings['offset_hours'], $hour ) ?>><?php echo \BookmePro\Lib\Utils\DateTime::secondsToInterval( $hour * HOUR_IN_SECONDS ) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                    <select class="form-control" name="<?php echo $name ?>[perform]">
                                        <option value="before"><?php esc_html_e( 'before', 'bookme_pro' ) ?></option>
                                        <option value="after"<?php selected( @$settings['perform'] == 'after' ) ?>> <?php esc_html_e( 'after', 'bookme_pro' ) ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-inline">
                                <div class="form-group">
                                    <input type="radio" name="<?php echo $name ?>[option]" value="2" <?php checked( @$settings['option'] == 2 ) ?>>
                                    <select class="form-control" name="<?php echo $name ?>[offset_bidirectional_hours]">
                                        <?php foreach ( array_merge( array( -672, -504 ), range( -336, -24, 24 ) ) as $hour ) : ?>
                                            <option value="<?php echo $hour ?>" <?php selected( @$settings['offset_bidirectional_hours'], $hour ) ?>><?php echo \BookmePro\Lib\Utils\DateTime::secondsToInterval( abs( $hour ) * HOUR_IN_SECONDS ) ?> <?php esc_html_e( 'before', 'bookme_pro' ) ?></option>
                                        <?php endforeach ?>
                                        <option value="0" <?php selected( @$settings['offset_bidirectional_hours'], 0 ) ?>><?php esc_html_e( 'on the same day', 'bookme_pro' ) ?></option>
                                        <?php foreach ( array_merge( range( 24, 336, 24 ), array( 504, 672 ) ) as $hour ) : ?>
                                            <option value="<?php echo $hour ?>" <?php selected( @$settings['offset_bidirectional_hours'], $hour ) ?>><?php echo \BookmePro\Lib\Utils\DateTime::secondsToInterval( $hour * HOUR_IN_SECONDS ) ?> <?php esc_html_e( 'after', 'bookme_pro' ) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                    <?php esc_html_e( 'at', 'bookme_pro' ) ?>
                                    <select class="form-control" name="<?php echo $name ?>[at_hour]">
                                        <?php foreach ( range( 0, 23 ) as $hour ) : ?>
                                            <option value="<?php echo $hour ?>" <?php selected( @$settings['at_hour'], $hour ) ?>><?php echo \BookmePro\Lib\Utils\DateTime::buildTimeString( $hour * HOUR_IN_SECONDS, false ) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                        $set      = Settings::SET_EXISTING_EVENT_WITH_DATE;
                        $settings = @$notification_settings[ $set ];
                    ?>
                    <div class="bookme-pro-js-settings bookme-pro-js-<?php echo $set ?>">
                        <?php $name = 'notification[' . $id . '][settings][' . $set . ']' ?>
                        <div class="col-md-6">
                            <labeL for="notification_<?php echo $id ?>_send_2"><?php esc_html_e( 'Send', 'bookme_pro' ) ?></labeL>
                            <div class="form-inline">
                                <div class="form-group">
                                    <select class="form-control" name="<?php echo $name ?>[offset_bidirectional_hours]" id="notification_<?php echo $id ?>_on_2">
                                        <?php foreach ( array_merge( array( -672, -504 ), range( -336, -24, 24 ) ) as $hour ) : ?>
                                            <option value="<?php echo $hour ?>" <?php selected( @$settings['offset_bidirectional_hours'], $hour ) ?>><?php echo \BookmePro\Lib\Utils\DateTime::secondsToInterval( abs( $hour ) * HOUR_IN_SECONDS ) ?> <?php esc_html_e( 'before', 'bookme_pro' ) ?></option>
                                        <?php endforeach ?>
                                        <option value="0" <?php selected( @$settings['offset_bidirectional_hours'], 0 ) ?>><?php esc_html_e( 'on the same day', 'bookme_pro' ) ?></option>
                                        <?php foreach ( array_merge( range( 24, 336, 24 ), array( 504, 672 ) ) as $hour ) : ?>
                                            <option value="<?php echo $hour ?>" <?php selected( @$settings['offset_bidirectional_hours'], $hour ) ?>><?php echo \BookmePro\Lib\Utils\DateTime::secondsToInterval( $hour * HOUR_IN_SECONDS ) ?> <?php esc_html_e( 'after', 'bookme_pro' ) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                    <?php esc_html_e( 'at', 'bookme_pro' ) ?>
                                    <select class="form-control" name="<?php echo $name ?>[at_hour]">
                                        <?php foreach ( range( 0, 23 ) as $hour ) : ?>
                                            <option value="<?php echo $hour ?>" <?php selected( @$settings['at_hour'], $hour ) ?>><?php echo \BookmePro\Lib\Utils\DateTime::buildTimeString( $hour * HOUR_IN_SECONDS, false ) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="notification_<?php echo $id ?>_subject"><?php esc_html_e( 'Notification name', 'bookme_pro' ) ?></label>
                        <input type="text" class="form-control" id="notification_<?php echo $id ?>_subject" name="notification[<?php echo $id ?>][subject]" value="<?php echo esc_attr( $notification['subject'] ) ?>" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <labeL><?php esc_html_e( 'Recipient', 'bookme_pro' ) ?></labeL>
                        <br>
                        <label class="check-inline">
                            <input type="hidden" name="notification[<?php echo $id ?>][to_customer]" value="0">
                            <input type="checkbox" name="notification[<?php echo $id ?>][to_customer]" value="1" <?php checked( $notification['to_customer'] ) ?>> <?php esc_html_e( 'Client', 'bookme_pro' ) ?>
                        </label>
                        <label class="check-inline">
                            <input type="hidden" name="notification[<?php echo $id ?>][to_staff]" value="0">
                            <input type="checkbox" name="notification[<?php echo $id ?>][to_staff]" value="1" <?php checked( $notification['to_staff'] ) ?>> <?php esc_html_e( 'Staff', 'bookme_pro' ) ?>
                        </label>
                        <label class="check-inline">
                            <input type="hidden" name="notification[<?php echo $id ?>][to_admin]" value="0">
                            <input type="checkbox" name="notification[<?php echo $id ?>][to_admin]" value="1" <?php checked( $notification['to_admin'] ) ?>> <?php esc_html_e( 'Administrators', 'bookme_pro' ) ?>
                        </label>
                    </div>
                </div>
            </div>

            <?php $form->renderEditor( $id ) ?>

            <div class="form-group">
                <label><?php esc_html_e( 'Codes', 'bookme_pro' ) ?></label>
                <?php include '_codes.php' ?>
            </div>
        </div>
    </div>
</div>