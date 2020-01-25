<?php
/**
 * Template to show appearance page
 * @var array $steps list of steps in booking form, could be string (the name of step) or false if step disabled
 * @var string $custom_css custom css text
 */
?>
<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>

<?php if (trim($custom_css)) : ?>
    <style type="text/css">
        <?php echo $custom_css ?>
    </style>
<?php endif ?>

<div id="bookme-pro-tbs" class="wrap">
    <div class="bookme-pro-tbs-body">
        <?php \BookmePro\Lib\Utils\Common::pageHeader(esc_html__('Appearance', 'bookme_pro')); ?>
        <div class="panel panel-default bookme-pro-main">
            <div class="panel-body">
                <div id="bookme-pro-appearance">
                    <div class="row">
                        <div class="col-sm-3 col-lg-2 bookme-pro-color-picker-wrapper">
                            <input type="text" name="color" class="bookme-pro-js-color-picker"
                                   value="<?php form_option('bookme_pro_app_color') ?>"
                                   data-selected="<?php form_option('bookme_pro_app_color') ?>"/>
                        </div>
                        <div class="col-sm-9 col-lg-10">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox"
                                           id=bookme-pro-show-progress-tracker <?php checked(get_option('bookme_pro_app_show_progress_tracker')) ?>>
                                    <?php _e('Show form progress tracker', 'bookme_pro') ?>
                                </label>
                            </div>
                        </div>
                    </div>

                    <ul class="bookme-pro-nav bookme-pro-nav-tabs bookme-pro-margin-top-lg" role="tablist">
                        <?php $i = 1 ?>
                        <?php foreach ($steps as $step => $step_name) : ?>
                            <li class="bookme-pro-nav-item <?php if ($step == 1) : ?>active<?php endif ?>"
                                data-target="#bookme-pro-step-<?php echo $step ?>" data-toggle="tab">
                                <?php echo $i++ ?>. <?php echo esc_html($step_name) ?>
                            </li>
                        <?php endforeach ?>
                    </ul>

                    <?php if (!get_user_meta(get_current_user_id(), \BookmePro\Lib\Plugin::getPrefix() . 'dismiss_appearance_notice', true)): ?>
                        <div class="alert alert-info alert-dismissible fade in bookme-pro-margin-top-lg bookme-pro-margin-bottom-remove"
                             id="bookme-pro-js-hint-alert" role="alert">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?php _e('Click on the underlined text to edit.', 'bookme_pro') ?>
                        </div>
                    <?php endif ?>

                    <div class="row" id="bookme-pro-step-settings">
                        <div class="bookme-pro-js-service-settings bookme-pro-margin-top-lg">
                            <?php \BookmePro\Lib\Proxy\Shared::renderAppearanceStepServiceSettings() ?>
                            <div class="col-md-3">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox"
                                               id=bookme-pro-required-employee <?php checked(get_option('bookme_pro_app_required_employee')) ?>>
                                        <?php _e('Make selecting employee required', 'bookme_pro') ?>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox"
                                               id=bookme-pro-staff-name-with-price <?php checked(get_option('bookme_pro_app_staff_name_with_price')) ?>>
                                        <?php _e('Show service price next to employee name', 'bookme_pro') ?>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox"
                                               id=bookme-pro-service-name-with-duration <?php checked(get_option('bookme_pro_app_service_name_with_duration')) ?>>
                                        <?php _e('Show service duration next to service name', 'bookme_pro') ?>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bookme-pro-table bookme-pro-calendar-color">
                                    <div class="checkbox"><?php _e('Calendar Color', 'bookme_pro') ?></div>
                                    <div class="bookme-pro-color-picker-wrapper">
                                        <input type="text" name="color" class="bookme-pro-js-calendar-color-picker-left"
                                               value="<?php form_option('bookme_pro_cal_color_left') ?>"
                                               data-selected="<?php form_option('bookme_pro_cal_color_left') ?>"/>
                                    </div>
                                    <div class="bookme-pro-color-picker-wrapper">
                                        <input type="text" name="color" class="bookme-pro-js-calendar-color-picker-right"
                                               value="<?php form_option('bookme_pro_cal_color_right') ?>"
                                               data-selected="<?php form_option('bookme_pro_cal_color_right') ?>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox"
                                               id=bookme-pro-calendar-availability <?php checked(get_option('bookme_pro_app_show_calendar_availability')) ?>>
                                        <?php _e('Show available timeslots on calendar', 'bookme_pro') ?>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox"
                                               id=bookme-pro-auto-calendar-height <?php checked(get_option('bookme_pro_app_auto_calendar_size')) ?>>
                                        <?php _e('Set calendar size automatically', 'bookme_pro') ?>
                                    </label>
                                    <p class="help-block"><?php _e('Set the calendar height and width equal, according to screen size.', 'bookme_pro') ?></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="checkbox">
                                    <select id="bookme-pro-app-layout" class="form-control">
                                        <option value="1" <?php selected(get_option('bookme_pro_app_layout'),1) ?>><?php _e('Layout: One column', 'bookme_pro') ?></option>
                                        <option value="2" <?php selected(get_option('bookme_pro_app_layout'),2) ?>><?php _e('Layout: Two column', 'bookme_pro') ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="bookme-pro-js-details-settings bookme-pro-margin-top-lg" style="display:none">
                            <div class="col-md-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox"
                                               id="bookme-pro-cst-required-phone" <?php checked(get_option('bookme_pro_cst_required_phone')) ?>>
                                        <?php _e('Make phone field required', 'bookme_pro') ?>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox"
                                               id="bookme-pro-show-login-button" <?php checked(get_option('bookme_pro_app_show_login_button')) ?>>
                                        <?php _e('Show Login button', 'bookme_pro') ?>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox"
                                               id="bookme-pro-cst-first-last-name" <?php checked(get_option('bookme_pro_cst_first_last_name')) ?>
                                               data-toggle="popover" data-trigger="focus" data-placement="auto bottom"
                                               data-content="<?php _e('Do not forget to update your email and SMS codes for customer names', 'bookme_pro') ?>">
                                        <?php _e('Use first and last name instead of full name', 'bookme_pro') ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="bookme-pro-js-done-settings bookme-pro-margin-top-lg" style="display:none">
                            <div class="col-md-12">
                                <div class="alert alert-info bookme-pro-margin-top-lg bookme-pro-margin-bottom-remove bookme-pro-flexbox">
                                    <div class="bookme-pro-flex-row">
                                        <div class="bookme-pro-flex-cell" style="width:39px"><i class="alert-icon"></i>
                                        </div>
                                        <div class="bookme-pro-flex-cell">
                                            <div>
                                                <?php _e('The booking form on this step may have different set or states of its elements. Select option and click on the underlined text to edit.', 'bookme_pro') ?>
                                            </div>
                                            <div class="bookme-pro-margin-top-lg">
                                                <select id="bookme-pro-done-step-view" class="form-control">
                                                    <option value="booking-success"><?php _e('Form view in case of successful booking', 'bookme_pro') ?></option>
                                                    <option value="booking-limit-error"><?php _e('Form view in case the number of bookings exceeds the limit', 'bookme_pro') ?></option>
                                                    <option value="booking-processing"><?php _e('Form view in case of payment has been accepted for processing', 'bookme_pro') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default bookme-pro-margin-top-lg">
                        <div class="panel-body">
                            <div class="tab-content">
                                <?php foreach ($steps as $step => $step_name) : ?>
                                    <div id="bookme-pro-step-<?php echo $step ?>"
                                         class="tab-pane <?php if ($step == 1) : ?>active<?php endif ?>"
                                         data-target="<?php echo $step ?>">
                                        <?php // Render unique data per step
                                        switch ($step) :
                                            case 1:
                                                include '_1_service.php';
                                                break;
                                            case 2:
                                                include '_2_time.php';
                                                break;
                                            case 3:
                                                include '_3_cart.php';
                                                break;
                                            case 4:
                                                include '_4_details.php';
                                                break;
                                            case 5:
                                                include '_5_complete.php';
                                                break;
                                        endswitch ?>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        </div>
                    </div>
                    <div>
                        <?php $this->render('_custom_css', array('custom_css' => $custom_css)); ?>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <?php \BookmePro\Lib\Utils\Common::submitButton('ajax-send-appearance') ?>
                <?php \BookmePro\Lib\Utils\Common::resetButton() ?>
            </div>
        </div>
    </div>
</div>