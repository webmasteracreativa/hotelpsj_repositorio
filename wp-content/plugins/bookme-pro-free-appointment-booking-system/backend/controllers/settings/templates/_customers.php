<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
use BookmePro\Lib\Utils\Common;

?>
<form method="post" action="<?php echo esc_url(add_query_arg('tab', 'customers')) ?>">
    <?php
    Common::optionToggle('bookme_pro_cst_phone_default_country', esc_html__('Phone field default country', 'bookme_pro'), esc_html__('Select default country for the phone field in the \'Details\' step of booking. You can also let Bookme Pro determine the country based on the IP address of the client.', 'bookme_pro'),
        array(array('disabled', esc_html__('Disabled', 'bookme_pro')), array('auto', esc_html__('Guess country by user\'s IP address', 'bookme_pro'))));
    Common::optionText('bookme_pro_cst_default_country_code', esc_html__('Default country code', 'bookme_pro'), esc_html__('Your clients must have their phone numbers in international format in order to receive text messages. However you can specify a default country code that will be used as a prefix for all phone numbers that do not start with "+" or "00". E.g. if you enter "1" as the default country code and a client enters their phone as "(600) 555-2222" the resulting phone number to send the SMS to will be "+1600555222".', 'bookme_pro'));
    Common::optionToggle('bookme_pro_cst_remember_in_cookie', esc_html__('Remember personal information in cookies', 'bookme_pro'), esc_html__('If this setting is enabled then returning customers will have their personal information fields filled in at the Details step with the data previously saved in cookies.', 'bookme_pro'));
    Common::optionToggle('bookme_pro_cst_show_update_details_dialog', esc_html__('Show confirmation dialog before updating customer\'s data', 'bookme_pro'), esc_html__('If this option is enabled and customer enters contact info different from the previous order, a warning message will appear asking to update the data.', 'bookme_pro'));
    ?>
    <div class="panel-footer">
        <?php Common::csrf() ?>
        <?php Common::submitButton() ?>
        <?php Common::resetButton('bookme-pro-customer-reset') ?>
    </div>
</form>