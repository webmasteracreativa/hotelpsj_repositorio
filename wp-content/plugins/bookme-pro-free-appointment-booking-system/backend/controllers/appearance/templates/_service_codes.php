<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly

use BookmePro\Lib;

$codes = array(
    array('code' => 'category_name', 'description' => esc_html__('name of category', 'bookme_pro'), 'flags' => array('step' => '2')),
    array('code' => 'number_of_persons', 'description' => esc_html__('number of persons', 'bookme_pro'), 'flags' => array('step' => '2')),
    array('code' => 'service_date', 'description' => esc_html__('date of service', 'bookme_pro'), 'flags' => array('step' => '1')),
    array('code' => 'service_name', 'description' => esc_html__('name of service', 'bookme_pro'), 'flags' => array('step' => '2')),
    array('code' => 'service_time', 'description' => esc_html__('time of service', 'bookme_pro'), 'flags' => array('step' => '1')),
    array('code' => 'staff_name', 'description' => esc_html__('name of staff', 'bookme_pro'), 'flags' => array('step' => '2')),
);

Lib\Utils\Common::codes(Lib\Proxy\Shared::prepareAppearanceCodes($codes), array('step' => $step, 'extra_codes' => isset ($extra_codes)));