<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
/** @var BookmePro\Backend\Controllers\Appearance\Lib\Helper $editable */

?>
<div class="bookme-pro-form">
    <?php include '_progress_tracker.php' ?>

    <div class="bookme-pro-box">
        <?php $editable::renderText('bookme_pro_l10n_info_time_step', $this->render('_codes', array('step' => 3), false)) ?>
        <div class="bookme-pro-holder bookme-pro-label-error bookme-pro-bold">
            <?php $editable::renderText('bookme_pro_l10n_step_time_slot_not_available', null, 'bottom', esc_html__('Visible when the chosen time slot has been already booked', 'bookme_pro')) ?>
        </div>
    </div>
    <!-- timeslots -->
    <div>
        <div class="bookme-pro-columnizer-wrap">
            <div class="bookme-pro-columnizer">
                <div id="bookme-pro-day-one-column">
                    <div class="bookme-pro-column col1">
                        <button class="bookme-pro-day bookme-pro-js-first-child"><?php echo date_i18n('D, M d', strtotime('+0 days', current_time('timestamp'))) ?></button>
                        <?php for ($j = 28800; $j <= 46800; $j += 3600) :
                            $time = \BookmePro\Lib\Utils\DateTime::formatTime($j) . ' - ' . \BookmePro\Lib\Utils\DateTime::formatTime($j + 3600);
                            ?>
                            <button class="bookme-pro-hour">
                            <span class="bookme-pro-hour-time">
                                <?php echo $time ?>
                            </span>
                                <span class="ladda-label">
                                    <span class="bookme-pro-button-time"><?php echo $time ?></span>
                                    <span class="bookme-pro-button-text"><?php $editable::renderString(array('bookme_pro_l10n_button_book_now'), is_rtl() ? 'right' : 'left'); ?></span>
                                </span>
                            </button>
                        <?php endfor ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bookme-pro-box bookme-pro-nav-steps">
        <button class="bookme-pro-time-next bookme-pro-btn bookme-pro-right ladda-button">
            <span class="bookme-pro-label"><img
                        src="<?php echo plugins_url('bookme-pro-free-appointment-booking-system/frontend/assets/images/calendar-arrow.png') ?>"></span>
        </button>
        <button class="bookme-pro-time-prev bookme-pro-btn bookme-pro-right ladda-button">
            <span class="bookme-pro-label"><img
                        src="<?php echo plugins_url('bookme-pro-free-appointment-booking-system/frontend/assets/images/calendar-arrow.png') ?>"></span>
        </button>
        <div class="bookme-pro-back-step bookme-pro-js-back-step bookme-pro-btn">
            <?php $editable::renderString(array('bookme_pro_l10n_button_back')) ?>
        </div>
        <?php if (\BookmePro\Lib\Config::showStepCart()): ?>
            <button class="bookme-pro-go-to-cart bookme-pro-js-go-to-cart bookme-pro-round bookme-pro-round-md ladda-button"
                    data-style="zoom-in" data-spinner-size="30"><span class="ladda-label"><img
                            src="<?php echo plugins_url('bookme-pro-free-appointment-booking-system/frontend/assets/images/cart.png') ?>"/></span>
            </button>
        <?php endif ?>
    </div>
</div>
