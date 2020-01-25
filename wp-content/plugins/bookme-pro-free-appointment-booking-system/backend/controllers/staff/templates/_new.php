<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div id="bookme-pro-tbs">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php _e('Add Employee', 'bookme_pro') ?></h2>
            </div>
            <div class="slidePanel-actions">
                <div class="btn-group-flat">
                    <?php \BookmePro\Lib\Utils\Common::customButton( 'bookme-pro-details-save', 'btn-sm btn-primary', '<i class="glyphicon glyphicon-ok"></i>' ) ?>
                    <button type="button" class="btn btn-default btn-sm slidePanel-close"><i
                                class="glyphicon glyphicon-remove"></i></button>
                </div>
            </div>
        </div>
    </header>
    <div class="slidePanel-inner">
        <div class="panel">
            <div class="panel-body">
                <form class="bookme-pro-new-staff">
                    <div class="form-group form-required">
                        <label for="bookme-pro-full-name"><?php _e( 'Full name', 'bookme_pro' ) ?></label>
                        <input type="text" class="form-control" id="bookme-pro-full-name" name="full_name" value=""/>
                    </div>
                        <div class="form-group">
                            <label for="bookme-pro-wp-user"><?php _e( 'User', 'bookme_pro' ) ?></label>

                            <p class="help-block">
                                <?php _e( 'If this staff member requires separate login to access personal calendar, a regular WP user needs to be created for this purpose.', 'bookme_pro' ) ?>
                                <?php _e( 'User with "Administrator" role will have access to calendars and settings of all staff members, user with another role will have access only to personal calendar and settings.', 'bookme_pro' ) ?>
                                <?php _e( 'If you leave this field blank, this staff member will not be able to access personal calendar using WP backend.', 'bookme_pro' ) ?>
                            </p>

                            <select class="form-control" name="wp_user_id" id="bookme-pro-wp-user">
                                <option value=""><?php _e( 'Select from WP users', 'bookme_pro' ) ?></option>
                                <?php foreach ( $users_for_staff as $user ) : ?>
                                    <option value="<?php echo $user->ID ?>" data-email="<?php echo $user->user_email ?>"><?php echo $user->display_name ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="bookme-pro-email"><?php _e( 'Email', 'bookme_pro' ) ?></label>
                                <input class="form-control" id="bookme-pro-email" name="email"
                                       value=""
                                       type="text"/>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="bookme-pro-phone"><?php _e( 'Phone', 'bookme_pro' ) ?></label>
                                <input class="form-control" id="bookme-pro-phone"
                                       value=""
                                       type="text"/>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="bookme-pro-info"><?php _e( 'Info', 'bookme_pro' ) ?></label>
                        <p class="help-block">
                            <?php printf( __( 'This text can be inserted into notifications with %s code.', 'bookme_pro' ), '{staff_info}' ) ?>
                        </p>
                        <textarea id="bookme-pro-info" name="info" rows="3" class="form-control"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="bookme-pro-visibility"><?php _e( 'Visibility', 'bookme_pro' ) ?></label>
                        <p class="help-block">
                            <?php _e( 'To make staff member invisible to your customers set the visibility to "Private".', 'bookme_pro' ) ?>
                        </p>
                        <select name="visibility" class="form-control" id="bookme-pro-visibility">
                            <option value="public"><?php _e( 'Public', 'bookme_pro' ) ?></option>
                            <option value="private"><?php _e( 'Private', 'bookme_pro' ) ?></option>
                        </select>
                    </div>

                    <input type="hidden" name="attachment_id" value="">
                    <?php \BookmePro\Lib\Utils\Common::csrf() ?>
                </form>
            </div>
        </div>
    </div>
</div>