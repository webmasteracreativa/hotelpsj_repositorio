<?php
namespace BookmePro\Lib\Proxy;

use BookmePro\Lib;

/**
 * Class WaitingList
 * Invoke local methods from Waiting List add-on.
 *
 * @package BookmePro\Lib\Proxy
 *
 * @method static void handleParticipantsChange( Lib\Entities\Appointment $appointment ) Handle the change of participants of given appointment
 * @see \BookmeProWaitingList\Lib\ProxyProviders\Local::handleParticipantsChange()
 */
abstract class WaitingList extends Lib\Base\ProxyInvoker
{

}