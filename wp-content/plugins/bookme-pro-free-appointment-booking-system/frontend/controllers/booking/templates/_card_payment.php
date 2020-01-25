<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="bookme-pro-box bookme-pro-table">
    <div class="bookme-pro-form-group" style="width:200px!important">
        <label><?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption( 'bookme_pro_l10n_label_ccard_number' ) ?></label>
        <div>
            <input type="text" name="card_number" autocomplete="off" />
        </div>
    </div>
    <div class="bookme-pro-form-group">
        <label><?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption( 'bookme_pro_l10n_label_ccard_expire' ) ?></label>
        <div>
            <select class="bookme-pro-card-exp" name="card_exp_month">
                <?php for ( $i = 1; $i <= 12; ++ $i ) : ?>
                    <option value="<?php echo $i ?>"><?php printf( '%02d', $i ) ?></option>
                <?php endfor ?>
            </select>
            <select class="bookme-pro-card-exp" name="card_exp_year">
                <?php for ( $i = date( 'Y' ); $i <= date( 'Y' ) + 10; ++ $i ) : ?>
                    <option value="<?php echo $i ?>"><?php echo $i ?></option>
                <?php endfor ?>
            </select>
        </div>
    </div>
</div>
<div class="bookme-pro-box bookme-pro-clear-bottom">
    <div class="bookme-pro-form-group">
        <label><?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption( 'bookme_pro_l10n_label_ccard_code' ) ?></label>
        <div>
            <input type="text" class="bookme-pro-card-cvc" name="card_cvc" autocomplete="off" />
        </div>
    </div>
</div>
<div class="bookme-pro-label-error bookme-pro-js-card-error"></div>