<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
use BookmePro\Lib\Utils\Common;
use BookmePro\Lib\Config;

?>
<script type="text/ng-template" id="bookme-pro-customer-dialog.tpl">
    <div id="bookme-pro-customer-dialog" class="slidePanel <?php echo is_rtl() ? 'slidePanel-left' : 'slidePanel-right'; ?>">
        <div class="slidePanel-scrollable">
            <div>
                <div class="slidePanel-content">
                    <header class="slidePanel-header">
                        <div class="slidePanel-overlay-panel">
                            <div class="slidePanel-heading">
                                <h2><?php esc_html_e('Add Customer', 'bookme_pro'); ?></h2>
                            </div>
                            <div class="slidePanel-actions">
                                <div class="btn-group-flat">
                                    <button type="button"
                                            class="btn btn-primary btn-sm ajax-service-send"
                                            ng-click=processForm()><i class="glyphicon glyphicon-ok"></i></button>
                                    <button type="button" class="btn btn-default btn-sm slidePanel-close"><i
                                                class="glyphicon glyphicon-remove"></i></button>
                                </div>
                            </div>
                        </div>
                    </header>
                    <div class="slidePanel-inner">
                        <div class="panel-body">
                            <div ng-show=loading class="modal-body">
                                <div class="bookme-pro-loading"></div>
                            </div>
                            <div class="modal-body" ng-hide="loading">
                                <div class="form-group">
                                    <label for="wp_user"><?php esc_html_e('User', 'bookme_pro') ?></label>
                                    <select ng-model="form.wp_user_id" class="form-control" id="wp_user">
                                        <option value=""></option>
                                        <?php foreach (get_users(array('fields' => array('ID', 'display_name'), 'orderby' => 'display_name')) as $wp_user) : ?>
                                            <option value="<?php echo $wp_user->ID ?>">
                                                <?php echo $wp_user->display_name ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>

                                <?php if (Config::showFirstLastName()) : ?>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label for="first_name"><?php esc_html_e('First name', 'bookme_pro') ?></label>
                                                <input class="form-control" type="text"
                                                       ng-model="form.first_name" id="first_name"/>
                                                <span style="font-size: 11px;color: red"
                                                      ng-show="errors.first_name.required"><?php esc_html_e('Required', 'bookme_pro') ?></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="last_name"><?php esc_html_e('Last name', 'bookme_pro') ?></label>
                                                <input class="form-control" type="text"
                                                       ng-model="form.last_name" id="last_name"/>
                                                <span style="font-size: 11px;color: red"
                                                      ng-show="errors.last_name.required"><?php esc_html_e('Required', 'bookme_pro') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <div class="form-group">
                                        <label for="full_name"><?php esc_html_e('Name', 'bookme_pro') ?></label>
                                        <input class="form-control" type="text" ng-model="form.full_name"
                                               id="full_name"/>
                                        <span style="font-size: 11px;color: red"
                                              ng-show="errors.full_name.required"><?php esc_html_e('Required', 'bookme_pro') ?></span>
                                    </div>
                                <?php endif ?>

                                <div class="form-group">
                                    <label for="phone"><?php esc_html_e('Phone', 'bookme_pro') ?></label>
                                    <input class="form-control" type="text" ng-model=form.phone id="phone"/>
                                </div>

                                <div class="form-group">
                                    <label for="email"><?php esc_html_e('Email', 'bookme_pro') ?></label>
                                    <input class="form-control" type="text" ng-model=form.email id="email"/>
                                </div>

                                <div class="form-group">
                                    <label for="notes"><?php esc_html_e('Notes', 'bookme_pro') ?></label>
                                    <textarea class="form-control" ng-model=form.notes
                                              id="notes"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="birthday"><?php esc_html_e('Date of birth', 'bookme_pro') ?></label>
                                    <input class="form-control" type="text" ng-model=form.birthday
                                           id="birthday"
                                           ui-date="dateOptions" ui-date-format="yy-mm-dd"
                                           autocomplete="off"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>