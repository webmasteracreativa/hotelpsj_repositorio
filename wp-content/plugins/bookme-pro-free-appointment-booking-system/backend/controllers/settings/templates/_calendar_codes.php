<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
$codes = array(
    array('code' => 'appointment_date', 'description' => esc_html__('date of appointment', 'bookme_pro')),
    array('code' => 'appointment_time', 'description' => esc_html__('time of appointment', 'bookme_pro')),
    array('code' => 'booking_number', 'description' => esc_html__('booking number', 'bookme_pro')),
    array('code' => 'category_name', 'description' => esc_html__('name of category', 'bookme_pro')),
    array('code' => 'company_address', 'description' => esc_html__('address of company', 'bookme_pro')),
    array('code' => 'company_name', 'description' => esc_html__('name of company', 'bookme_pro')),
    array('code' => 'company_phone', 'description' => esc_html__('company phone', 'bookme_pro')),
    array('code' => 'company_website', 'description' => esc_html__('company web-site address', 'bookme_pro')),
    array('code' => 'service_capacity', 'description' => esc_html__('capacity of service', 'bookme_pro')),
    array('code' => 'service_info', 'description' => esc_html__('info of service', 'bookme_pro')),
    array('code' => 'service_name', 'description' => esc_html__('name of service', 'bookme_pro')),
    array('code' => 'service_price', 'description' => esc_html__('price of service', 'bookme_pro')),
    array('code' => 'signed_up', 'description' => esc_html__('number of persons already in the list', 'bookme_pro')),
    array('code' => 'staff_email', 'description' => esc_html__('email of staff', 'bookme_pro')),
    array('code' => 'staff_info', 'description' => esc_html__('info of staff', 'bookme_pro')),
    array('code' => 'staff_name', 'description' => esc_html__('name of staff', 'bookme_pro')),
    array('code' => 'staff_phone', 'description' => esc_html__('phone of staff', 'bookme_pro')),
);
if ($participants == 'one') {
    $codes[] = array('code' => 'client_email', 'description' => esc_html__('email of client', 'bookme_pro'));
    $codes[] = array('code' => 'client_name', 'description' => esc_html__('full name of client', 'bookme_pro'));
    $codes[] = array('code' => 'client_first_name', 'description' => esc_html__('first name of client', 'bookme_pro'));
    $codes[] = array('code' => 'client_last_name', 'description' => esc_html__('last name of client', 'bookme_pro'));
    $codes[] = array('code' => 'client_phone', 'description' => esc_html__('phone of client', 'bookme_pro'));
    $codes[] = array('code' => 'custom_fields', 'description' => esc_html__('combined values of all custom fields', 'bookme_pro'));
    $codes[] = array('code' => 'payment_status', 'description' => esc_html__('status of payment', 'bookme_pro'));
    $codes[] = array('code' => 'payment_type', 'description' => esc_html__('payment type', 'bookme_pro'));
    $codes[] = array('code' => 'status', 'description' => esc_html__('status of appointment', 'bookme_pro'));
    $codes[] = array('code' => 'total_price', 'description' => esc_html__('total price of booking (sum of all cart items after applying coupon)', 'bookme_pro'));
}

$codes = BookmePro\Lib\Proxy\Shared::prepareCalendarAppointmentCodes($codes, $participants);

BookmePro\Lib\Utils\Common::codes($codes);