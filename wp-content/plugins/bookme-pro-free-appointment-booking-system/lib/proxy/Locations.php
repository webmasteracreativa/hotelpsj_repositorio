<?php
namespace BookmePro\Lib\Proxy;

use BookmePro\Lib\Base;

/**
 * Class Locations
 * Invoke local methods from Locations add-on.
 *
 * @package BookmePro\Lib\Proxy
 *
 * @method static \BookmeProlocations\Lib\Entities\Location|false findById( int $location_id ) Return Location entity.
 * @see \BookmeProLocations\Lib\ProxyProviders\Local::findById()
 *
 * @method static \BookmeProlocations\Lib\Entities\Location[] findByStaffId( int $staff_id ) Return locations associated with given staff.
 * @see \BookmeProLocations\Lib\ProxyProviders\Local::findByStaffId()
 *
 * @method static void renderAppearance() Render Locations in Appearance
 * @see \BookmeProLocations\Lib\ProxyProviders\Local::renderAppearance()
 */
abstract class Locations extends Base\ProxyInvoker
{

}