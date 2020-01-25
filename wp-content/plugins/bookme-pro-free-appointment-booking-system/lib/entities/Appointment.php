<?php
namespace BookmePro\Lib\Entities;

use BookmePro\Lib;

/**
 * Class Appointment
 * @package BookmePro\Lib\Entities
 */
class Appointment extends Lib\Base\Entity
{
    /** @var  int */
    protected $series_id;
    /** @var  int */
    protected $location_id;
    /** @var  int */
    protected $staff_id;
    /** @var  int */
    protected $staff_any = 0;
    /** @var  int */
    protected $service_id;
    /** @var  string */
    protected $start_date;
    /** @var  string */
    protected $end_date;
    /** @var  string */
    protected $google_event_id;
    /** @var  int */
    protected $extras_duration = 0;
    /** @var  string */
    protected $internal_note;

    protected static $table = 'bookme_pro_appointments';

    protected static $schema = array(
        'id'              => array( 'format' => '%d' ),
        'series_id'       => array( 'format' => '%d', 'reference' => array( 'entity' => 'Series' ) ),
        'location_id'     => array( 'format' => '%d' ),
        'staff_id'        => array( 'format' => '%d', 'reference' => array( 'entity' => 'Staff' ) ),
        'staff_any'       => array( 'format' => '%d' ),
        'service_id'      => array( 'format' => '%d', 'reference' => array( 'entity' => 'Service' ) ),
        'start_date'      => array( 'format' => '%s' ),
        'end_date'        => array( 'format' => '%s' ),
        'google_event_id' => array( 'format' => '%s' ),
        'extras_duration' => array( 'format' => '%d' ),
        'internal_note'   => array( 'format' => '%s' ),
    );

    /**
     * Get color of service
     *
     * @param string $default
     * @return string
     */
    public function getColor( $default = '#DDDDDD' )
    {
        if ( ! $this->isLoaded() ) {
            return $default;
        }

        $service = new Service();

        if ( $service->load( $this->getServiceId() ) ) {
            return $service->getColor();
        }

        return $default;
    }

    /**
     * Get CustomerAppointment entities associated with this appointment.
     *
     * @param bool $with_cancelled
     * @return CustomerAppointment[]   Array of entities
     */
    public function getCustomerAppointments( $with_cancelled = false )
    {
        $result = array();

        if ( $this->getId() ) {
            $appointments = CustomerAppointment::query( 'ca' )
                ->select( 'ca.*, c.full_name, c.first_name, c.last_name, c.phone, c.email' )
                ->leftJoin( 'Customer', 'c', 'c.id = ca.customer_id' )
                ->where( 'ca.appointment_id', $this->getId() );
            if ( ! $with_cancelled ) {
                $appointments->whereIn( 'ca.status', array( Lib\Entities\CustomerAppointment::STATUS_PENDING, Lib\Entities\CustomerAppointment::STATUS_APPROVED ) );
            }

            foreach ( $appointments->fetchArray() as $data ) {
                $ca = new CustomerAppointment( $data );

                // Inject Customer entity.
                $ca->customer = new Customer();
                $data['id']   = $data['customer_id'];
                $ca->customer->setFields( $data, true );

                $result[] = $ca;
            }
        }

        return $result;
    }

    /**
     * Set array of customers associated with this appointment.
     *
     * @param array  $cst_data  Array of customer IDs, custom_fields, number_of_persons, extras and status
     * @return CustomerAppointment[] Array of customer_appointment with changed status
     */
    public function saveCustomerAppointments( array $cst_data )
    {
        $ca_status_changed = array();
        $ca_data = array();
        foreach ( $cst_data as $item ) {
            if ( array_key_exists( 'ca_id', $item ) ) {
                $ca_id = $item['ca_id'];
            } else do {
                // New CustomerAppointment.
                $ca_id = 'new-' . mt_rand( 1, 999 );
            } while ( array_key_exists( $ca_id, $ca_data ) === true );
            $ca_data[ $ca_id ] = $item;
        }

        // Retrieve customer appointments IDs currently associated with this appointment.
        $current_ids   = array_map( function( CustomerAppointment $ca ) { return $ca->getId(); }, $this->getCustomerAppointments( true ) );
        $ids_to_delete = array_diff( $current_ids, array_keys( $ca_data ) );
        if ( ! empty ( $ids_to_delete ) ) {
            // Remove redundant customer appointments.
            CustomerAppointment::query()->delete()->whereIn( 'id', $ids_to_delete )->execute();
        }
        // Add new customer appointments.
        foreach ( array_diff( array_keys( $ca_data ), $current_ids ) as $id ) {
            $customer_appointment = new CustomerAppointment();
            $customer_appointment
                ->setAppointmentId( $this->getId() )
                ->setCustomerId( $ca_data[ $id ]['id'] )
                ->setCustomFields( json_encode( $ca_data[ $id ]['custom_fields'] ) )
                ->setExtras( json_encode( $ca_data[ $id ]['extras'] ) )
                ->setStatus( $ca_data[ $id ]['status'] )
                ->setNumberOfPersons( $ca_data[ $id ]['number_of_persons'] )
                ->setCreatedFrom( $ca_data[ $id ]['created_from'] )
                ->setCreated( current_time( 'mysql' ) )
                ->save();
            $ca_status_changed[] = $customer_appointment;
        }

        // Update existing customer appointments.
        foreach ( array_intersect( $current_ids, array_keys( $ca_data ) ) as $id ) {
            $customer_appointment = new CustomerAppointment();
            $customer_appointment->load( $id );

            if ( $customer_appointment->getStatus() != $ca_data[ $id ]['status'] ) {
                $ca_status_changed[] = $customer_appointment;
                $customer_appointment->setStatus( $ca_data[ $id ]['status'] );
            }
            $customer_appointment
                ->setNumberOfPersons( $ca_data[ $id ]['number_of_persons'] )
                ->setCustomFields( json_encode( $ca_data[ $id ]['custom_fields'] ) )
                ->setExtras( json_encode( $ca_data[ $id ]['extras'] ) )
                ->save();
        }

        return $ca_status_changed;
    }


    /**
     * Get max sum of extras duration of associated customer appointments.
     *
     * @return int
     */
    public function getMaxExtrasDuration()
    {
        $duration = 0;
        // Calculate extras duration for appointments with duration < 1 day.
        if ( strtotime( $this->getEndDate() ) - strtotime( $this->getStartDate() ) < DAY_IN_SECONDS ) {
            $customer_appointments = CustomerAppointment::query()
                ->select( 'extras' )
                ->where( 'appointment_id', $this->getId() )
                ->whereIn( 'status', array( CustomerAppointment::STATUS_PENDING, CustomerAppointment::STATUS_APPROVED ) )
                ->fetchArray();
            foreach ( $customer_appointments as $customer_appointment ) {
                if ( $customer_appointment['extras'] != '[]' ) {
                    $extras_duration = Lib\Proxy\ServiceExtras::getTotalDuration( (array) json_decode( $customer_appointment['extras'], true ) );
                    if ( $extras_duration > $duration ) {
                        $duration = $extras_duration;
                    }
                }
            }
        }

        return $duration;
    }

    /**
     * Get information about number of persons grouped by status.
     *
     * @return array
     */
    public function getNopInfo()
    {
        $res = self::query( 'a' )
           ->select( sprintf(
               'SUM(IF(ca.status = "%s", ca.number_of_persons, 0)) AS pending,
                SUM(IF(ca.status = "%s", ca.number_of_persons, 0)) AS approved,
                SUM(IF(ca.status = "%s", ca.number_of_persons, 0)) AS cancelled,
                SUM(IF(ca.status = "%s", ca.number_of_persons, 0)) AS rejected,
                SUM(IF(ca.status = "%s", ca.number_of_persons, 0)) AS waitlisted,
                ss.capacity_max',
                CustomerAppointment::STATUS_PENDING,
                CustomerAppointment::STATUS_APPROVED,
                CustomerAppointment::STATUS_CANCELLED,
                CustomerAppointment::STATUS_REJECTED,
                CustomerAppointment::STATUS_WAITLISTED
           ) )
           ->leftJoin( 'CustomerAppointment', 'ca', 'ca.appointment_id = a.id' )
           ->leftJoin( 'StaffService', 'ss', 'ss.staff_id = a.staff_id AND ss.service_id = a.service_id' )
           ->where( 'a.id', $this->getId() )
           ->groupBy( 'a.id' )
           ->fetchRow()
        ;

        $res['total_nop'] = $res['pending'] + $res['approved'];

        return $res;
    }

    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

    /**
     * Gets series_id
     *
     * @return int
     */
    public function getSeriesId()
    {
        return $this->series_id;
    }

    /**
     * Sets series_id
     *
     * @param Series $series
     * @return $this
     */
    public function setSeries( Series $series )
    {
        return $this->setSeriesId( $series->getId() );
    }

    /**
     * Sets series_id
     *
     * @param int $series_id
     * @return $this
     */
    public function setSeriesId( $series_id )
    {
        $this->series_id = $series_id;

        return $this;
    }

    /**
     * Gets location_id
     *
     * @return int
     */
    public function getLocationId()
    {
        return $this->location_id;
    }

    /**
     * Sets location_id
     *
     * @param int $location_id
     * @return $this
     */
    public function setLocationId( $location_id )
    {
        $this->location_id = $location_id;

        return $this;
    }

    /**
     * Gets staff_id
     *
     * @return int
     */
    public function getStaffId()
    {
        return $this->staff_id;
    }

    /**
     * Sets staff
     *
     * @param Staff $staff
     * @return $this
     */
    public function setStaff( Staff $staff )
    {
        return $this->setStaffId( $staff->getId() );
    }
    /**
     * Sets staff_id
     *
     * @param int $staff_id
     * @return $this
     */
    public function setStaffId( $staff_id )
    {
        $this->staff_id = $staff_id;

        return $this;
    }

    /**
     * Gets staff_any
     *
     * @return int
     */
    public function getStaffAny()
    {
        return $this->staff_any;
    }

    /**
     * Sets staff_any
     *
     * @param int $staff_any
     * @return $this
     */
    public function setStaffAny( $staff_any )
    {
        $this->staff_any = $staff_any;

        return $this;
    }

    /**
     * Gets service_id
     *
     * @return int
     */
    public function getServiceId()
    {
        return $this->service_id;
    }

    /**
     * Sets service
     *
     * @param Service $service
     * @return $this
     */
    public function setService( Service $service )
    {
        return $this->setServiceId( $service->getId() );
    }

    /**
     * Sets service_id
     *
     * @param int $service_id
     * @return $this
     */
    public function setServiceId( $service_id )
    {
        $this->service_id = $service_id;

        return $this;
    }

    /**
     * Gets start_date
     *
     * @return string
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Sets start_date
     *
     * @param string $start_date
     * @return $this
     */
    public function setStartDate( $start_date )
    {
        $this->start_date = $start_date;

        return $this;
    }

    /**
     * Gets end_date
     *
     * @return string
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * Sets end_date
     *
     * @param string $end_date
     * @return $this
     */
    public function setEndDate( $end_date )
    {
        $this->end_date = $end_date;

        return $this;
    }


    /**
     * Gets extras_duration
     *
     * @return int
     */
    public function getExtrasDuration()
    {
        return $this->extras_duration;
    }

    /**
     * Sets extras_duration
     *
     * @param int $extras_duration
     * @return $this
     */
    public function setExtrasDuration( $extras_duration )
    {
        $this->extras_duration = $extras_duration;

        return $this;
    }

    /**
     * Gets internal_note
     *
     * @return string
     */
    public function getInternalNote()
    {
        return $this->internal_note;
    }

    /**
     * Sets internal_note
     *
     * @param string $internal_note
     * @return $this
     */
    public function setInternalNote( $internal_note )
    {
        $this->internal_note = $internal_note;

        return $this;
    }

    /**************************************************************************
     * Overridden Methods                                                     *
     **************************************************************************/

    /**
     * Save appointment to database
     *
     * @return false|int
     */
    public function save()
    {

        return parent::save();
    }

    /**
     * Delete entity from database
     *
     * @return bool|false|int
     */
    public function delete()
    {
        // Delete all CustomerAppointments for current appointments
        $ca_list = Lib\Entities\CustomerAppointment::query()
            ->where( 'appointment_id', $this->getId() )
            ->find();
        /** @var Lib\Entities\CustomerAppointment $ca */
        foreach ( $ca_list as $ca ) {
            $ca->delete();
        }

        $result = parent::delete();
        if ( $result ) {
            if ( $this->getSeriesId() !== null ) {
                if ( Appointment::query()->where( 'series_id', $this->getSeriesId() )->count() === 0 ) {
                    Series::query()->delete()->where( 'id', $this->getSeriesId() )->execute();
                }
            }
        }

        return $result;
    }

}