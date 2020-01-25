<?php
namespace BookmePro\Lib\Proxy;

use BookmePro\Lib\Base;

/**
 * Class SpecialDays
 * Invoke local methods from Special Days add-on.
 *
 * @package BookmePro\Lib\Proxy
 *
 * @method static array getSchedule( array $staff_ids, \DateTime $start_date, \DateTime $end_date )
 * @see \BookmeProSpecialDays\Lib\ProxyProviders\Local::getSchedule()
 *
 *  @method static array getServiceSchedule( int $service_id, \DateTime $start_date, \DateTime $end_date )
 * @see \BookmeProSpecialDays\Lib\ProxyProviders\Local::getServiceSchedule()
 *
 * @method static array getDaysAndTimes()
 * @see \BookmeProSpecialDays\Lib\ProxyProviders\Local::getDaysAndTimes()
 */
abstract class SpecialDays extends Base\ProxyInvoker
{

}