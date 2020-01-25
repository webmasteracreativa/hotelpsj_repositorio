<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
use BookmePro\Lib\Utils\DateTime;
use BookmePro\Lib\Utils\Price;
use BookmePro\Lib\Proxy;

$time_interval = get_option('bookme_pro_gen_time_slot_length');
?>
<?php if (!empty($service_collection)) : ?>
<table class="table table-striped" width="100%">
    <thead>
    <tr>
        <th><?php esc_html_e('Name', 'bookme_pro') ?></th>
        <th><?php esc_html_e('Duration', 'bookme_pro') ?></th>
        <th><?php esc_html_e('Price', 'bookme_pro') ?></th>
        <th width="16"></th>
        <th width="16"></th>
    </tr>
    </thead>
    <tbody id="services_list">
        <?php foreach ($service_collection as $service) : ?>
            <?php $service_id = $service['id']; ?>
            <tr class="bookme-pro-js-collapse" role="row" data-service-id="<?php echo $service_id ?>">
                <td>
                    <div class="bookme-pro-flexbox">
                        <div class="bookme-pro-flex-cell bookme-pro-vertical-middle" style="width: 1%">
                            <i class="bookme-pro-js-handle bookme-pro-icon bookme-pro-icon-draghandle bookme-pro-margin-right-sm bookme-pro-cursor-move"
                               title="<?php esc_attr_e('Reorder', 'bookme_pro') ?>"></i>
                        </div>
                        <div class="bookme-pro-flex-cell bookme-pro-vertical-middle bookme-pro-js-service-color"
                             style="width: 55px; padding-left: 25px;">
                                    <span class="bookme-pro-service-color bookme-pro-margin-right-sm bookme-pro-js-service bookme-pro-js-service-simple bookme-pro-js-service-compound bookme-pro-js-service-package"
                                          style="background-color: <?php echo esc_attr($service['colors'][0] == '-1' ? 'grey' : $service['colors'][0]) ?>">&nbsp;</span>
                            <span class="bookme-pro-service-color bookme-pro-margin-right-sm bookme-pro-js-service bookme-pro-js-service-compound bookme-pro-js-service-package"
                                  style="background-color: <?php echo esc_attr($service['colors'][1] == '-1' ? 'grey' : $service['colors'][1]) ?>; <?php if ($service['type'] == \BookmePro\Lib\Entities\Service::TYPE_SIMPLE) : ?>display: none;<?php endif ?>">&nbsp;</span>
                            <span class="bookme-pro-service-color bookme-pro-margin-right-sm bookme-pro-js-service bookme-pro-js-service-package"
                                  style="background-color: <?php echo esc_attr($service['colors'][2] == '-1' ? 'grey' : $service['colors'][2]) ?>; <?php if ($service['type'] != \BookmePro\Lib\Entities\Service::TYPE_PACKAGE) : ?>display: none;<?php endif ?>">&nbsp;</span>
                        </div>
                        <div class="bookme-pro-flex-cell bookme-pro-vertical-middle">
                            <button type="button" class="bookme-pro-js-service-title btn-link" data-toggle="slidePanel"
                                data-url="<?php echo add_query_arg(array('service_id' => $service_id), $panel_url); ?>">
                                <?php echo esc_html($service['title']) ?>
                            </button>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="bookme-pro-js-service-duration">
                                    <?php echo($service['type'] == \BookmePro\Lib\Entities\Service::TYPE_SIMPLE || $service['type'] == \BookmePro\Lib\Entities\Service::TYPE_PACKAGE ? DateTime::secondsToInterval($service['duration']) : sprintf(_n('%d service', '%d services', count($service['sub_services']), 'bookme_pro'), count($service['sub_services']))) ?>
                                </span>
                </td>
                <td>
                    <span class="bookme-pro-js-service-price">
                                    <?php echo Price::format($service['price']) ?>
                                </span>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-default" data-toggle="slidePanel"
                            data-url="<?php echo add_query_arg(array('service_id' => $service_id), $panel_url); ?>"><i class="glyphicon glyphicon-pencil"></i></button>
                </td>
                <td>
                    <div class="checkbox bookme-pro-margin-remove">
                        <label><input type="checkbox" class="service-checker"
                                      value="<?php esc_attr_e($service_id) ?>"/></label>
                    </div>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
<?php endif ?>
<div style="display: none">
    <?php Proxy\Shared::renderAfterServiceList($service_collection) ?>
</div>