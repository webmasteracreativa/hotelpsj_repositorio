<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="bookme-pro-modal bookme-pro-fade bookme-pro-js-modal bookme-pro-js-login">
    <div class="bookme-pro-modal-dialog">
        <div class="bookme-pro-modal-content">
            <form>
                <div class="bookme-pro-modal-header">
                    <div><?php esc_html_e( 'Login', 'bookme_pro' ) ?></div>
                    <button type="button" class="bookme-pro-close bookme-pro-js-close">×</button>
                </div>
                <div class="bookme-pro-modal-body bookme-pro-form">
                    <div class="bookme-pro-form-group">
                        <label><?php esc_html_e( 'Username' ) ?></label>
                        <div>
                            <input type="text" name="log" required />
                        </div>
                    </div>
                    <div class="bookme-pro-form-group">
                        <label><?php esc_html_e( 'Password' ) ?></label>
                        <div>
                            <input type="password" name="pwd" required />
                        </div>
                    </div>
                    <div class="bookme-pro-label-error"></div>
                    <div>
                        <div>
                            <label>
                                <input type="checkbox" name="rememberme" />
                                <span><?php esc_html_e( 'Remember Me' ) ?></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="bookme-pro-modal-footer">
                    <a class="bookme-pro-left bookme-pro-btn-cancel" href="<?php echo esc_url( wp_lostpassword_url() ) ?>" target="_blank"><?php esc_html_e( 'Lost your password?' ) ?></a>
                    <button class="bookme-pro-btn-submit ladda-button" type="submit" data-spinner-size="40" data-style="zoom-in">
                        <span class="ladda-label"><?php esc_html_e( 'Log In' ) ?></span>
                    </button>
                    <a href="#" class="bookme-pro-btn-cancel bookme-pro-js-close"><?php esc_html_e( 'Cancel' ) ?></a>
                </div>
            </form>
        </div>
    </div>
</div>