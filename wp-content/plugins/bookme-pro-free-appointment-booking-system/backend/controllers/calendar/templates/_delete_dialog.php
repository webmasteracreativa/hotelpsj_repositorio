<?php
/**
 * Template for delete appointment dialog
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<div id="bookme-pro-delete-dialog" class="modal fade" tabindex=-1 role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <div class="modal-title h2"><?php esc_html_e( 'Delete', 'bookme_pro' ) ?></div>
            </div>
            <div class="modal-body">
                <div class="checkbox">
                    <label>
                        <input id="bookme-pro-delete-notify" type="checkbox" />
                        <?php esc_html_e( 'Send notifications', 'bookme_pro' ) ?>
                    </label>
                </div>
                <div class="form-group" style="display: none;" id="bookme-pro-delete-reason-cover">
                    <input class="form-control" type="text" id="bookme-pro-delete-reason" placeholder="<?php esc_attr_e( 'Cancellation reason (optional)', 'bookme_pro' ) ?>" />
                </div>
            </div>
            <div class="modal-footer">
                <?php \BookmePro\Lib\Utils\Common::deleteButton(); ?>
                <?php \BookmePro\Lib\Utils\Common::customButton( null, 'btn-default', esc_html__( 'Cancel', 'bookme_pro' ), array( 'ng-click' => 'closeDialog()', 'data-dismiss' => 'modal' ) ) ?>
            </div>
        </div>
    </div>
</div>
