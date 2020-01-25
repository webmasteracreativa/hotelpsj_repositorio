<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
/** ServiceExtras $extras */
use BookmePro\Lib\Entities\CustomerAppointment;
use BookmePro\Lib\Utils\Price;
use BookmePro\Lib\Utils\Common;
use BookmePro\Lib\Config;

?>
<div id="bookme-pro-customer-details-dialog" class="slidePanel <?php echo is_rtl() ? 'slidePanel-left' : 'slidePanel-right'; ?>">
    <div class="slidePanel-scrollable">
        <div>
            <div class="slidePanel-content">
                <header class="slidePanel-header">
                    <div class="slidePanel-overlay-panel">
                        <div class="slidePanel-heading">
                            <h2><?php esc_html_e('Edit booking details', 'bookme_pro') ?></h2>
                        </div>
                        <div class="slidePanel-actions">
                            <div class="btn-group-flat">
                                <button type="button"
                                        class="btn btn-primary btn-sm ajax-service-send"
                                        ng-click=saveCustomFields()><i class="glyphicon glyphicon-ok"></i></button>
                                <button type="button" class="btn btn-default btn-sm slidePanel-close"><i
                                            class="glyphicon glyphicon-remove"></i></button>
                            </div>
                        </div>
                    </div>
                </header>
                <div class="slidePanel-inner">
                    <div class="panel-body">
                        <form ng-hide=loading style="z-index: 1050">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="bookme-pro-appointment-status"><?php esc_html_e('Status', 'bookme_pro') ?></label>
                                    <select class="bookme-pro-custom-field form-control"
                                            id="bookme-pro-appointment-status">
                                        <option value="<?php echo CustomerAppointment::STATUS_PENDING ?>"><?php echo esc_html(CustomerAppointment::statusToString(CustomerAppointment::STATUS_PENDING)) ?></option>
                                        <option value="<?php echo CustomerAppointment::STATUS_APPROVED ?>"><?php echo esc_html(CustomerAppointment::statusToString(CustomerAppointment::STATUS_APPROVED)) ?></option>
                                        <option value="<?php echo CustomerAppointment::STATUS_CANCELLED ?>"><?php echo esc_html(CustomerAppointment::statusToString(CustomerAppointment::STATUS_CANCELLED)) ?></option>
                                        <option value="<?php echo CustomerAppointment::STATUS_REJECTED ?>"><?php echo esc_html(CustomerAppointment::statusToString(CustomerAppointment::STATUS_REJECTED)) ?></option>
                                        <?php if (Config::waitingListActive()) : ?>
                                            <option value="<?php echo CustomerAppointment::STATUS_WAITLISTED ?>"><?php echo esc_html(CustomerAppointment::statusToString(CustomerAppointment::STATUS_WAITLISTED)) ?></option>
                                        <?php endif ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="bookme-pro-edit-number-of-persons"><?php esc_html_e('Number of persons', 'bookme_pro') ?></label>
                                    <select class="bookme-pro-custom-field form-control"
                                            id="bookme-pro-edit-number-of-persons"></select>
                                </div>
                                <h3 class="bookme-pro-block-head bookme-pro-color-gray">
                                    <?php esc_html_e('Custom Fields', 'bookme_pro') ?>
                                </h3>
                                <div id="bookme-pro-js-custom-fields">
                                    <?php foreach ($custom_fields as $custom_field) : ?>
                                        <div class="form-group"
                                             data-type="<?php echo esc_attr($custom_field->type) ?>"
                                             data-id="<?php echo esc_attr($custom_field->id) ?>"
                                             data-services="<?php echo esc_attr(json_encode($custom_field->services)) ?>">
                                            <label for="custom_field_<?php echo esc_attr($custom_field->id) ?>"><?php echo $custom_field->label ?></label>
                                            <div>
                                                <?php if ($custom_field->type == 'text-field') : ?>
                                                    <input id="custom_field_<?php echo esc_attr($custom_field->id) ?>"
                                                           type="text"
                                                           class="bookme-pro-custom-field form-control"/>

                                                <?php elseif ($custom_field->type == 'textarea') : ?>
                                                    <textarea
                                                            id="custom_field_<?php echo esc_attr($custom_field->id) ?>"
                                                            rows="3"
                                                            class="bookme-pro-custom-field form-control"></textarea>

                                                <?php elseif ($custom_field->type == 'checkboxes') : ?>
                                                    <?php foreach ($custom_field->items as $item) : ?>
                                                        <div class="checkbox">
                                                            <label>
                                                                <input class="bookme-pro-custom-field"
                                                                       type="checkbox"
                                                                       value="<?php echo esc_attr($item) ?>"/>
                                                                <?php echo $item ?>
                                                            </label>
                                                        </div>
                                                    <?php endforeach ?>

                                                <?php elseif ($custom_field->type == 'radio-buttons') : ?>
                                                    <?php foreach ($custom_field->items as $item) : ?>
                                                        <div class="radio">
                                                            <label>
                                                                <input type="radio"
                                                                       name="<?php echo $custom_field->id ?>"
                                                                       class="bookme-pro-custom-field"
                                                                       value="<?php echo esc_attr($item) ?>"/>
                                                                <?php echo $item ?>
                                                            </label>
                                                        </div>
                                                    <?php endforeach ?>

                                                <?php elseif ($custom_field->type == 'drop-down') : ?>
                                                    <select id="custom_field_<?php echo esc_attr($custom_field->id) ?>"
                                                            class="bookme-pro-custom-field form-control">
                                                        <option value=""></option>
                                                        <?php foreach ($custom_field->items as $item) : ?>
                                                            <option value="<?php echo esc_attr($item) ?>"><?php echo $item ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                <?php endif ?>
                                            </div>
                                        </div>
                                    <?php endforeach ?>
                                </div>

                                <?php if ($extras = (array)BookmePro\Lib\Proxy\ServiceExtras::findAll()) : ?>
                                    <h3 class="bookme-pro-block-head bookme-pro-color-gray">
                                        <?php esc_html_e('Extras', 'bookme_pro') ?>
                                    </h3>
                                    <div id="bookme-pro-extras" class="bookme-pro-flexbox">
                                        <?php foreach ($extras as $extra) : ?>
                                            <div class="bookme-pro-flex-row service_<?php echo $extra->getServiceId() ?> bookme-pro-margin-bottom-sm">
                                                <div class="bookme-pro-flex-cell bookme-pro-padding-bottom-sm"
                                                     style="width:5em">
                                                    <input class="extras-count form-control"
                                                           data-id="<?php echo $extra->getId() ?>"
                                                           type="number" min="0"
                                                           name="extra[<?php echo $extra->getId() ?>]"
                                                           value="0"/>
                                                </div>
                                                <div class="bookme-pro-flex-cell bookme-pro-padding-bottom-sm bookme-pro-vertical-middle">
                                                    &nbsp;&times; <b><?php echo $extra->getTitle() ?></b>
                                                    (<?php echo Price::format($extra->getPrice()) ?>)
                                                </div>
                                            </div>
                                        <?php endforeach ?>
                                    </div>
                                <?php endif ?>
                            </div>
                        </form>
                    </div><!-- /.modal-content -->
                </div>
            </div>
        </div>
    </div>
</div>