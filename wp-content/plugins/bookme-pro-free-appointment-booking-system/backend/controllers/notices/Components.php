<?php
namespace BookmePro\Backend\Controllers\Notices;

use BookmePro\Lib;
/**
 * Class Components
 * @package BookmePro\Backend\Controllers\Notices
 */
class Components extends Lib\Base\Components
{
    /**
     * Render limitation notice.
     */
    public function getLimitationHtml()
    {
        return $this->render( 'limitation', array(), false );
    }
}