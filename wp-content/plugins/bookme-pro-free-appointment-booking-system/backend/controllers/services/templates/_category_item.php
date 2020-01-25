<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<li class="bookme-pro-nav-item bookme-pro-category-item" data-category-id="<?php echo esc_attr($category['id']) ?>">
    <div class="bookme-pro-flexbox">
        <div class="bookme-pro-flex-cell bookme-pro-vertical-middle" style="width: 1%">
            <i class="bookme-pro-js-handle bookme-pro-icon bookme-pro-icon-draghandle bookme-pro-margin-right-sm bookme-pro-cursor-move" title="<?php esc_attr_e( 'Reorder', 'bookme_pro' ) ?>"></i>
        </div>
        <div class="bookme-pro-flex-cell bookme-pro-vertical-middle">
            <span class="displayed-value"><?php echo esc_html( $category['name'] ) ?></span>
            <input class="form-control" type="text" name="name" value="<?php echo esc_attr( $category['name'] ) ?>" style="display: none"/>
        </div>
        <div class="bookme-pro-flex-cell bookme-pro-vertical-middle" style="width: 1%">
            <a href="#" class="glyphicon glyphicon-pencil bookme-pro-margin-horizontal-xs bookme-pro-js-edit" title="<?php esc_attr_e( 'Edit', 'bookme_pro' ) ?>"></a>
        </div>
        <div class="bookme-pro-flex-cell bookme-pro-vertical-middle" style="width: 1%">
            <a href="#" class="glyphicon glyphicon-trash text-danger bookme-pro-js-delete" title="<?php esc_attr_e( 'Delete', 'bookme_pro' ) ?>"></a>
        </div>
    </div>
</li>
