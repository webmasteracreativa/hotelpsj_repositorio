<?php
namespace BookmePro\Backend\Controllers\Payments;

use BookmePro\Lib;

/**
 * Class Components
 * @package BookmePro\Backend\Controllers\Payments
 */
class Components extends Lib\Base\Components
{
    /**
     * Render payment details dialog.
     * @throws \Exception
     */
    public function renderPaymentDetailsDialog()
    {
        $this->enqueueStyles( array(
            'frontend' => array( 'css/ladda.min.css', ),
        ) );

        $this->enqueueScripts( array(
            'backend' => array( 'js/angular.min.js' => array( 'jquery' ), ),
            'frontend' => array(
                'js/spin.min.js'  => array( 'jquery' ),
                'js/ladda.min.js' => array( 'jquery' ),
            ),
            'module' => array( 'js/ng-payment_details_dialog.js' => array( 'bookme-pro-angular.min.js' ), ),
        ) );

        $this->render( '_payment_details_dialog' );
    }

}