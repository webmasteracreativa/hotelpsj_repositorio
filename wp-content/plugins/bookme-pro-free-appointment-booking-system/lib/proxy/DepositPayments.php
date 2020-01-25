<?php
namespace BookmePro\Lib\Proxy;

use BookmePro\Lib\Base;

/**
 * Class DepositPayments
 * Invoke local methods from Deposit Payments Standard add-on.
 *
 * @package BookmePro\Lib\Proxy
 *
 * @method static string formatDeposit( double $deposit_amount, string $deposit ) Return formatted deposit amount
 * @see \BookmeProDepositPayments\Lib\ProxyProviders\Local::formatDeposit()
 *
 * @method static double|string prepareAmount( double $deposit_amount, string $deposit, int $number_of_persons ) Return deposit amount for all persons
 * @see \BookmeProDepositPayments\Lib\ProxyProviders\Local::prepareAmount()
 *
 * @method static void renderStaffServiceLabel() Render column header for deposit
 * @see \BookmeProDepositPayments\Lib\ProxyProviders\Local::renderStaffServiceLabel()
 */
abstract class DepositPayments extends Base\ProxyInvoker
{

}