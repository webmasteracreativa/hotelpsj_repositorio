<?php
namespace BookmePro\Lib;

use BookmePro\Lib\DataHolders\Booking as DataHolders;

/**
 * Class NotificationSender
 * @package BookmePro\Lib
 */
abstract class NotificationSender
{
    /** @var SMS */
    private static $sms = null;

    /**
     * Send notifications from cart.
     *
     * @param DataHolders\Order $order
     */
    public static function sendFromCart( DataHolders\Order $order )
    {
        if ( Config::combinedNotificationsEnabled() ) {
            self::_sendCombined( $order );
        } else {
            foreach ( $order->getItems() as $item ) {
                switch ( $item->getType() ) {
                    case DataHolders\Item::TYPE_SIMPLE:
                    case DataHolders\Item::TYPE_COMPOUND:
                        self::sendSingle( $item, $order );
                        break;
                    case DataHolders\Item::TYPE_SERIES:
                        Proxy\RecurringAppointments::sendRecurring( $item, $order );
                        break;
                }
            }
        }
    }

    /**
     * Send notifications for single appointment.
     *
     * @param DataHolders\Item $item
     * @param DataHolders\Order $order
     * @param array $codes_data
     * @param bool $to_staff
     * @param bool $to_customer
     */
    public static function sendSingle(
        DataHolders\Item $item,
        DataHolders\Order $order = null,
        array $codes_data = array(),
        $to_staff = true,
        $to_customer = true
    )
    {
        global $sitepress;

        $wp_locale                 = $sitepress instanceof \SitePress ? $sitepress->get_default_language() : null;
        $order                     = $order ?: DataHolders\Order::createFromItem( $item );
        $status                    = $item->getCA()->getStatus();
        $staff_email_notification  = $to_staff ? self::_getEmailNotification( 'staff', $status ) : false;
        $staff_sms_notification    = $to_staff ? self::_getSmsNotification( 'staff', $status ) : false;
        $client_email_notification = $to_customer ? self::_getEmailNotification( 'client', $status ) : false;
        $client_sms_notification   = $to_customer ? self::_getSmsNotification( 'client', $status ) : false;

        if ( $staff_email_notification || $staff_sms_notification || $client_email_notification || $client_sms_notification ) {
            // Prepare codes.
            $codes = NotificationCodes::createForOrder( $order, $item );
            if ( isset ( $codes_data['cancellation_reason'] ) ) {
                $codes->cancellation_reason = $codes_data['cancellation_reason'];
            }

            // Notify staff by email.
            if ( $staff_email_notification ) {
                self::_sendEmailToStaff( $staff_email_notification, $codes, $item->getStaff()->getEmail() );
            }
            // Notify staff by SMS.
            if ( $staff_sms_notification ) {
                self::_sendSmsToStaff( $staff_sms_notification, $codes, $item->getStaff()->getPhone() );
            }

            // Customer locale.
            $customer_locale = $item->getCA()->getLocale() ?: $wp_locale;
            if ( $customer_locale != $wp_locale ) {
                self::_switchLocale( $customer_locale );
                $codes->refresh();
            }

            // Client time zone offset.
            if ( $item->getCA()->getTimeZoneOffset() !== null ) {
                $codes->appointment_start = self::_applyTimeZone( $codes->appointment_start, $item->getCA() );
                $codes->appointment_end   = self::_applyTimeZone( $codes->appointment_end, $item->getCA() );
            }
            // Notify client by email.
            if ( $client_email_notification ) {
                self::_sendEmailToClient( $client_email_notification, $codes, $order->getCustomer()->getEmail() );
            }
            // Notify client by SMS.
            if ( $client_sms_notification ) {
                self::_sendSmsToClient( $client_sms_notification, $codes, $order->getCustomer()->getPhone() );
            }

            if ( $customer_locale != $wp_locale ) {
                self::_switchLocale( $wp_locale );
            }
        }
    }

    /**
     * Send combined notifications.
     *
     * @param DataHolders\Order $order
     */
    protected static function _sendCombined( DataHolders\Order $order )
    {
        $status    = get_option( 'bookme_pro_gen_default_appointment_status' );
        $cart_info = array();
        $total     = 0.0;

        foreach ( $order->getItems() as $item ) {
            $sub_items = array();

            // Send notification to staff.
            switch ( $item->getType() ) {
                case DataHolders\Item::TYPE_SIMPLE:
                case DataHolders\Item::TYPE_COMPOUND:
                    self::sendSingle( $item, $order, array(),true, false );
                    $sub_items[] = $item;
                    break;
                case DataHolders\Item::TYPE_SERIES:
                    /** @var DataHolders\Series $item */
                    Proxy\RecurringAppointments::sendRecurring( $item, $order, array(), true, false );
                    $sub_items = $item->getItems();
                    if ( get_option( 'bookme_pro_recurring_appointments_payment' ) == 'first' ) {
                        array_splice( $sub_items, 1 );
                    }
                    break;
            }

            foreach ( $sub_items as $sub_item ) {
                // Sub-item price.
                $price = $sub_item->getPrice();

                // Prepare data for {cart_info} || {cart_info_c}.
                $cart_info[] = array(
                    'appointment_price' => $price,
                    'appointment_start' => self::_applyTimeZone( $sub_item->getAppointment()->getStartDate(), $sub_item->getCA() ),
                    'cancel_url'        => admin_url( 'admin-ajax.php?action=bookme_pro_cancel_appointment&token=' . $sub_item->getCA()->getToken() ),
                    'service_name'      => $sub_item->getService()->getTranslatedTitle(),
                    'staff_name'        => $sub_item->getStaff()->getTranslatedName(),
                    'extras'            => (array) Proxy\ServiceExtras::getInfo( json_decode( $sub_item->getCA()->getExtras(), true ), true ),
                    'appointment_start_info' => $sub_item->getService()->getDuration() < DAY_IN_SECONDS ? null : $sub_item->getService()->getStartTimeInfo(),
                );

                // Total price.
                $total += $price;
            }
        }

        // Prepare codes.
        $items = $order->getItems();
        $codes = NotificationCodes::createForOrder( $order, $items[0] );
        $codes->cart_info = $cart_info;
        if ( ! $order->hasPayment() ) {
            $codes->total_price = $total;
        }

        // Send notifications to client.
        if ( $to_client = self::_getCombinedEmailNotification( $status ) ) {
            self::_sendEmailToClient( $to_client, $codes, $order->getCustomer()->getEmail() );
        }
        if ( $to_client = self::_getCombinedSmsNotification( $status ) ) {
            self::_sendSmsToClient( $to_client, $codes, $order->getCustomer()->getPhone() );
        }
    }

    /**
     * Send reminder (email or SMS) to client.
     *
     * @param Entities\Notification $notification
     * @param DataHolders\Item $item
     * @return bool
     */
    public static function sendFromCronToClient( Entities\Notification $notification, DataHolders\Item $item )
    {
        global $sitepress;

        $wp_locale = $sitepress instanceof \SitePress ? $sitepress->get_default_language() : null;

        $order = DataHolders\Order::createFromItem( $item );

        $customer_locale = $item->getCA()->getLocale() ?: $wp_locale;
        if ( $customer_locale != $wp_locale ) {
            self::_switchLocale( $customer_locale );
        }

        $codes = NotificationCodes::createForOrder( $order, $item );

        // Client time zone offset.
        if ( $item->getCA()->getTimeZoneOffset() !== null ) {
            $codes->appointment_start = self::_applyTimeZone( $codes->appointment_start, $item->getCA() );
            $codes->appointment_end   = self::_applyTimeZone( $codes->appointment_end, $item->getCA() );
        }

        // Send notification to client.
        $result = $notification->getGateway() == 'email'
            ? self::_sendEmailToClient( $notification, $codes, $order->getCustomer()->getEmail() )
            : self::_sendSmsToClient( $notification, $codes, $order->getCustomer()->getPhone() );

        if ( $customer_locale != $wp_locale ) {
            self::_switchLocale( $wp_locale );
        }

        return $result;
    }

    /**
     * Send notification to Staff.
     *
     * @param Entities\Notification $notification
     * @param DataHolders\Item $item
     * @return bool
     */
    public static function sendFromCronToStaff( Entities\Notification $notification, DataHolders\Item $item )
    {
        $order = DataHolders\Order::createFromItem( $item );

        $codes = NotificationCodes::createForOrder( $order, $item );

        // Send notification to client.
        $result = $notification->getGateway() == 'email'
            ? self::_sendEmailToStaff( $notification, $codes, $item->getStaff()->getEmail() )
            : self::_sendSmsToStaff( $notification, $codes, $item->getStaff()->getPhone() );

        return $result;
    }

    /**
     * Send notification to administrators.
     *
     * @param Entities\Notification $notification
     * @param DataHolders\Item $item
     * @return bool
     */
    public static function sendFromCronToAdmin( Entities\Notification $notification, DataHolders\Item $item )
    {
        $order = DataHolders\Order::createFromItem( $item );

        $codes = NotificationCodes::createForOrder( $order, $item );

        // Send notification to client.
        $result = $notification->getGateway() == 'email'
            ? self::_sendEmailToAdmins( $notification, $codes )
            : self::_sendSmsToAdmin( $notification, $codes );

        return $result;
    }

    /**
     * Send reminder (email or SMS) to staff.
     *
     * @param Entities\Notification $notification
     * @param NotificationCodes $codes
     * @param string $email
     * @param string $phone
     * @return bool
     */
    public static function sendFromCronToStaffAgenda( Entities\Notification $notification, NotificationCodes $codes, $email, $phone )
    {
        return $notification->getGateway() == 'email'
            ? self::_sendEmailToStaff( $notification, $codes, $email, false )
            : self::_sendSmsToStaff( $notification, $codes, $phone );
    }

    /**
     * Send birthday greeting to client.
     *
     * @param Entities\Notification $notification
     * @param Entities\Customer $customer
     * @return bool
     */
    public static function sendFromCronBirthdayGreeting( Entities\Notification $notification, Entities\Customer $customer )
    {
        $codes = new NotificationCodes();
        $codes->client_email      = $customer->getEmail();
        $codes->client_name       = $customer->getFullName();
        $codes->client_first_name = $customer->getFirstName();
        $codes->client_last_name  = $customer->getLastName();
        $codes->client_phone      = $customer->getPhone();

        $result = $notification->getGateway() == 'email'
            ? self::_sendEmailToClient( $notification, $codes, $customer->getEmail() )
            : self::_sendSmsToClient( $notification, $codes, $customer->getPhone() );

        if ( $notification->getToAdmin() ) {
            $notification->getGateway() == 'email'
                ? self::_sendEmailToAdmins( $notification, $codes )
                : self::_sendSmsToAdmin( $notification, $codes );
        }

        return $result;
    }

    /**
     * Send email/sms with username and password for newly created WP user.
     *
     * @param Entities\Customer $customer
     * @param $username
     * @param $password
     */
    public static function sendNewUserCredentials( Entities\Customer $customer, $username, $password )
    {
        $codes = new NotificationCodes();
        $codes->client_email       = $customer->getEmail();
        $codes->client_name        = $customer->getFullName();
        $codes->client_first_name  = $customer->getFirstName();
        $codes->client_last_name   = $customer->getLastName();
        $codes->client_phone       = $customer->getPhone();
        $codes->new_password       = $password;
        $codes->new_username       = $username;
        $codes->site_address       = site_url();

        $to_client = new Entities\Notification();
        if ( $to_client->loadBy( array( 'type' => 'client_new_wp_user', 'gateway' => 'email', 'active' => 1 ) ) ) {
            self::_sendEmailToClient( $to_client, $codes, $customer->getEmail() );
        }
        if ( $to_client->loadBy( array( 'type' => 'client_new_wp_user', 'gateway' => 'sms', 'active' => 1 ) ) ) {
            self::_sendSmsToClient( $to_client, $codes, $customer->getPhone() );
        }
    }

    /**
     * Send test notification emails.
     *
     * @param string $to_mail
     * @param array  $notification_types
     * @param string $send_as
     */
    public static function sendTestEmailNotifications( $to_mail, array $notification_types, $send_as )
    {
        $codes = NotificationCodes::createForTest();
        $notification = new Entities\Notification();

        /**
         * @see \BookmePro\Backend\Controllers\Notifications\Controller::executeTestEmailNotifications
         * overwrite this setting and headers
         * in filter bookme_pro_email_headers
         */
        $reply_to_customer = false;

        foreach ( $notification_types as $type ) {
            $notification->loadBy( array( 'type' => $type, 'gateway' => 'email' ) );

            switch ( $type ) {
                case 'client_pending_appointment':
                case 'client_approved_appointment':
                case 'client_cancelled_appointment':
                case 'client_rejected_appointment':
                case 'client_waitlisted_appointment':
                case 'client_pending_appointment_cart':
                case 'client_approved_appointment_cart':
                case 'client_birthday_greeting':
                case 'client_follow_up':
                case 'client_new_wp_user':
                case 'client_reminder':
                case 'client_reminder_1st':
                case 'client_reminder_2nd':
                case 'client_reminder_3rd':
                case Entities\Notification::TYPE_CUSTOMER_BIRTHDAY:
                    self::_sendEmailToClient( $notification, $codes, $to_mail, $send_as );
                    break;
                case 'staff_pending_appointment':
                case 'staff_approved_appointment':
                case 'staff_cancelled_appointment':
                case 'staff_rejected_appointment':
                case 'staff_waitlisted_appointment':
                case 'staff_waiting_list':
                case 'staff_agenda':
                    self::_sendEmailToStaff( $notification, $codes, $to_mail, $reply_to_customer, $send_as );
                    break;
                // Recurring Appointments email notifications.
                case 'client_pending_recurring_appointment':
                case 'client_approved_recurring_appointment':
                case 'client_cancelled_recurring_appointment':
                case 'client_rejected_recurring_appointment':
                case 'client_waitlisted_recurring_appointment':
                    self::_sendEmailToClient( $notification, $codes, $to_mail, $send_as );
                    break;
                case 'staff_pending_recurring_appointment':
                case 'staff_approved_recurring_appointment':
                case 'staff_cancelled_recurring_appointment':
                case 'staff_rejected_recurring_appointment':
                case 'staff_waitlisted_recurring_appointment':
                    self::_sendEmailToStaff( $notification, $codes, $to_mail, $reply_to_customer, $send_as );
                    break;
                // Packages email notifications.
                case 'client_package_purchased':
                case 'client_package_deleted':
                    self::_sendEmailToClient( $notification, $codes, $to_mail, $send_as );
                    break;
                case 'staff_package_purchased':
                case 'staff_package_deleted':
                    self::_sendEmailToStaff( $notification, $codes, $to_mail, $reply_to_customer, $send_as );
                    break;
                // Custom email notifications.
                case Entities\Notification::TYPE_APPOINTMENT_START_TIME:
                case Entities\Notification::TYPE_LAST_CUSTOMER_APPOINTMENT:
                    if ( $notification->getToStaff() ) {
                        self::_sendEmailToStaff( $notification, $codes, $to_mail, $reply_to_customer, $send_as );
                    }
                    if ( $notification->getToCustomer() ) {
                        self::_sendEmailToClient( $notification, $codes, $to_mail, $send_as );
                    }
                    if ( ! $notification->getToStaff() && $notification->getToAdmin() ) {
                        self::_sendEmailToAdmins( $notification, $codes );
                    }
                    break;
            }
        }
    }

    /******************************************************************************************************************
     * Protected methods                                                                                                *
     ******************************************************************************************************************/

    /**
     * Send email notification to client.
     *
     * @param Entities\Notification $notification
     * @param NotificationCodes $codes
     * @param string $email
     * @param string|null $send_as
     * @return bool
     */
    protected static function _sendEmailToClient( Entities\Notification $notification, NotificationCodes $codes, $email, $send_as = null )
    {
        $subject = $codes->replace( Utils\Common::getTranslatedString(
            'email_' . $notification->getType() . '_subject',
            $notification->getSubject()
        ), 'text' );

        $message = Utils\Common::getTranslatedString(
            'email_' . $notification->getType(),
            $notification->getMessage()
        );

        $send_as_html = $send_as === null ? Config::sendEmailAsHtml() : $send_as == 'html';
        if ( $send_as_html ) {
            $message = wpautop( $codes->replace( $message, 'html' ) );
        } else {
            $message = $codes->replace( $message, 'text' );
        }

        return wp_mail( $email, $subject, $message, Utils\Common::getEmailHeaders() );
    }

    /**
     * Send email notification to staff.
     *
     * @param Entities\Notification $notification
     * @param NotificationCodes $codes
     * @param string $email
     * @param bool $reply_to_customer
     * @param string|null $send_as
     * @return bool
     */
    protected static function _sendEmailToStaff(
        Entities\Notification $notification,
        NotificationCodes $codes,
        $email,
        $reply_to_customer = null,
        $send_as = null
    )
    {
        // Subject.
        $subject = $codes->replace( $notification->getSubject(), 'text' );

        // Message.
        $message = self::_getMessageForStaff( $notification, 'staff', $grace );
        $send_as_html = $send_as === null ? Config::sendEmailAsHtml() : $send_as == 'html';
        if ( $send_as_html ) {
            $message = wpautop( $codes->replace( $message, 'html' ) );
        } else {
            $message = $codes->replace( $message, 'text' );
        }

        // Headers.
        $extra_headers = array();
        if ( $reply_to_customer === null ? get_option( 'bookme_pro_email_reply_to_customers' ) : $reply_to_customer ) {
            // Codes can be without order.
            if ( $codes->getOrder() !== null ) {
                $customer      = $codes->getOrder()->getCustomer();
                $extra_headers = array( 'reply-to' => array( 'email' => $customer->getEmail(), 'name' => $customer->getFullName() ) );
            }
        }

        $headers = Utils\Common::getEmailHeaders( $extra_headers );

        // Send email to staff.
        $result = wp_mail( $email, $subject, $message, $headers );

        // Send to administrators.
        if ( $notification->getToAdmin() ) {
            self::_sendEmailToAdmins( $notification, $codes );
        }

        return $result;
    }
    /**
     * Send email notification to admin.
     *
     * @param Entities\Notification $notification
     * @param NotificationCodes $codes
     *
     * @return bool
     */
    protected static function _sendEmailToAdmins(
        Entities\Notification $notification,
        NotificationCodes $codes
    )
    {
        $admin_emails = Utils\Common::getAdminEmails();
        if ( ! empty( $admin_emails ) ) {
            // Subject.
            $subject = $codes->replace( $notification->getSubject(), 'text' );

            // Message.
            $message      = self::_getMessageForStaff( $notification, 'staff', $grace );
            $send_as_html = Config::sendEmailAsHtml() == 'html';
            if ( $send_as_html ) {
                $message = wpautop( $codes->replace( $message, 'html' ) );
            } else {
                $message = $codes->replace( $message, 'text' );
            }

            return wp_mail( $admin_emails, $subject, $message, Utils\Common::getEmailHeaders() );
        }
        return true;
    }

    /**
     * Send SMS notification to client.
     *
     * @param Entities\Notification $notification
     * @param NotificationCodes $codes
     * @param string $phone
     * @return bool
     */
    protected static function _sendSmsToClient( Entities\Notification $notification, NotificationCodes $codes, $phone )
    {
        $message = $codes->replace( Utils\Common::getTranslatedString(
            'sms_' . $notification->getType(),
            $notification->getMessage()
        ), 'text' );

        if ( self::$sms === null ) {
            self::$sms = new SMS();
        }

        return self::$sms->sendSms( $phone, $message);
    }

    /**
     * Send SMS notification to staff.
     *
     * @param Entities\Notification $notification
     * @param NotificationCodes $codes
     * @param string $phone
     * @return bool
     */
    protected static function _sendSmsToStaff( Entities\Notification $notification, NotificationCodes $codes, $phone )
    {
        // Message.
        $message = $codes->replace( self::_getMessageForStaff( $notification, 'staff', $grace ), 'text' );

        // Send SMS to staff.
        if ( self::$sms === null ) {
            self::$sms = new SMS();
        }

        $result = self::$sms->sendSms( $phone, $message);

        // Send to administrators.
        if ( $notification->getToAdmin() ) {
            if ( $grace ) {
                $message = $codes->replace( self::_getMessageForStaff( $notification, 'admin' ), 'text' );
            }

            self::$sms->sendSms( get_option( 'bookme_pro_sms_administrator_phone', '' ), $message );
        }

        return $result;
    }

    /**
     * Send SMS notification to admin.
     *
     * @param Entities\Notification $notification
     * @param NotificationCodes $codes
     * @return bool
     */
    protected static function _sendSmsToAdmin( Entities\Notification $notification, NotificationCodes $codes )
    {
        // Message.
        $message = $codes->replace( self::_getMessageForStaff( $notification, 'staff', $grace ), 'text' );

        // Send SMS to staff.
        if ( self::$sms === null ) {
            self::$sms = new SMS();
        }

        // Send to administrators.
        if ( $grace ) {
            $message = $codes->replace( self::_getMessageForStaff( $notification, 'admin' ), 'text' );
        }

        return self::$sms->sendSms( get_option( 'bookme_pro_sms_administrator_phone', '' ), $message);
    }

    /**
     * Get email notification for given recipient and status.
     *
     * @param string $recipient
     * @param string $status
     * @param bool $is_recurring
     * @return Entities\Notification|bool
     */
    protected static function _getEmailNotification( $recipient, $status, $is_recurring = false )
    {
        $postfix = $is_recurring ? '_recurring' : '';
        return self::_getNotification( "{$recipient}_{$status}{$postfix}_appointment", 'email' );
    }

    /**
     * Get SMS notification for given recipient and appointment status.
     *
     * @param string $recipient
     * @param string $status
     * @param bool $is_recurring
     * @return Entities\Notification|bool
     */
    protected static function _getSmsNotification( $recipient, $status, $is_recurring = false )
    {
        $postfix = $is_recurring ? '_recurring' : '';
        return self::_getNotification( "{$recipient}_{$status}{$postfix}_appointment", 'sms' );
    }

    /**
     * Get combined email notification for given appointment status.
     *
     * @param string $status
     * @return Entities\Notification|bool
     */
    protected static function _getCombinedEmailNotification( $status )
    {
        return self::_getNotification( "client_{$status}_appointment_cart", 'email' );
    }

    /**
     * Get combined SMS notification for given appointment status.
     *
     * @param string $status
     * @return Entities\Notification|bool
     */
    protected static function _getCombinedSmsNotification( $status )
    {
        return self::_getNotification( "client_{$status}_appointment_cart", 'sms' );
    }

    /**
     * Get notification object.
     *
     * @param string $type
     * @param string $gateway
     * @return Entities\Notification|bool
     */
    protected static function _getNotification( $type, $gateway )
    {
        $notification = new Entities\Notification();
        if ( $notification->loadBy( array(
            'type'    => $type,
            'gateway' => $gateway,
            'active'  => 1
        ) ) ) {
            return $notification;
        }

        return false;
    }

    /**
     * @param Entities\Notification $notification
     * @param string                $recipient
     * @param bool                  $grace
     * @return string
     */
    protected static function _getMessageForStaff( Entities\Notification $notification, $recipient, &$grace = null )
    {
        return $notification->getMessage();
    }

    /**
     * Switch WordPress and WPML locale
     *
     * @param $locale
     */
    protected static function _switchLocale( $locale )
    {
        global $sitepress;

        if ( $sitepress instanceof \SitePress ) {
            $languages   = apply_filters( 'wpml_active_languages', 'skip_missing=0' );
            $locale_code = isset( $languages[ $locale ]['default_locale'] ) ? $languages[ $locale ]['default_locale'] : $locale;
            switch_to_locale( $locale_code );

            $sitepress->switch_lang( $locale );
        }
    }

    /**
     * Apply client time zone to given datetime string in WP time zone.
     *
     * @param string $datetime
     * @param Entities\CustomerAppointment $ca
     * @return false|string
     */
    protected static function _applyTimeZone( $datetime, Entities\CustomerAppointment $ca )
    {
        $time_zone        = $ca->getTimeZone();
        $time_zone_offset = $ca->getTimeZoneOffset();

        if ( $time_zone !== null ) {
            $datetime = date_create( $datetime . ' ' . Config::getWPTimeZone() );
            return date_format( date_timestamp_set( date_create( $time_zone ), $datetime->getTimestamp() ), 'Y-m-d H:i:s' );
        } else if ( $time_zone_offset !== null ) {
            return Utils\DateTime::applyTimeZoneOffset( $datetime, $time_zone_offset );
        }

        return $datetime;
    }
}