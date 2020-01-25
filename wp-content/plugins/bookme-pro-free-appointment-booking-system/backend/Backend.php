<?php
namespace BookmePro\Backend;

use BookmePro\Frontend;
use BookmePro\Lib;
use BookmePro\Lib\Plugin;

/**
 * Class Backend
 * @package BookmePro\Backend
 */
class Backend
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        // Backend controllers.
        $this->apearanceController = Controllers\Appearance\Controller::getInstance();
        $this->appointmentsController = Controllers\Appointments\Controller::getInstance();
        $this->calendarController = Controllers\Calendar\Controller::getInstance();
        $this->customerController = Controllers\Customers\Controller::getInstance();
        $this->debugController = Controllers\Debug\Controller::getInstance();
        $this->notificationsController = Controllers\Notifications\Controller::getInstance();
        $this->paymentController = Controllers\Payments\Controller::getInstance();
        $this->serviceController = Controllers\Services\Controller::getInstance();
        $this->settingsController = Controllers\Settings\Controller::getInstance();
        $this->smsController = Controllers\Sms\Controller::getInstance();
        $this->staffController = Controllers\Staff\Controller::getInstance();

        // Frontend controllers that work via admin-ajax.php.
        $this->bookingController = Frontend\Controllers\Booking\Controller::getInstance();
        $this->customerProfileController = Frontend\Controllers\CustomerProfile\Controller::getInstance();

        add_action('admin_menu', array($this, 'addAdminMenu'));
        add_action('wp_loaded', array($this, 'init'));
        add_action('admin_init', array($this, 'addTinyMCEPlugin'));
    }

    /**
     * Init.
     */
    public function init()
    {
        if (!session_id()) {
            // WP 4.9+ fix loopback request failure
            if ( ! isset( $_GET['wp_scrape_key'] ) ) {
                // Start session.
                @session_start();
            }
        }
    }

    public function addTinyMCEPlugin()
    {
        new Controllers\TinyMce\Plugin();
    }

    /**
     * Admin menu.
     */
    public function addAdminMenu()
    {
        /** @var \WP_User $current_user */
        global $current_user, $submenu;

        if ($current_user->has_cap('administrator') || Lib\Entities\Staff::query()->where('wp_user_id', $current_user->ID)->count()) {
            add_menu_page('Bookme Pro Lite', 'Bookme Pro Lite', 'read', 'bookme-pro-menu', '',
                plugins_url('assets/images/logo-16.png', __FILE__), 40);

            // Translated submenu pages.
            $services = esc_html__('Services', 'bookme_pro');
            $staff_members = esc_html__('Staff Members', 'bookme_pro');
            $appointments = esc_html__('All Bookings', 'bookme_pro');
            $calendar = esc_html__('Calendar', 'bookme_pro');
            $customers = esc_html__('Customers', 'bookme_pro');
            $payments = esc_html__('Payments', 'bookme_pro');
            $appearance = esc_html__('Appearance', 'bookme_pro');
            $notifications = esc_html__('Email Notifications', 'bookme_pro');
            $sms = esc_html__('SMS Notifications', 'bookme_pro');
            $custom_fields = esc_html__('Custom Fields', 'bookme_pro');
            $coupons = esc_html__('Coupons', 'bookme_pro');
            $settings = esc_html__('Settings', 'bookme_pro');


            if ($current_user->has_cap('administrator')) {
                add_submenu_page('bookme-pro-menu', $staff_members, $staff_members, 'manage_options',
                    Controllers\Staff\Controller::page_slug, array($this->staffController, 'index'));
            } else {
                if (get_option('bookme_pro_gen_allow_staff_edit_profile') == 1) {
                    add_submenu_page('bookme-pro-menu', esc_html__('Profile', 'bookme_pro'), esc_html__('Profile', 'bookme_pro'), 'read',
                        Controllers\Staff\Controller::page_slug, array($this->staffController, 'index'));
                }
            }
            add_submenu_page('bookme-pro-menu', $services, $services, 'manage_options',
                Controllers\Services\Controller::page_slug, array($this->serviceController, 'index'));

            add_submenu_page('bookme-pro-menu', $appointments, $appointments, 'manage_options',
                Controllers\Appointments\Controller::page_slug, array($this->appointmentsController, 'index'));
            Lib\Proxy\Shared::renderBookmeProMenuAfterAppointments();

            add_submenu_page('bookme-pro-menu', $calendar, $calendar, 'read',
                Controllers\Calendar\Controller::page_slug, array($this->calendarController, 'index'));
            add_submenu_page('bookme-pro-menu', $customers, $customers, 'manage_options',
                Controllers\Customers\Controller::page_slug, array($this->customerController, 'index'));
            add_submenu_page('bookme-pro-menu', $payments, $payments, 'manage_options',
                Controllers\Payments\Controller::page_slug, array($this->paymentController, 'index'));
            add_submenu_page('bookme-pro-menu', $appearance, $appearance, 'manage_options',
                Controllers\Appearance\Controller::page_slug, array($this->apearanceController, 'index'));
            add_submenu_page('bookme-pro-menu', $notifications, $notifications, 'manage_options',
                Controllers\Notifications\Controller::page_slug, array($this->notificationsController, 'index'));
            add_submenu_page('bookme-pro-menu', $sms, $sms, 'manage_options',
                Controllers\Sms\Controller::page_slug, array($this->smsController, 'index'));
            add_submenu_page('bookme-pro-menu', $settings, $settings, 'manage_options',
                Controllers\Settings\Controller::page_slug, array($this->settingsController, 'index'));

            if (isset ($_GET['page']) && $_GET['page'] == 'bookme-pro-debug') {
                add_submenu_page('bookme-pro-menu', 'Debug', 'Debug', 'manage_options',
                    Controllers\Debug\Controller::page_slug, array($this->debugController, 'index'));
            }


            unset ($submenu['bookme-pro-menu'][0]);
        }
    }
}