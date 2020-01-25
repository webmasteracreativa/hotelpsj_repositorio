<?php
/**
 * Template to work with custom css.
 * Template includes button to show custom css form + form to edit it
 *
 * @var string $custom_css custom css text
 */
?>
<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>

<div class="form-group">
    <button type="button" class="btn btn-default" id="bookme-pro-custom-css-dialog-show">
        <?php esc_html_e('Edit custom CSS', 'bookme_pro'); ?>
    </button>
</div>

<div id="bookme-pro-custom-css-dialog" class="slidePanel <?php echo is_rtl() ? 'slidePanel-left' : 'slidePanel-right'; ?>">
    <div class="slidePanel-scrollable">
        <div>
            <div class="slidePanel-content">
                <header class="slidePanel-header">
                    <div class="slidePanel-overlay-panel">
                        <div class="slidePanel-heading">
                            <h2><?php esc_html_e('Edit custom CSS', 'bookme_pro') ?></h2>
                        </div>
                        <div class="slidePanel-actions">
                            <div class="btn-group-flat">
                                <button type="button"
                                        class="btn btn-primary btn-sm ajax-service-send" id="bookme-pro-custom-css-save"
                                        data-spinner-size="40" data-style="zoom-in"><i
                                            class="glyphicon glyphicon-ok"></i></button>
                                <button type="button" class="btn btn-default btn-sm" id="bookme-pro-custom-css-cancel"><i
                                            class="glyphicon glyphicon-remove"></i></button>
                            </div>
                        </div>
                    </div>
                </header>
                <div class="slidePanel-inner">
                    <div class="panel-body">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="bookme-pro-custom-css"
                                       class="control-label"><?php esc_html_e('Set up your custom CSS styles', 'bookme_pro') ?></label>
                                <textarea id="bookme-pro-custom-css" class="form-control"
                                          rows="10"><?php echo $custom_css ?></textarea>
                            </div>
                            <div id="bookme-pro-custom-css-error"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        var saved_css = <?php echo json_encode($custom_css); ?>;
    </script>
