<?php
namespace BookmePro\Backend\Controllers\Appearance;

use BookmePro\Lib;
use BookmePro\Backend\Controllers\Appearance\Lib\Helper;

/**
 * Class Controller
 * @package BookmePro\Backend\Controllers\Appearance
 */
class Controller extends Lib\Base\Controller
{
    const page_slug = 'bookme-pro-appearance';

    /**
     *  Default Action
     */
    public function index()
    {
        /** @var \WP_Locale $wp_locale */
        global $wp_locale;

        $this->enqueueStyles(array(
            'frontend' => array_merge(
                (get_option('bookme_pro_cst_phone_default_country') == 'disabled'
                    ? array()
                    : array('css/intlTelInput.css')),
                array(
                    'css/ladda.min.css',
                    'css/picker.classic.css',
                    'css/picker.classic.date.css',
                    'css/bookme-pro.css',
                ),
                (is_rtl()
                    ? array('css/bookme-pro-rtl.css')
                    : array())
            ),
            'backend' => array_merge(array(
                'bootstrap/css/bootstrap-theme.min.css',
                'css/slidePanel.min.css',
                'css/tooltipster.bundle.min.css',
                'css/tooltipster-sideTip-borderless.min.css',
                'css/jquery.multiselect.css'
            ), (is_rtl() ? array('bootstrap/css/bootstrap-rtl.css') : array())),
            'wp' => array('wp-color-picker',),
            'module' => array_merge(array('css/bootstrap-editable.css'), (is_rtl() ? array('css/bootstrap-editable-rtl.css') : array()))
        ));

        $this->enqueueScripts(array(
            'backend' => array(
                'bootstrap/js/bootstrap.min.js' => array('jquery'),
                'js/alert.js' => array('jquery'),
                'js/sidePanel.js' => array('jquery'),
                'js/tooltipster.bundle.min.js' => array('jquery'),
                'js/help.js' => array('jquery'),
            ),
            'frontend' => array_merge(
                array(
                    'js/picker.js' => array('jquery'),
                    'js/picker.date.js' => array('jquery'),
                    'js/spin.min.js' => array('jquery'),
                    'js/ladda.min.js' => array('jquery'),
                    'js/tooltip.js' => array('jquery'),
                ),
                get_option('bookme_pro_cst_phone_default_country') == 'disabled'
                    ? array()
                    : array('js/intlTelInput.min.js' => array('jquery'))
            ),
            'wp' => array('wp-color-picker'),
            'module' => array(
                'js/bootstrap-editable.min.js' => array('bookme-pro-bootstrap.min.js'),
                'js/bootstrap-editable.bookme-pro.js' => array('bookme-pro-bootstrap-editable.min.js'),
                'js/appearance.js' => array('bookme-pro-bootstrap-editable.bookme-pro.js')
            )
        ));

        wp_localize_script('bookme-pro-picker.date.js', 'BookmeProL10n', array(
            'csrf_token' => Lib\Utils\Common::getCsrfToken(),
            'today' => esc_html__('Today', 'bookme_pro'),
            'months' => array_values($wp_locale->month),
            'days' => array_values($wp_locale->weekday_abbrev),
            'nextMonth' => esc_html__('Next month', 'bookme_pro'),
            'prevMonth' => esc_html__('Previous month', 'bookme_pro'),
            'date_format' => Lib\Utils\DateTime::convertFormat('date', Lib\Utils\DateTime::FORMAT_PICKADATE),
            'start_of_week' => (int)get_option('start_of_week'),
            'saved' => esc_html__('Settings saved.', 'bookme_pro'),
            'intlTelInput' => array(
                'enabled' => get_option('bookme_pro_cst_phone_default_country') != 'disabled',
                'utils' => plugins_url('intlTelInput.utils.js', Lib\Plugin::getDirectory() . '/frontend/assets/js/intlTelInput.utils.js'),
                'country' => get_option('bookme_pro_cst_phone_default_country'),
            )
        ));

        // Initialize steps (tabs).
        $steps = array(
            1 => get_option('bookme_pro_l10n_step_service'),
            2 => get_option('bookme_pro_l10n_step_time'),
            4 => get_option('bookme_pro_l10n_step_details'),
            5 => get_option('bookme_pro_l10n_step_done')
        );

        if (Lib\Config::showStepCart()) {
            $steps[3] = get_option('bookme_pro_l10n_step_cart');
        }
        ksort($steps);

        $custom_css = get_option('bookme_pro_app_custom_styles');

        // Shortcut to helper class.
        $editable = new Helper();

        // Render general layout.
        $this->render('index', compact('steps', 'custom_css', 'editable'));
    }

    /**
     *  Update options
     */
    public function executeUpdateAppearanceOptions()
    {
        $options = $this->getParameter('options', array());

        // Make sure that we save only allowed options.
        $options_to_save = array_intersect_key($options, array_flip(array(
            // Info text.
            'bookme_pro_l10n_info_cart_step',
            'bookme_pro_l10n_info_complete_step',
            'bookme_pro_l10n_info_complete_step_limit_error',
            'bookme_pro_l10n_info_complete_step_processing',
            'bookme_pro_l10n_info_coupon_single_app',
            'bookme_pro_l10n_info_coupon_several_apps',
            'bookme_pro_l10n_info_details_step',
            'bookme_pro_l10n_info_details_step_guest',
            'bookme_pro_l10n_info_service_step',
            'bookme_pro_l10n_info_time_step',
            // Step, label and option texts.
            'bookme_pro_l10n_button_apply',
            'bookme_pro_l10n_button_back',
            'bookme_pro_l10n_button_book_now',
            'bookme_pro_l10n_button_book_more',
            'bookme_pro_l10n_label_category',
            'bookme_pro_l10n_label_ccard_code',
            'bookme_pro_l10n_label_ccard_expire',
            'bookme_pro_l10n_label_ccard_number',
            'bookme_pro_l10n_label_coupon',
            'bookme_pro_l10n_label_service_details',
            'bookme_pro_l10n_label_service_time',
            'bookme_pro_l10n_label_time',
            'bookme_pro_l10n_label_choose_payment',
            'bookme_pro_l10n_label_sub_total',
            'bookme_pro_l10n_label_discount',
            'bookme_pro_l10n_label_total',
            'bookme_pro_l10n_label_email',
            'bookme_pro_l10n_label_employee',
            'bookme_pro_l10n_label_finish_by',
            'bookme_pro_l10n_label_name',
            'bookme_pro_l10n_label_first_name',
            'bookme_pro_l10n_label_last_name',
            'bookme_pro_l10n_label_number_of_persons',
            'bookme_pro_l10n_label_pay_ccard',
            'bookme_pro_l10n_label_pay_locally',
            'bookme_pro_l10n_label_pay_mollie',
            'bookme_pro_l10n_label_pay_paypal',
            'bookme_pro_l10n_label_phone',
            'bookme_pro_l10n_label_select_date',
            'bookme_pro_l10n_calendar_availability',
            'bookme_pro_l10n_label_service',
            'bookme_pro_l10n_label_start_from',
            'bookme_pro_l10n_option_category',
            'bookme_pro_l10n_option_employee',
            'bookme_pro_l10n_option_service',
            'bookme_pro_l10n_step_service',
            'bookme_pro_l10n_step_service_button_next',
            'bookme_pro_l10n_step_time',
            'bookme_pro_l10n_step_time_slot_not_available',
            'bookme_pro_l10n_step_cart',
            'bookme_pro_l10n_step_cart_slot_not_available',
            'bookme_pro_l10n_step_cart_button_next',
            'bookme_pro_l10n_step_details',
            'bookme_pro_l10n_step_details_button_next',
            'bookme_pro_l10n_step_details_button_login',
            'bookme_pro_l10n_step_done',
            // Validator errors.
            'bookme_pro_l10n_required_email',
            'bookme_pro_l10n_required_employee',
            'bookme_pro_l10n_required_name',
            'bookme_pro_l10n_required_first_name',
            'bookme_pro_l10n_required_last_name',
            'bookme_pro_l10n_required_phone',
            'bookme_pro_l10n_required_service',
            // Color.
            'bookme_pro_app_color',
            'bookme_pro_cal_color_left',
            'bookme_pro_cal_color_right',
            // Checkboxes.
            'bookme_pro_app_required_employee',
            'bookme_pro_app_service_name_with_duration',
            'bookme_pro_app_show_login_button',
            'bookme_pro_app_show_progress_tracker',
            'bookme_pro_app_staff_name_with_price',
            'bookme_pro_app_show_calendar_availability',
            'bookme_pro_app_auto_calendar_size',
            'bookme_pro_cst_required_phone',
            'bookme_pro_cst_first_last_name',
            // Select
            'bookme_pro_app_layout',
        )));

        // Allow add-ons to add their options.
        $options_to_save = Lib\Proxy\Shared::prepareAppearanceOptions($options_to_save, $options);

        // Save options.
        foreach ($options_to_save as $option_name => $option_value) {
            update_option($option_name, $option_value);
            // Register string for translate in WPML.
            if (strpos($option_name, 'bookme_pro_l10n_') === 0) {
                do_action('wpml_register_single_string', 'bookme_pro', $option_name, $option_value);
            }
        }

        wp_send_json_success();
    }

    /**
     * Ajax request to dismiss appearance notice for current user.
     */
    public function executeDismissAppearanceNotice()
    {
        update_user_meta(get_current_user_id(), Lib\Plugin::getPrefix() . 'dismiss_appearance_notice', 1);
    }

    /**
     * Process ajax request to save custom css
     */
    public function executeSaveCustomCss()
    {
        update_option('bookme_pro_app_custom_styles', $this->getParameter('custom_css'));

        wp_send_json_success(array('message' => esc_html__('Your custom CSS was saved. Please refresh the page to see your changes.', 'bookme_pro')));
    }
}