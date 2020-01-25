<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
Plugin Name: Bookme Pro - Free Appointment Booking System
Plugin URI: https://bookme.bylancer.com/
Description: Bookme Pro Lite is a responsive, multipurpose and fully customizable free WordPress appointment booking and scheduling software for accepting online appointments bookings & scheduling. Check the Bookme Pro full version <a href="https://codecanyon.net/item/bookme-pro-wordpress-appointment-booking-and-scheduling-software/23939246" target="_blank">here</a>.
Version: 1.1
Author: Bylancer
Author URI: https://bylancer.com/
Text Domain: bookme_pro
Domain Path: /languages
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/


if ( version_compare( PHP_VERSION, '5.3.7', '<' ) ) {
    function bookme_pro_php_outdated()
    {
        echo '<div class="updated"><h3>Bookme Pro</h3><p>To install the plugin - <strong>PHP 5.3.7</strong> or higher is required.</p></div>';
    }
    add_action( is_network_admin() ? 'network_admin_notices' : 'admin_notices', 'bookme_pro_php_outdated' );
} else {
    include_once __DIR__ . '/autoload.php';

    call_user_func( array( '\BookmePro\Lib\Plugin', 'run' ) );
    $app = is_admin() ? '\BookmePro\Backend\Backend' : '\BookmePro\Frontend\Frontend';
    new $app();
}