<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BookmePro\Lib\Utils\Price;
use BookmePro\Lib\Utils\DateTime;
use BookmePro\Lib\Entities;
use BookmePro\Lib\Proxy;

$subtotal = 0;
$subtotal_deposit = 0;
?>
<?php if ( $payment ) : ?>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th width="50%"><?php esc_html_e( 'Customer', 'bookme_pro' ) ?></th>
                    <th width="50%"><?php esc_html_e( 'Payment', 'bookme_pro' ) ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $payment['customer'] ?></td>
                    <td>
                        <div><?php esc_html_e( 'Date', 'bookme_pro' ) ?>: <?php echo DateTime::formatDateTime( $payment['created'] ) ?></div>
                        <div><?php esc_html_e( 'Type', 'bookme_pro' ) ?>: <?php echo Entities\Payment::typeToString( $payment['type'] ) ?></div>
                        <div><?php esc_html_e( 'Status', 'bookme_pro' ) ?>: <?php echo Entities\Payment::statusToString( $payment['status'] ) ?></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Service', 'bookme_pro' ) ?></th>
                    <th><?php esc_html_e( 'Date', 'bookme_pro' ) ?></th>
                    <th><?php esc_html_e( 'Provider', 'bookme_pro' ) ?></th>
                    <?php if ( $deposit_enabled ): ?>
                        <th class="text-right"><?php esc_html_e( 'Deposit', 'bookme_pro' ) ?></th>
                    <?php endif ?>
                    <th class="text-right"><?php esc_html_e( 'Price', 'bookme_pro' ) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $items as $item ) :
                    $extras_price = 0; ?>
                    <tr>
                        <td>
                            <?php if ( $item['number_of_persons'] > 1 ) echo $item['number_of_persons'] . '&nbsp;&times;&nbsp;'  ?><?php echo $item['service_name'] ?>
                            <?php if ( ! empty ( $item['extras'] ) ) : ?>
                                <ul class="bookme-pro-list list-dots">
                                    <?php foreach ( $item['extras'] as $extra ) : ?>
                                        <li><?php if ( $extra['quantity'] > 1 ) echo $extra['quantity'] . '&nbsp;&times;&nbsp;' ?><?php echo $extra['title'] ?></li>
                                        <?php $extras_price += $extra['price'] * $extra['quantity'] ?>
                                    <?php endforeach ?>
                                </ul>
                            <?php endif ?>
                        </td>
                        <td><?php echo DateTime::formatDateTime( $item['appointment_date'] ) ?></td>
                        <td><?php echo $item['staff_name'] ?></td>
                        <?php $deposit = Proxy\DepositPayments::prepareAmount( $item['number_of_persons'] * ( $item['service_price'] + $extras_price ), $item['deposit'], $item['number_of_persons'] ) ?>
                        <?php if ( $deposit_enabled ) : ?>
                            <td class="text-right"><?php echo Proxy\DepositPayments::formatDeposit( $deposit, $item['deposit'] ) ?></td>
                        <?php endif ?>
                        <td class="text-right">
                            <?php $service_price = Price::format( $item['service_price'] ) ?>
                            <?php if ( $item['number_of_persons'] > 1 ) $service_price = $item['number_of_persons'] . '&nbsp;&times;&nbsp' . $service_price ?>
                            <?php echo $service_price ?>
                            <ul class="bookme-pro-list">
                            <?php foreach ( $item['extras'] as $extra ) : ?>
                                <li>
                                    <?php printf( '%s%s%s',
                                        ( $item['number_of_persons'] > 1 ) ? $item['number_of_persons'] . '&nbsp;&times;&nbsp;' : '',
                                        ( $extra['quantity'] > 1 ) ? $extra['quantity'] . '&nbsp;&times;&nbsp;' : '',
                                        Price::format( $extra['price'] )
                                    ) ?>
                                </li>
                                <?php $subtotal += $item['number_of_persons'] * $extra['price'] * $extra['quantity'] ?>
                            <?php endforeach ?>
                            </ul>
                        </td>
                    </tr>
                    <?php $subtotal += $item['number_of_persons'] * $item['service_price'] ?>
                    <?php $subtotal_deposit += $deposit ?>
                <?php endforeach ?>
            </tbody>
            <tfoot>
                <tr>
                    <th rowspan="3"></th>
                    <th colspan="2"><?php esc_html_e( 'Subtotal', 'bookme_pro' ) ?></th>
                    <?php if ( $deposit_enabled ) : ?>
                        <th class="text-right"><?php echo Price::format( $subtotal_deposit ) ?></th>
                    <?php endif ?>
                    <th class="text-right"><?php echo Price::format( $subtotal ) ?></th>
                </tr>
                <tr>
                    <th colspan="<?php echo 2 + (int) $deposit_enabled ?>">
                        <?php esc_html_e( 'Discount', 'bookme_pro' ) ?>
                        <?php if ( $payment['coupon'] ) : ?><div><small>(<?php echo $payment['coupon']['code'] ?>)</small></div><?php endif ?>
                    </th>
                    <th class="text-right">
                        <?php if ( $payment['coupon'] ) : ?>
                            <?php if ( $payment['coupon']['discount'] ) : ?>
                                <div>-<?php echo $payment['coupon']['discount'] ?>%</div>
                            <?php endif ?>
                            <?php if ( $payment['coupon']['deduction'] ) : ?>
                                <div><?php echo Price::format( -$payment['coupon']['deduction'] ) ?></div>
                            <?php endif ?>
                        <?php else : ?>
                            <?php echo Price::format( 0 ) ?>
                        <?php endif ?>
                    </th>
                </tr>
                <tr>
                    <th colspan="<?php echo 2 + (int) $deposit_enabled ?>"><?php esc_html_e( 'Total', 'bookme_pro' ) ?></th>
                    <th class="text-right"><?php echo Price::format( $payment['total'] ) ?></th>
                </tr>
                <?php if ( $payment['total'] != $payment['paid'] ) : ?>
                    <tr>
                        <td rowspan="2"></td>
                        <td colspan="<?php echo 2 + (int) $deposit_enabled ?>"><i><?php esc_html_e( 'Paid', 'bookme_pro' ) ?></i></td>
                        <td class="text-right"><i><?php echo Price::format( $payment['paid'] ) ?></i></td>
                    </tr>
                    <tr>
                        <td colspan="<?php echo 2 + (int) $deposit_enabled ?>"><i><?php esc_html_e( 'Due', 'bookme_pro' ) ?></i></td>
                        <td class="text-right"><i><?php echo Price::format( $payment['total'] - $payment['paid'] ) ?></i></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="<?php echo 3 + (int) $deposit_enabled ?>" class="text-right"><button type="button" class="btn btn-primary ladda-button" id="bookme-pro-complete-payment" data-spinner-size="40" data-style="zoom-in"><i><?php esc_html_e( 'Complete payment', 'bookme_pro' ) ?></i></button></td>
                    </tr>
                <?php endif ?>
            </tfoot>
        </table>
    </div>
<?php endif ?>