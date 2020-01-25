<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

use BookmePro\Lib\Entities\Notification;

/** @var BookmePro\Backend\Controllers\Notifications\Forms\Notifications $form */
?>
<div id="bookme-pro-tbs" class="wrap">
    <div class="bookme-pro-tbs-body">
        <?php \BookmePro\Lib\Utils\Common::pageHeader(__('SMS Notifications', 'bookme_pro')); ?>
        <div class="panel panel-default bookme-pro-main">
            <div class="panel-body">
                <form action="" method="post">
                    <input type="hidden" name="form-notifications">
                    <div class="row bookme-pro-margin-bottom-xlg">
                        <div class="col-md-12">
                            <div class="alert alert-info bookme-pro-margin-top-remove">
                                <?php printf(__('BookmePro uses <a href="%s" target="_blank">Twilio SMS API</a> for sending SMS. Just follow the below steps and enjoy the SMS service.', 'bookme_pro'), 'https://www.twilio.com/'); ?>
                            </div>
                            <ol>
                                <li><?php printf(__('Visit to <a href="%s" target="_blank">Twilio website</a> and sign up or log in.', 'bookme_pro'), 'https://www.twilio.com/'); ?></li>
                                <li><?php printf(__('After that go to <a href="%s" target="_blank">Console page.</a>', 'bookme_pro'), 'https://www.twilio.com/console'); ?></li>
                                <li><?php _e('And now copy ACCOUNT SID and AUTH TOKEN and paste below.', 'bookme_pro') ?></li>
                                <li><?php _e('After that create Twilio phone number and paste below.', 'bookme_pro') ?></li>
                                <li><?php _e("That's it. Now enjoy the SMS service.", 'bookme_pro') ?></li>
                            </ol>
                            <hr>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="bookme_pro_twillio_account_sid"><?php _e('Account SID', 'bookme_pro') ?></label>
                                <p class="help-block"><?php _e('Get Twilio Account SID from Twilio website.', 'bookme_pro') ?></p>
                                <input id="bookme_pro_twillio_account_sid" name="bookme_pro_twillio_account_sid"
                                       class="form-control" type="text"
                                       value="<?php form_option('bookme_pro_twillio_account_sid') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="bookme_pro_twillio_auth_token"><?php _e('Auth Token', 'bookme_pro') ?></label>
                                <p class="help-block"><?php _e('Get Twilio Auth Token from Twilio website', 'bookme_pro') ?></p>
                                <input id="bookme_pro_twillio_auth_token" name="bookme_pro_twillio_auth_token"
                                       class="form-control bookme-pro-sender" type="text"
                                       value="<?php form_option('bookme_pro_twillio_auth_token') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="bookme_pro_twillio_phone_number"><?php _e('Twilio Phone Number', 'bookme_pro') ?></label>
                            <p class="help-block"><?php _e('Get Twilio Phone number from Twilio website', 'bookme_pro') ?></p>
                            <input id="bookme_pro_twillio_phone_number" name="bookme_pro_twillio_phone_number"
                                   class="form-control bookme-pro-sender" type="text"
                                   value="<?php form_option('bookme_pro_twillio_phone_number') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="bookme_pro_sms_admin_phone"><?php _e('Admin Phone Number', 'bookme_pro') ?></label>
                            <p class="help-block"><?php _e('Admin phone number for SMS notification.', 'bookme_pro') ?></p>
                            <input id="bookme_pro_sms_admin_phone" name="bookme_pro_sms_admin_phone"
                                   class="form-control bookme-pro-sender" type="text"
                                   value="<?php form_option('bookme_pro_sms_admin_phone') ?>">
                        </div>
                    </div>

                    <h4 class="bookme-pro-block-head bookme-pro-color-gray"><?php _e('Single', 'bookme_pro') ?></h4>

                    <div class="panel-group bookme-pro-margin-vertical-xlg" id="bookme-pro-js-single-notifications"
                         role="tablist" aria-multiselectable="true">
                        <?php foreach ($form->getNotifications('single') as $notification) :
                            $id = $notification['id'];
                            ?>
                            <div class="panel panel-default bookme-pro-js-collapse">
                                <div class="panel-heading" role="tab">
                                    <div class="checkbox bookme-pro-margin-remove">
                                        <label>
                                            <input name="notification[<?php echo $id ?>][active]" value="0"
                                                   type="checkbox" checked="checked" class="hidden">
                                            <input id="<?php echo $id ?>_active"
                                                   name="notification[<?php echo $id ?>][active]" value="1"
                                                   type="checkbox" <?php checked($notification['active']) ?>>
                                            <a href="#collapse_<?php echo $id ?>" class="collapsed panel-title"
                                               role="button" data-toggle="collapse"
                                               data-parent="#bookme-pro-js-single-notifications">
                                                <?php echo Notification::getName($notification['type']) ?>
                                            </a>
                                        </label>
                                    </div>
                                </div>
                                <div id="collapse_<?php echo $id ?>" class="panel-collapse collapse">
                                    <div class="panel-body">

                                        <?php $form->renderSendingTime($notification) ?>
                                        <?php $form->renderEditor($id) ?>
                                        <?php $form->renderCopy($notification) ?>

                                        <div class="form-group">
                                            <label><?php _e('Codes', 'bookme_pro') ?></label>
                                            <?php switch ($notification['type']) :
                                                case 'client_new_wp_user':
                                                    include '_codes_client_new_wp_user.php';
                                                    break;
                                                case 'client_birthday_greeting':
                                                    include '_codes_client_birthday_greeting.php';
                                                    break;
                                                case 'staff_agenda':
                                                    include '_codes_staff_agenda.php';
                                                    break;
                                                default:
                                                    echo \BookmePro\Lib\Proxy\Shared::prepareCodes($this->render('_codes', array(), false), $notification['type'], 'sms');
                                            endswitch ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>

                    <?php \BookmePro\Lib\Proxy\Shared::renderSmsNotifications($form) ?>

                    <div class="panel-footer">
                        <?php \BookmePro\Lib\Utils\Common::submitButton('bookme-pro-js-submit-notifications') ?>
                        <?php \BookmePro\Lib\Utils\Common::resetButton() ?>
                        <div class="form-inline pull-left text-left">
                            <div class="form-group">
                                <div>
                                    <input class="form-control" id="bookme_pro_test_sms_phone" name="bookme_pro_test_sms_phone"
                                           type="text">
                                    <?php \BookmePro\Lib\Utils\Common::customButton('send_test_sms', 'btn-primary', __('Send test SMS', 'bookme_pro')) ?>
                                </div>
                                <p class="description"><?php _e('SMS charges may be applied by Twilio.', 'bookme_pro'); ?></p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>