<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>
<div id="bookme-pro-tbs" class="wrap">
    <div class="bookme-pro-tbs-body">
        <?php \BookmePro\Lib\Utils\Common::pageHeader(esc_html__('Services', 'bookme_pro')); ?>
        <div class="panel panel-default bookme-pro-main">
            <div class="panel-body">
                <div class="row">
                    <div id="bookme-pro-service-sidebar" class="col-sm-3">
                        <div id="bookme-pro-categories-list" class="bookme-pro-nav">
                            <div class="bookme-pro-nav-item active bookme-pro-category-item bookme-pro-js-all-services">
                                <?php esc_html_e('All Services', 'bookme_pro') ?>
                            </div>
                            <ul id="bookme-pro-category-item-list">
                                <?php foreach ($category_collection as $category) $this->render('_category_item', compact('category')) ?>
                            </ul>
                        </div>

                        <div class="form-group">
                            <button id="bookme-pro-new-category" type="button"
                                    class="btn btn-lg btn-block btn-primary">
                                <i class="dashicons dashicons-plus-alt"></i>
                                <?php esc_html_e('New Category', 'bookme_pro') ?>
                            </button>
                        </div>

                        <div id="bookme-pro-add-new-category" class="modal fade" tabindex=-1 role="dialog">
                            <form method="post" id="new-category-form">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                        aria-hidden="true">&times;</span></button>
                                            <div class="modal-title h2"><?php esc_html_e('Add new category', 'bookme_pro') ?></div>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group bookme-pro-margin-bottom-md">
                                                <div class="form-field form-required">
                                                    <label for="bookme-pro-category-name"><?php esc_html_e('Name', 'bookme_pro') ?></label>
                                                    <input class="form-control" id="bookme-pro-category-name" type="text"
                                                           name="name"/>
                                                    <input type="hidden" name="action" value="bookme_pro_add_category"/>
                                                    <?php \BookmePro\Lib\Utils\Common::csrf() ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="text-right">
                                                <button type="submit" class="btn btn-primary">
                                                    <?php esc_html_e('Save', 'bookme_pro') ?>
                                                </button>
                                                <button type="button" class="btn btn-default">
                                                    <?php esc_html_e('Cancel', 'bookme_pro') ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div id="bookme-pro-services-wrapper" class="col-sm-9">

                        <h4 class="bookme-pro-block-head">
                            <span class="bookme-pro-category-title"><?php esc_html_e('All Services', 'bookme_pro') ?></span>
                            <button type="button" class="pull-right btn btn-primary" data-toggle="slidePanel"
                                    data-url="<?php echo esc_url($panel_url); ?>">
                                <span class="ladda-label"><i
                                            class="glyphicon glyphicon-plus"></i> <?php esc_html_e('Add Service', 'bookme_pro') ?></span>
                            </button>
                        </h4>

                        <div class="bookme-pro-margin-top-xlg no-result"
                             <?php if (!empty ($service_collection)) : ?>style="display: none;"<?php endif ?>>
                            <table class="table table-striped" width="100%">
                                <thead>
                                <tr>
                                    <th><?php esc_html_e('Name', 'bookme_pro') ?></th>
                                    <th><?php esc_html_e('Duration', 'bookme_pro') ?></th>
                                    <th><?php esc_html_e('Price', 'bookme_pro') ?></th>
                                    <th width="80"><?php esc_html_e('Action', 'bookme_pro') ?></th>
                                    <th width="16"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="5"><?php esc_html_e('No services found. Please add services.', 'bookme_pro') ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="bookme-pro-margin-top-xlg table-responsive" id="bookme-pro-js-services-list">
                            <?php include '_list.php' ?>
                        </div>
                        <div class="text-right">
                            <?php \BookmePro\Lib\Utils\Common::deleteButton() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="bookme-pro-update-service-settings" class="modal fade" tabindex=-1 role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <div class="modal-title h2"><?php esc_html_e('Update service setting', 'bookme_pro') ?></div>
                </div>
                <div class="modal-body">
                    <p><?php esc_html_e('You are about to change a service setting which is also configured separately for each staff member. Do you want to update it in staff settings too?', 'bookme_pro') ?></p>
                    <div class="checkbox">
                        <label>
                            <input id="bookme-pro-remember-my-choice" type="checkbox">
                            <?php esc_html_e('Remember my choice', 'bookme_pro') ?>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-default bookme-pro-no" data-dismiss="modal" aria-hidden="true">
                        <?php esc_html_e('No, update just here in services', 'bookme_pro') ?>
                    </button>
                    <button type="submit" class="btn btn-primary bookme-pro-yes"><?php esc_html_e('Yes', 'bookme_pro') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>