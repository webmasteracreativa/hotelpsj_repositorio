<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>
<ul class="bookme-pro-steps">
    <?php if ($skip_service_step == false) : ?>
        <li <?php if ($step == 1) : ?>class="bookme-pro-steps-is-active"<?php endif ?>>
            <span><?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_step_service') ?></span>
        </li>
    <?php endif ?>
    <li <?php if ($step == 2) : ?>class="bookme-pro-steps-is-active"<?php endif ?>>
        <span><?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_step_time') ?></span>
    </li>
    <?php if ($show_cart) : ?>
        <li <?php if ($step == 3) : ?>class="bookme-pro-steps-is-active"<?php endif ?>>
            <span><?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_step_cart') ?></span>
        </li>
    <?php endif ?>
    <li <?php if ($step == 4) : ?>class="bookme-pro-steps-is-active"<?php endif ?>>
        <span><?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_step_details') ?></span>
    </li>
    <li <?php if ($step == 5) : ?>class="bookme-pro-steps-is-active"<?php endif ?>>
        <span><?php echo \BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_step_done') ?></span>
    </li>
</ul>