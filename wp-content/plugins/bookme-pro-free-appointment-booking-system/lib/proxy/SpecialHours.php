<?php
namespace BookmePro\Lib\Proxy;

use BookmePro\Lib\Base;

/**
 * Class SpecialHours
 * Invoke local methods from Special Hours add-on.
 *
 * @package BookmePro\Lib\Proxy
 *
 * @method static string preparePrice( string $price, int $staff_id, int $service_id, $start_time )
 * @see \BookmeProSpecialHours\Lib\ProxyProviders\Local::preparePrice()
 */
abstract class SpecialHours extends Base\ProxyInvoker
{

}