<?php
namespace BookmePro\Lib\DataHolders\Booking;

use BookmePro\Lib;

/**
 * Class Item
 * @package BookmePro\Lib\DataHolders\Booking
 */
abstract class Item
{
    const TYPE_SIMPLE   = 1;
    const TYPE_COMPOUND = 2;
    const TYPE_SERIES   = 3;

    /** @var int */
    protected $type;

    /**
     * Get type.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Check if item is simple.
     *
     * @return bool
     */
    public function isSimple()
    {
        return $this->type == self::TYPE_SIMPLE;
    }

    /**
     * Check if item is compound.
     *
     * @return bool
     */
    public function isCompound()
    {
        return $this->type == self::TYPE_COMPOUND;
    }

    /**
     * Check if item is series.
     *
     * @return bool
     */
    public function isSeries()
    {
        return $this->type == self::TYPE_SERIES;
    }

    /**
     * Get service.
     *
     * @return Lib\Entities\Service;
     */
    abstract public function getService();

    /**
     * Get staff.
     *
     * @return Lib\Entities\Staff
     */
    abstract public function getStaff();

    /**
     * Get appointment.
     *
     * @return Lib\Entities\Appointment
     */
    abstract public function getAppointment();

    /**
     * Get customer appointment.
     *
     * @return Lib\Entities\CustomerAppointment
     */
    abstract public function getCA();

    /**
     * Get price.
     *
     * @return float
     */
    abstract public function getPrice();

    /**
     * Get deposit.
     *
     * @return string
     */
    abstract public function getDeposit();
}