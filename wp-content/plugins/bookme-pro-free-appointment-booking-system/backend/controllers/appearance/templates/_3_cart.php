<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BookmePro\Lib\Utils\Price;
use BookmePro\Lib\Utils\DateTime;

/** @var BookmePro\Backend\Controllers\Appearance\Lib\Helper $editable */
?>
<div class="bookme-pro-form">
    <?php include '_progress_tracker.php' ?>

    <div class="bookme-pro-box">
        <?php $editable::renderText( 'bookme_pro_l10n_info_cart_step', $this->render( '_codes', array( 'step' => 5 ), false ) ) ?>
        <div class="bookme-pro-holder bookme-pro-label-error bookme-pro-bold">
            <?php $editable::renderText( 'bookme_pro_l10n_step_cart_slot_not_available', null, 'bottom', esc_html__( 'Visible when the chosen time slot has been already booked', 'bookme_pro' ) ) ?>
        </div>
    </div>

    <div class="bookme-pro-box">
        <div class="bookme-pro-btn bookme-pro-add-item bookme-pro-inline-block">
            <?php $editable::renderString( array( 'bookme_pro_l10n_button_book_more', ) ) ?>
        </div>
    </div>

    <div class="bookme-pro-cart-step">
        <div class="bookme-pro-cart bookme-pro-box">
            <table>
                <thead class="bookme-pro-desktop-version">
                    <tr>
                        <th class="bookme-pro-js-option bookme_pro_l10n_label_service"><?php echo esc_html( get_option( 'bookme_pro_l10n_label_service' ) ) ?></th>
                        <th><?php esc_html_e( 'Date', 'bookme_pro' ) ?></th>
                        <th><?php esc_html_e( 'Time', 'bookme_pro' ) ?></th>
                        <th class="bookme-pro-js-option bookme_pro_l10n_label_employee"><?php echo esc_html( get_option( 'bookme_pro_l10n_label_employee' ) ) ?></th>
                        <th><?php esc_html_e( 'Price', 'bookme_pro' ) ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="bookme-pro-desktop-version">
                    <tr class="bookme-pro-cart-primary">
                        <td>Back Pain Treatment</td>
                        <td><?php echo DateTime::formatDate( '+2 days' ) ?></td>
                        <td><?php echo DateTime::formatTime( 28800 ) ?></td>
                        <td>Laura White</td>
                        <td><?php echo Price::format( 150 ) ?></td>
                        <td>
                            <button class="bookme-pro-round" title="<?php esc_attr_e( 'Edit', 'bookme_pro' ) ?>"><i class="bookme-pro-icon-sm bookme-pro-icon-edit"></i></button>
                            <button class="bookme-pro-round" title="<?php esc_attr_e( 'Remove', 'bookme_pro' ) ?>"><i class="bookme-pro-icon-sm bookme-pro-icon-drop"></i></button>
                        </td>
                    </tr>
                    <tr class="bookme-pro-cart-primary">
                        <td>Scoliosis Treatment</td>
                        <td><?php echo DateTime::formatDate( '+3 days' ) ?></td>
                        <td><?php echo DateTime::formatTime( 57600 ) ?></td>
                        <td>Magen Granger</td>
                        <td><?php echo Price::format( 450 ) ?></td>
                        <td>
                            <button class="bookme-pro-round" title="<?php esc_attr_e( 'Edit', 'bookme_pro' ) ?>"><i class="bookme-pro-icon-sm bookme-pro-icon-edit"></i></button>
                            <button class="bookme-pro-round" title="<?php esc_attr_e( 'Remove', 'bookme_pro' ) ?>"><i class="bookme-pro-icon-sm bookme-pro-icon-drop"></i></button>
                        </td>
                    </tr>
                </tbody>
                <tbody class="bookme-pro-mobile-version">
                    <tr class="bookme-pro-cart-primary">
                        <th class="bookme-pro-js-option bookme_pro_l10n_label_service"><?php echo esc_html( get_option( 'bookme_pro_l10n_label_service' ) ) ?></th>
                        <td>Back Pain Treatment</td>
                    </tr>
                    <tr class="bookme-pro-cart-primary">
                        <th><?php esc_html_e( 'Date', 'bookme_pro' ) ?></th>
                        <td><?php echo DateTime::formatDate( '+2 days' ) ?></td>
                    </tr>
                    <tr class="bookme-pro-cart-primary">
                        <th><?php esc_html_e( 'Time', 'bookme_pro' ) ?></th>
                        <td><?php echo DateTime::formatTime( 28800 ) ?></td>
                    </tr>
                    <tr class="bookme-pro-cart-primary">
                        <th class="bookme-pro-js-option bookme_pro_l10n_label_employee"><?php echo esc_html( get_option( 'bookme_pro_l10n_label_employee' ) ) ?></th>
                        <td>Laura White</td>
                    </tr>
                    <tr class="bookme-pro-cart-primary">
                        <th><?php esc_html_e( 'Price', 'bookme_pro' ) ?></th>
                        <td><?php echo Price::format( 150 ) ?></td>
                    </tr>
                    <tr class="bookme-pro-cart-primary">
                        <th></th>
                        <td>
                            <button class="bookme-pro-round" title="<?php esc_attr_e( 'Edit', 'bookme_pro' ) ?>"><i class="bookme-pro-icon-sm bookme-pro-icon-edit"></i></button>
                            <button class="bookme-pro-round" title="<?php esc_attr_e( 'Remove', 'bookme_pro' ) ?>"><i class="bookme-pro-icon-sm bookme-pro-icon-drop"></i></button>
                        </td>
                    </tr>
                    <tr class="bookme-pro-cart-primary">
                        <th class="bookme-pro-js-option bookme_pro_l10n_label_service"><?php echo esc_html( get_option( 'bookme_pro_l10n_label_service' ) ) ?></th>
                        <td>Scoliosis Treatment</td>
                    </tr>
                    <tr class="bookme-pro-cart-primary">
                        <th><?php esc_html_e( 'Date', 'bookme_pro' ) ?></th>
                        <td><?php echo DateTime::formatDate( '+3 days' ) ?></td>
                    </tr>
                    <tr class="bookme-pro-cart-primary">
                        <th><?php esc_html_e( 'Time', 'bookme_pro' ) ?></th>
                        <td><?php echo DateTime::formatTime( 57600 ) ?></td>
                    </tr>
                    <tr class="bookme-pro-cart-primary">
                        <th class="bookme-pro-js-option bookme_pro_l10n_label_employee"><?php echo esc_html( get_option( 'bookme_pro_l10n_label_employee' ) ) ?></th>
                        <td>Magen Granger</td>
                    </tr>
                    <tr class="bookme-pro-cart-primary">
                        <th><?php esc_html_e( 'Price', 'bookme_pro' ) ?></th>
                        <td><?php echo Price::format( 450 ) ?></td>
                    </tr>
                    <tr class="bookme-pro-cart-primary">
                        <th></th>
                        <td>
                            <button class="bookme-pro-round" title="<?php esc_attr_e( 'Edit', 'bookme_pro' ) ?>"><i class="bookme-pro-icon-sm bookme-pro-icon-edit"></i></button>
                            <button class="bookme-pro-round" title="<?php esc_attr_e( 'Remove', 'bookme_pro' ) ?>"><i class="bookme-pro-icon-sm bookme-pro-icon-drop"></i></button>
                        </td>
                    </tr>
                </tbody>
                <tfoot class="bookme-pro-desktop-version">
                    <tr>
                        <td colspan="4"><strong><?php esc_html_e( 'Total', 'bookme_pro' ) ?>:</strong></td>
                        <td><strong class="bookme-pro-js-total-price"><?php echo Price::format( 750 ) ?></strong></td>
                        <td></td>
                    </tr>
                </tfoot>
                <tfoot class="bookme-pro-mobile-version">
                    <tr>
                        <th><strong><?php esc_html_e( 'Total', 'bookme_pro' ) ?>:</strong></th>
                        <td><strong class="bookme-pro-js-total-price"><?php echo Price::format( 750 ) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <?php \BookmePro\Lib\Proxy\RecurringAppointments::renderAppearanceEditableInfoMessage() ?>

    <div class="bookme-pro-box bookme-pro-nav-steps">
        <div class="bookme-pro-back-step bookme-pro-js-back-step bookme-pro-btn">
            <?php $editable::renderString( array( 'bookme_pro_l10n_button_back' ) ) ?>
        </div>
        <div class="bookme-pro-next-step bookme-pro-js-next-step bookme-pro-btn">
            <?php $editable::renderString( array( 'bookme_pro_l10n_step_cart_button_next' ),is_rtl() ? 'right' : 'left' ) ?>
        </div>
    </div>
</div>
