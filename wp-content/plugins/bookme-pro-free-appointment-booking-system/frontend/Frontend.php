<?php
namespace BookmePro\Frontend;

use BookmePro\Lib;

/**
 * Class Frontend
 * @package BookmePro\Frontend
 */
class Frontend
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        add_action('wp_loaded', array($this, 'init'));
        add_action(get_option('bookme_pro_gen_link_assets_method') == 'enqueue' ? 'wp_enqueue_scripts' : 'wp_loaded', array($this, 'linkAssets'));

        // Init controllers.
        $this->bookingController = Controllers\Booking\Controller::getInstance();
        $this->customerProfileController = Controllers\CustomerProfile\Controller::getInstance();
        // Register shortcodes.
        add_shortcode('bookme-pro-form', array($this->bookingController, 'renderShortCode'));
        add_shortcode('bookme-pro-appointments-list', array($this->customerProfileController, 'renderShortCode'));
    }

    /**
     * Link assets.
     */
    public function linkAssets()
    {
        /** @var \WP_Locale $wp_locale */
        global $wp_locale;

        $link_style = get_option('bookme_pro_gen_link_assets_method') == 'enqueue' ? 'wp_enqueue_style' : 'wp_register_style';
        $link_script = get_option('bookme_pro_gen_link_assets_method') == 'enqueue' ? 'wp_enqueue_script' : 'wp_register_script';
        $version = Lib\Plugin::getVersion();
        $assets = plugins_url('assets', __FILE__);

        // Assets for [bookme-pro-form].
        if (get_option('bookme_pro_cst_phone_default_country') != 'disabled') {
            call_user_func($link_style, 'bookme-pro-intlTelInput', $assets . '/css/intlTelInput.css', array(), $version);
        }
        call_user_func($link_style, 'bookme-pro-ladda-min', $assets . '/css/ladda.min.css', array(), $version);
        call_user_func($link_style, 'bookme-pro-picker', $assets . '/css/picker.classic.css', array(), $version);
        call_user_func($link_style, 'bookme-pro-picker-date', $assets . '/css/picker.classic.date.css', array(), $version);
        call_user_func($link_style, 'bookme-pro-scroll', $assets . '/css/trackpad-scroll.css', array(), $version);
        call_user_func($link_style, 'bookme-pro', $assets . '/css/bookme-pro.css', get_option('bookme_pro_cst_phone_default_country') != 'disabled' ? array('bookme-pro-intlTelInput', 'bookme-pro-picker-date') : array('bookme-pro-picker-date'), $version);
        if (is_rtl()) {
            call_user_func($link_style, 'bookme-pro-rtl', $assets . '/css/bookme-pro-rtl.css', array(), $version);
        }
        call_user_func($link_script, 'bookme-pro-spin', $assets . '/js/spin.min.js', array(), $version);
        call_user_func($link_script, 'bookme-pro-ladda', $assets . '/js/ladda.min.js', array('bookme-pro-spin'), $version);
        call_user_func($link_script, 'bookme-pro-hammer', $assets . '/js/hammer.min.js', array('jquery'), $version);
        call_user_func($link_script, 'bookme-pro-jq-hammer', $assets . '/js/jquery.hammer.min.js', array('jquery'), $version);
        call_user_func($link_script, 'bookme-pro-scroll', $assets . '/js/jquery.scroll.min.js', array('jquery'), $version);
        call_user_func($link_script, 'bookme-pro-picker', $assets . '/js/picker.js', array('jquery'), $version);
        call_user_func($link_script, 'bookme-pro-picker-date', $assets . '/js/picker.date.js', array('bookme-pro-picker'), $version);
        if (get_option('bookme_pro_cst_phone_default_country') != 'disabled') {
            call_user_func($link_script, 'bookme-pro-intlTelInput', $assets . '/js/intlTelInput.min.js', array('jquery'), $version);
        }
        call_user_func($link_script, 'bookme-pro-tooltip', $assets . '/js/tooltip.js', array('jquery'), $version);
        call_user_func($link_script, 'bookme-pro', $assets . '/js/bookme-pro.js', array('bookme-pro-ladda', 'bookme-pro-hammer', 'bookme-pro-picker-date', 'bookme-pro-tooltip', 'bookme-pro-scroll'), $version);

        // Assets for [bookme-pro-appointments-list].
        call_user_func($link_style, 'bookme-pro-customer-profile', plugins_url('controllers/customer_profile/assets/css/customer_profile.css', __FILE__), array('bookme-pro'), $version);
        call_user_func($link_script, 'bookme-pro-customer-profile', plugins_url('controllers/customer_profile/assets/js/customer_profile.js', __FILE__), array('jquery'), $version);

        Lib\Proxy\Shared::enqueueBookingAssets();

        wp_localize_script('bookme-pro', 'BookmeProL10n', array(
            'csrf_token' => Lib\Utils\Common::getCsrfToken(),
            'today' => esc_html__('Today', 'bookme_pro'),
            'months' => array_values($wp_locale->month),
            'days' => array_values($wp_locale->weekday),
            'daysShort' => array_values($wp_locale->weekday_abbrev),
            'nextMonth' => esc_html__('Next month', 'bookme_pro'),
            'prevMonth' => esc_html__('Previous month', 'bookme_pro'),
            'show_more' => esc_html__('Show more', 'bookme_pro'),
        ));

        // Android animation.
        if (array_key_exists('HTTP_USER_AGENT', $_SERVER) && stripos(strtolower($_SERVER['HTTP_USER_AGENT']), 'android') !== false) {
            call_user_func($link_script, 'bookme-pro-jquery-animate-enhanced', $assets . '/js/jquery.animate-enhanced.min.js', array('jquery'), Lib\Plugin::getVersion());
        }
    }

    /**
     * Init.
     */
    public function init()
    {
        if (!session_id()) {
            // WP 4.9+ fix loopback request failure
            if (!isset($_GET['wp_scrape_key'])) {
                // Start session.
                @session_start();
            }
        }
    }

}