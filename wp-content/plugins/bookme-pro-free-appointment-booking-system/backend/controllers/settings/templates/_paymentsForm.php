<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
use BookmePro\Lib\Utils\Common;
use BookmePro\Lib\Utils\Price;
use BookmePro\Lib\Proxy;

?>
<form method="post" action="<?php echo esc_url(add_query_arg('tab', 'payments')) ?>">
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group">
                <label for="bookme_pro_pmt_currency"><?php esc_html_e('Currency', 'bookme_pro') ?></label>
                <select id="bookme_pro_pmt_currency" class="form-control" name="bookme_pro_pmt_currency">
                    <?php foreach (Price::getCurrencies() as $code => $currency) : ?>
                        <option value="<?php echo $code ?>"
                                data-symbol="<?php esc_attr_e($currency['symbol']) ?>" <?php selected(get_option('bookme_pro_pmt_currency'), $code) ?> ><?php echo $code ?>
                            (<?php esc_html_e($currency['symbol']) ?>)
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group">
                <label for="bookme_pro_pmt_price_format"><?php esc_html_e('Price format', 'bookme_pro') ?></label>
                <select id="bookme_pro_pmt_price_format" class="form-control" name="bookme_pro_pmt_price_format">
                    <?php foreach (Price::getFormats() as $format) : ?>
                        <option value="<?php echo $format ?>" <?php selected(get_option('bookme_pro_pmt_price_format'), $format) ?> ></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <label for="bookme_pro_pmt_local"><?php esc_html_e('Service paid locally', 'bookme_pro') ?></label>
        </div>
        <div class="panel-body">
            <?php Common::optionToggle('bookme_pro_pmt_local', null, null, array(array('disabled', esc_html__('Disabled', 'bookme_pro')), array('1', esc_html__('Enabled', 'bookme_pro')))) ?>
        </div>
    </div>

    <?php BookmePro\Lib\Proxy\Shared::renderPaymentSettings() ?>

    <div class="panel-footer">
        <?php Common::csrf() ?>
        <?php Common::submitButton() ?>
        <?php Common::resetButton('bookme-pro-payments-reset') ?>
    </div>
</form>