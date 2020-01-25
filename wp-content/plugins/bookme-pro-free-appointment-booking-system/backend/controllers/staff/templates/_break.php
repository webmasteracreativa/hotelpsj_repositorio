<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="bookme-pro-intervals-wrapper bookme-pro-hide-on-off" data-break_id="<?php echo $staff_schedule_item_break_id ?>">
    <div class="btn-group btn-group-sm bookme-pro-margin-top-sm">
        <button type="button" class="btn btn-info bookme-pro-js-toggle-popover break-interval"
                data-popover-content=".bookme-pro-js-content-break-<?php echo $staff_schedule_item_break_id ?>">
            <?php echo $formatted_interval ?>
        </button>
        <button title="<?php _e( 'Delete break', 'bookme_pro' ) ?>" type="button" class="btn btn-info delete-break ladda-button" data-style="zoom-in" data-spinner-size="20"><span class="ladda-label">&times;</span></button>
    </div>

    <div class="bookme-pro-js-content-break-<?php echo $staff_schedule_item_break_id ?> hidden">
        <div class="bookme-pro-js-schedule-form">
            <div class="bookme-pro-flexbox" style="width: 280px;">
                <div class="bookme-pro-flex-cell" style="width: 48%;">
                    <?php echo $break_start_choices ?>
                </div>
                <div class="bookme-pro-flex-cell" style="width: 4%;">
                    <div class="bookme-pro-margin-horizontal-lg">
                        <?php _e( 'to', 'bookme_pro' ) ?>
                    </div>
                </div>
                <div class="bookme-pro-flex-cell" style="width: 48%;">
                    <?php echo $break_end_choices ?>
                </div>
            </div>

            <hr>

            <div class="clearfix text-right">
                <?php \BookmePro\Lib\Utils\Common::submitButton( null, 'bookme-pro-js-save-break' ) ?>
                <?php \BookmePro\Lib\Utils\Common::customButton( null, 'bookme-pro-popover-close btn-lg btn-default', __( 'Close', 'bookme_pro' ) ) ?>
            </div>
        </div>
    </div>
</div>