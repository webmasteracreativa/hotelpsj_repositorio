<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly

use BookmePro\Lib;

$codes = array(
    array('code' => 'appointments_count', 'description' => esc_html__('total quantity of appointments in cart', 'bookme_pro'), 'flags' => array('step' => 4, 'extra_codes' => true)),
    array('code' => 'booking_number', 'description' => esc_html__('booking number', 'bookme_pro'), 'flags' => array('step' => 5, 'extra_codes' => true)),
    array('code' => 'category_name', 'description' => esc_html__('name of category', 'bookme_pro')),
    array('code' => 'login_form', 'description' => esc_html__('login form', 'bookme_pro'), 'flags' => array('step' => 4, 'extra_codes' => true)),
    array('code' => 'number_of_persons', 'description' => esc_html__('number of persons', 'bookme_pro')),
    array('code' => 'service_date', 'description' => esc_html__('date of service', 'bookme_pro'), 'flags' => array('step' => '>2')),
    array('code' => 'service_info', 'description' => esc_html__('info of service', 'bookme_pro')),
    array('code' => 'service_name', 'description' => esc_html__('name of service', 'bookme_pro')),
    array('code' => 'service_price', 'description' => esc_html__('price of service', 'bookme_pro')),
    array('code' => 'service_time', 'description' => esc_html__('time of service', 'bookme_pro'), 'flags' => array('step' => '>2')),
    array('code' => 'staff_info', 'description' => esc_html__('info of staff', 'bookme_pro')),
    array('code' => 'staff_name', 'description' => esc_html__('name of staff', 'bookme_pro')),
    array('code' => 'total_price', 'description' => esc_html__('total price of booking', 'bookme_pro')),
);

Lib\Utils\Common::codes(Lib\Proxy\Shared::prepareAppearanceCodes($codes), array('step' => $step, 'extra_codes' => isset ($extra_codes)));