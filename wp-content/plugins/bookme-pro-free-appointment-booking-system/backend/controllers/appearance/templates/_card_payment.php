<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
/** @var BookmePro\Backend\Controllers\Appearance\Lib\Helper $editable */
?>
<div class="bookme-pro-box bookme-pro-table">
    <div class="bookme-pro-form-group" style="width:200px!important">
        <label>
            <?php $editable::renderString(array('bookme_pro_l10n_label_ccard_number',)) ?>
        </label>
        <div>
            <input type="text"/>
        </div>
    </div>
    <div class="bookme-pro-form-group">
        <label>
            <?php $editable::renderString(array('bookme_pro_l10n_label_ccard_expire',)) ?>
        </label>
        <div>
            <select class="bookme-pro-card-exp">
                <?php for ($i = 1; $i <= 12; ++$i) : ?>
                    <option value="<?php echo $i ?>"><?php printf('%02d', $i) ?></option>
                <?php endfor ?>
            </select>
            <select class="bookme-pro-card-exp">
                <?php for ($i = date('Y'); $i <= date('Y') + 10; ++$i) : ?>
                    <option value="<?php echo $i ?>"><?php echo $i ?></option>
                <?php endfor ?>
            </select>
        </div>
    </div>
</div>
<div class="bookme-pro-box bookme-pro-clear-bottom">
    <div class="bookme-pro-form-group">
        <label>
            <?php $editable::renderString(array('bookme_pro_l10n_label_ccard_code',)) ?>
        </label>
        <div>
            <input class="bookme-pro-card-cvc" type="text"/>
        </div>
    </div>
</div>