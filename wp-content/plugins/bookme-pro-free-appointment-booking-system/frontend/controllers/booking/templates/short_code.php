<?php
/**
 * Main template to render booking form on frontend
 * @var array $attrs
 * @var array $options
 * @var array $required
 * @var bool $print_assets
 * @var string $form_id current form id
 * @var string $ajax_url url that should receive ajax calls
 * @var array $status
 * @var array $skip_steps all steps that could be skipped (should be skipped if value is truthy)
 * @var string $custom_css css styles created by admin
 */
?>
<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>

<?php if ($print_assets) include '_css.php' ?>
<div id="bookme-pro-form-<?php echo $form_id ?>" class="bookme-pro-form" data-form_id="<?php echo $form_id ?>">
    <div style="text-align: center"><div class="bookme-pro-shortcode-loader bookme-pro-loader"></div></div>
</div>
<script type="text/javascript">
    (function (win, fn) {
        var done = false, top = true,
            doc = win.document,
            root = doc.documentElement,
            modern = doc.addEventListener,
            add = modern ? 'addEventListener' : 'attachEvent',
            rem = modern ? 'removeEventListener' : 'detachEvent',
            pre = modern ? '' : 'on',
            init = function (e) {
                if (e.type == 'readystatechange') if (doc.readyState != 'complete') return;
                (e.type == 'load' ? win : doc)[rem](pre + e.type, init, false);
                if (!done) {
                    done = true;
                    fn.call(win, e.type || e);
                }
            },
            poll = function () {
                try {
                    root.doScroll('left');
                } catch (e) {
                    setTimeout(poll, 50);
                    return;
                }
                init('poll');
            };
        if (doc.readyState == 'complete') fn.call(win, 'lazy');
        else {
            if (!modern) if (root.doScroll) {
                try {
                    top = !win.frameElement;
                } catch (e) {
                }
                if (top) poll();
            }
            doc[add](pre + 'DOMContentLoaded', init, false);
            doc[add](pre + 'readystatechange', init, false);
            win[add](pre + 'load', init, false);
        }
    })(window, function () {
        window.bookmePro({
            ajaxurl: <?php echo json_encode($ajax_url) ?>,
            form_id: <?php echo json_encode($form_id) ?>,
            attributes: <?php echo json_encode($attrs) ?>,
            status: <?php echo json_encode($status) ?>,
            start_of_week: <?php echo (int)get_option('start_of_week') ?>,
            show_calendar: <?php echo (int)get_option('bookme_pro_app_show_calendar') ?>,
            required: <?php echo json_encode($required) ?>,
            skip_steps: <?php echo json_encode($skip_steps) ?>,
            date_format: <?php echo json_encode(\BookmePro\Lib\Utils\DateTime::convertFormat('date', \BookmePro\Lib\Utils\DateTime::FORMAT_PICKADATE)) ?>,
            final_step_url: <?php echo json_encode(get_option('bookme_pro_url_final_step_url')) ?>,
            intlTelInput: <?php echo json_encode($options['intlTelInput']) ?>,
            woocommerce: <?php echo json_encode($options['woocommerce']) ?>,
            update_details_dialog: <?php echo (int)get_option('bookme_pro_cst_show_update_details_dialog') ?>,
            cart: <?php echo json_encode($options['cart']) ?>,
            show_calendar_availability: <?php echo json_encode($options['show_calendar_availability']) ?>,
            auto_calendar_size: <?php echo json_encode($options['auto_calendar_size']) ?>,
            is_rtl: <?php echo (int)is_rtl() ?>
        });
    });
</script>

<?php if (trim($custom_css)): ?>
    <style type="text/css">
        <?php echo $custom_css; ?>
    </style>
<?php endif; ?>
