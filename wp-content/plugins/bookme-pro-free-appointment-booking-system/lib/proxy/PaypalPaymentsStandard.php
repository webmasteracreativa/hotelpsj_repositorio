<?php
namespace BookmePro\Lib\Proxy;

use BookmePro\Lib\Base;

/**
 * Class PaypalPaymentsStandard
 * Invoke local methods from PayPal Payments Standard add-on.
 *
 * @package BookmePro\Lib\Proxy
 *
 * @method static array prepareToggleOptions( array $options ) returns option to enable PayPal Payments Standard
 * @see \BookmeProPaypalPaymentsStandard\Lib\ProxyProviders\Local::prepareToggleOptions()
 *
 * @method static string renderSetUpOptions() prints list of options to set up PayPal Payments Standard
 * @see \BookmeProPaypalPaymentsStandard\Lib\ProxyProviders\Local::renderSetUpOptions()
 *
 * @method static string renderPaymentForm( string $form_id, string $page_url ) outputs HTML form for PayPal Payments Standard.
 * @see \BookmeProPaypalPaymentsStandard\Lib\ProxyProviders\Local::renderPaymentForm()
 */
abstract class PaypalPaymentsStandard extends Base\ProxyInvoker
{

}
