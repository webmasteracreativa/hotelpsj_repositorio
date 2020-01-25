<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
/** @var BookmePro\Backend\Controllers\Appearance\Lib\Helper $editable */
?>
<ul class="bookme-pro-steps">
    <li <?php if ($step == 1) : ?>class="bookme-pro-steps-is-active"<?php endif ?>>
        <span><?php $editable::renderString(array('bookme_pro_l10n_step_service')) ?></span>
    </li>
    <li <?php if ($step == 2) : ?>class="bookme-pro-steps-is-active"<?php endif ?>>
        <span><?php $editable::renderString(array('bookme_pro_l10n_step_time')) ?></span>
    </li>
    <?php if (\BookmePro\Lib\Config::showStepCart()): ?>
        <li <?php if ($step == 3) : ?>class="bookme-pro-steps-is-active"<?php endif ?>>
            <span><?php $editable::renderString(array('bookme_pro_l10n_step_cart')) ?></span>
        </li>
    <?php endif ?>
    <li <?php if ($step == 4) : ?>class="bookme-pro-steps-is-active"<?php endif ?>>
        <span><?php $editable::renderString(array('bookme_pro_l10n_step_details')) ?></span>
    </li>
    <li <?php if ($step == 5) : ?>class="bookme-pro-steps-is-active"<?php endif ?>>
        <span><?php $editable::renderString(array('bookme_pro_l10n_step_done')) ?></span>
    </li>
</ul>