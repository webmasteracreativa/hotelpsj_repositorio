<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
use BookmePro\Lib\Utils\Common;
use BookmePro\Lib\Utils\DateTime;
use BookmePro\Lib\Proxy;
use BookmePro\Lib\Entities\Service;

$time_interval = get_option('bookme_pro_gen_time_slot_length');
$service_id = $service['id'];
$assigned_staff_ids = $service['staff_ids'] ? explode(',', $service['staff_ids']) : array();
$all_staff_selected = count($assigned_staff_ids) == count($staff_collection);
?>

<div id="bookme-pro-tbs">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php echo (!empty($service['title'])) ? $service['title'] : esc_html__('Add new Service', 'bookme_pro'); ?></h2>
            </div>
            <div class="slidePanel-actions">
                <div class="btn-group-flat">
                    <button type="button"
                            class="btn btn-primary btn-sm ajax-service-send"
                            id="edit_service" data-spinner-size="40" data-style="zoom-in"><i class="glyphicon glyphicon-ok"></i></button>
                    <?php if (isset($service['id'])) { ?>
                        <button type="button" class="btn btn-danger btn-sm"
                                id="bookme-pro-delete" data-spinner-size="40" data-style="zoom-in"><i class="glyphicon glyphicon-trash"></i></button>
                    <?php } ?>
                    <button type="button" class="btn btn-default btn-sm slidePanel-close"><i
                                class="glyphicon glyphicon-remove"></i></button>
                </div>
            </div>
        </div>
    </header>
    <div class="slidePanel-inner">
        <div class="panel-body">
            <form method="post">
                <div class="row">
                    <div class="col-md-9 col-sm-6 bookme-pro-js-service bookme-pro-js-service-simple bookme-pro-js-service-compound bookme-pro-js-service-package">
                        <div class="form-group">
                            <label for="title_<?php echo $service_id ?>"><?php esc_html_e('Title', 'bookme_pro') ?></label>
                            <input name="title" value="<?php echo esc_attr($service['title']) ?>"
                                   id="title_<?php echo $service_id ?>" class="form-control" type="text">
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 bookme-pro-js-service bookme-pro-js-service-simple">
                        <div class="form-group">
                            <label><?php esc_html_e('Color', 'bookme_pro') ?></label>
                            <div class="bookme-pro-color-picker-wrapper">
                                <input name="color" value="<?php echo esc_attr($service['color']) ?>"
                                       class="bookme-pro-js-color-picker"
                                       data-last-color="<?php echo esc_attr($service['color']) ?>" type="text">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4 bookme-pro-js-service bookme-pro-js-service-simple bookme-pro-js-service-compound bookme-pro-js-service-package">
                        <div class="form-group">
                            <label for="price_<?php echo $service_id ?>"><?php esc_html_e('Price', 'bookme_pro') ?></label>
                            <input id="price_<?php echo $service_id ?>" class="form-control bookme-pro-question"
                                   type="number" min="0" step="1" name="price"
                                   value="<?php echo esc_attr($service['price']) ?>">
                        </div>
                    </div>
                    <div class="col-sm-4 bookme-pro-js-service bookme-pro-js-service-simple">
                        <div class="form-group">
                            <label for="capacity_<?php echo $service_id ?>"><?php esc_html_e('Capacity (min and max)', 'bookme_pro') ?></label>
                            <p class="help-block"><?php esc_html_e('How many customers you want in a single time period.', 'bookme_pro') ?></p>
                            <div class="row">
                                <div class="col-xs-6">
                                    <input id="capacity_min_<?php echo $service_id ?>"
                                           class="form-control bookme-pro-question bookme-pro-js-capacity" type="number" min="1"
                                           step="1" name="capacity_min"
                                           value="<?php echo esc_attr($service['capacity_min']) ?>">
                                </div>
                                <div class="col-xs-6">
                                    <input id="capacity_max_<?php echo $service_id ?>"
                                           class="form-control bookme-pro-question bookme-pro-js-capacity" type="number" min="1"
                                           step="1" name="capacity_max"
                                           value="<?php echo esc_attr($service['capacity_max']) ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bookme-pro-js-service bookme-pro-js-service-simple">
                    <div class="row">
                        <div class="col-sm-4 bookme-pro-js-service bookme-pro-js-service-simple">
                            <div class="form-group">
                                <label for="duration_<?php echo $service_id ?>">
                                    <?php esc_html_e('Duration', 'bookme_pro') ?>
                                </label>
                                <select id="duration_<?php echo $service_id ?>" class="form-control" name="duration">
                                    <?php for ($j = $time_interval; $j <= 720; $j += $time_interval) : ?><?php if ($service['duration'] / 60 > $j - $time_interval && $service['duration'] / 60 < $j) : ?>
                                        <option value="<?php echo esc_attr($service['duration']) ?>"
                                                selected><?php echo DateTime::secondsToInterval($service['duration']) ?></option><?php endif ?>
                                        <option
                                        value="<?php echo $j * 60 ?>" <?php selected($service['duration'], $j * 60) ?>><?php echo DateTime::secondsToInterval($j * 60) ?></option><?php endfor ?>
                                    <?php for ($j = 86400; $j <= 604800; $j += 86400) : ?>
                                        <option
                                        value="<?php echo $j ?>" <?php selected($service['duration'], $j) ?>><?php echo DateTime::secondsToInterval($j) ?></option><?php endfor ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4 bookme-pro-js-service bookme-pro-js-service-simple">
                            <div class="form-group">
                                <label for="start_time_info_<?php echo $service_id ?>"><?php esc_html_e('Start and end times of the appointment', 'bookme_pro') ?></label>
                                <p class="help-block"><?php esc_html_e('Allows to set the start and end times for an appointment for services with the duration of 1 day or longer. This time will be displayed in notifications to customers.', 'bookme_pro') ?></p>
                                <div class="row">
                                    <div class="col-xs-6">
                                        <input id="start_time_info_<?php echo $service_id ?>" class="form-control"
                                               type="text" name="start_time_info"
                                               value="<?php echo esc_attr($service['start_time_info']) ?>">
                                    </div>
                                    <div class="col-xs-6">
                                        <input class="form-control" type="text" name="end_time_info"
                                               value="<?php echo esc_attr($service['end_time_info']) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6 bookme-pro-js-service bookme-pro-js-service-simple bookme-pro-js-service-compound">
                        <div class="form-group">
                            <label for="category_<?php echo $service_id ?>"><?php esc_html_e('Category', 'bookme_pro') ?></label>
                            <select id="category_<?php echo $service_id ?>" class="form-control" name="category_id">
                                <option value="0"><?php esc_html_e('Uncategorized', 'bookme_pro') ?></option>
                                <?php foreach ($category_collection as $category) : ?>
                                    <option value="<?php echo $category['id'] ?>" <?php selected($category['id'], $service['category_id']) ?>><?php echo esc_html($category['name']) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 bookme-pro-js-service bookme-pro-js-service-simple bookme-pro-js-service-package">
                        <div class="form-group">
                            <label for="bookme-pro-js-staff-selector"><?php esc_html_e('Staff Members', 'bookme_pro') ?></label><br>
                            <select name="staff_ids[]" id="bookme-pro-js-staff-selector" class="form-control" multiple data-placeholder="<?php esc_attr_e('Select Staff Members', 'bookme_pro') ?>" data-nothing="<?php esc_attr_e('No staff selected', 'bookme_pro') ?>" data-selected="<?php esc_attr_e('selected', 'bookme_pro') ?>" data-selectall="<?php esc_attr_e('Select All', 'bookme_pro') ?>" data-unselectall="<?php esc_attr_e('Unselect All', 'bookme_pro') ?>" data-allselected="<?php esc_attr_e('All Staffs Selected', 'bookme_pro') ?>">
                                <?php foreach ($staff_collection as $i => $staff) : ?>
                                <option value="<?php echo $staff['id'] ?>" <?php selected(in_array($staff['id'], $assigned_staff_ids)) ?>><?php echo esc_html($staff['full_name']) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group bookme-pro-js-service bookme-pro-js-service-simple bookme-pro-js-service-compound bookme-pro-js-service-package">
                    <label for="info_<?php echo $service_id ?>">
                        <?php esc_html_e('Info', 'bookme_pro') ?>
                    </label>
                    <p class="help-block">
                        <?php printf(esc_html__('This text can be inserted into notifications with %s code.', 'bookme_pro'), '{service_info}') ?>
                    </p>
                    <textarea class="form-control" id="info_<?php echo $service_id ?>" name="info" rows="3"
                              type="text"><?php echo esc_textarea($service['info']) ?></textarea>
                </div>

                <?php Proxy\CompoundServices::renderSubServices($service, $service_collection, $service['sub_services']) ?>
                <?php Proxy\Shared::renderServiceForm($service) ?>
                <input type="hidden" name="action" value="bookme_pro_update_service">
                <input type="hidden" name="id" value="<?php echo esc_html($service_id) ?>">
                <input type="hidden" name="update_staff" value="0">
                <span class="bookme-pro-js-services-error text-danger"></span>
                <?php Common::csrf() ?>
            </form>
        </div>
    </div>
</div>