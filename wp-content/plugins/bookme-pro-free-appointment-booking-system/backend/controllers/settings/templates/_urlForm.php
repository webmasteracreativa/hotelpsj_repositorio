<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
use BookmePro\Lib\Proxy;
use BookmePro\Lib\Utils\Common;

?>
<form method="post" action="<?php echo esc_url(add_query_arg('tab', 'url')) ?>">
    <?php
    Common::optionText('bookme_pro_url_approve_page_url', esc_html__('Approve appointment URL (success)', 'bookme_pro'), esc_html__('Set the URL of a page that is shown to staff after they successfully approved the appointment.', 'bookme_pro'));
    Common::optionText('bookme_pro_url_approve_denied_page_url', esc_html__('Approve appointment URL (denied)', 'bookme_pro'), esc_html__('Set the URL of a page that is shown to staff when the approval of appointment cannot be done (due to capacity, changed status, etc.).', 'bookme_pro'));
    Common::optionText('bookme_pro_url_cancel_page_url', esc_html__('Cancel appointment URL (success)', 'bookme_pro'), esc_html__('Set the URL of a page that is shown to clients after they successfully cancelled their appointment.', 'bookme_pro'));
    Common::optionText('bookme_pro_url_cancel_denied_page_url', esc_html__('Cancel appointment URL (denied)', 'bookme_pro'), esc_html__('Set the URL of a page that is shown to clients when the cancellation of appointment is not available anymore.', 'bookme_pro'));
    Common::optionText('bookme_pro_url_reject_page_url', esc_html__('Reject appointment URL (success)', 'bookme_pro'), esc_html__('Set the URL of a page that is shown to staff after they successfully rejected the appointment.', 'bookme_pro'));
    Common::optionText('bookme_pro_url_reject_denied_page_url', esc_html__('Reject appointment URL (denied)', 'bookme_pro'), esc_html__('Set the URL of a page that is shown to staff when the rejection of appointment cannot be done (due to changed status, etc.).', 'bookme_pro'));
    ?>
    <?php Proxy\Shared::renderUrlSettings() ?>
    <div class="panel-footer">
        <?php Common::csrf() ?>
        <?php Common::submitButton() ?>
        <?php Common::resetButton() ?>
    </div>
</form>