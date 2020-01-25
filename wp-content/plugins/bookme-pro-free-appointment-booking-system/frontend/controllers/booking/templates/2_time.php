<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
echo $progress_tracker;
?>
<div class="bookme-pro-box">
    <div><?php echo $info_text ?></div>
    <div class="bookme-pro-holder bookme-pro-label-error bookme-pro-bold"></div>
</div>
<?php if ($has_slots) : ?>
<div class="bookme-pro-time-step">
    <div class="bookme-pro-columnizer-wrap">
        <div class="bookme-pro-columnizer">
            <?php /* here _time_slots */ ?>
        </div>
    </div>
</div>
<div class="bookme-pro-box bookme-pro-nav-steps bookme-pro-clear">
    <button class="bookme-pro-time-next bookme-pro-btn bookme-pro-right ladda-button" data-style="zoom-in" data-spinner-size="40">
        <span class="ladda-label"><img src="<?php echo $arrow_img; ?>"></span>
    </button>
    <button class="bookme-pro-time-prev bookme-pro-btn bookme-pro-right ladda-button" data-style="zoom-in" style="display: none"
            data-spinner-size="40">
        <span class="ladda-label"><img src="<?php echo $arrow_img; ?>"></span>
    </button>
    <?php else : ?>
    <div class="bookme-pro-not-time-screen bookme-pro-not-calendar">
        <?php esc_html_e('No time is available for selected criteria.', 'bookme_pro') ?>
    </div>
    <div class="bookme-pro-box bookme-pro-nav-steps">
        <?php endif ?>
        <button class="bookme-pro-back-step bookme-pro-js-back-step bookme-pro-btn ladda-button" data-style="zoom-in"
                data-spinner-size="40">
            <span class="ladda-label"><?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_button_back') ?></span>
        </button>
        <?php if ($show_cart_btn) : ?>
            <button class="bookme-pro-go-to-cart bookme-pro-js-go-to-cart bookme-pro-round bookme-pro-round-md ladda-button"
                    data-style="zoom-in" data-spinner-size="30">
                <span class="ladda-label"><img
                            src="<?php echo plugins_url('bookme-pro-free-appointment-booking-system/frontend/assets/images/cart.png') ?>"/></span>
            </button>
        <?php endif ?>
    </div>
