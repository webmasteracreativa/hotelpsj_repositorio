<?php
namespace BookmePro\Lib\Proxy;

use BookmePro\Lib;
use BookmePro\Lib\DataHolders\Booking as DataHolders;

/**
 * Class RecurringAppointments
 * Invoke local methods from Recurring Appointments add-on.
 *
 * @package BookmePro\Lib\Proxy
 *
 * @method static string getStepHtml( Lib\UserBookingData $userData, bool $show_cart_btn, string $info_text, string $progress_tracker ) Render Repeat step
 * @see \BookmeProRecurringAppointments\Lib\ProxyProviders\Local::getStepHtml()
 *
 * @method static bool couldBeRepeated( bool $default, Lib\UserBookingData $userData ) Check current appointment ca be repeatable, (Appointment from repeat appointment can't be repeated).
 * @see \BookmeProRecurringAppointments\Lib\ProxyProviders\Local::couldBeRepeated()
 *
 * @method static bool hideChildAppointments( bool $default, Lib\CartItem $cart_item ) When need pay only first appointment in series, we hide next appointments.
 * @see \BookmeProRecurringAppointments\Lib\ProxyProviders\Local::hideChildAppointments()
 *
 * @method static void cancelPayment( int $payment_id ) Cancel payment for whole series
 * @see \BookmeProRecurringAppointments\Lib\ProxyProviders\Local::cancelPayment()
 *
 * @method static array buildSchedule( Lib\UserBookingData $userData, string $start_time, string $end_time, string $repeat, array $params, int[] $slots ) Build schedule with passed slots
 * @see \BookmeProRecurringAppointments\Lib\ProxyProviders\Local::buildSchedule()
 *
 * @method static void renderRecurringSubForm() Render recurring sub form in appointment dialog
 * @see \BookmeProRecurringAppointments\Lib\ProxyProviders\Local::renderRecurringSubForm()
 *
 * @method static void renderSchedule() Render recurring schedule in appointment dialog
 * @see \BookmeProRecurringAppointments\Lib\ProxyProviders\Local::renderSchedule()
 *
 * @method static void renderAppearance( string $progress_tracker ) Render recurring sub form in appearance.
 * @see \BookmeProRecurringAppointments\Lib\ProxyProviders\Local::renderAppearance()
 *
 * @method static void renderAppearanceEditableInfoMessage() Render editable message.
 * @see \BookmeProRecurringAppointments\Lib\ProxyProviders\Local::renderAppearanceEditableInfoMessage()
 *
 * @method static void sendRecurring( DataHolders\Series $series, DataHolders\Order $order, $codes_data = array(), $to_staff = true, $to_customer = true )
 * @see \BookmeProRecurringAppointments\Lib\ProxyProviders\Local::sendRecurring()
 */
abstract class RecurringAppointments extends Lib\Base\ProxyInvoker
{

}