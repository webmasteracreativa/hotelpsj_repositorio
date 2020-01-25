<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
$codes = array(
    array('code' => 'client_email', 'description' => esc_html__('email of client', 'bookme_pro')),
    array('code' => 'client_name', 'description' => esc_html__('full name of client', 'bookme_pro')),
    array('code' => 'client_first_name', 'description' => esc_html__('first name of client', 'bookme_pro')),
    array('code' => 'client_last_name', 'description' => esc_html__('last name of client', 'bookme_pro')),
    array('code' => 'client_phone', 'description' => esc_html__('phone of client', 'bookme_pro')),
    array('code' => 'company_name', 'description' => esc_html__('name of your company', 'bookme_pro')),
    array('code' => 'company_logo', 'description' => esc_html__('your company logo', 'bookme_pro')),
    array('code' => 'company_address', 'description' => esc_html__('address of your company', 'bookme_pro')),
    array('code' => 'company_phone', 'description' => esc_html__('your company phone', 'bookme_pro')),
    array('code' => 'company_website', 'description' => esc_html__('this web-site address', 'bookme_pro')),
);
\BookmePro\Lib\Utils\Common::codes($codes);