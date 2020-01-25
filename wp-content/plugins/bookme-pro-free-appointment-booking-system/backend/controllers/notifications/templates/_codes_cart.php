<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
$codes = array(
    array('code' => 'cart_info', 'description' => esc_html__('cart information', 'bookme_pro')),
    array('code' => 'cart_info_c', 'description' => esc_html__('cart information with cancel', 'bookme_pro')),
    array('code' => 'client_email', 'description' => esc_html__('email of client', 'bookme_pro')),
    array('code' => 'client_name', 'description' => esc_html__('full name of client', 'bookme_pro')),
    array('code' => 'client_first_name', 'description' => esc_html__('first name of client', 'bookme_pro')),
    array('code' => 'client_last_name', 'description' => esc_html__('last name of client', 'bookme_pro')),
    array('code' => 'client_phone', 'description' => esc_html__('phone of client', 'bookme_pro')),
    array('code' => 'company_name', 'description' => esc_html__('name of company', 'bookme_pro')),
    array('code' => 'company_logo', 'description' => esc_html__('company logo', 'bookme_pro')),
    array('code' => 'company_address', 'description' => esc_html__('address of company', 'bookme_pro')),
    array('code' => 'company_phone', 'description' => esc_html__('company phone', 'bookme_pro')),
    array('code' => 'company_website', 'description' => esc_html__('company web-site address', 'bookme_pro')),
    array('code' => 'payment_type', 'description' => esc_html__('payment type', 'bookme_pro')),
    array('code' => 'total_price', 'description' => esc_html__('total price of booking (sum of all cart items after applying coupon)', 'bookme_pro')),
);
BookmePro\Lib\Utils\Common::codes(BookmePro\Lib\Proxy\Shared::prepareCartNotificationShortCodes($codes));