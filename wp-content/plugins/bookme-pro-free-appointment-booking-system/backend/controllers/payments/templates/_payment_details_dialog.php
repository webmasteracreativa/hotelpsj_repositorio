<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>
<script type="text/ng-template" id="bookme-pro-payment-details-dialog.tpl">
    <div id="bookme-pro-payment-details-modal" class="slidePanel <?php echo is_rtl() ? 'slidePanel-left' : 'slidePanel-right'; ?>">
        <div class="slidePanel-scrollable">
            <div>
                <div class="slidePanel-content">
                    <header class="slidePanel-header">
                        <div class="slidePanel-overlay-panel">
                            <div class="slidePanel-heading">
                                <h2><?php esc_html_e('Payment', 'bookme_pro') ?></h2>
                            </div>
                            <div class="slidePanel-actions">
                                <div class="btn-group-flat">
                                    <button type="button" class="btn btn-default btn-sm slidePanel-close"><i
                                                class="glyphicon glyphicon-remove"></i></button>
                                </div>
                            </div>
                        </div>
                    </header>
                    <div class="slidePanel-inner">
                        <div class="panel-body">
                            <div class="modal-body">
                                <div class="bookme-pro-loading"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>