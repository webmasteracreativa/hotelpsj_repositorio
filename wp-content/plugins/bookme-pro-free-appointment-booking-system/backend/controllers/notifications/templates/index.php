<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
use BookmePro\Lib\Entities\Notification;

$bookme_pro_email_sender_name = get_option('bookme_pro_email_sender_name') == '' ?
    get_option('blogname') : get_option('bookme_pro_email_sender_name');
$bookme_pro_email_sender = get_option('bookme_pro_email_sender') == '' ?
    get_option('admin_email') : get_option('bookme_pro_email_sender');

/** @var BookmePro\Backend\Controllers\Notifications\Forms\Notifications $form */
?>
<div id="bookme-pro-tbs" class="wrap">
    <div class="bookme-pro-tbs-body" ng-app="notifications">
        <?php \BookmePro\Lib\Utils\Common::pageHeader(esc_html__('Email Notifications', 'bookme_pro')); ?>
        <form method="post" action="">
            <div class="panel panel-default bookme-pro-main" ng-controller="emailNotifications">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sender_name"><?php esc_html_e('Sender name', 'bookme_pro') ?></label>
                                <input id="sender_name" name="bookme_pro_email_sender_name" class="form-control"
                                       type="text" value="<?php echo esc_attr($bookme_pro_email_sender_name) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sender_email"><?php esc_html_e('Sender email', 'bookme_pro') ?></label>
                                <input id="sender_email" name="bookme_pro_email_sender"
                                       class="form-control bookme-pro-sender" type="text"
                                       value="<?php echo esc_attr($bookme_pro_email_sender) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?php \BookmePro\Lib\Utils\Common::optionToggle('bookme_pro_email_send_as', esc_html__('Send emails as', 'bookme_pro'), esc_html__('HTML allows formatting, colors, fonts, positioning, etc. With Text you must use Text mode of rich-text editors below. On some servers only text emails are sent successfully.', 'bookme_pro'),
                                array(array('html', esc_html__('HTML', 'bookme_pro')), array('text', esc_html__('Text', 'bookme_pro')))
                            ) ?>
                        </div>
                        <div class="col-md-6">
                            <?php \BookmePro\Lib\Utils\Common::optionToggle('bookme_pro_email_reply_to_customers', esc_html__('Reply directly to customers', 'bookme_pro'), esc_html__('If this option is enabled then the email address of the customer is used as a sender email address for notifications sent to staff members and administrators.', 'bookme_pro')) ?>
                        </div>
                    </div>

                    <h4 class="bookme-pro-block-head bookme-pro-color-gray"><?php esc_html_e('Single', 'bookme_pro') ?></h4>

                    <div class="panel-group bookme-pro-margin-vertical-xlg" id="bookme-pro-js-single-notifications">
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
                                        <?php $form->renderSubject($id) ?>
                                        <?php $form->renderEditor($id) ?>
                                        <?php $form->renderCopy($notification) ?>

                                        <div class="form-group">
                                            <label><?php esc_html_e('Codes', 'bookme_pro') ?></label>
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
                                                    echo \BookmePro\Lib\Proxy\Shared::prepareCodes($this->render('_codes', array(), false), $notification['type'], 'email');
                                            endswitch ?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>


                    <?php \BookmePro\Lib\Proxy\Shared::renderEmailNotifications($form) ?>
                </div>

                <div class="panel-footer">
                    <?php \BookmePro\Lib\Utils\Common::csrf() ?>
                    <?php \BookmePro\Lib\Utils\Common::submitButton() ?>
                    <?php \BookmePro\Lib\Utils\Common::resetButton() ?>

                    <div class="pull-left">
                        <button type="button" class="btn btn-default bookme-pro-test-email-notifications btn-lg"
                                ng-click="showTestEmailNotificationDialog(); $event.stopPropagation();">
                            <?php esc_html_e('Test Email Notifications', 'bookme_pro') ?>
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <?php include '_test_email_notifications_modal.php' ?>
    </div>
</div>