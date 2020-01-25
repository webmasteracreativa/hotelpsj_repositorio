<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
$color = get_option('bookme_pro_app_color', '#225c9d');
$color_cal_left = get_option('bookme_pro_cal_color_left', '#225c9d');
$color_cal_right = get_option('bookme_pro_cal_color_right', '#44619d');
?>
<style type="text/css">
    /* Color */
    .bookme-pro-shortcode-loader.bookme-pro-loader:before {
        border-left-color: <?php echo $color ?> !important;
        border-right-color: <?php echo $color ?> !important;
        border-top-color: <?php echo $color ?> !important;
    }

    .bookme-pro-form-group > label,
    .bookme-pro-label-error,
    .bookme-pro-steps > li,
    .bookme-pro-form .picker__nav--next,
    .bookme-pro-form .pickadate__nav--prev,
    .bookme-pro-form .picker__day:hover,
    .bookme-pro-form .picker__day--selected:hover,
    .bookme-pro-form .picker--opened .picker__day--selected,
    .bookme-pro-form .picker__button--clear,
    .bookme-pro-form .picker__button--today,
    .bookme-pro-form .picker__day:hover span, .bookme-pro-form .picker__day--selected span{
        color: <?php echo $color ?> !important;
    }

    /* Calendar */
    .bookme-pro-form .picker__frame {
        background: <?php echo $color_cal_left ?> !important;
        background: -webkit-linear-gradient(135deg, <?php echo $color_cal_left ?>, <?php echo $color_cal_right ?>)  !important;
        background: linear-gradient(135deg, <?php echo $color_cal_left ?> 0%, <?php echo $color_cal_right ?> 100%)  !important;
    }

    /* Background */
    .bookme-pro-steps > li:after,
    .bookme-pro-columnizer .bookme-pro-hour .ladda-label,
    .bookme-pro-btn,
    .bookme-pro-btn:active,
    .bookme-pro-btn:focus,
    .bookme-pro-btn:hover,
    .bookme-pro-btn-submit,
    .bookme-pro-round,
    .bookme-pro-square {
        background-color: <?php echo $color ?> !important;
    }

    .bookme-pro-triangle {
        border-bottom-color: <?php echo $color ?> !important;
    }

    /* Border */
    .bookme-pro-steps > li:before,
    .bookme-pro-form input[type="text"].bookme-pro-error,
    .bookme-pro-form input[type="password"].bookme-pro-error,
    .bookme-pro-form select.bookme-pro-error,
    .bookme-pro-form textarea.bookme-pro-error {
        border: 2px solid <?php echo $color ?> !important;
    }

    /* Other */
    .bookme-pro-steps > li.is-active:before, .bookme-pro-columnizer .bookme-pro-hour .ladda-label {
        border-color: <?php echo $color ?> !important;
    }

    .bookme-pro-form .picker__nav--next:before {
        border-left: 6px solid <?php echo $color ?> !important;
    }

    .bookme-pro-form .picker__nav--prev:before {
        border-right: 6px solid <?php echo $color ?> !important;
    }

    .bookme-pro-pagination > li.active, .bookme-pro-columnizer .bookme-pro-day, .bookme-pro-schedule-date {
        background: <?php echo $color ?> !important;
        border: 1px solid <?php echo $color ?> !important;
    }
</style>