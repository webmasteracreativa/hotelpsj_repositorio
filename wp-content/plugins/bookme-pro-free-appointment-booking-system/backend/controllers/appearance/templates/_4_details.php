<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
use BookmePro\Lib\Utils\Price;
/** @var BookmePro\Backend\Controllers\Appearance\Lib\Helper $editable */
?>
<div class="bookme-pro-form">
    <?php include '_progress_tracker.php' ?>

    <div class="bookme-pro-box">
        <?php $editable::renderText('bookme_pro_l10n_info_details_step', $this->render('_codes', array('step' => 6), false)) ?>
    </div>
    <div class="bookme-pro-box">
        <?php $editable::renderText('bookme_pro_l10n_info_details_step_guest', $this->render('_codes', array('step' => 6, 'extra_codes' => 1), false), 'bottom', esc_html__('Visible to non-logged in customers only', 'bookme_pro')) ?>
    </div>
    <div class="bookme-pro-box" id="bookme-pro-js-show-login-form">
        <div class="bookme-pro-btn bookme-pro-inline-block">
            <?php $editable::renderString(array('bookme_pro_l10n_step_details_button_login')) ?>
        </div>
    </div>
    <div class="bookme-pro-details-step bookme-pro-table">
        <div>
            <div class="bookme-pro-box bookme-pro-details-first-last-name"
                 style="display: <?php echo get_option('bookme_pro_cst_first_last_name') == 0 ? ' none' : 'block' ?>">
                <div class="bookme-pro-form-group">
                    <?php $editable::renderLabel(array('bookme_pro_l10n_label_first_name', 'bookme_pro_l10n_required_first_name',)) ?>
                    <div>
                        <input type="text" value="" maxlength="60"/>
                    </div>
                </div>
                <div class="bookme-pro-form-group">
                    <?php $editable::renderLabel(array('bookme_pro_l10n_label_last_name', 'bookme_pro_l10n_required_last_name',)) ?>
                    <div>
                        <input type="text" value="" maxlength="60"/>
                    </div>
                </div>
            </div>
            <div class="bookme-pro-box">
                <div class="bookme-pro-form-group bookme-pro-details-full-name"
                     style="display: <?php echo get_option('bookme_pro_cst_first_last_name') == 1 ? ' none' : 'block' ?>">
                    <?php $editable::renderLabel(array('bookme_pro_l10n_label_name', 'bookme_pro_l10n_required_name',)) ?>
                    <div>
                        <input type="text" value="" maxlength="60"/>
                    </div>
                </div>
                <div class="bookme-pro-form-group">
                    <?php $editable::renderLabel(array('bookme_pro_l10n_label_phone', 'bookme_pro_l10n_required_phone',)) ?>
                    <div>
                        <input type="text"
                               class="<?php if (get_option('bookme_pro_cst_phone_default_country') != 'disabled') : ?>bookme-pro-user-phone<?php endif ?>"
                               value=""/>
                    </div>
                </div>
                <div class="bookme-pro-form-group">
                    <?php $editable::renderLabel(array('bookme_pro_l10n_label_email', 'bookme_pro_l10n_required_email',)) ?>
                    <div>
                        <input maxlength="40" type="text" value=""/>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div>
                    <div class="bookme-pro-box bookme-pro-appointment">
                        <div class="bookme-pro-table">
                            <div>
                                <?php $editable::renderString( array( 'bookme_pro_l10n_label_time', ) ) ?>
                            </div>
                            <div>
                                <?php $editable::renderText('bookme_pro_l10n_label_service_time', $this->render('_service_codes', array('step' => '1'), false)) ?>
                            </div>
                        </div>
                        <div class="bookme-pro-table">
                            <div>
                                <?php $editable::renderText('bookme_pro_l10n_label_service_details', $this->render('_service_codes', array('step' => '2'), false)) ?>
                            </div>
                            <div>
                                <strong><?php echo Price::format( 350 ) ?></strong>
                            </div>
                        </div>
                    </div>
                <div class="bookme-pro-box bookme-pro-appointment">
                    <div class="bookme-pro-table">
                        <div>
                            <?php $editable::renderString( array( 'bookme_pro_l10n_label_total', ) ) ?>
                        </div>
                        <div>
                            <strong id="bookme-pro-js-coupon-total"><?php echo Price::format( 350 ) ?></strong>
                        </div>
                    </div>
                </div>
                <div class="bookme-pro-payment-nav">
                    <div class="bookme-pro-box"><strong><?php $editable::renderString( array( 'bookme_pro_l10n_label_choose_payment', ) ) ?></strong></div>
                    <div class="bookme-pro-box bookme-pro-list">
                        <label>
                            <input type="radio" name="payment" checked="checked" />
                            <?php $editable::renderString( array( 'bookme_pro_l10n_label_pay_locally', ) ) ?>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php \BookmePro\Lib\Proxy\RecurringAppointments::renderAppearanceEditableInfoMessage() ?>

    <div class="bookme-pro-box bookme-pro-nav-steps">
        <div class="bookme-pro-back-step bookme-pro-js-back-step bookme-pro-btn">
            <?php $editable::renderString(array('bookme_pro_l10n_button_back')) ?>
        </div>
        <div class="bookme-pro-next-step bookme-pro-js-next-step bookme-pro-btn">
            <?php $editable::renderString(array('bookme_pro_l10n_step_details_button_next'),is_rtl() ? 'right' : 'left') ?>
        </div>
    </div>
</div>
