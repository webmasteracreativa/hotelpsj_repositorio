<?php
namespace BookmePro\Lib;

/**
 * Class Installer
 * @package BookmePro
 */
class Installer extends Base\Installer
{
    protected $notifications;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Load l10n for fixtures creating.
        load_plugin_textdomain('bookme_pro', false, Plugin::getSlug() . '/languages');

        /*
         * Notifications email & sms.
         */
        $this->notifications = array(
            array(
                'gateway' => 'email',
                'type' => 'client_pending_appointment',
                'subject' => __('Your appointment information', 'bookme_pro'),
                'message' => wpautop(__("Dear {client_name}.\n\nThis is a confirmation that you have booked {service_name}.\n\nWe are waiting you at {company_address} on {appointment_date} at {appointment_time}.\n\nThank you for choosing our company.\n\n{company_name}\n{company_phone}\n{company_website}", 'bookme_pro')),
                'active' => 1,
            ),
            array(
                'gateway' => 'email',
                'type' => 'staff_pending_appointment',
                'subject' => __('New booking information', 'bookme_pro'),
                'message' => wpautop(__("Hello.\n\nYou have a new booking.\n\nService: {service_name}\nDate: {appointment_date}\nTime: {appointment_time}\nClient name: {client_name}\nClient phone: {client_phone}\nClient email: {client_email}", 'bookme_pro')),
                'active' => 1,
            ),
            array(
                'gateway' => 'email',
                'type' => 'client_approved_appointment',
                'subject' => __('Your appointment information', 'bookme_pro'),
                'message' => wpautop(__("Dear {client_name}.\n\nThis is a confirmation that you have booked {service_name}.\n\nWe are waiting you at {company_address} on {appointment_date} at {appointment_time}.\n\nThank you for choosing our company.\n\n{company_name}\n{company_phone}\n{company_website}", 'bookme_pro')),
                'active' => 1,
            ),
            array(
                'gateway' => 'email',
                'type' => 'staff_approved_appointment',
                'subject' => __('New booking information', 'bookme_pro'),
                'message' => wpautop(__("Hello.\n\nYou have a new booking.\n\nService: {service_name}\nDate: {appointment_date}\nTime: {appointment_time}\nClient name: {client_name}\nClient phone: {client_phone}\nClient email: {client_email}", 'bookme_pro')),
                'active' => 1,
            ),
            array(
                'gateway' => 'email',
                'type' => 'client_cancelled_appointment',
                'subject' => __('Booking cancellation', 'bookme_pro'),
                'message' => wpautop(__("Dear {client_name}.\n\nYou have cancelled your booking of {service_name} on {appointment_date} at {appointment_time}.\n\nThank you for choosing our company.\n\n{company_name}\n{company_phone}\n{company_website}", 'bookme_pro')),
                'active' => 1,
            ),
            array(
                'gateway' => 'email',
                'type' => 'staff_cancelled_appointment',
                'subject' => __('Booking cancellation', 'bookme_pro'),
                'message' => wpautop(__("Hello.\n\nThe following booking has been cancelled.\n\nService: {service_name}\nDate: {appointment_date}\nTime: {appointment_time}\nClient name: {client_name}\nClient phone: {client_phone}\nClient email: {client_email}", 'bookme_pro')),
                'active' => 1,
            ),
            array(
                'gateway' => 'email',
                'type' => 'client_rejected_appointment',
                'subject' => __('Booking rejection', 'bookme_pro'),
                'message' => wpautop(__("Dear {client_name}.\n\nYour booking of {service_name} on {appointment_date} at {appointment_time} has been rejected.\n\nReason: {cancellation_reason}\n\nThank you for choosing our company.\n\n{company_name}\n{company_phone}\n{company_website}", 'bookme_pro')),
                'active' => 1,
            ),
            array(
                'gateway' => 'email',
                'type' => 'staff_rejected_appointment',
                'subject' => __('Booking rejection', 'bookme_pro'),
                'message' => wpautop(__("Hello.\n\nThe following booking has been rejected.\n\nReason: {cancellation_reason}\n\nService: {service_name}\nDate: {appointment_date}\nTime: {appointment_time}\nClient name: {client_name}\nClient phone: {client_phone}\nClient email: {client_email}", 'bookme_pro')),
                'active' => 1,
            ),

            array(
                'gateway' => 'sms',
                'type' => 'client_pending_appointment',
                'subject' => '',
                'message' => __("Dear {client_name}.\nThis is a confirmation that you have booked {service_name}.\nWe are waiting you at {company_address} on {appointment_date} at {appointment_time}.\nThank you for choosing our company.\n{company_name}\n{company_phone}\n{company_website}", 'bookme_pro'),
                'active' => 1,
            ),
            array(
                'gateway' => 'sms',
                'type' => 'staff_pending_appointment',
                'subject' => '',
                'message' => __("Hello.\nYou have a new booking.\nService: {service_name}\nDate: {appointment_date}\nTime: {appointment_time}\nClient name: {client_name}\nClient phone: {client_phone}\nClient email: {client_email}", 'bookme_pro'),
                'active' => 1,
            ),
            array(
                'gateway' => 'sms',
                'type' => 'client_approved_appointment',
                'subject' => '',
                'message' => __("Dear {client_name}.\nThis is a confirmation that you have booked {service_name}.\nWe are waiting you at {company_address} on {appointment_date} at {appointment_time}.\nThank you for choosing our company.\n{company_name}\n{company_phone}\n{company_website}", 'bookme_pro'),
                'active' => 1,
            ),
            array(
                'gateway' => 'sms',
                'type' => 'staff_approved_appointment',
                'subject' => '',
                'message' => __("Hello.\nYou have a new booking.\nService: {service_name}\nDate: {appointment_date}\nTime: {appointment_time}\nClient name: {client_name}\nClient phone: {client_phone}\nClient email: {client_email}", 'bookme_pro'),
                'active' => 1,
            ),
            array(
                'gateway' => 'sms',
                'type' => 'client_cancelled_appointment',
                'subject' => '',
                'message' => __("Dear {client_name}.\nYou have cancelled your booking of {service_name} on {appointment_date} at {appointment_time}.\nThank you for choosing our company.\n{company_name}\n{company_phone}\n{company_website}", 'bookme_pro'),
                'active' => 1,
            ),
            array(
                'gateway' => 'sms',
                'type' => 'staff_cancelled_appointment',
                'subject' => '',
                'message' => __("Hello.\nThe following booking has been cancelled.\nService: {service_name}\nDate: {appointment_date}\nTime: {appointment_time}\nClient name: {client_name}\nClient phone: {client_phone}\nClient email: {client_email}", 'bookme_pro'),
                'active' => 1,
            ),
            array(
                'gateway' => 'sms',
                'type' => 'client_rejected_appointment',
                'subject' => '',
                'message' => __("Dear {client_name}.\nYour booking of {service_name} on {appointment_date} at {appointment_time} has been rejected.\nReason: {cancellation_reason}\nThank you for choosing our company.\n{company_name}\n{company_phone}\n{company_website}", 'bookme_pro'),
                'active' => 1,
            ),
            array(
                'gateway' => 'sms',
                'type' => 'staff_rejected_appointment',
                'subject' => '',
                'message' => __("Hello.\nThe following booking has been rejected.\nReason: {cancellation_reason}\nService: {service_name}\nDate: {appointment_date}\nTime: {appointment_time}\nClient name: {client_name}\nClient phone: {client_phone}\nClient email: {client_email}", 'bookme_pro'),
                'active' => 1,
            )
        );
        /*
         * Options.
         */
        $this->options = array(
            // Appearance.
            'bookme_pro_app_color' => '#4e54c8',
            'bookme_pro_cal_color_left' => '#8f94fb',
            'bookme_pro_cal_color_right' => '#4e54c8',
            'bookme_pro_app_custom_styles' => '',
            'bookme_pro_app_required_employee' => '0',
            'bookme_pro_app_service_name_with_duration' => '0',
            'bookme_pro_app_show_login_button' => '0',
            'bookme_pro_app_show_progress_tracker' => '1',
            'bookme_pro_app_staff_name_with_price' => '1',
            'bookme_pro_app_show_calendar_availability' => '1',
            'bookme_pro_app_auto_calendar_size' => '0',
            'bookme_pro_app_layout' => '2',
            'bookme_pro_l10n_button_apply' => __('Apply', 'bookme_pro'),
            'bookme_pro_l10n_button_back' => __('Back', 'bookme_pro'),
            'bookme_pro_l10n_button_book_now' => __('Book Now', 'bookme_pro'),
            'bookme_pro_l10n_button_book_more' => __('Book More', 'bookme_pro'),
            'bookme_pro_l10n_info_cart_step' => __("Below you can find a list of services selected for booking.\nClick BOOK MORE if you want to add more services.", 'bookme_pro'),
            'bookme_pro_l10n_info_complete_step' => __('Thank you! Your booking is complete. An email with details of your booking has been sent to you.', 'bookme_pro'),
            'bookme_pro_l10n_info_complete_step_limit_error' => __('You are trying to use the service too often. Please contact us to make a booking.', 'bookme_pro'),
            'bookme_pro_l10n_info_complete_step_processing' => __('Your payment has been accepted for processing.', 'bookme_pro'),
            'bookme_pro_l10n_info_coupon_single_app' => __('The total price for the booking is {total_price}.', 'bookme_pro'),
            'bookme_pro_l10n_info_coupon_several_apps' => __('You selected to book {appointments_count} appointments with total price {total_price}.', 'bookme_pro'),
            'bookme_pro_l10n_info_details_step' => __("Please provide your details in the form below to proceed with booking.", 'bookme_pro'),
            'bookme_pro_l10n_info_details_step_guest' => '',
            'bookme_pro_l10n_info_service_step' => __('Please select service: ', 'bookme_pro'),
            'bookme_pro_l10n_info_time_step' => __("Below you can find a list of available time slots for {service_name} by {staff_name}.", 'bookme_pro'),
            'bookme_pro_l10n_label_category' => __('Category', 'bookme_pro'),
            'bookme_pro_l10n_label_ccard_code' => __('Card Security Code', 'bookme_pro'),
            'bookme_pro_l10n_label_ccard_expire' => __('Expiration Date', 'bookme_pro'),
            'bookme_pro_l10n_label_ccard_number' => __('Credit Card Number', 'bookme_pro'),
            'bookme_pro_l10n_label_coupon' => __('Coupon', 'bookme_pro'),
            'bookme_pro_l10n_label_service_time' => __('{service_date} {service_time}', 'bookme_pro'),
            'bookme_pro_l10n_label_service_details' => __('{service_name} by {staff_name} ⨯ {number_of_persons}', 'bookme_pro'),
            'bookme_pro_l10n_label_time' => __('Time', 'bookme_pro'),
            'bookme_pro_l10n_label_choose_payment' => __('Choose your payment method', 'bookme_pro'),
            'bookme_pro_l10n_label_sub_total' => __('Sub Total', 'bookme_pro'),
            'bookme_pro_l10n_label_discount' => __('Discount', 'bookme_pro'),
            'bookme_pro_l10n_label_total' => __('Total', 'bookme_pro'),
            'bookme_pro_l10n_label_email' => __('Email', 'bookme_pro'),
            'bookme_pro_l10n_label_employee' => __('Employee', 'bookme_pro'),
            'bookme_pro_l10n_label_finish_by' => __('Finish by', 'bookme_pro'),
            'bookme_pro_l10n_label_name' => __('Name', 'bookme_pro'),
            'bookme_pro_l10n_label_first_name' => __('First name', 'bookme_pro'),
            'bookme_pro_l10n_label_last_name' => __('Last name', 'bookme_pro'),
            'bookme_pro_l10n_label_number_of_persons' => __('Number of persons', 'bookme_pro'),
            'bookme_pro_l10n_label_pay_ccard' => __('I will pay now with Credit Card', 'bookme_pro'),
            'bookme_pro_l10n_label_pay_locally' => __('I will pay locally', 'bookme_pro'),
            'bookme_pro_l10n_label_pay_mollie' => __('I will pay now with Mollie', 'bookme_pro'),
            'bookme_pro_l10n_label_pay_paypal' => __('I will pay now with PayPal', 'bookme_pro'),
            'bookme_pro_l10n_label_phone' => __('Phone', 'bookme_pro'),
            'bookme_pro_l10n_label_select_date' => __('I\'m available on or after', 'bookme_pro'),
            'bookme_pro_l10n_calendar_availability' => __('Available', 'bookme_pro'),
            'bookme_pro_l10n_label_service' => __('Service', 'bookme_pro'),
            'bookme_pro_l10n_label_start_from' => __('Start from', 'bookme_pro'),
            'bookme_pro_l10n_option_category' => __('Select category', 'bookme_pro'),
            'bookme_pro_l10n_option_employee' => __('Any', 'bookme_pro'),
            'bookme_pro_l10n_option_service' => __('Select service', 'bookme_pro'),
            'bookme_pro_l10n_required_email' => __('Please tell us your email', 'bookme_pro'),
            'bookme_pro_l10n_required_employee' => __('Please select an employee', 'bookme_pro'),
            'bookme_pro_l10n_required_name' => __('Please tell us your name', 'bookme_pro'),
            'bookme_pro_l10n_required_first_name' => __('Please tell us your first name', 'bookme_pro'),
            'bookme_pro_l10n_required_last_name' => __('Please tell us your last name', 'bookme_pro'),
            'bookme_pro_l10n_required_phone' => __('Please tell us your phone', 'bookme_pro'),
            'bookme_pro_l10n_required_service' => __('Please select a service', 'bookme_pro'),
            'bookme_pro_l10n_step_service' => __('Service', 'bookme_pro'),
            'bookme_pro_l10n_step_time' => __('Time', 'bookme_pro'),
            'bookme_pro_l10n_step_time_slot_not_available' => __('The selected time is not available anymore. Please, choose another time slot.', 'bookme_pro'),
            'bookme_pro_l10n_step_cart' => __('Cart', 'bookme_pro'),
            'bookme_pro_l10n_step_cart_slot_not_available' => __('The highlighted time is not available anymore. Please, choose another time slot.', 'bookme_pro'),
            'bookme_pro_l10n_step_details' => __('Details', 'bookme_pro'),
            'bookme_pro_l10n_step_details_button_login' => __('Login', 'bookme_pro'),
            'bookme_pro_l10n_step_done' => __('Done', 'bookme_pro'),
            // Button Next.
            'bookme_pro_l10n_step_service_button_next' => __('Next', 'bookme_pro'),
            'bookme_pro_l10n_step_cart_button_next' => __('Next', 'bookme_pro'),
            'bookme_pro_l10n_step_details_button_next' => __('Next', 'bookme_pro'),
            // Cart.
            'bookme_pro_cart_enabled' => '0',
            'bookme_pro_cart_show_columns' => array(
                'service' => array('show' => 1), 'date' => array('show' => 1), 'time' => array('show' => 1),
                'employee' => array('show' => 1), 'price' => array('show' => 1), 'deposit' => array('show' => 1),
            ),
            // Calendar.
            'bookme_pro_cal_one_participant' => '{service_name}' . "\n" . '{client_name}' . "\n" . '{client_phone}' . "\n" . '{client_email}' . "\n" . '{custom_fields}' . "\n" . '{total_price} {payment_type} {payment_status}' . "\n" . __('Status', 'bookme_pro') . ': {status}' . "\n" . __('Signed up', 'bookme_pro') . ': {signed_up}' . "\n" . __('Capacity', 'bookme_pro') . ': {service_capacity}',
            'bookme_pro_cal_many_participants' => '{service_name}' . "\n" . __('Signed up', 'bookme_pro') . ': {signed_up}' . "\n" . __('Capacity', 'bookme_pro') . ': {service_capacity}',
            // Company.
            'bookme_pro_co_logo_attachment_id' => '',
            'bookme_pro_co_name' => '',
            'bookme_pro_co_address' => '',
            'bookme_pro_co_phone' => '',
            'bookme_pro_co_website' => '',
            // Customers.
            'bookme_pro_cst_cancel_action' => 'cancel',
            'bookme_pro_cst_combined_notifications' => '0',
            'bookme_pro_cst_create_account' => '0',
            'bookme_pro_cst_default_country_code' => '',
            'bookme_pro_cst_new_account_role' => 'subscriber',
            'bookme_pro_cst_phone_default_country' => 'auto',
            'bookme_pro_cst_remember_in_cookie' => '0',
            'bookme_pro_cst_show_update_details_dialog' => '1',
            'bookme_pro_cst_first_last_name' => '0',
            'bookme_pro_cst_required_phone' => '1',
            // Custom fields.
            'bookme_pro_custom_fields' => '[{"type":"textarea","label":'
                . json_encode(__('Notes', 'bookme_pro')) . ',"required":false,"id":1,"services":[]}]',
            'bookme_pro_custom_fields_per_service' => '0',
            'bookme_pro_custom_fields_merge_repetitive' => '1',
            // Email notifications.
            'bookme_pro_email_sender' => get_option('admin_email'),
            'bookme_pro_email_sender_name' => get_option('blogname'),
            'bookme_pro_email_send_as' => 'html',
            'bookme_pro_email_reply_to_customers' => '1',
            // Google Calendar.
            'bookme_pro_gc_client_id' => '',
            'bookme_pro_gc_client_secret' => '',
            'bookme_pro_gc_event_title' => '{service_name}',
            'bookme_pro_gc_limit_events' => '50',
            'bookme_pro_gc_two_way_sync' => '1',
            // General.
            'bookme_pro_purchase_code' => '',
            'bookme_pro_secret_file' => '',
            'bookme_pro_gen_time_slot_length' => '15',
            'bookme_pro_gen_service_duration_as_slot_length' => '0',
            'bookme_pro_gen_default_appointment_status' => Entities\CustomerAppointment::STATUS_APPROVED,
            'bookme_pro_gen_min_time_prior_booking' => '0',
            'bookme_pro_gen_min_time_prior_cancel' => '0',
            'bookme_pro_gen_max_days_for_booking' => '365',
            'bookme_pro_gen_use_client_time_zone' => '0',
            'bookme_pro_gen_allow_staff_edit_profile' => '1',
            'bookme_pro_gen_link_assets_method' => 'enqueue',
            'bookme_pro_gen_delete_data_on_uninstall' => '0',
            // URL.
            'bookme_pro_url_approve_page_url' => home_url(),
            'bookme_pro_url_approve_denied_page_url' => home_url(),
            'bookme_pro_url_cancel_page_url' => home_url(),
            'bookme_pro_url_cancel_denied_page_url' => home_url(),
            'bookme_pro_url_reject_page_url' => home_url(),
            'bookme_pro_url_reject_denied_page_url' => home_url(),
            'bookme_pro_url_final_step_url' => '',
            // Cron.
            'bookme_pro_cron_reminder_times' => array('client_follow_up' => 21, 'client_reminder' => 18, 'client_birthday_greeting' => 9, 'staff_agenda' => 18, 'client_reminder_1st' => 1, 'client_reminder_2nd' => 2, 'client_reminder_3rd' => 3),
            // SMS.
            'bookme_pro_sms_admin_phone' => '',
            'bookme_pro_twillio_account_sid' => '',
            'bookme_pro_twillio_auth_token' => '',
            'bookme_pro_twillio_phone_number' => '',
            // WooCommerce.
            'bookme_pro_wc_enabled' => '0',
            'bookme_pro_wc_product' => '',
            'bookme_pro_l10n_wc_cart_info_name' => __('Appointment', 'bookme_pro'),
            'bookme_pro_l10n_wc_cart_info_value' => __('Date', 'bookme_pro') . ": {appointment_date}\n"
                . __('Time', 'bookme_pro') . ": {appointment_time}\n" . __('Service', 'bookme_pro') . ': {service_name}',
            // Business hours.
            'bookme_pro_bh_monday_start' => '08:00',
            'bookme_pro_bh_monday_end' => '18:00',
            'bookme_pro_bh_tuesday_start' => '08:00',
            'bookme_pro_bh_tuesday_end' => '18:00',
            'bookme_pro_bh_wednesday_start' => '08:00',
            'bookme_pro_bh_wednesday_end' => '18:00',
            'bookme_pro_bh_thursday_end' => '18:00',
            'bookme_pro_bh_thursday_start' => '08:00',
            'bookme_pro_bh_friday_start' => '08:00',
            'bookme_pro_bh_friday_end' => '18:00',
            'bookme_pro_bh_saturday_start' => '',
            'bookme_pro_bh_saturday_end' => '',
            'bookme_pro_bh_sunday_start' => '',
            'bookme_pro_bh_sunday_end' => '',
            // Payments.
            'bookme_pro_pmt_currency' => 'USD',
            'bookme_pro_pmt_price_format' => '{symbol}{price|2}',
            'bookme_pro_pmt_coupons' => '0',
            'bookme_pro_pmt_local' => '1',
            // PayPal.
            'bookme_pro_pmt_paypal' => 'disabled',
            'bookme_pro_pmt_paypal_sandbox' => '0',
            'bookme_pro_pmt_paypal_api_password' => '',
            'bookme_pro_pmt_paypal_api_signature' => '',
            'bookme_pro_pmt_paypal_api_username' => '',
            'bookme_pro_pmt_paypal_id' => '',
            // Authorize.net
            'bookme_pro_pmt_authorize_net' => 'disabled',
            'bookme_pro_pmt_authorize_net_api_login_id' => '',
            'bookme_pro_pmt_authorize_net_transaction_key' => '',
            'bookme_pro_pmt_authorize_net_sandbox' => '0',
            // Stripe.
            'bookme_pro_pmt_stripe' => 'disabled',
            'bookme_pro_pmt_stripe_publishable_key' => '',
            'bookme_pro_pmt_stripe_secret_key' => '',
            // 2Checkout.
            'bookme_pro_pmt_2checkout' => 'disabled',
            'bookme_pro_pmt_2checkout_api_secret_word' => '',
            'bookme_pro_pmt_2checkout_api_seller_id' => '',
            'bookme_pro_pmt_2checkout_sandbox' => '0',
            // Payson.
            'bookme_pro_pmt_payson' => 'disabled',
            'bookme_pro_pmt_payson_api_agent_id' => '',
            'bookme_pro_pmt_payson_api_key' => '',
            'bookme_pro_pmt_payson_api_receiver_email' => '',
            'bookme_pro_pmt_payson_fees_payer' => 'PRIMARYRECEIVER',
            'bookme_pro_pmt_payson_funding' => array('CREDITCARD'),
            'bookme_pro_pmt_payson_sandbox' => '0',
            // Mollie.
            'bookme_pro_pmt_mollie' => 'disabled',
            'bookme_pro_pmt_mollie_api_key' => '',
        );
    }

    /**
     * Uninstall.
     */
    public function uninstall()
    {
       wp_clear_scheduled_hook('bookme_pro_daily_task');
    }

    /**
     * Create tables in database.
     */
    public function createTables()
    {
        /** @global \wpdb $wpdb */
        global $wpdb;

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . Entities\Staff::getTableName() . '` (
                `id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_user_id`         BIGINT(20) UNSIGNED DEFAULT NULL,
                `attachment_id`      INT UNSIGNED DEFAULT NULL,
                `full_name`          VARCHAR(255) DEFAULT NULL,
                `email`              VARCHAR(255) DEFAULT NULL,
                `phone`              VARCHAR(255) DEFAULT NULL,
                `info`               TEXT DEFAULT NULL,
                `google_data`        TEXT DEFAULT NULL,
                `google_calendar_id` VARCHAR(255) DEFAULT NULL,
                `visibility`         ENUM("public","private") NOT NULL DEFAULT "public",
                `position`           INT NOT NULL DEFAULT 9999
            ) ENGINE = INNODB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci'
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . Entities\Category::getTableName() . '` (
                `id`       INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `name`     VARCHAR(255) NOT NULL,
                `position` INT NOT NULL DEFAULT 9999
             ) ENGINE = INNODB
             DEFAULT CHARACTER SET = utf8
             COLLATE = utf8_general_ci'
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . Entities\Service::getTableName() . '` (
                `id`                    INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `category_id`           INT UNSIGNED DEFAULT NULL,
                `title`                 VARCHAR(255) DEFAULT "",
                `duration`              INT NOT NULL DEFAULT 900,
                `price`                 DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                `color`                 VARCHAR(255) NOT NULL DEFAULT "#FFFFFF",
                `capacity_min`          INT NOT NULL DEFAULT 1,
                `capacity_max`          INT NOT NULL DEFAULT 1,
                `padding_left`          INT NOT NULL DEFAULT 0,
                `padding_right`         INT NOT NULL DEFAULT 0,
                `info`                  TEXT DEFAULT NULL,
                `start_time_info`       VARCHAR(255) DEFAULT "",
                `end_time_info`         VARCHAR(255) DEFAULT "",
                `type`                  ENUM("simple","compound","package") NOT NULL DEFAULT "simple",
                `package_life_time`     INT DEFAULT NULL,
                `package_size`          INT DEFAULT NULL,
                `appointments_limit`    INT DEFAULT NULL,
                `limit_period`          ENUM("off", "day","week","month","year") NOT NULL DEFAULT "off",
                `staff_preference`      ENUM("order", "least_occupied", "most_occupied", "least_expensive", "most_expensive") NOT NULL DEFAULT "most_expensive",
                `visibility`            ENUM("public","private") NOT NULL DEFAULT "public",
                `position`              INT NOT NULL DEFAULT 9999,
                CONSTRAINT
                    FOREIGN KEY (category_id)
                    REFERENCES ' . Entities\Category::getTableName() . '(id)
                    ON DELETE SET NULL
                    ON UPDATE CASCADE
            ) ENGINE = INNODB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci'
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . Entities\SubService::getTableName() . '` (
                `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `type`              ENUM("service","spare_time") NOT NULL DEFAULT "service",
                `service_id`        INT UNSIGNED NOT NULL,
                `sub_service_id`    INT UNSIGNED DEFAULT NULL,
                `duration`          INT DEFAULT NULL,
                `position`          INT NOT NULL DEFAULT 9999,
                UNIQUE KEY unique_ids_idx (service_id, sub_service_id),
                CONSTRAINT
                    FOREIGN KEY (service_id)
                    REFERENCES ' . Entities\Service::getTableName() . '(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT
                    FOREIGN KEY (sub_service_id)
                    REFERENCES ' . Entities\Service::getTableName() . '(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
            ) ENGINE = INNODB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci'
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . Entities\StaffPreferenceOrder::getTableName() . '` (
                `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `service_id`  INT UNSIGNED NOT NULL,
                `staff_id`    INT UNSIGNED NOT NULL,
                `position`    INT NOT NULL DEFAULT 9999,
                CONSTRAINT
                    FOREIGN KEY (service_id)
                    REFERENCES ' . Entities\Service::getTableName() . '(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT
                    FOREIGN KEY (staff_id)
                    REFERENCES ' . Entities\Staff::getTableName() . '(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
            ) ENGINE = INNODB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci'
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . Entities\StaffScheduleItem::getTableName() . '` (
                `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `staff_id`   INT UNSIGNED NOT NULL,
                `day_index`  INT UNSIGNED NOT NULL,
                `start_time` TIME DEFAULT NULL,
                `end_time`   TIME DEFAULT NULL,
                UNIQUE KEY unique_ids_idx (staff_id, day_index),
                CONSTRAINT
                    FOREIGN KEY (staff_id)
                    REFERENCES ' . Entities\Staff::getTableName() . '(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
             ) ENGINE = INNODB
             DEFAULT CHARACTER SET = utf8
             COLLATE = utf8_general_ci'
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . Entities\StaffService::getTableName() . '` (
                `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `staff_id`     INT UNSIGNED NOT NULL,
                `service_id`   INT UNSIGNED NOT NULL,
                `price`        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                `deposit`      VARCHAR(100) NOT NULL DEFAULT "100%",
                `capacity_min` INT NOT NULL DEFAULT 1,
                `capacity_max` INT NOT NULL DEFAULT 1,
                UNIQUE KEY unique_ids_idx (staff_id, service_id),
                CONSTRAINT
                    FOREIGN KEY (staff_id)
                    REFERENCES ' . Entities\Staff::getTableName() . '(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT
                    FOREIGN KEY (service_id)
                    REFERENCES ' . Entities\Service::getTableName() . '(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
            ) ENGINE = INNODB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci'
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . Entities\ScheduleItemBreak::getTableName() . '` (
                `id`                     INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `staff_schedule_item_id` INT UNSIGNED NOT NULL,
                `start_time`             TIME DEFAULT NULL,
                `end_time`               TIME DEFAULT NULL,
                CONSTRAINT
                    FOREIGN KEY (staff_schedule_item_id)
                    REFERENCES ' . Entities\StaffScheduleItem::getTableName() . '(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
             ) ENGINE = INNODB
             DEFAULT CHARACTER SET = utf8
             COLLATE = utf8_general_ci'
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . Entities\Notification::getTableName() . '` (
                `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `gateway`      ENUM("email","sms") NOT NULL DEFAULT "email",
                `type`         VARCHAR(255) NOT NULL DEFAULT "",
                `active`       TINYINT(1) NOT NULL DEFAULT 0,
                `subject`      VARCHAR(255) NOT NULL DEFAULT "",
                `message`      TEXT DEFAULT NULL,
                `settings`     TEXT NULL,
                `to_staff`     TINYINT(1) NOT NULL DEFAULT 0,
                `to_customer`  TINYINT(1) NOT NULL DEFAULT 0,
                `to_admin`     TINYINT(1) NOT NULL DEFAULT 0
            ) ENGINE = INNODB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci'
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . Entities\Customer::getTableName() . '` (
                `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_user_id` BIGINT(20) UNSIGNED DEFAULT NULL,
                `full_name`  VARCHAR(255) NOT NULL DEFAULT "",
                `first_name` VARCHAR(255) NOT NULL DEFAULT "",
                `last_name`  VARCHAR(255) NOT NULL DEFAULT "",
                `phone`      VARCHAR(255) NOT NULL DEFAULT "",
                `email`      VARCHAR(255) NOT NULL DEFAULT "",
                `notes`      TEXT NOT NULL DEFAULT "",
                `birthday`   DATE DEFAULT NULL
            ) ENGINE = INNODB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci'
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . Entities\Series::getTableName() . '` (
                `id`     INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `repeat` VARCHAR(255) DEFAULT NULL,
                `token`  VARCHAR(255) NOT NULL
            ) ENGINE = INNODB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci'
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . Entities\Appointment::getTableName() . '` (
                `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `series_id`       INT UNSIGNED DEFAULT NULL,
                `location_id`     INT UNSIGNED DEFAULT NULL,
                `staff_id`        INT UNSIGNED NOT NULL,
                `staff_any`       TINYINT(1)   NOT NULL DEFAULT 0,
                `service_id`      INT UNSIGNED NOT NULL,
                `start_date`      DATETIME NOT NULL,
                `end_date`        DATETIME NOT NULL,
                `google_event_id` VARCHAR(255) DEFAULT NULL,
                `extras_duration` INT NOT NULL DEFAULT 0,
                `internal_note`   TEXT DEFAULT NULL,
                CONSTRAINT
                    FOREIGN KEY (series_id)
                    REFERENCES  ' . Entities\Series::getTableName() . '(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT
                    FOREIGN KEY (staff_id)
                    REFERENCES ' . Entities\Staff::getTableName() . '(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT
                    FOREIGN KEY (service_id)
                    REFERENCES ' . Entities\Service::getTableName() . '(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
            ) ENGINE = INNODB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci'
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . Entities\Holiday::getTableName() . '` (
                  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                  `staff_id`     INT UNSIGNED NULL DEFAULT NULL,
                  `parent_id`    INT UNSIGNED NULL DEFAULT NULL,
                  `date`         DATE NOT NULL,
                  `repeat_event` TINYINT(1) NOT NULL DEFAULT 0,
                  CONSTRAINT
                      FOREIGN KEY (staff_id)
                      REFERENCES ' . Entities\Staff::getTableName() . '(id)
                      ON DELETE CASCADE
              ) ENGINE = INNODB
              DEFAULT CHARACTER SET = utf8
              COLLATE = utf8_general_ci'
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . Entities\Payment::getTableName() . '` (
                `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `type`      ENUM("local","coupon","paypal","authorize_net","stripe","2checkout","payu_latam","payson","mollie","woocommerce") NOT NULL DEFAULT "local",
                `total`     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                `paid`      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                `paid_type` ENUM("in_full","deposit") NOT NULL DEFAULT "in_full",
                `status`    ENUM("pending","completed") NOT NULL DEFAULT "completed",
                `details`   TEXT DEFAULT NULL,
                `created`   DATETIME NOT NULL
            ) ENGINE = INNODB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci'
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . Entities\CustomerAppointment::getTableName() . '` (
                `id`                  INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `package_id`          INT UNSIGNED DEFAULT NULL,
                `customer_id`         INT UNSIGNED NOT NULL,
                `appointment_id`      INT UNSIGNED NOT NULL,
                `payment_id`          INT UNSIGNED DEFAULT NULL,
                `number_of_persons`   INT UNSIGNED NOT NULL DEFAULT 1,
                `extras`              TEXT DEFAULT NULL,
                `custom_fields`       TEXT DEFAULT NULL,
                `status`              ENUM("pending","approved","cancelled","rejected","waitlisted") NOT NULL DEFAULT "approved",
                `token`               VARCHAR(255) DEFAULT NULL,
                `time_zone`           VARCHAR(255) DEFAULT NULL,
                `time_zone_offset`    INT DEFAULT NULL,
                `locale`              VARCHAR(8) NULL,
                `compound_service_id` INT UNSIGNED DEFAULT NULL,
                `compound_token`      VARCHAR(255) DEFAULT NULL,
                `created_from`        ENUM("frontend","backend") NOT NULL DEFAULT "frontend",
                `created`             DATETIME NOT NULL,
                CONSTRAINT
                    FOREIGN KEY (customer_id)
                    REFERENCES  ' . Entities\Customer::getTableName() . '(id)
                    ON DELETE   CASCADE
                    ON UPDATE   CASCADE,
                CONSTRAINT
                    FOREIGN KEY (appointment_id)
                    REFERENCES  ' . Entities\Appointment::getTableName() . '(id)
                    ON DELETE   CASCADE
                    ON UPDATE   CASCADE,
                CONSTRAINT 
                    FOREIGN KEY (payment_id)
                    REFERENCES ' . Entities\Payment::getTableName() . '(id)
                    ON DELETE   SET NULL
                    ON UPDATE   CASCADE
            ) ENGINE = INNODB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci'
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . Entities\Coupon::getTableName() . '` (
                `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `code`        VARCHAR(255) NOT NULL DEFAULT "",
                `discount`    DECIMAL(3,0) NOT NULL DEFAULT 0,
                `deduction`   DECIMAL(10,2) NOT NULL DEFAULT 0,
                `usage_limit` INT UNSIGNED NOT NULL DEFAULT 1,
                `used`        INT UNSIGNED NOT NULL DEFAULT 0
            ) ENGINE = INNODB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci'
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . Entities\CouponService::getTableName() . '` (
                `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `coupon_id`   INT UNSIGNED NOT NULL,
                `service_id`  INT UNSIGNED NOT NULL,
                CONSTRAINT
                    FOREIGN KEY (coupon_id)
                    REFERENCES  ' . Entities\Coupon::getTableName() . '(id)
                    ON DELETE   CASCADE
                    ON UPDATE   CASCADE,
                CONSTRAINT
                    FOREIGN KEY (service_id)
                    REFERENCES  ' . Entities\Service::getTableName() . '(id)
                    ON DELETE   CASCADE
                    ON UPDATE   CASCADE
            ) ENGINE = INNODB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci'
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . Entities\SentNotification::getTableName() . '` (
                `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `ref_id`          INT UNSIGNED NOT NULL,
                `notification_id` INT UNSIGNED NOT NULL,
                `created`         DATETIME NOT NULL,
                INDEX `ref_id_idx` (`ref_id`),
                CONSTRAINT
                    FOREIGN KEY (notification_id) 
                    REFERENCES  ' . Entities\Notification::getTableName() . ' (`id`) 
                    ON DELETE   CASCADE 
                    ON UPDATE   CASCADE
              ) ENGINE = INNODB
              DEFAULT CHARACTER SET = utf8
              COLLATE = utf8_general_ci'
        );

    }

    /**
     * Load data.
     */
    public function loadData()
    {
        parent::loadData();

        // Insert notifications.
        foreach ($this->notifications as $data) {
            $notification = new Entities\Notification();
            $notification->setFields($data)->save();
        }

        // Register custom fields for translate in WPML
        foreach (json_decode($this->options['bookme_pro_custom_fields']) as $custom_field) {
            switch ($custom_field->type) {
                case 'textarea':
                case 'text-field':
                case 'captcha':
                    do_action('wpml_register_single_string', 'bookme_pro', 'custom_field_' . $custom_field->id . '_' . sanitize_title($custom_field->label), $custom_field->label);
                    break;
                case 'checkboxes':
                case 'radio-buttons':
                case 'drop-down':
                    do_action('wpml_register_single_string', 'bookme_pro', 'custom_field_' . $custom_field->id . '_' . sanitize_title($custom_field->label), $custom_field->label);
                    foreach ($custom_field->items as $label) {
                        do_action('wpml_register_single_string', 'bookme_pro', 'custom_field_' . $custom_field->id . '_' . sanitize_title($custom_field->label) . '=' . sanitize_title($label), $label);
                    }
                    break;
            }
        }
    }

    /**
     * Remove l10n data.
     */
    protected function _removeL10nData()
    {
        global $wpdb;
        $wpml_strings_table = $wpdb->prefix . 'icl_strings';
        $result = $wpdb->query("SELECT table_name FROM information_schema.tables WHERE table_name = '$wpml_strings_table' AND TABLE_SCHEMA=SCHEMA()");
        if ($result == 1) {
            @$wpdb->query("DELETE FROM {$wpdb->prefix}icl_string_translations WHERE string_id IN (SELECT id FROM $wpml_strings_table WHERE context='bookme_pro')");
            @$wpdb->query("DELETE FROM {$wpdb->prefix}icl_string_positions WHERE string_id IN (SELECT id FROM $wpml_strings_table WHERE context='bookme_pro')");
            @$wpdb->query("DELETE FROM {$wpml_strings_table} WHERE context='bookme_pro'");
        }
    }

}