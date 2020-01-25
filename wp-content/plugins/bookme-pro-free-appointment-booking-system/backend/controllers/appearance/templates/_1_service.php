<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
use BookmePro\Lib\Utils\Price;
use BookmePro\Lib\Utils\DateTime;

/**
 * @var BookmePro\Backend\Controllers\Appearance\Lib\Helper $editable
 * @var WP_Locale $wp_locale
 */
global $wp_locale;
?>
<div class="bookme-pro-form">
    <?php include '_progress_tracker.php' ?>

    <div class="bookme-pro-service-step">
        <div class="bookme-pro-box">
            <span class="bookme-pro-bold bookme-pro-desc">
                <?php $editable::renderText('bookme_pro_l10n_info_service_step') ?>
            </span>
        </div>
        <div class="bookme-pro-table">
            <div>
                <div class="bookme-pro-js-chain-item bookme-pro-box">
                    <?php if (\BookmePro\Lib\Config::locationsEnabled()) : ?>
                        <div class="bookme-pro-form-group">
                            <?php \BookmePro\Lib\Proxy\Locations::renderAppearance() ?>
                        </div>
                    <?php endif ?>
                    <div class="bookme-pro-form-group">
                        <?php $editable::renderLabel(array('bookme_pro_l10n_label_category', 'bookme_pro_l10n_option_category',)) ?>
                        <div>
                            <select class="bookme-pro-select-mobile bookme-pro-js-select-category">
                                <option value=""
                                        class="bookme-pro-js-option bookme_pro_l10n_option_category"><?php echo esc_html(get_option('bookme_pro_l10n_option_category')) ?></option>
                                <option value="1">Back Pain and Neck Pain</option>
                                <option value="2">Ankle Pain</option>
                                <option value="3">Hand Pain and Hand Injury</option>
                                <option value="4">Foot Pain and Foot Injury</option>
                            </select>
                        </div>
                    </div>
                    <div class="bookme-pro-form-group">
                        <?php $editable::renderLabel(array(
                            'bookme_pro_l10n_label_service',
                            'bookme_pro_l10n_option_service',
                            'bookme_pro_l10n_required_service',
                        )) ?>
                        <div>
                            <select class="bookme-pro-select-mobile bookme-pro-js-select-service">
                                <option value="0"
                                        class="bookme-pro-js-option bookme_pro_l10n_option_service"><?php echo esc_html(get_option('bookme_pro_l10n_option_service')) ?></option>
                                <option value="1" class="service-name-duration">Back Pain Treatment
                                    (<?php echo DateTime::secondsToInterval(3600) ?>)
                                </option>
                                <option value="-1" class="service-name">Back Pain Treatment</option>
                                <option value="2" class="service-name-duration">Spine Cancer
                                    (<?php echo DateTime::secondsToInterval(3600 * 2) ?>)
                                </option>
                                <option value="-2" class="service-name">Spine Cancer</option>
                                <option value="3" class="service-name-duration">Spine Fracture
                                    (<?php echo DateTime::secondsToInterval(3600 * 12) ?>)
                                </option>
                                <option value="-3" class="service-name">Spine Fracture</option>
                                <option value="4" class="service-name-duration">Scoliosis Treatment
                                    (<?php echo DateTime::secondsToInterval(3600 * 24) ?>)
                                </option>
                                <option value="-4" class="service-name">Scoliosis Treatment</option>
                                <option value="5" class="service-name-duration">Ankle Arthroscopy
                                    (<?php echo DateTime::secondsToInterval(3600 * 8) ?>)
                                </option>
                                <option value="-5" class="service-name">Ankle Arthroscopy</option>
                                <option value="6" class="service-name-duration">Ankle Replacement Surgery
                                    (<?php echo DateTime::secondsToInterval(3600 * 6) ?>)
                                </option>
                                <option value="-6" class="service-name">Ankle Replacement Surgery</option>
                                <option value="7" class="service-name-duration">Ankle Fusion
                                    (<?php echo DateTime::secondsToInterval(3600 * 16) ?>)
                                </option>
                                <option value="-7" class="service-name">Ankle Fusion</option>
                            </select>
                        </div>
                    </div>
                    <div class="bookme-pro-form-group">
                        <?php $editable::renderLabel(array(
                            'bookme_pro_l10n_label_employee',
                            'bookme_pro_l10n_option_employee',
                            'bookme_pro_l10n_required_employee',
                        )) ?>
                        <div>
                            <select class="bookme-pro-select-mobile bookme-pro-js-select-employee">
                                <option value="0"
                                        class="bookme-pro-js-option bookme_pro_l10n_option_employee"><?php echo esc_html(get_option('bookme_pro_l10n_option_employee')) ?></option>
                                <option value="1" class="employee-name-price">Laura White
                                    (<?php echo Price::format(350) ?>)
                                </option>
                                <option value="-1" class="employee-name">Laura White</option>
                                <option value="2" class="employee-name-price">Adam Footman
                                    (<?php echo Price::format(375) ?>)
                                </option>
                                <option value="-2" class="employee-name">Adam Footman</option>
                                <option value="3" class="employee-name-price">Magen Granger
                                    (<?php echo Price::format(300) ?>)
                                </option>
                                <option value="-3" class="employee-name">Magen Granger</option>
                                <option value="4" class="employee-name-price">Max Plank
                                    (<?php echo Price::format(400) ?>)
                                </option>
                                <option value="-4" class="employee-name">Max Plank</option>
                                <option value="5" class="employee-name-price">Lisa Hil
                                    (<?php echo Price::format(350) ?>)
                                </option>
                                <option value="-5" class="employee-name">Lisa Hil</option>
                                <option value="6" class="employee-name-price">Maanav Arthur
                                    (<?php echo Price::format(350) ?>)
                                </option>
                                <option value="-6" class="employee-name">Maanav Arthur</option>
                                <option value="7" class="employee-name-price">Ryan Dennis
                                    (<?php echo Price::format(380) ?>)
                                </option>
                                <option value="-7" class="employee-name">Ryan Dennis</option>
                                <option value="8" class="employee-name-price">Thomas Granger
                                    (<?php echo Price::format(390) ?>)
                                </option>
                                <option value="-8" class="employee-name">Thomas Granger</option>
                                <option value="9" class="employee-name-price">Paul Ordish
                                    (<?php echo Price::format(360) ?>)
                                </option>
                                <option value="-9" class="employee-name">Paul Ordish</option>
                                <option value="10" class="employee-name-price">Kyle Jordan
                                    (<?php echo Price::format(350) ?>)
                                </option>
                                <option value="-10" class="employee-name">Kyle Jordan</option>
                            </select>
                        </div>
                    </div>
                    <div class="bookme-pro-form-group">
                        <?php $editable::renderLabel(array('bookme_pro_l10n_label_number_of_persons',)) ?>
                        <div>
                            <select class="bookme-pro-select-mobile bookme-pro-js-select-number-of-persons">
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                            </select>
                        </div>
                    </div>
                    <?php if (\BookmePro\Lib\Config::multiplyAppointmentsEnabled()) : ?>
                        <div class="bookme-pro-form-group">
                            <?php \BookmePro\Lib\Proxy\MultiplyAppointments::renderAppearance() ?>
                        </div>
                    <?php endif ?>
                    <?php if (\BookmePro\Lib\Config::chainAppointmentsEnabled()) : ?>
                        <div class="bookme-pro-form-group">
                            <label></label>
                            <div>
                                <button class="bookme-pro-round"><i class="bookme-pro-icon-sm bookme-pro-icon-plus"></i>
                                </button>
                            </div>
                        </div>
                    <?php endif ?>
                </div>
            </div>
            <div>
                <div class="bookme-pro-box">
                    <div class="bookme-pro-left bookme-pro-mobile-float-none">
                        <div class="bookme-pro-available-date bookme-pro-js-available-date bookme-pro-left bookme-pro-mobile-float-none">
                            <div class="bookme-pro-form-group">
                                <?php $editable::renderLabel(array('bookme_pro_l10n_label_select_date', 'bookme_pro_l10n_calendar_availability')) ?>
                                <div class="bookme-pro-input-wrap bookme-pro-slot-calendar bookme-pro-js-slot-calendar">
                                    <input style="display: none" class="bookme-pro-date-from bookme-pro-js-date-from"
                                           type="text"
                                           value=""
                                           data-value="<?php echo date('Y-m-d') ?>"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bookme-pro-box bookme-pro-nav-steps">
            <div class="bookme-pro-right bookme-pro-mobile-prev-step bookme-pro-js-mobile-prev-step bookme-pro-btn bookme-pro-none">
                <?php $editable::renderString(array('bookme_pro_l10n_button_back')) ?>
            </div>
            <div class="bookme-pro-next-step bookme-pro-js-next-step bookme-pro-btn">
                <?php $editable::renderString(array('bookme_pro_l10n_step_service_button_next'), is_rtl() ? 'right' : 'left') ?>
            </div>
            <?php if (\BookmePro\Lib\Config::showStepCart()): ?>
                <button class="bookme-pro-go-to-cart bookme-pro-js-go-to-cart bookme-pro-round bookme-pro-round-md ladda-button"><span><img
                                src="<?php echo plugins_url('bookme-pro-free-appointment-booking-system/frontend/assets/images/cart.png') ?>"/></span>
                </button>
            <?php endif ?>
        </div>
    </div>
</div>
<div style="display: none">
    <?php foreach (array('bookme_pro_l10n_required_service', 'bookme_pro_l10n_required_name', 'bookme_pro_l10n_required_phone', 'bookme_pro_l10n_required_email', 'bookme_pro_l10n_required_employee', 'bookme_pro_l10n_required_location') as $validator) : ?>
        <div class="bookme-pro-js-option <?php echo $validator ?>"><?php echo get_option($validator) ?></div>
    <?php endforeach ?>
</div>
<style id="bookme-pro-pickadate-style"></style>
