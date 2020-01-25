<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    $custom_fields = array_filter( json_decode( get_option( 'bookme_pro_custom_fields' ) ), function ( $field ) {
        return ! in_array( $field->type, array( 'captcha', 'text-content' ) );
    } );
?>
<div id="bookme-pro-tinymce-appointment-popup" style="display: none">
    <form id="bookme-pro-shortcode-form">
        <table>
            <tr>
                <th class="bookme-pro-title-col"><?php _e( 'Titles', 'bookme_pro' ) ?></th>
                <td>
                    <label><input type="checkbox" id="bookme-pro-show-column-titles" /><?php _e( 'Yes', 'bookme_pro' ) ?></label>
                </td>
            </tr>
            <tr>
                <th class="bookme-pro-title-col"><?php _e( 'Columns', 'bookme_pro' ) ?></th>
                <td>
                    <label><input type="checkbox" data-column="category" /><?php _e( 'Category', 'bookme_pro' ) ?></label>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <label><input type="checkbox" data-column="service" /><?php _e( 'Service', 'bookme_pro' ) ?></label>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <label><input type="checkbox" data-column="staff" /><?php _e( 'Staff', 'bookme_pro' ) ?></label>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <label><input type="checkbox" data-column="date" /><?php _e( 'Date', 'bookme_pro' ) ?></label>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <label><input type="checkbox" data-column="time" /><?php _e( 'Time', 'bookme_pro' ) ?></label>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <label><input type="checkbox" data-column="price" /><?php _e( 'Price', 'bookme_pro' ) ?></label>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <label><input type="checkbox" data-column="status" /><?php _e( 'Status', 'bookme_pro' ) ?></label>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <label><input type="checkbox" data-column="cancel" /><?php _e( 'Cancel', 'bookme_pro' ) ?></label>
                </td>
            </tr>
            <tr>
                <th colspan="2"><?php _e( 'Custom Fields', 'bookme_pro' ) ?></th>
            </tr>
            <?php foreach ( $custom_fields as $field ) : ?>
                <tr>
                    <td class="bookme-pro-cf-col"><?php echo $field->label ?></td>
                    <td>
                        <label><input type="checkbox" data-custom_field="<?php echo $field->id ?>" /><?php _e( 'Yes', 'bookme_pro' ) ?></label>
                    </td>
                </tr>
            <?php endforeach ?>
            <tr>
                <td></td>
                <td>
                    <input class="button button-primary" id="bookme-pro-insert-ap-shortcode" type="submit" value="<?php esc_attr_e( 'Insert', 'bookme_pro' ) ?>" />
                </td>
            </tr>
        </table>
    </form>
</div>