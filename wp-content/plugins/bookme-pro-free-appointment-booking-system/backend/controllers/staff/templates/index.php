<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>
<div id="bookme-pro-tbs" class="wrap">
    <div class="bookme-pro-tbs-body">
        <?php if (\BookmePro\Lib\Utils\Common::isCurrentUserAdmin()) :
            \BookmePro\Lib\Utils\Common::pageHeader(__('Staff Members', 'bookme_pro'));
        else :
            \BookmePro\Lib\Utils\Common::pageHeader(__('Profile', 'bookme_pro'));
        endif;

        if (empty($staff_members)) :
            ?>
            <div class="alert well alert-dismissible fade in">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <div class="h1"><?php _e('Welcome to Bookme Pro!', 'bookme_pro') ?></div>
                <h2><?php _e('Thank you for using our plugin.', 'bookme_pro') ?></h2>
                <h4><?php _e('Bookme Pro offers a simple solution for making appointments. With our plugin you will be able to easily manage your availability time and handle the flow of your clients.', 'bookme_pro') ?></h4>
                <h4><?php _e('To start using Bookme Pro, you need to follow these steps which are the minimum requirements to get it running!', 'bookme_pro') ?></h4>
                <h4><?php echo sprintf(__('And also check the Bookme Pro full version <a href="%s" target="_blank">here</a>, with more advanced features.', 'bookme_pro'),'https://codecanyon.net/item/bookme-pro-wordpress-appointment-booking-and-scheduling-software/23939246') ?></h4>
                <ol>
                    <li><?php _e('Add staff members.', 'bookme_pro') ?></li>
                    <li><?php _e('Add services and assign them to staff members.', 'bookme_pro') ?></li>
                    <li><?php _e('Go to Posts/Pages and use [bookme-pro-form] shortcode to publish the booking form on your website.', 'bookly') ?></li>
                </ol>
                <hr>
                <a class="btn btn-primary"
                   href="#bookme-pro-no-staff">
                    <?php _e('Add Staff Members', 'bookme_pro') ?>
                </a>
                <a class="btn btn-primary"
                   href="<?php echo \BookmePro\Lib\Utils\Common::escAdminUrl(BookmePro\Backend\Controllers\Services\Controller::page_slug) ?>">
                    <?php _e('Add Services', 'bookme_pro') ?>
                </a>
            </div>
        <?php endif; ?>
        <div class="panel panel-default bookme-pro-main">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="bookme-pro-table-title">
                            <?php _e('Total Members: ', 'bookme_pro') ?>
                            <span class="bookme-pro-color-gray"><span
                                        id="bookme-pro-staff-count"><?php echo count($staff_members) ?></span></span>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <?php if (\BookmePro\Lib\Utils\Common::isCurrentUserAdmin()) : ?>
                            <div class="form-inline bookme-pro-margin-bottom-lg text-right">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary bookme-pro-btn-block-xs"
                                            data-toggle="slidePanel"
                                            data-url="<?php echo $add_panel_url ?>" data-event="newEmployee"><i
                                                class="glyphicon glyphicon-plus"></i> <?php _e('Add Employee', 'bookme_pro') ?>
                                    </button>
                                </div>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped" width="100%">
                        <thead>
                        <tr>
                            <th><?php _e('Name & Visibility', 'bookme_pro') ?></th>
                            <th><?php _e('Email', 'bookme_pro') ?></th>
                            <th><?php _e('Phone', 'bookme_pro') ?></th>
                            <th width="16"></th>
                            <th width="16"></th>
                        </tr>
                        </thead>
                        <tbody id="bookme-pro-all-staff-details" <?php if (empty($staff_members)) { ?> style="display: none" <?php } ?>>
                        <?php
                        foreach ($staff_members as $staff) :
                            include '_list_item.php';
                        endforeach;
                        ?>
                        </tbody>
                        <tbody id="bookme-pro-no-staff" <?php if (!empty($staff_members)) { ?> style="display: none" <?php } ?>>
                        <tr>
                            <td colspan="5"><?php _e('No staff members found.', 'bookme_pro'); ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-right">
                    <?php \BookmePro\Lib\Utils\Common::deleteButton() ?>
                </div>
            </div>
        </div>
    </div>
</div>