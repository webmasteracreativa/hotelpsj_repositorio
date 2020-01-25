<?php
namespace BookmePro\Lib\Proxy;

use BookmePro\Lib\Base;

/**
 * Class ServiceSchedule
 * Invoke local methods from Service Schedule add-on.
 *
 * @package BookmePro\Lib\Proxy
 *
 * @method static array getSchedule( int $service_id ) Get schedule for service
 * @see \BookmeProServiceSchedule\Lib\ProxyProviders\Local::getSchedule()
 */
abstract class ServiceSchedule extends Base\ProxyInvoker
{

}