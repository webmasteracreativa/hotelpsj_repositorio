<?php
namespace BookmePro\Backend\Controllers\Customers\Forms;

use BookmePro\Lib;

/**
 * Class Customer
 * @package BookmePro\Backend\Controllers\Customers\Forms
 */
class Customer extends Lib\Base\Form
{
    protected static $entity_class = 'Customer';

    public function configure()
    {
        $this->setFields( array(
            'wp_user_id',
            'full_name',
            'first_name',
            'last_name',
            'phone',
            'email',
            'notes',
            'birthday',
        ) );
    }

}
