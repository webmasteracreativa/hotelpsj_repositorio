<?php
namespace BookmePro\Lib\Entities;

use BookmePro\Lib;
use BookmePro\Lib\DataHolders\Booking as DataHolders;

/**
 * Class CustomerAppointment
 * @package BookmePro\Lib\Entities
 */
class CustomerAppointment extends Lib\Base\Entity
{
    const STATUS_PENDING    = 'pending';
    const STATUS_APPROVED   = 'approved';
    const STATUS_CANCELLED  = 'cancelled';
    const STATUS_REJECTED   = 'rejected';
    const STATUS_WAITLISTED = 'waitlisted';

    /** @var  int */
    protected $package_id;
    /** @var  int */
    protected $customer_id;
    /** @var  int */
    protected $appointment_id;
    /** @var  int */
    protected $payment_id;
    /** @var  int */
    protected $number_of_persons = 1;
    /** @var  string */
    protected $extras = '[]';
    /** @var  string */
    protected $custom_fields = '[]';
    /** @var  string */
    protected $status;
    /** @var  string */
    protected $token;
    /** @var  string */
    protected $time_zone;
    /** @var  int */
    protected $time_zone_offset;
    /** @var  string */
    protected $locale;
    /** @var  int */
    protected $compound_service_id;
    /** @var  string */
    protected $compound_token;
    /** @var  string */
    protected $created_from;
    /** @var  string */
    protected $created;

    protected static $table = 'bookme_pro_customer_appointments';

    protected static $schema = array(
        'id'                  => array( 'format' => '%d' ),
        'package_id'          => array( 'format' => '%d' ),
        'customer_id'         => array( 'format' => '%d', 'reference' => array( 'entity' => 'Customer' ) ),
        'appointment_id'      => array( 'format' => '%d', 'reference' => array( 'entity' => 'Appointment' ) ),
        'payment_id'          => array( 'format' => '%d', 'reference' => array( 'entity' => 'Payment' ) ),
        'number_of_persons'   => array( 'format' => '%d' ),
        'extras'              => array( 'format' => '%s' ),
        'custom_fields'       => array( 'format' => '%s' ),
        'status'              => array( 'format' => '%s' ),
        'token'               => array( 'format' => '%s' ),
        'time_zone'           => array( 'format' => '%s' ),
        'time_zone_offset'    => array( 'format' => '%d' ),
        'locale'              => array( 'format' => '%s' ),
        'compound_service_id' => array( 'format' => '%d' ),
        'compound_token'      => array( 'format' => '%s' ),
        'created_from'        => array( 'format' => '%s' ),
        'created'             => array( 'format' => '%s' ),
    );

    /** @var Customer */
    public $customer;

    /**
     * Get array of custom fields with labels and values.
     *
     * @return array
     */
    public function getCustomFieldsData()
    {
        return $this->getPreparedCustomFields();
    }

    /**
     * Get translated array of custom fields with labels and values.
     *
     * @param string $locale
     * @return array
     */
    public function getTranslatedCustomFields( $locale = null )
    {
        return $this->getPreparedCustomFields( true, $locale );
    }

    /**
     * Get formatted custom fields.
     *
     * @param string $format
     * @param string $locale
     * @return string
     */
    public function getFormattedCustomFields( $format, $locale = null )
    {
        $result = '';
        switch ( $format ) {
            case 'html':
                foreach ( $this->getTranslatedCustomFields( $locale ) as $custom_field ) {
                    if ( $custom_field['value'] != '' ) {
                        $result .= sprintf(
                            '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                            $custom_field['label'], $custom_field['value']
                        );
                    }
                }
                if ( $result != '' ) {
                    $result = "<table cellspacing=0 cellpadding=0 border=0>$result</table>";
                }
                break;

            case 'text':
                foreach ( $this->getTranslatedCustomFields( $locale ) as $custom_field ) {
                    if ( $custom_field['value'] != '' ) {
                        $result .= sprintf(
                            "%s: %s\n",
                            $custom_field['label'], $custom_field['value']
                        );
                    }
                }
                break;
        }

        return $result;
    }

    /**
     * Delete entity and appointment if there are no more customers.
     *
     * @param bool $compound
     */
    public function deleteCascade( $compound = false )
    {
        $this->delete();
        $appointment = new Appointment();
        if ( $appointment->load( $this->getAppointmentId() ) ) {
            // Check if there are any customers left.
            if ( CustomerAppointment::query()->where( 'appointment_id', $appointment->getId() )->count() == 0 ) {
                // If no customers then delete the appointment.
                $appointment->delete();
            } else {
                // If there are customers then recalculate extras duration.
                if ( $this->getExtras() != '[]' ) {
                    $extras_duration = $appointment->getMaxExtrasDuration();
                    if ( $appointment->getExtrasDuration() != $extras_duration ) {
                        $appointment->setExtrasDuration( $extras_duration );
                        $appointment->save();
                    }
                }
                // Update GC event.
                $appointment->handleGoogleCalendar();
                // Waiting list.
                Lib\Proxy\WaitingList::handleParticipantsChange( $appointment );
            }
            if ( $compound && $this->getCompoundToken() ) {
                // Remove compound CustomerAppointments
                /** @var CustomerAppointment[] $ca_list */
                $ca_list = CustomerAppointment::query()
                    ->where( 'compound_token', $this->getCompoundToken() )
                    ->where( 'compound_service_id', $this->getCompoundServiceId() )
                    ->find();
                foreach ( $ca_list as $ca ) {
                    $ca->deleteCascade();
                }
            }
        }
    }

    public function getStatusTitle()
    {
        return self::statusToString( $this->getStatus() );
    }

    public function cancel()
    {
        $appointment = new Appointment();
        if ( $appointment->load( $this->getAppointmentId() ) ) {
            if ( $this->getStatus() != CustomerAppointment::STATUS_CANCELLED
                && $this->getStatus()!= CustomerAppointment::STATUS_REJECTED
            ) {
                $this->setStatus( CustomerAppointment::STATUS_CANCELLED );
                Lib\NotificationSender::sendSingle( DataHolders\Simple::create( $this ) );
            }

            if ( get_option( 'bookme_pro_cst_cancel_action' ) == 'delete' ) {
                $this->deleteCascade( true );
            } else {
                if ( $this->getCompoundToken() ) {
                    Lib\Proxy\CompoundServices::cancelAppointment( $this );
                } else {
                    $this->save();
                    if ( $this->getExtras() != '[]' ) {
                        $extras_duration = $appointment->getMaxExtrasDuration();
                        if ( $appointment->getExtrasDuration() != $extras_duration ) {
                            $appointment->setExtrasDuration( $extras_duration );
                            $appointment->save();
                        }
                    }
                    // Waiting list.
                    Lib\Proxy\WaitingList::handleParticipantsChange( $appointment );
                }
            }
        }
    }

    public static function statusToString( $status )
    {
        switch ( $status ) {
            case self::STATUS_PENDING:    return __( 'Pending',   'bookme_pro' );
            case self::STATUS_APPROVED:   return __( 'Approved',  'bookme_pro' );
            case self::STATUS_CANCELLED:  return __( 'Cancelled', 'bookme_pro' );
            case self::STATUS_REJECTED:   return __( 'Rejected',  'bookme_pro' );
            case self::STATUS_WAITLISTED: return __( 'On waiting list',  'bookme_pro' );
            default: return '';
        }
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        $statuses = array(
            CustomerAppointment::STATUS_PENDING,
            CustomerAppointment::STATUS_APPROVED,
            CustomerAppointment::STATUS_CANCELLED,
            CustomerAppointment::STATUS_REJECTED,
        );
        if ( Lib\Config::waitingListActive() ) {
            $statuses[] = CustomerAppointment::STATUS_WAITLISTED;
        }

        return $statuses;
    }

    /**
     * @param bool $translate
     * @param null $locale
     * @return array
     */
    private function getPreparedCustomFields( $translate = false, $locale = null )
    {
        $service_id = null;
        if ( Lib\Config::customFieldsPerService() ) {
            $service_id = Appointment::find( $this->getAppointmentId() )->getServiceId();
        }
        $result = array();
        if ( $this->custom_fields != '[]' ) {
            $custom_fields = array();
            $cf = $translate ? Lib\Utils\Common::getTranslatedCustomFields( $service_id, $locale ) : Lib\Utils\Common::getCustomFields( $service_id );
            foreach ( $cf as $field ) {
                $custom_fields[ $field->id ] = $field;
            }
            $data = json_decode( $this->custom_fields, true );
            if ( is_array( $data ) ) {
                foreach ( $data as $customer_custom_field ) {
                    if ( array_key_exists( $customer_custom_field['id'], $custom_fields ) ) {
                        $field = $custom_fields[ $customer_custom_field['id'] ];
                        $translated_value = array();
                        if ( array_key_exists( 'value', $customer_custom_field ) ) {
                            // Custom field have items ( radio group, etc. )
                            if ( property_exists( $field, 'items' ) ) {
                                foreach ( $field->items as $item ) {
                                    // Customer select many values ( checkbox )
                                    if ( is_array( $customer_custom_field['value'] ) ) {
                                        foreach ( $customer_custom_field['value'] as $field_value ) {
                                            if ( $item['value'] == $field_value ) {
                                                $translated_value[] = $item['label'];
                                            }
                                        }
                                    } elseif ( $item['value'] == $customer_custom_field['value'] ) {
                                        $translated_value[] = $item['label'];
                                    }
                                }
                            } else {
                                $translated_value[] = $customer_custom_field['value'];
                            }
                        }
                        $result[] = array(
                            'id'    => $customer_custom_field['id'],
                            'label' => $field->label,
                            'value' => implode( ', ', $translated_value )
                        );
                    }
                }
            }
        }

        return $result;
    }

    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

    /**
     * Gets customer_id
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customer_id;
    }

    /**
     * Sets package
     * @param \BookmeProPackages\Lib\Entities\Package $package
     * @return $this
     */
    public function setPackage( \BookmeProPackages\Lib\Entities\Package $package )
    {
        return $this->setPackageId( $package->getId() );
    }

    /**
     * Sets service_id
     *
     * @param int $package_id
     * @return $this
     */
    public function setPackageId( $package_id )
    {
        $this->package_id = $package_id;

        return $this;
    }

    /**
     * Gets service_id
     *
     * @return int
     */
    public function getPackageId()
    {
        return $this->package_id;
    }

    /**
     * Sets customer
     * @param Customer $customer
     * @return $this
     */
    public function setCustomer( Customer $customer )
    {
        return $this->setCustomerId( $customer->getId() );
    }

    /**
     * Sets customer_id
     *
     * @param int $customer_id
     * @return $this
     */
    public function setCustomerId( $customer_id )
    {
        $this->customer_id = $customer_id;

        return $this;
    }

    /**
     * Gets appointment_id
     *
     * @return int
     */
    public function getAppointmentId()
    {
        return $this->appointment_id;
    }

    /**
     * @param Appointment $appointment
     * @return $this
     */
    public function setAppointment( Appointment $appointment )
    {
        return $this->setAppointmentId( $appointment->getId() );
    }
    /**
     * Sets appointment_id
     *
     * @param int $appointment_id
     * @return $this
     */
    public function setAppointmentId( $appointment_id )
    {
        $this->appointment_id = $appointment_id;

        return $this;
    }

    /**
     * Gets payment_id
     *
     * @return int
     */
    public function getPaymentId()
    {
        return $this->payment_id;
    }

    /**
     * Sets payment_id
     *
     * @param int $payment_id
     * @return $this
     */
    public function setPaymentId( $payment_id )
    {
        $this->payment_id = $payment_id;

        return $this;
    }

    /**
     * Gets number_of_persons
     *
     * @return int
     */
    public function getNumberOfPersons()
    {
        return $this->number_of_persons;
    }

    /**
     * Sets number_of_persons
     *
     * @param int $number_of_persons
     * @return $this
     */
    public function setNumberOfPersons( $number_of_persons )
    {
        $this->number_of_persons = $number_of_persons;

        return $this;
    }

    /**
     * Gets extras
     *
     * @return string
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * Sets extras
     *
     * @param string $extras
     * @return $this
     */
    public function setExtras( $extras )
    {
        $this->extras = $extras;

        return $this;
    }

    /**
     * Sets custom_fields
     *
     * @param string $custom_fields
     * @return $this
     */
    public function setCustomFields( $custom_fields )
    {
        $this->custom_fields = $custom_fields;

        return $this;
    }

    /**
     * Gets custom_fields
     *
     * @return string
     */
    public function getCustomFields()
    {
        return $this->custom_fields;
    }

    /**
     * Gets status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus( $status )
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Gets token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Sets token
     *
     * @param string $token
     * @return $this
     */
    public function setToken( $token )
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Gets time_zone
     *
     * @return string
     */
    public function getTimeZone()
    {
        return $this->time_zone;
    }

    /**
     * Sets time_zone
     *
     * @param string $time_zone
     * @return $this
     */
    public function setTimeZone( $time_zone )
    {
        $this->time_zone = $time_zone;

        return $this;
    }

    /**
     * Gets time_zone_offset
     *
     * @return int
     */
    public function getTimeZoneOffset()
    {
        return $this->time_zone_offset;
    }

    /**
     * Sets time_zone_offset
     *
     * @param int $time_zone_offset
     * @return $this
     */
    public function setTimeZoneOffset( $time_zone_offset )
    {
        $this->time_zone_offset = $time_zone_offset;

        return $this;
    }

    /**
     * Gets locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Sets locale
     *
     * @param string $locale
     * @return $this
     */
    public function setLocale( $locale )
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Gets compound_service_id
     *
     * @return int
     */
    public function getCompoundServiceId()
    {
        return $this->compound_service_id;
    }

    /**
     * Sets compound_service_id
     *
     * @param int $compound_service_id
     * @return $this
     */
    public function setCompoundServiceId( $compound_service_id )
    {
        $this->compound_service_id = $compound_service_id;

        return $this;
    }

    /**
     * Gets compound_token
     *
     * @return string
     */
    public function getCompoundToken()
    {
        return $this->compound_token;
    }

    /**
     * Sets compound_token
     *
     * @param string $compound_token
     * @return $this
     */
    public function setCompoundToken( $compound_token )
    {
        $this->compound_token = $compound_token;

        return $this;
    }

    /**
     * Gets created_from
     *
     * @return string
     */
    public function getCreatedFrom()
    {
        return $this->created_from;
    }

    /**
     * Sets created_from
     *
     * @param string $created_from
     * @return $this
     */
    public function setCreatedFrom( $created_from )
    {
        $this->created_from = $created_from;

        return $this;
    }

    /**
     * Gets created
     *
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Sets created
     *
     * @param string $created
     * @return $this
     */
    public function setCreated( $created )
    {
        $this->created = $created;

        return $this;
    }

    /**************************************************************************
     * Overridden Methods                                                     *
     **************************************************************************/

    /**
     * Save entity to database.
     * Generate token before saving.
     *
     * @return int|false
     */
    public function save()
    {
        // Generate new token if it is not set.
        if ( $this->getToken() == '' ) {
            $this->setToken( Lib\Utils\Common::generateToken( get_class( $this ), 'token' ) );
        }
        if ( $this->getLocale() === null ) {
            $this->setLocale( apply_filters( 'wpml_current_language', null ) );
        }

        return parent::save();
    }

}