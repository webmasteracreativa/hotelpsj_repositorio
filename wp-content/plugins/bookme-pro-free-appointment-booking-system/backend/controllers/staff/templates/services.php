<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/** @var \BookmePro\Lib\Entities\Category[] $categories */
?>
<div>
    <?php if ( $categories || $uncategorized_services ) : ?>
        <form>
            <?php if ( ! empty ( $uncategorized_services ) ) : ?>
                <div class="panel panel-default bookme-pro-panel-unborder">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="checkbox bookme-pro-margin-remove">
                                    <label>
                                        <input id="bookme-pro-check-all-entities" type="checkbox">
                                        <b><?php _e( 'All services', 'bookme_pro' ) ?></b>
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="<?php echo \BookmePro\Lib\Proxy\Shared::prepareStaffServiceLabelClass( 'col-lg-4' ) ?> hidden-xs hidden-sm hidden-md text-right">
                                        <div class="bookme-pro-font-smaller bookme-pro-color-gray">
                                            <?php _e( 'Price', 'bookme_pro' ) ?>
                                        </div>
                                    </div>

                                    <?php \BookmePro\Lib\Proxy\DepositPayments::renderStaffServiceLabel() ?>

                                    <div class="<?php echo \BookmePro\Lib\Proxy\Shared::prepareStaffServiceLabelClass( 'col-lg-8' ) ?> hidden-xs hidden-sm hidden-md">
                                        <div class="bookme-pro-font-smaller bookme-pro-color-gray bookme-pro-truncate">
                                            <?php _e( 'Capacity (min and max)', 'bookme_pro' ) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <ul class="bookme-pro-category-services list-group bookme-pro-padding-top-md">
                        <?php foreach ( $uncategorized_services as $service ) : ?>
                            <li class="list-group-item" data-service-id="<?php echo $service['id'] ?>" data-service-type="<?php echo $service['type'] ?>" data-sub-service="<?php echo $service['sub_service_id'] ?>">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="checkbox">
                                            <label>
                                                <input class="bookme-pro-service-checkbox" <?php checked( array_key_exists( $service['id'], $services_data ) ) ?>
                                                       type="checkbox" value="<?php echo $service['id'] ?>"
                                                       name="service[<?php echo $service['id'] ?>]"
                                                >
                                                <span class="bookme-pro-toggle-label"><?php echo esc_html( $service['title'] ) ?></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <div class="<?php echo \BookmePro\Lib\Proxy\Shared::prepareStaffServiceInputClass( 'col-xs-4' ) ?>">
                                                <div class="bookme-pro-font-smaller bookme-pro-margin-bottom-xs bookme-pro-color-gray visible-xs visible-sm visible-md">
                                                    <?php _e( 'Price', 'bookme_pro' ) ?>
                                                </div>
                                                <input class="form-control text-right" type="text" <?php disabled( !array_key_exists( $service['id'], $services_data ) ) ?>
                                                       name="price[<?php echo $service['id'] ?>]"
                                                       value="<?php echo array_key_exists( $service['id'], $services_data ) ? $services_data[ $service['id'] ]['price'] : $service['price'] ?>"
                                                >
                                            </div>

                                            <?php \BookmePro\Lib\Proxy\Shared::renderStaffService( $staff_id, $service['id'], $services_data, $service['type'] == \BookmePro\Lib\Entities\Service::TYPE_PACKAGE ? array( 'read-only' => array( 'deposit' => true ) ) : array() ) ?>

                                            <div class="<?php echo \BookmePro\Lib\Proxy\Shared::prepareStaffServiceInputClass( 'col-xs-8' ) ?>">
                                                <div class="form-group bookme-pro-js-capacity-form-group">
                                                    <div class="bookme-pro-font-smaller bookme-pro-margin-bottom-xs bookme-pro-color-gray visible-xs visible-sm visible-md">
                                                        <?php _e( 'Capacity (min and max)', 'bookme_pro' ) ?>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xs-6">
                                                            <input class="form-control bookme-pro-js-capacity bookme-pro-js-capacity-min" type="number" min=1 <?php disabled( ! array_key_exists( $service['id'], $services_data ) ) ?>
                                                                   name="capacity_min[<?php echo $service['id'] ?>]"
                                                                   value="<?php echo array_key_exists( $service['id'], $services_data ) ? $services_data[ $service['id'] ]['capacity_min'] : $service['capacity_min'] ?>"
                                                                   <?php if ( $service['type'] == \BookmePro\Lib\Entities\Service::TYPE_PACKAGE ) : ?>readonly<?php endif ?>
                                                            >
                                                        </div>
                                                        <div class="col-xs-6">
                                                            <input class="form-control bookme-pro-js-capacity bookme-pro-js-capacity-max" type="number" min=1 <?php disabled( ! array_key_exists( $service['id'], $services_data ) ) ?>
                                                                   name="capacity_max[<?php echo $service['id'] ?>]"
                                                                   value="<?php echo array_key_exists( $service['id'], $services_data ) ? $services_data[ $service['id'] ]['capacity_max'] : $service['capacity_max'] ?>"
                                                                   <?php if ( $service['type'] == \BookmePro\Lib\Entities\Service::TYPE_PACKAGE ) : ?>readonly<?php endif ?>
                                                            >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if ( $service['type'] == \BookmePro\Lib\Entities\Service::TYPE_SIMPLE ) { \BookmePro\Lib\Proxy\Shared::renderStaffServiceTail( $staff_id, $service[ 'id' ] ); } ?>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif ?>

            <?php if ( ! empty ( $categories ) ) : ?>
                <?php foreach ( $categories as $category ) : ?>
                    <div class="panel panel-default bookme-pro-panel-unborder">
                        <div class="panel-heading bookme-pro-services-category">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="checkbox bookme-pro-margin-remove">
                                        <label>
                                            <input type="checkbox" class="bookme-pro-category-checkbox bookme-pro-category-<?php echo $category->getId() ?>"
                                                   data-category-id="<?php echo $category->getId() ?>">
                                            <b><?php echo esc_html( $category->getName() ) ?></b>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="<?php echo \BookmePro\Lib\Proxy\Shared::prepareStaffServiceLabelClass( 'col-lg-4' )?> hidden-xs hidden-sm hidden-md text-right">
                                            <div class="bookme-pro-font-smaller bookme-pro-color-gray"><?php _e( 'Price', 'bookme_pro' ) ?></div>
                                        </div>

                                        <?php \BookmePro\Lib\Proxy\DepositPayments::renderStaffServiceLabel() ?>

                                        <div class="<?php echo \BookmePro\Lib\Proxy\Shared::prepareStaffServiceLabelClass( 'col-lg-8' ) ?> hidden-xs hidden-sm hidden-md">
                                            <div class="bookme-pro-font-smaller bookme-pro-color-gray bookme-pro-truncate"><?php _e( 'Capacity (min and max)', 'bookme_pro' ) ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <ul class="bookme-pro-category-services list-group bookme-pro-padding-top-md">
                            <?php foreach ( $category->getServices() as $service ) : ?>
                                <?php $sub_service = current( $service->getSubServices() ) ?>
                                <li class="list-group-item" data-service-id="<?php echo $service->getId() ?>" data-service-type="<?php echo $service->getType() ?>" data-sub-service="<?php echo empty( $sub_service ) ? null : $sub_service->getId(); ?>">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="checkbox">
                                                <label>
                                                    <input class="bookme-pro-service-checkbox bookme-pro-category-<?php echo $category->getId() ?>"
                                                           data-category-id="<?php echo $category->getId() ?>" <?php checked( array_key_exists( $service->getId(), $services_data ) ) ?>
                                                           type="checkbox" value="<?php echo $service->getId() ?>"
                                                           name="service[<?php echo $service->getId() ?>]"
                                                    >
                                                    <span class="bookme-pro-toggle-label"><?php echo esc_html( $service->getTitle() ) ?></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="row">
                                                <div class="<?php echo \BookmePro\Lib\Proxy\Shared::prepareStaffServiceInputClass( 'col-xs-4' ) ?>">
                                                    <div class="bookme-pro-font-smaller bookme-pro-margin-bottom-xs bookme-pro-color-gray visible-xs visible-sm visible-md">
                                                        <?php _e( 'Price', 'bookme_pro' ) ?>
                                                    </div>
                                                    <input class="form-control text-right" type="text" <?php disabled( ! array_key_exists( $service->getId(), $services_data ) ) ?>
                                                           name="price[<?php echo $service->getId() ?>]"
                                                           value="<?php echo array_key_exists( $service->getId(), $services_data ) ? $services_data[ $service->getId() ]['price'] : $service->getPrice() ?>"
                                                    >
                                                </div>

                                                <?php \BookmePro\Lib\Proxy\Shared::renderStaffService( $staff_id, $service->getId(), $services_data, $service->getType() == \BookmePro\Lib\Entities\Service::TYPE_PACKAGE ? array( 'read-only' => array( 'deposit' => true ) ) : array() ) ?>

                                                <div class="<?php echo \BookmePro\Lib\Proxy\Shared::prepareStaffServiceInputClass( 'col-xs-8' ) ?>">
                                                    <div class="form-group bookme-pro-js-capacity-form-group">
                                                        <div class="bookme-pro-font-smaller bookme-pro-margin-bottom-xs bookme-pro-color-gray visible-xs visible-sm visible-md">
                                                            <?php _e( 'Capacity (min and max)', 'bookme_pro' ) ?>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-xs-6">
                                                                <input class="form-control bookme-pro-js-capacity bookme-pro-js-capacity-min" type="number" min="1" <?php disabled( ! array_key_exists( $service->getId(), $services_data ) ) ?>
                                                                       name="capacity_min[<?php echo $service->getId() ?>]"
                                                                       value="<?php echo array_key_exists( $service->getId(), $services_data ) ? $services_data[ $service->getId() ]['capacity_min'] : $service->getCapacityMin() ?>"
                                                                       <?php if ( $service->getType() == \BookmePro\Lib\Entities\Service::TYPE_PACKAGE ) : ?>readonly<?php endif ?>
                                                                >
                                                            </div>
                                                            <div class="col-xs-6">
                                                                <input class="form-control bookme-pro-js-capacity bookme-pro-js-capacity-max" type="number" min="1" <?php disabled( ! array_key_exists( $service->getId(), $services_data ) ) ?>
                                                                       name="capacity_max[<?php echo $service->getId() ?>]"
                                                                       value="<?php echo array_key_exists( $service->getId(), $services_data ) ? $services_data[ $service->getId() ]['capacity_max'] : $service->getCapacityMax() ?>"
                                                                       <?php if ( $service->getType() == \BookmePro\Lib\Entities\Service::TYPE_PACKAGE ) : ?>readonly<?php endif ?>
                                                                >
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if ( $service->getType() == \BookmePro\Lib\Entities\Service::TYPE_SIMPLE ) { \BookmePro\Lib\Proxy\Shared::renderStaffServiceTail( $staff_id, $service->getId() ); } ?>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php endforeach ?>
            <?php endif ?>

            <input type="hidden" name="action" value="bookme_pro_staff_services_update">
            <input type="hidden" name="staff_id" value="<?php echo $staff_id ?>">
            <?php \BookmePro\Lib\Utils\Common::csrf() ?>

            <div class="panel-footer">
                <span class="bookme-pro-js-services-error text-danger"></span>
                <?php \BookmePro\Lib\Utils\Common::customButton( '', 'btn-default btn-lg slidePanel-close', __( 'Cancel', 'bookme_pro' ) ) ?>
                <?php \BookmePro\Lib\Utils\Common::submitButton( 'bookme-pro-services-save' ) ?>
            </div>
        </form>
    <?php else : ?>
        <h5 class="text-center"><?php _e( 'No services found. Please add services.', 'bookme_pro' ) ?></h5>
        <p class="bookme-pro-margin-top-xlg text-center">
            <a class="btn btn-lg btn-primary-outline"
               href="<?php echo \BookmePro\Lib\Utils\Common::escAdminUrl( \BookmePro\Backend\Controllers\Services\Controller::page_slug ) ?>" >
                <?php _e( 'Add Service', 'bookme_pro' ) ?>
            </a>
        </p>
    <?php endif ?>
    <div style="display: none">
        <?php BookmePro\Lib\Proxy\Shared::renderStaffServices( $staff_id ) ?>
    </div>
</div>