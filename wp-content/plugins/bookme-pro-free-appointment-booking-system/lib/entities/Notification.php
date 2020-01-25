<?php
namespace BookmePro\Lib\Entities;

use BookmePro\Lib;

/**
 * Class Notification
 * @package BookmePro\Lib\Entities
 */
class Notification extends Lib\Base\Entity
{
    const TYPE_APPOINTMENT_START_TIME    = 'appointment_start_time';
    const TYPE_CUSTOMER_BIRTHDAY         = 'customer_birthday';
    const TYPE_LAST_CUSTOMER_APPOINTMENT = 'last_appointment';

    /** @var  string */
    protected $gateway = 'email';
    /** @var  string */
    protected $type;
    /** @var  bool  */
    protected $active = 0;
    /** @var  string */
    protected $subject = '';
    /** @var  string */
    protected $message = '';
    /** @var  int */
    protected $to_staff = 0;
    /** @var  int */
    protected $to_customer = 0;
    /** @var  bool  */
    protected $to_admin = 0;
    /** @var  string json */
    protected $settings = '[]';

    protected static $table = 'bookme_pro_notifications';

    protected static $schema = array(
        'id'       => array( 'format' => '%d' ),
        'gateway'  => array( 'format' => '%s' ),
        'type'     => array( 'format' => '%s' ),
        'active'   => array( 'format' => '%d' ),
        'subject'  => array( 'format' => '%s' ),
        'message'  => array( 'format' => '%s' ),
        'to_staff' => array( 'format' => '%d' ),
        'to_customer' => array( 'format' => '%d' ),
        'to_admin' => array( 'format' => '%d' ),
        'settings' => array( 'format' => '%s' ),
    );

    /** @var array Human readable notification names */
    public static $names;

    /** @var array */
    public static $type_ids;

    /**
     * Get type ID.
     *
     * @return int|null
     */
    public function getTypeId()
    {
        self::initTypeIds();

        return isset ( self::$type_ids[ $this->getType() ] )
            ? self::$type_ids[ $this->getType() ]
            : null;
    }

    /**
     * Get type string for given type ID.
     *
     * @param int $type_id
     * @return string|null
     */
    public static function getTypeString( $type_id )
    {
        self::initTypeIds();

        return array_search( $type_id, self::$type_ids ) ?: null;
    }

    /**
     * Notification name.
     *
     * @param $type
     * @return string
     */
    public static function getName( $type = null )
    {
        self::initNames();

        if ( array_key_exists( $type, self::$names ) ) {
            return self::$names[ $type ];
        } else {
            return __( 'Message', 'bookme_pro' );
        }
    }

    /**
     * Fill array with notification names.
     */
    private static function initNames()
    {
        if ( self::$names === null ) {
            self::$names = array(
                'client_approved_appointment'      => __( 'Notification to customer about approved appointment', 'bookme_pro' ),
                'client_approved_appointment_cart' => __( 'Notification to customer about approved appointments', 'bookme_pro' ),
                'client_cancelled_appointment'     => __( 'Notification to customer about cancelled appointment', 'bookme_pro' ),
                'client_rejected_appointment'      => __( 'Notification to customer about rejected appointment', 'bookme_pro' ),
                'client_follow_up'                 => __( 'Follow-up message in the same day after appointment (requires cron setup)', 'bookme_pro' ),
                'client_new_wp_user'               => __( 'Notification to customer about their WordPress user login details', 'bookme_pro' ),
                'client_pending_appointment'       => __( 'Notification to customer about pending appointment', 'bookme_pro' ),
                'client_pending_appointment_cart'  => __( 'Notification to customer about pending appointments', 'bookme_pro' ),
                'client_reminder'                  => __( 'Evening reminder to customer about next day appointment (requires cron setup)', 'bookme_pro' ),
                'client_reminder_1st'              => __( '1st reminder to customer about upcoming appointment (requires cron setup)', 'bookme_pro' ),
                'client_reminder_2nd'              => __( '2nd reminder to customer about upcoming appointment (requires cron setup)', 'bookme_pro' ),
                'client_reminder_3rd'              => __( '3rd reminder to customer about upcoming appointment (requires cron setup)', 'bookme_pro' ),
                'client_birthday_greeting'         => __( 'Customer birthday greeting (requires cron setup)', 'bookme_pro' ),
                'staff_agenda'                     => __( 'Evening notification with the next day agenda to staff member (requires cron setup)', 'bookme_pro' ),
                'staff_approved_appointment'       => __( 'Notification to staff member about approved appointment', 'bookme_pro' ),
                'staff_cancelled_appointment'      => __( 'Notification to staff member about cancelled appointment', 'bookme_pro' ),
                'staff_rejected_appointment'       => __( 'Notification to staff member about rejected appointment', 'bookme_pro' ),
                'staff_pending_appointment'        => __( 'Notification to staff member about pending appointment', 'bookme_pro' ),

                Notification::TYPE_APPOINTMENT_START_TIME => __( 'Notification about appointment date and time (requires cron setup)', 'bookme_pro' ),
                Notification::TYPE_CUSTOMER_BIRTHDAY      => __( 'Customer birthday greeting (requires cron setup)', 'bookme_pro' ),
                Notification::TYPE_LAST_CUSTOMER_APPOINTMENT => __( 'Notification about last appointment (requires cron setup)', 'bookme_pro' ),

                /** @see \BookmePro\Backend\Controllers\Sms\Controller::executeSendTestSms */
                'test_message'                     => __( 'Test message', 'bookme_pro' ),
            );

            self::$names = Lib\Proxy\Shared::prepareNotificationNames( self::$names );
        }
    }

    /**
     * Fill array of type ids.
     */
    private static function initTypeIds()
    {
        if ( self::$type_ids === null ) {
            self::$type_ids = array(
                /** @see \BookmePro\Backend\Controllers\Sms\Controller::executeSendTestSms */
                'test_message'                     => 0,

                'client_approved_appointment'      => 1,
                'client_approved_appointment_cart' => 2,
                'client_cancelled_appointment'     => 3,
                'client_follow_up'                 => 4,
                'client_new_wp_user'               => 5,
                'client_pending_appointment'       => 6,
                'client_pending_appointment_cart'  => 7,
                'client_reminder'                  => 8,
                'staff_agenda'                     => 9,
                'staff_approved_appointment'       => 10,
                'staff_cancelled_appointment'      => 11,
                'staff_pending_appointment'        => 12,
                'client_rejected_appointment'      => 13,
                'staff_rejected_appointment'       => 14,
                'client_birthday_greeting'         => 15,
                'client_reminder_1st'              => 16,
                'client_reminder_2nd'              => 17,
                'client_reminder_3rd'              => 18,
                Notification::TYPE_APPOINTMENT_START_TIME => 19,
                Notification::TYPE_CUSTOMER_BIRTHDAY => 15,
                Notification::TYPE_LAST_CUSTOMER_APPOINTMENT => 20,
                // Recurring Appointments add-on   => [31-38],
                // Waiting List add-on             => [51-53],
                // Packages add-on                 => [81-82],
            );

            self::$type_ids = Lib\Proxy\Shared::prepareNotificationTypeIds( self::$type_ids );
        }
    }

    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

    /**
     * Gets gateway
     *
     * @return string
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * Sets gateway
     *
     * @param string $gateway
     * @return $this
     */
    public function setGateway( $gateway )
    {
        $this->gateway = $gateway;

        return $this;
    }

    /**
     * Gets type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets type
     *
     * @param string $type
     * @return $this
     */
    public function setType( $type )
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Gets active
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Sets active
     *
     * @param bool $active
     * @return $this
     */
    public function setActive( $active )
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Gets to admin
     *
     * @return bool
     */
    public function getToAdmin()
    {
        return $this->to_admin;
    }

    /**
     * Sets to admin
     *
     * @param bool $to_admin
     * @return $this
     */
    public function setToAdmin( $to_admin )
    {
        $this->to_admin = $to_admin;

        return $this;
    }

    /**
     * Gets subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Sets subject
     *
     * @param string $subject
     * @return $this
     */
    public function setSubject( $subject )
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Gets message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Sets message
     *
     * @param string $message
     * @return $this
     */
    public function setMessage( $message )
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Gets to_staff
     *
     * @return int
     */
    public function getToStaff()
    {
        return $this->to_staff;
    }

    /**
     * Sets to_staff
     *
     * @param int $to_staff
     * @return $this
     */
    public function setToStaff( $to_staff )
    {
        $this->to_staff = $to_staff;

        return $this;
    }

    /**
     * Gets to_customer
     *
     * @return int
     */
    public function getToCustomer()
    {
        return $this->to_customer;
    }

    /**
     * Sets to_customer
     *
     * @param int $to_customer
     * @return $this
     */
    public function setToCustomer( $to_customer )
    {
        $this->to_customer = $to_customer;

        return $this;
    }

    /**
     * Gets settings
     *
     * @return string
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Sets settings
     *
     * @param string $settings
     * @return $this
     */
    public function setSettings( $settings )
    {
        $this->settings = $settings;

        return $this;
    }


    /**************************************************************************
     * Overridden Methods                                                     *
     **************************************************************************/

    /**
     * Save entity.
     *
     * @return false|int
     */
    public function save()
    {
        if ( is_array( $this->settings ) ) {
            $this->settings = json_encode( $this->settings );
        }
        $return = parent::save();
        if ( $this->isLoaded() ) {
            // Register string for translate in WPML.
            do_action( 'wpml_register_single_string', 'bookme_pro', $this->getGateway() . '_' . $this->getType(), $this->getMessage() );
            if ( $this->getGateway() == 'email' ) {
                do_action( 'wpml_register_single_string', 'bookme_pro', $this->getGateway() . '_' . $this->getType() . '_subject', $this->getSubject() );
            }
        }

        return $return;
    }

}