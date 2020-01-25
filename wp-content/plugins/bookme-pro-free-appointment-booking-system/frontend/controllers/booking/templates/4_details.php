<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
use BookmePro\Lib\Utils\Common;
use BookmePro\Lib\Config;
use BookmePro\Lib\Plugin;

/** @var \BookmePro\Lib\UserBookingData $userData */
echo $progress_tracker;
?>

<div class="bookme-pro-box"><?php echo $info_text ?></div>
<?php if ($info_text_guest) : ?>
    <div class="bookme-pro-box bookme-pro-js-guest"><?php echo $info_text_guest ?></div>
<?php endif ?>
<?php if (get_option('bookme_pro_app_show_login_button') && get_current_user_id() == 0) : ?>
    <div class="bookme-pro-box bookme-pro-js-guest">
        <button class="bookme-pro-btn bookme-pro-inline-block bookme-pro-js-login-show ladda-button"><?php echo Common::getTranslatedOption('bookme_pro_l10n_step_details_button_login') ?></button>
    </div>
<?php endif ?>

<div class="bookme-pro-details-step bookme-pro-table">
    <div>
        <?php if (Config::showFirstLastName()) : ?>
            <div class="bookme-pro-box">
                <div class="bookme-pro-form-group">
                    <label><?php echo Common::getTranslatedOption('bookme_pro_l10n_label_first_name') ?></label>
                    <div>
                        <input class="bookme-pro-js-first-name" type="text"
                               value="<?php echo esc_attr($userData->get('first_name')) ?>"/>
                    </div>
                    <div class="bookme-pro-js-first-name-error bookme-pro-label-error"></div>
                </div>
                <div class="bookme-pro-form-group">
                    <label><?php echo Common::getTranslatedOption('bookme_pro_l10n_label_last_name') ?></label>
                    <div>
                        <input class="bookme-pro-js-last-name" type="text"
                               value="<?php echo esc_attr($userData->get('last_name')) ?>"/>
                    </div>
                    <div class="bookme-pro-js-last-name-error bookme-pro-label-error"></div>
                </div>
            </div>
        <?php endif ?>
        <div class="bookme-pro-box">
            <?php if (!get_option('bookme_pro_cst_first_last_name')) : ?>
                <div class="bookme-pro-form-group">
                    <label><?php echo Common::getTranslatedOption('bookme_pro_l10n_label_name') ?></label>
                    <div>
                        <input class="bookme-pro-js-full-name" type="text"
                               value="<?php echo esc_attr($userData->get('full_name')) ?>"/>
                    </div>
                    <div class="bookme-pro-js-full-name-error bookme-pro-label-error"></div>
                </div>
            <?php endif ?>
            <div class="bookme-pro-form-group">
                <label><?php echo Common::getTranslatedOption('bookme_pro_l10n_label_phone') ?></label>
                <div>
                    <input class="bookme-pro-js-user-phone-input<?php if (get_option('bookme_pro_cst_phone_default_country') != 'disabled') : ?> bookme-pro-user-phone<?php endif ?>"
                           value="<?php echo esc_attr($userData->get('phone')) ?>" type="text"/>
                </div>
                <div class="bookme-pro-js-user-phone-error bookme-pro-label-error"></div>
            </div>
            <div class="bookme-pro-form-group">
                <label><?php echo Common::getTranslatedOption('bookme_pro_l10n_label_email') ?></label>
                <div>
                    <input class="bookme-pro-js-user-email" maxlength="255" type="text"
                           value="<?php echo esc_attr($userData->get('email')) ?>"/>
                </div>
                <div class="bookme-pro-js-user-email-error bookme-pro-label-error"></div>
            </div>
        </div>
        <?php foreach ($cf_data as $key => $cf_item) : ?>
            <div class="bookme-pro-custom-fields-container" data-key="<?php echo $key ?>">
                <?php foreach ($cf_item['custom_fields'] as $custom_field) : ?>
                    <div class="bookme-pro-box bookme-pro-custom-field-row" data-id="<?php echo $custom_field->id ?>"
                         data-type="<?php echo $custom_field->type ?>">
                        <div class="bookme-pro-form-group">
                            <label><?php echo $custom_field->label; ?></label>
                            <div>
                                <?php if ($custom_field->type == 'textarea') : ?>
                                    <textarea rows="3"
                                              class="bookme-pro-custom-field"><?php isset($cf_item['data'][$custom_field->id]) ? $cf_item['data'][$custom_field->id] : null; ?></textarea>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endforeach ?>
    </div>
    <?php if (!$disabled): ?>
        <div>
            <div>
                <?php foreach ($booking_data as $data): ?>
                    <div class="bookme-pro-box bookme-pro-appointment">
                        <div class="bookme-pro-table">
                            <div>
                                <?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_label_time') ?>
                            </div>
                            <div>
                                <?php echo str_replace(
                                    array(
                                        '{service_date}',
                                        '{service_time}'
                                    ),
                                    array(
                                        $data['service_date'],
                                        $data['service_time']
                                    ),
                                    \BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_label_service_time')
                                ) ?>
                            </div>
                        </div>
                        <div class="bookme-pro-table">
                            <div>
                                <?php echo str_replace(
                                    array(
                                        '{category_name}',
                                        '{number_of_persons}',
                                        '{service_name}',
                                        '{staff_name}',
                                        'x'
                                    ),
                                    array(
                                        '<strong>' . $data['category_name'] . '</strong>',
                                        '<strong>' . $data['number_of_persons'] . '</strong>',
                                        '<strong>' . $data['service_name'] . '</strong>',
                                        '<strong>' . $data['staff_name'] . '</strong>',
                                        '&times;'
                                    ),
                                    \BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_label_service_details')) ?>
                            </div>
                            <div>
                                <strong><?php echo $data['service_price']; ?></strong>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="bookme-pro-box bookme-pro-appointment">
                    <div class="bookme-pro-table">
                        <div>
                            <?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_label_total') ?>
                        </div>
                        <div>
                            <strong id="bookme-pro-js-coupon-total"><?php echo $total; ?></strong>
                        </div>
                    </div>
                </div>
                <?php if (!\BookmePro\Lib\Config::wooCommerceEnabled()): ?>
                    <div class="bookme-pro-payment-nav">
                        <div class="bookme-pro-box">
                            <strong><?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_label_choose_payment') ?></strong>
                        </div>
                        <?php if ($pay_local) : ?>
                            <div class="bookme-pro-box bookme-pro-list">
                                <label>
                                    <input type="radio" class="bookme-pro-payment"
                                           name="payment-method-<?php echo $form_id ?>"
                                           value="local"/>
                                    <span><?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_label_pay_locally') ?></span>
                                </label>
                            </div>
                        <?php endif ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php $this->render('_info_block', compact('info_message')) ?>


<div class="bookme-pro-gateway-buttons bookme-pro-box bookme-pro-nav-steps">
    <button class="bookme-pro-back-step bookme-pro-js-back-step bookme-pro-btn ladda-button" data-style="zoom-in"
            data-spinner-size="40">
        <span class="ladda-label"><?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_button_back') ?></span>
    </button>
    <button class="bookme-pro-next-step bookme-pro-js-next-step bookme-pro-btn ladda-button" data-style="zoom-in"
            data-spinner-size="40">
        <span class="ladda-label"><?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_step_details_button_next') ?></span>
    </button>
</div>