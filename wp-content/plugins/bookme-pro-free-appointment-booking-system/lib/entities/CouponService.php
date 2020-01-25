<?php
namespace BookmePro\Lib\Entities;

use BookmePro\Lib;

/**
 * Class CouponService
 * @package BookmePro\Lib\Entities
 */
class CouponService extends Lib\Base\Entity
{
    /** @var  int */
    protected $coupon_id = 0;
    /** @var  int  */
    protected $service_id = 0;

    protected static $table = 'bookme_pro_coupon_services';

    protected static $schema = array(
        'id'          => array( 'format' => '%d' ),
        'coupon_id'   => array( 'format' => '%d', 'reference' => array( 'entity' => 'Coupon' ) ),
        'service_id'  => array( 'format' => '%d', 'reference' => array( 'entity' => 'Service' ) ),
    );

    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

    /**
     * Gets coupon_id
     *
     * @return int
     */
    public function getCouponId()
    {
        return $this->coupon_id;
    }

    /**
     * Sets coupon_id
     *
     * @param int $coupon_id
     * @return $this
     */
    public function setCouponId( $coupon_id )
    {
        $this->coupon_id = $coupon_id;

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

}
