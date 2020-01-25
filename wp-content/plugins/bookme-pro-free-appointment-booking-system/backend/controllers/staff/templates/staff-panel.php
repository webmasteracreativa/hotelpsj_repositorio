<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>
<div id="bookme-pro-tbs">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php echo $staff->getFullName() ?></h2>
            </div>
            <div class="slidePanel-actions">
                <div class="btn-group-flat">
                    <?php if (\BookmePro\Lib\Utils\Common::isCurrentUserAdmin()) : ?>
                        <button type="button" class="btn btn-danger btn-sm"
                                id="bookme-pro-staff-delete"><i class="glyphicon glyphicon-trash"></i></button>
                    <?php endif ?>
                    <button type="button" class="btn btn-default btn-sm slidePanel-close"><i
                                class="glyphicon glyphicon-remove"></i></button>
                </div>
            </div>
        </div>
    </header>
    <div class="slidePanel-inner">
        <div class="panel">
            <div class="panel-body">
                <div class="bookme-pro-margin-bottom-md text-center">
                    <div class="bookme-pro-vertical-middle">
                        <div id="bookme-pro-js-staff-avatar"
                             class="bookme-pro-thumb bookme-pro-thumb-lg bookme-pro-div-center">
                            <div class="bookme-pro-flex-cell" style="width: 100%">
                                <div class="form-group">
                                    <?php $img = wp_get_attachment_image_src($staff->getAttachmentId(), 'thumbnail') ?>

                                    <div class="bookme-pro-js-image bookme-pro-thumb bookme-pro-thumb-lg bookme-pro-margin-right-lg"
                                        <?php echo $img ? 'style="background-image: url(' . $img[0] . '); background-size: cover;"' : '' ?>
                                    >
                                        <a class="dashicons dashicons-no-alt bookme-pro-thumb-delete"
                                           href="javascript:void(0)"
                                           title="<?php esc_attr_e('Delete', 'bookme_pro') ?>"
                                           <?php if (!$img) : ?>style="display: none;"<?php endif ?>>
                                        </a>
                                        <div class="bookme-pro-thumb-edit">
                                            <div class="bookme-pro-pretty">
                                                <label class="bookme-pro-pretty-indicator bookme-pro-thumb-edit-btn">
                                                    <?php _e('Image', 'bookme_pro') ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class=" bookme-pro-vertical-top"><h1
                                class="bookme-pro-js-staff-name-<?php echo $staff->getId() ?>"><?php echo $staff->getFullName() ?></h1>
                    </div>
                </div>

                <ul class="nav nav-tabs nav-justified bookme-pro-nav-justified">
                    <li class="active">
                        <a id="bookme-pro-details-tab" href="#details" data-toggle="tab">
                            <span class="bookme-pro-nav-tabs-title"><?php _e('Details', 'bookme_pro') ?></span>
                        </a>
                    </li>
                    <li>
                        <a id="bookme-pro-services-tab" href="#services" data-toggle="tab">
                            <span class="bookme-pro-nav-tabs-title"><?php _e('Services', 'bookme_pro') ?></span>
                        </a>
                    </li>
                    <li>
                        <a id="bookme-pro-schedule-tab" href="#schedule" data-toggle="tab">
                            <span class="bookme-pro-nav-tabs-title"><?php _e('Schedule', 'bookme_pro') ?></span>
                        </a>
                    </li>
                    <?php \BookmePro\Lib\Proxy\Shared::renderStaffTab($staff) ?>
                    <li>
                        <a id="bookme-pro-holidays-tab" href="#daysoff" data-toggle="tab">
                            <span class="bookme-pro-nav-tabs-title"><?php _e('Days off', 'bookme_pro') ?></span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div style="display: none;" class="bookme-pro-loading"></div>

                    <div class="tab-pane active" id="details">
                        <div id="bookme-pro-details-container"></div>
                    </div>
                    <div class="tab-pane" id="services">
                        <div id="bookme-pro-services-container" style="display: none"></div>
                    </div>
                    <div class="tab-pane" id="schedule">
                        <div id="bookme-pro-schedule-container" style="display: none"></div>
                    </div>
                    <div class="tab-pane" id="daysoff">
                        <div id="bookme-pro-holidays-container" style="display: none"></div>
                    </div>
                </div>
                <input type="hidden" name="staff_id" value="<?php echo $staff->getId() ?>">
            </div>
        </div>
    </div>
</div>