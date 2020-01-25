<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
/** @var \BookmePro\Lib\UserBookingData $userData */
echo $progress_tracker;
?>
<div class="bookme-pro-service-step">
    <div class="bookme-pro-box bookme-pro-bold"><?php echo $info_text ?></div>
    <div class="<?php echo $layout == 1 ? 'bookme-pro-table-row' : 'bookme-pro-table' ?>">
        <div id="bookme-pro-service-part1">
            <div class="bookme-pro-js-chain-item bookme-pro-js-draft bookme-pro-box" style="display: none;">
                <div class="bookme-pro-form-group">
                    <label><?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_label_category') ?></label>
                    <div>
                        <select class="bookme-pro-select-mobile bookme-pro-js-select-category">
                            <option value=""><?php echo esc_html(\BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_option_category')) ?></option>
                        </select>
                    </div>
                </div>
                <div class="bookme-pro-form-group">
                    <label><?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_label_service') ?></label>
                    <div>
                        <select class="bookme-pro-select-mobile bookme-pro-js-select-service">
                            <option value=""><?php echo esc_html(\BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_option_service')) ?></option>
                        </select>
                    </div>
                    <div class="bookme-pro-js-select-service-error bookme-pro-label-error" style="display: none">
                        <?php echo esc_html(\BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_required_service')) ?>
                    </div>
                </div>
                <div class="bookme-pro-form-group">
                    <label><?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_label_employee') ?></label>
                    <div>
                        <select class="bookme-pro-select-mobile bookme-pro-js-select-employee">
                            <option value=""><?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_option_employee') ?></option>
                        </select>
                    </div>
                    <div class="bookme-pro-js-select-employee-error bookme-pro-label-error" style="display: none">
                        <?php echo esc_html(\BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_required_employee')) ?>
                    </div>
                </div>
                <div class="bookme-pro-form-group">
                    <label><?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_label_number_of_persons') ?></label>
                    <div>
                        <select class="bookme-pro-select-mobile bookme-pro-js-select-number-of-persons">
                            <option value="1">1</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div id="bookme-pro-service-part2">
            <div class="bookme-pro-box">
                <div class="bookme-pro-left bookme-pro-mobile-float-none">
                    <div class="bookme-pro-available-date bookme-pro-js-available-date bookme-pro-left bookme-pro-mobile-float-none">
                        <div class="bookme-pro-form-group">
                            <label class="bookme-pro-bold"><?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_label_select_date') ?></label>
                            <div class="bookme-pro-input-wrap bookme-pro-slot-calendar bookme-pro-js-slot-calendar">
                                <input style="display: none" class="bookme-pro-date-from bookme-pro-js-date-from" type="text"
                                       value=""
                                       data-value="<?php echo esc_attr($userData->get('date_from')) ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (!empty ($times)) : ?>
                    <?php reset($times); ?>
                    <input class="bookme-pro-js-select-time-from" type="hidden" value="<?php echo key($times); ?>">
                    <?php end($times); ?>
                    <input class="bookme-pro-js-select-time-to" type="hidden" value="<?php echo key($times); ?>">
                <?php endif ?>
            </div>
        </div>
    </div>
    <div class="bookme-pro-box bookme-pro-nav-steps">
        <button class="bookme-pro-next-step bookme-pro-js-next-step bookme-pro-btn ladda-button" data-style="zoom-in"
                data-spinner-size="40">
            <span class="ladda-label"><?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_step_service_button_next') ?></span>
        </button>
        <?php if ($show_cart_btn) : ?>
            <button class="bookme-pro-go-to-cart bookme-pro-js-go-to-cart bookme-pro-round bookme-pro-round-md ladda-button"
                    data-style="zoom-in" data-spinner-size="30"><span class="ladda-label"><img
                            src="<?php echo plugins_url('bookme-pro-free-appointment-booking-system/frontend/assets/images/cart.png') ?>"/></span>
            </button>
        <?php endif ?>
        Add in your site, <a href="https://codentheme.com/item/bookmepro-wordpress-appointment-booking-scheduling-plugin/">WordPress Booking Plugin</a>
    </div>

</div>