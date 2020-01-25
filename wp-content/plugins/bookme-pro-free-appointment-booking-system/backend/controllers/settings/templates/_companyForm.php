<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>
<form method="post" action="<?php echo esc_url(add_query_arg('tab', 'company')) ?>">
    <div class="row">
        <div class="col-xs-3 col-lg-2">
            <div class="bookme-pro-flexbox">
                <div id="bookme-pro-js-logo"
                     class="bookme-pro-thumb bookme-pro-thumb-lg bookme-pro-margin-right-lg bookme-pro-margin-left-remove">
                    <input type="hidden" name="bookme_pro_co_logo_attachment_id"
                           data-default="<?php form_option('bookme_pro_co_logo_attachment_id') ?>"
                           value="<?php form_option('bookme_pro_co_logo_attachment_id') ?>">
                    <div class="bookme-pro-flex-cell">
                        <div class="form-group">
                            <?php $img = wp_get_attachment_image_src(get_option('bookme_pro_co_logo_attachment_id'), 'thumbnail') ?>
                            <div class="bookme-pro-js-image bookme-pro-thumb bookme-pro-thumb-lg bookme-pro-margin-right-lg"
                                 data-style="<?php echo $img ? 'background-image: url(' . $img[0] . '); background-size: cover;' : '' ?>"
                                <?php echo $img ? 'style="background-image: url(' . $img[0] . '); background-size: cover;"' : '' ?>
                            >
                                <a class="dashicons dashicons-no-alt bookme-pro-thumb-delete"
                                   href="javascript:void(0)"
                                   title="<?php esc_html_e('Delete', 'bookme_pro') ?>"
                                   <?php if (!$img) : ?>style="display: none;"<?php endif ?>>
                                </a>
                                <div class="bookme-pro-thumb-edit">
                                    <div class="bookme-pro-pretty">
                                        <label class="bookme-pro-pretty-indicator bookme-pro-thumb-edit-btn">
                                            <?php esc_html_e('Image', 'bookme_pro') ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-9 col-lg-10">
            <div class="bookme-pro-flex-cell bookme-pro-vertical-middle">
                <?php \BookmePro\Lib\Utils\Common::optionText('bookme_pro_co_name', esc_html__('Company name', 'bookme_pro')) ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="bookme_pro_co_address"><?php esc_html_e('Address', 'bookme_pro') ?></label>
        <textarea id="bookme_pro_co_address" class="form-control" rows="5"
                  name="bookme_pro_co_address"><?php form_option('bookme_pro_co_address') ?></textarea>
    </div>
    <?php \BookmePro\Lib\Utils\Common::optionText('bookme_pro_co_phone', esc_html__('Phone', 'bookme_pro')) ?>
    <?php \BookmePro\Lib\Utils\Common::optionText('bookme_pro_co_website', esc_html__('Website', 'bookme_pro')) ?>

    <div class="panel-footer">
        <?php \BookmePro\Lib\Utils\Common::csrf() ?>
        <?php \BookmePro\Lib\Utils\Common::submitButton() ?>
        <?php \BookmePro\Lib\Utils\Common::resetButton('bookme-pro-company-reset') ?>
    </div>
</form>