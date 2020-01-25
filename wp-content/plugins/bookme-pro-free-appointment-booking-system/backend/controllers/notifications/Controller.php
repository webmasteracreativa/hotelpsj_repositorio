<?php
namespace BookmePro\Backend\Controllers\Notifications;

use BookmePro\Lib;

/**
 * Class Controller
 * @package BookmePro\Backend\Controllers\Notifications
 */
class Controller extends Lib\Base\Controller
{
    const page_slug = 'bookme-pro-notifications';

    public function index()
    {
        $this->enqueueStyles( array(
            'frontend' => array( 'css/ladda.min.css' ),
            'backend'  => array_merge(array(
                'bootstrap/css/bootstrap-theme.min.css',
                'css/slidePanel.min.css',
                'css/tooltipster.bundle.min.css',
                'css/tooltipster-sideTip-borderless.min.css',
                'css/jquery.multiselect.css'
            ), (is_rtl() ? array('bootstrap/css/bootstrap-rtl.css') : array()))
        ) );

        $this->enqueueScripts( array(
            'backend'  => array(
                'bootstrap/js/bootstrap.min.js' => array( 'jquery' ),
                'js/angular.min.js',
                'js/tooltipster.bundle.min.js' => array('jquery'),
                'js/help.js'  => array( 'jquery' ),
                'js/alert.js' => array( 'jquery' ),
                'js/sidePanel.js' => array( 'jquery' ),
                'js/jquery.multiselect.js' => array('jquery')
            ),
            'module'   => array(
                'js/notification.js' => array( 'jquery' ),
                'js/ng-app.js' => array( 'jquery', 'bookme-pro-angular.min.js' ),
            ),
            'frontend' => array(
                'js/spin.min.js'  => array( 'jquery' ),
                'js/ladda.min.js' => array( 'jquery' ),
            )
        ) );
        $cron_reminder = (array) get_option( 'bookme_pro_cron_reminder_times' );
        $form  = new Forms\Notifications( 'email' );
        $alert = array( 'success' => array() );
        // Save action.
        if ( ! empty ( $_POST ) ) {
            if ( $this->csrfTokenValid() ) {
                $form->bind( $this->getPostParameters() );
                $form->save();
                $alert['success'][] = esc_html__( 'Settings saved.', 'bookme_pro' );
                update_option( 'bookme_pro_email_send_as', $this->getParameter( 'bookme_pro_email_send_as' ) );
                update_option( 'bookme_pro_email_reply_to_customers', $this->getParameter( 'bookme_pro_email_reply_to_customers' ) );
                update_option( 'bookme_pro_email_sender', $this->getParameter( 'bookme_pro_email_sender' ) );
                update_option( 'bookme_pro_email_sender_name', $this->getParameter( 'bookme_pro_email_sender_name' ) );
                foreach ( array( 'staff_agenda', 'client_follow_up', 'client_reminder', 'client_birthday_greeting' ) as $type ) {
                    $cron_reminder[ $type ] = $this->getParameter( $type . '_cron_hour' );
                }
                foreach ( array( 'client_reminder_1st', 'client_reminder_2nd', 'client_reminder_3rd', ) as $type ) {
                    $cron_reminder[ $type ] = $this->getParameter( $type . '_cron_before_hour' );
                }
                update_option( 'bookme_pro_cron_reminder_times', $cron_reminder );
            }
        }
        $cron_uri = plugins_url( 'lib/utils/send_notifications_cron.php', Lib\Plugin::getMainFile() );
        wp_localize_script( 'bookme-pro-alert.js', 'BookmeProL10n',  array(
            'csrf_token'   => Lib\Utils\Common::getCsrfToken(),
            'are_you_sure' => esc_html__( 'Are you sure?', 'bookme_pro' ),
            'alert'        => $alert,
            'sent_successfully' => esc_html__( 'Sent successfully.', 'bookme_pro' )
        ) );
        $statuses = Lib\Entities\CustomerAppointment::getStatuses();
        $this->render( 'index', compact( 'form', 'cron_uri', 'cron_reminder', 'statuses' ) );
    }

    public function executeGetEmailNotificationsData()
    {
        $form = new Forms\Notifications( 'email' );

        $bookme_pro_email_sender_name  = get_option( 'bookme_pro_email_sender_name' ) == '' ?
            get_option( 'blogname' )    : get_option( 'bookme_pro_email_sender_name' );

        $bookme_pro_email_sender = get_option( 'bookme_pro_email_sender' ) == '' ?
            get_option( 'admin_email' ) : get_option( 'bookme_pro_email_sender' );

        $notifications = array();
        foreach ( $form->getData() as $notification ) {
            $name = Lib\Entities\Notification::getName( $notification['type'] );
            if ( in_array( $notification['type'], array(
                    Lib\Entities\Notification::TYPE_APPOINTMENT_START_TIME,
                    Lib\Entities\Notification::TYPE_CUSTOMER_BIRTHDAY,
                    Lib\Entities\Notification::TYPE_LAST_CUSTOMER_APPOINTMENT,
                ) ) && $notification['subject'] != '' ) {
                // In window Test Email Notification
                // for custom notification, subject is name.
                $name = $notification['subject'];
            }
            $notifications[] = array(
                'type'   => $notification['type'],
                'name'   => $name,
                'active' => $notification['active'],
            );
        }

        $result = array(
            'notifications' => $notifications,
            'sender_email'  => $bookme_pro_email_sender,
            'sender_name'   => $bookme_pro_email_sender_name,
            'send_as'       => get_option( 'bookme_pro_email_send_as' ),
            'reply_to_customers' => get_option( 'bookme_pro_email_reply_to_customers' ),
        );

        wp_send_json_success( $result );
    }

    public function executeTestEmailNotifications()
    {
        $to_email      = $this->getParameter( 'to_email' );
        $sender_name   = $this->getParameter( 'sender_name' );
        $sender_email  = $this->getParameter( 'sender_email' );
        $send_as       = $this->getParameter( 'send_as' );
        $notifications = $this->getParameter( 'notifications' );
        $reply_to_customers = $this->getParameter( 'reply_to_customers' );

        // Change 'Content-Type' and 'Reply-To' for test email notification.
        add_filter( 'bookme_pro_email_headers', function ( $headers ) use ( $sender_name, $sender_email, $send_as, $reply_to_customers ) {
            $headers = array();
            if ( $send_as == 'html' ) {
                $headers[] = 'Content-Type: text/html; charset=utf-8';
            } else {
                $headers[] = 'Content-Type: text/plain; charset=utf-8';
            }
            $headers[] = 'From: ' . $sender_name . ' <' . $sender_email . '>';
            if ( $reply_to_customers ) {
                $headers[] = 'Reply-To: ' . $sender_name . ' <' . $sender_email . '>';
            }

            return $headers;
        }, 10, 1 );

        Lib\NotificationSender::sendTestEmailNotifications( $to_email, $notifications, $send_as );

        wp_send_json_success();
    }

}