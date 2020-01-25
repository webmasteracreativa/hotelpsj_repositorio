<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
$codes = array(
    array('code' => 'tomorrow_date', 'description' => esc_html__('date of next day', 'bookme_pro')),
    array('code' => 'next_day_agenda', 'description' => esc_html__('staff agenda for next day', 'bookme_pro')),
    array('code' => 'staff_name', 'description' => esc_html__('name of staff', 'bookme_pro')),
);
\BookmePro\Lib\Utils\Common::codes($codes);