<?php
namespace BookmePro\Lib\Entities;

use BookmePro\Lib;

/**
 * Class Coupon
 * @package BookmePro\Lib\Entities
 */
class Coupon extends Lib\Base\Entity
{
    /** @var  string */
    protected $code = '';
    /** @var  float */
    protected $discount = 0;
    /** @var  float  */
    protected $deduction = 0;
    /** @var  int */
    protected $usage_limit = 1;
    /** @var  int  */
    protected $used = 0;

    protected static $table = 'bookme_pro_coupons';

    protected static $schema = array(
        'id'          => array( 'format' => '%d' ),
        'code'        => array( 'format' => '%s' ),
        'discount'    => array( 'format' => '%d' ),
        'deduction'   => array( 'format' => '%f' ),
        'usage_limit' => array( 'format' => '%d' ),
        'used'        => array( 'format' => '%d' ),
    );

    /**
     * Apply coupon.
     *
     * @param $amount
     * @return float
     */
    public function apply( $amount )
    {
        $amount = round( $amount * ( 100 - $this->getDiscount() ) / 100 - $this->getDeduction(), 2 );

        return $amount > 0 ? $amount : 0;
    }

    /**
     * Increase the number of times the coupon has been used.
     *
     * @param int $quantity
     */
    public function claim( $quantity = 1 )
    {
        $this->setUsed( $this->getUsed() + $quantity );
    }

    /**
     * It's valid if the contains at least one service with  an applicable coupon.
     *
     * @param array $service_ids
     * @return bool
     */
    public function valid( array $service_ids )
    {
        return null !== Lib\Entities\CouponService::query()->whereIn( 'service_id', $service_ids )->where( 'coupon_id', $this->getId() )->fetchRow();
    }

    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

    /**
     * Gets code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Sets code
     *
     * @param string $code
     * @return $this
     */
    public function setCode( $code )
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Gets discount
     *
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Sets discount
     *
     * @param float $discount
     * @return $this
     */
    public function setDiscount( $discount )
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Gets deduction
     *
     * @return float
     */
    public function getDeduction()
    {
        return $this->deduction;
    }

    /**
     * Sets deduction
     *
     * @param float $deduction
     * @return $this
     */
    public function setDeduction( $deduction )
    {
        $this->deduction = $deduction;

        return $this;
    }

    /**
     * Gets usage_limit
     *
     * @return int
     */
    public function getUsageLimit()
    {
        return $this->usage_limit;
    }

    /**
     * Sets usage_limit
     *
     * @param int $usage_limit
     * @return $this
     */
    public function setUsageLimit( $usage_limit )
    {
        $this->usage_limit = $usage_limit;

        return $this;
    }

    /**
     * Gets used
     *
     * @return int
     */
    public function getUsed()
    {
        return $this->used;
    }

    /**
     * Sets used
     *
     * @param int $used
     * @return $this
     */
    public function setUsed( $used )
    {
        $this->used = $used;

        return $this;
    }

}