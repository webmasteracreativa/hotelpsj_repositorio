<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/** @var BookmePro\Backend\Controllers\Appearance\Lib\Helper $editable */
?>
<div class="bookme-pro-form">
    <?php include '_progress_tracker.php' ?>
    <div class="bookme-pro-box bookme-pro-text-center bookme-pro-js-done-success">
        <?php $editable::renderText( 'bookme_pro_l10n_info_complete_step', $this->render( '_codes', array( 'step' => 8, 'extra_codes' => 1 ), false ) ) ?>
    </div>
    <div class="bookme-pro-box bookme-pro-text-center bookme-pro-js-done-limit-error collapse">
        <?php $editable::renderText( 'bookme_pro_l10n_info_complete_step_limit_error', $this->render( '_codes', array( 'step' => 8 ), false ) ) ?>
    </div>
    <div class="bookme-pro-box bookme-pro-text-center bookme-pro-js-done-processing collapse">
        <?php $editable::renderText( 'bookme_pro_l10n_info_complete_step_processing', $this->render( '_codes', array( 'step' => 8, 'extra_codes' => 1 ), false ) ) ?>
    </div>
</div>