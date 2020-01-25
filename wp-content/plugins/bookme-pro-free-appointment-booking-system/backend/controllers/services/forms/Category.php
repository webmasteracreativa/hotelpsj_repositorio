<?php
namespace BookmePro\Backend\Controllers\Services\Forms;

use BookmePro\Lib;

/**
 * Class Category
 * @method Lib\Entities\Category save()
 *
 * @package BookmePro\Backend\Controllers\Services\Forms
 */
class Category extends Lib\Base\Form
{
    protected static $entity_class = 'Category';

    /**
     * Configure the form.
     */
    public function configure()
    {
        $this->setFields( array( 'name' ) );
    }

}
