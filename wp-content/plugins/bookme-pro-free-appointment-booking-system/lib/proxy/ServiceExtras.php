<?php
namespace BookmePro\Lib\Proxy;

use BookmePro\Lib\Base;

/**
 * Class ServiceExtras
 * Invoke local methods from Service Extras add-on.
 *
 * @package BookmePro\Lib\Proxy
 *
 * @method static string getStepHtml( \BookmePro\Lib\UserBookingData $userData, bool $show_cart_btn, string $info_text, string $progress_tracker ) Render step Repeat
 * @see \BookmeProServiceExtras\Lib\ProxyProviders\Local::getStepHtml()
 *
 * @method static void renderAppearance( string $progress_tracker ) Render extras in appearance.
 * @see \BookmeProServiceExtras\Lib\ProxyProviders\Local::renderAppearance()
 *
 * @method static \BookmeProServiceExtras\Lib\Entities\ServiceExtra[] findByIds( array $extras_ids ) Return extras entities.
 * @see \BookmeProServiceExtras\Lib\ProxyProviders\Local::findByIds()
 *
 * @method static \BookmeProServiceExtras\Lib\Entities\ServiceExtra[] findByServiceId( int $service_id ) Return extras entities.
 * @see \BookmeProServiceExtras\Lib\ProxyProviders\Local::findByServiceId()
 *
 * @method static \BookmeProServiceExtras\Lib\Entities\ServiceExtra[] findAll() Return all extras entities.
 * @see \BookmeProServiceExtras\Lib\ProxyProviders\Local::findAll()
 *
 * @method static array getInfo( array $extras, bool $translate )
 * @see \BookmeProServiceExtras\Lib\ProxyProviders\Local::getInfo()
 *
 * @method static int getTotalDuration( array $extras )
 * @see \BookmeProServiceExtras\Lib\ProxyProviders\Local::getTotalDuration()
 *
 * @method static int reorder( array $order )
 * @see \BookmeProServiceExtras\Lib\ProxyProviders\Local::reorder()
 */
abstract class ServiceExtras extends Base\ProxyInvoker
{

}