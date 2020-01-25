<?php
namespace BookmePro\Lib\Proxy;

use BookmePro\Lib\Base;

/**
 * Class CompoundServices
 * Invoke local methods from Compound Services add-on.
 *
 * @package BookmePro\Lib\Proxy
 *
 * @method static void cancelAppointment( \BookmePro\Lib\Entities\CustomerAppointment $customer_appointment ) Cancel compound appointment
 * @see \BookmeProCompoundServices\Lib\ProxyProviders\Local::cancelAppointment()
 *
 * @method static void renderSubServices( array $service, array $service_collection, $sub_services ) Render sub services for compound
 * @see \BookmeProCompoundServices\Lib\ProxyProviders\Local::renderSubServices()
 */
abstract class CompoundServices extends Base\ProxyInvoker
{

}