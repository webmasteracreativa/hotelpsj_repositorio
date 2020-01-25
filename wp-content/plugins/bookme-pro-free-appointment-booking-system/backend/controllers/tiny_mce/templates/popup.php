<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>
<div id="bookme-pro-tinymce-popup" style="display: none">
    <form id="bookme-pro-shortcode-form">
        <table>
            <?php \BookmePro\Lib\Proxy\Shared::renderPopUpShortCodeBookmeProFormHead() ?>
            <tr>
                <td>
                    <label for="bookme-pro-select-category"><?php _e('Default value for category select', 'bookme_pro') ?></label>
                </td>
                <td>
                    <select id="bookme-pro-select-category">
                        <option value=""><?php _e('Select category', 'bookme_pro') ?></option>
                    </select>
                    <div><label><input type="checkbox"
                                       id="bookme-pro-hide-categories"/><?php _e('Hide this field', 'bookme_pro') ?></label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="bookme-pro-select-service"><?php _e('Default value for service select', 'bookme_pro') ?></label>
                </td>
                <td>
                    <select id="bookme-pro-select-service">
                        <option value=""><?php _e('Select service', 'bookme_pro') ?></option>
                    </select>
                    <div><label><input type="checkbox"
                                       id="bookme-pro-hide-services"/><?php _e('Hide this field', 'bookme_pro') ?></label></div>
                    <i><?php _e('Please be aware that a value in this field is required in the frontend. If you choose to hide this field, please be sure to select a default value for it', 'bookme_pro') ?></i>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="bookme-pro-select-employee"><?php _e('Default value for employee select', 'bookme_pro') ?></label>
                </td>
                <td>
                    <select class="bookme-pro-select-mobile" id="bookme-pro-select-employee">
                        <option value=""><?php _e('Any', 'bookme_pro') ?></option>
                    </select>
                    <div><label><input type="checkbox"
                                       id="bookme-pro-hide-employee"/><?php _e('Hide this field', 'bookme_pro') ?></label></div>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="bookme-pro-hide-number-of-persons"><?php echo esc_html(get_option('bookme_pro_l10n_label_number_of_persons')) ?></label>
                </td>
                <td>
                    <label><input type="checkbox" id="bookme-pro-hide-number-of-persons"
                                  checked/><?php _e('Hide this field', 'bookme_pro') ?></label>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="bookme-pro-hide-calendar"><?php _e('Calendar', 'bookme_pro') ?></label>
                </td>
                <td>
                    <label><input type="checkbox" id="bookme-pro-hide-calendar"/><?php _e('Hide this field', 'bookme_pro') ?></label>
                </td>
            </tr>
            <?php \BookmePro\Lib\Proxy\Shared::renderPopUpShortCodeBookmeProForm() ?>
            <tr>
                <td></td>
                <td>
                    <input class="button button-primary" id="bookme-pro-insert-shortcode" type="submit"
                           value="<?php _e('Insert', 'bookme_pro') ?>"/>
                </td>
            </tr>
        </table>
    </form>
</div>