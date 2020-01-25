<?php
namespace BookmePro\Lib\Proxy;

use BookmePro\Lib\Base;

/**
 * Class MultiplyAppointments
 * Invoke local methods from Multiply Appointments add-on.
 *
 * @package BookmePro\Lib\Proxy
 *
 * @method static void renderAppearance() Render Multiply Appointments in Appearance
 * @see \BookmeProMultiplyAppointments\Lib\ProxyProviders\Local::renderAppearance()
 */
abstract class MultiplyAppointments extends Base\ProxyInvoker
{

}