<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly

$img = wp_get_attachment_image_src($staff['attachment_id'], 'thumbnail');
?>
<tr role="row" class="odd" id="bookme-pro-staff-<?php echo $staff['id'] ?>" data-staff-id="<?php echo $staff['id'] ?>">
    <td>
        <div class="bookme-pro-flexbox">
            <div class="bookme-pro-flex-cell bookme-pro-vertical-middle" style="width: 1%">
                <i class="bookme-pro-js-handle bookme-pro-icon bookme-pro-icon-draghandle bookme-pro-margin-right-sm bookme-pro-cursor-move"
                   title="<?php esc_attr_e('Reorder', 'bookme_pro') ?>"></i>
            </div>
            <div class="bookme-pro-flex-cell bookme-pro-vertical-middle" style="width: 1%">
                <div class="bookme-pro-thumb bookme-pro-thumb-sm bookme-pro-margin-right-lg"
                    <?php echo $img ? 'style="background-image: url(' . $img[0] . '); background-size: cover;background-position:0"' : '' ?>>
                </div>
            </div>
            <div class="bookme-pro-flex-cell bookme-pro-vertical-middle">
                <?php echo esc_html($staff['full_name']) ?><br>
                <label class="label <?php echo $staff['visibility'] == 'public' ? 'label-success' : 'label-warning' ?>"><?php echo esc_html(ucfirst($staff['visibility'])) ?></label>
            </div>
        </div>
    </td>
    <td><?php echo esc_html($staff['email']) ?></td>
    <td><?php echo esc_html($staff['phone']) ?></td>
    <td>
        <button type="button" class="btn btn-sm btn-default" data-staff-id="<?php echo $staff['id'] ?>"
                data-toggle="slidePanel"
                data-url="<?php echo add_query_arg(array('id' => $staff['id']), $edit_panel_url); ?>"><i
                    class="glyphicon glyphicon-pencil"></i></button>
    </td>
    <td>
        <div class="checkbox bookme-pro-margin-remove">
            <label><input type="checkbox" class="staff-checker"
                          value="<?php echo $staff['id'] ?>"/></label>
        </div>
    </td>
</tr>