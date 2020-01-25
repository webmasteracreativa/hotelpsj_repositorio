<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    use BookmePro\Backend\Controllers\Staff\Forms\Widgets\TimeChoice;
    use BookmePro\Lib\Utils\Common;
    /** @var \BookmePro\Lib\Entities\StaffScheduleItem[] $schedule_items */
    $working_start  = new TimeChoice( array( 'empty_value' => __( 'OFF', 'bookme_pro' ), 'type' => 'from' ) );
    $working_end    = new TimeChoice( array( 'use_empty' => false, 'type' => 'to' ) );
    $default_breaks = array( 'staff_id' => $staff_id );
    $break_start   = new TimeChoice( array( 'use_empty' => false, 'type' => 'break_from' ) );
    $break_end     = clone $working_end;
?>
<div>
    <form>
        <?php foreach ( $schedule_items as $item ) : ?>
            <div data-id="<?php echo $item->getDayIndex() ?>"
                data-staff_schedule_item_id="<?php echo $item->getId() ?>"
                class="staff-schedule-item-row panel panel-default bookme-pro-panel-unborder">

                <div class="panel-heading bookme-pro-padding-vertical-md">
                    <div class="row">
                        <div class="col-sm-7 col-lg-5">
                            <span class="panel-title"><?php _e( \BookmePro\Lib\Utils\DateTime::getWeekDayByNumber( $item->getDayIndex() - 1 ) /* take translation from WP catalog */ ) ?></span>
                        </div>
                        <div class="col-sm-5 col-lg-7 hidden-xs hidden-sm">
                            <div class="bookme-pro-font-smaller bookme-pro-color-gray">
                                <?php _e( 'Breaks', 'bookme_pro' ) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel-body padding-lr-none">
                    <div class="row">
                        <div class="col-sm-7 col-lg-5">
                            <div class="bookme-pro-flexbox">
                                <div class="bookme-pro-flex-cell" style="width: 50%">
                                    <?php
                                        $day_is_not_available = null === $item->getStartTime();
                                        echo $working_start->render(
                                            "start_time[{$item->getDayIndex()}]",
                                            $item->getStartTime(),
                                            array( 'class' => 'working-schedule-start form-control' )
                                        );
                                    ?>
                                </div>
                                <div class="bookme-pro-flex-cell text-center" style="width: 1%">
                                    <div class="bookme-pro-margin-horizontal-lg bookme-pro-hide-on-off">
                                        <?php _e( 'to', 'bookme_pro' ) ?>
                                    </div>
                                </div>
                                <div class="bookme-pro-flex-cell" style="width: 50%">
                                    <?php
                                        echo $working_end->render(
                                            "end_time[{$item->getDayIndex()}]",
                                            $item->getEndTime(),
                                            array( 'class' => 'working-schedule-end form-control bookme-pro-hide-on-off' )
                                        );
                                    ?>
                                </div>
                            </div>

                            <input type="hidden"
                                   name="days[<?php echo $item->getId() ?>]"
                                   value="<?php echo $item->getDayIndex() ?>"
                            >
                        </div>

                        <div class="col-sm-5 col-lg-7">
                            <div class="bookme-pro-intervals-wrapper bookme-pro-hide-on-off">
                                <button type="button"
                                        class="bookme-pro-js-toggle-popover btn btn-link bookme-pro-btn-unborder bookme-pro-margin-vertical-screenxs-sm"
                                        data-popover-content=".bookme-pro-js-content-break-<?php echo $item->getId() ?>">
                                    <?php _e( 'add break', 'bookme_pro' ) ?>
                                </button>

                                <div class="bookme-pro-js-content-break-<?php echo $item->getId() ?> hidden">
                                    <div class="error" style="display: none"></div>

                                    <div class="bookme-pro-js-schedule-form">
                                        <div class="bookme-pro-flexbox" style="width: 260px">
                                            <div class="bookme-pro-flex-cell" style="width: 48%;">
                                                <?php echo $break_start->render( '', $item->getStartTime(), array( 'class' => 'break-start form-control' ) ) ?>
                                            </div>
                                            <div class="bookme-pro-flex-cell" style="width: 4%;">
                                                <div class="bookme-pro-margin-horizontal-lg">
                                                    <?php _e( 'to', 'bookme_pro' ) ?>
                                                </div>
                                            </div>
                                            <div class="bookme-pro-flex-cell" style="width: 48%;">
                                                <?php echo $break_end->render( '', $item->getEndTime(), array( 'class' => 'break-end form-control' ) ) ?>
                                            </div>
                                        </div>
                                        <div class="text-right bookme-pro-margin-top-sm">
                                            <?php Common::customButton( null, 'bookme-pro-js-save-break btn-sm btn-primary', '<i class="glyphicon glyphicon-ok"></i>' ) ?>
                                            <?php Common::customButton( null, 'bookme-pro-popover-close btn-sm btn-default', '<i class="glyphicon glyphicon-remove"></i>' ) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="breaks bookme-pro-hide-on-off">
                                <?php include '_breaks.php' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach ?>

        <input type="hidden" name="action" value="bookme_pro_staff_schedule_update">
        <?php Common::csrf() ?>

        <div class="panel-footer">
            <?php Common::customButton( '', 'btn-default btn-lg slidePanel-close', __( 'Cancel', 'bookme_pro' ) ) ?>
            <?php Common::submitButton( 'bookme-pro-schedule-save' ) ?>
        </div>
    </form>
</div>