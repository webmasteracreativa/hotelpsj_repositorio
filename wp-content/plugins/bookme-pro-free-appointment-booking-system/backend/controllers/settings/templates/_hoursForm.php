<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
$start_of_week = (int)get_option('start_of_week');
$form = new \BookmePro\Backend\Controllers\Settings\Forms\BusinessHours()
?>
<p>
    <?php esc_html_e('Please note, the business hours below work as a template for all new staff members. To render a list of available time slots the system takes into account only staff members\' schedule, not the company business hours. Be sure to check the schedule of your staff members if you have some unexpected behavior of the booking system.', 'bookme_pro') ?>
</p>
<form method="post" action="<?php echo esc_url(add_query_arg('tab', 'business_hours')) ?>" id="business-hours">
    <?php for ($i = 0; $i < 7; $i++) :
        $day = strtolower(\BookmePro\Lib\Utils\DateTime::getWeekDayByNumber(($i + $start_of_week) % 7));
        ?>
        <div class="row">
            <div class="form-group col-sm-7 col-xs-8">
                <label><?php esc_html_e(ucfirst($day)) ?> </label>
                <div class="bookme-pro-flexbox">
                    <div class="bookme-pro-flex-cell" style="width: 45%">
                        <?php echo $form->renderField('bookme_pro_bh_' . $day) ?>
                    </div>
                    <div class="bookme-pro-flex-cell text-center" style="width: 10%">
                        <div class="bookme-pro-margin-horizontal-sm bookme-pro-hide-on-off"><?php esc_html_e('to', 'bookme_pro') ?></div>
                    </div>
                    <div class="bookme-pro-flex-cell" style="width: 45%">
                        <?php echo $form->renderField('bookme_pro_bh_' . $day, false) ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endfor ?>

    <div class="panel-footer">
        <?php \BookmePro\Lib\Utils\Common::csrf() ?>
        <?php \BookmePro\Lib\Utils\Common::submitButton() ?>
        <?php \BookmePro\Lib\Utils\Common::resetButton('bookme-pro-hours-reset') ?>
    </div>
</form>