<?php
namespace BookmePro\Lib\Proxy;

use BookmePro\Lib\Base;

/**
 * Class Packages
 * Invoke local methods from Packages add-on.
 *
 * @package BookmePro\Lib\Proxy
 *
 * @method static void renderServicePackage( array $service, array $service_collection ) Render sub services for packages
 * @see \BookmeProPackages\Lib\ProxyProviders\Local::renderServicePackage()
 *
 * @method static void renderPackageScheduleDialog()
 * @see \BookmeProPackages\Lib\ProxyProviders\Local::renderPackageScheduleDialog()
 */
abstract class Packages extends Base\ProxyInvoker
{

}