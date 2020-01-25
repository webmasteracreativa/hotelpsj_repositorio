<?php
namespace BookmePro\Backend\Controllers\Sms;

use BookmePro\Lib;

/**
 * Class Controller
 * @package BookmePro\Backend\Controllers\Sms
 */
class Controller extends Lib\Base\Controller
{
    const page_slug = 'bookme-pro-sms';

    public function index()
    {
        $this->enqueueStyles(array(
            'frontend' => array_merge(
                array('css/ladda.min.css',),
                get_option('bookme_pro_cst_phone_default_country') == 'disabled'
                    ? array()
                    : array('css/intlTelInput.css')
            ),
            'backend' => array_merge(array(
                'bootstrap/css/bootstrap-theme.min.css',
                'css/daterangepicker.css',
                'css/tooltipster.bundle.min.css',
                'css/tooltipster-sideTip-borderless.min.css'
            ), (is_rtl() ? array('bootstrap/css/bootstrap-rtl.css') : array()))
        ));

        $this->enqueueScripts(array(
            'backend' => array(
                'bootstrap/js/bootstrap.min.js' => array('jquery'),
                'js/datatables.min.js' => array('jquery', 'moment'),
                'js/daterangepicker.js' => array('jquery', 'moment'),
                'js/tooltipster.bundle.min.js' => array('jquery'),
                'js/help.js' => array('jquery'),
                'js/alert.js' => array('jquery'),
            ),
            'frontend' => array_merge(
                array(
                    'js/spin.min.js' => array('jquery'),
                    'js/ladda.min.js' => array('jquery'),
                ),
                get_option('bookme_pro_cst_phone_default_country') == 'disabled'
                    ? array()
                    : array('js/intlTelInput.min.js' => array('jquery'))
            ),
            'module' => array('js/sms.js' => array('jquery')),
        ));

        $form = new \BookmePro\Backend\Controllers\Notifications\Forms\Notifications('sms');
        wp_localize_script('bookme-pro-daterangepicker.js', 'BookmeProL10n',
            array(
                'csrf_token' => Lib\Utils\Common::getCsrfToken(),
                'are_you_sure' => esc_html__('Are you sure?', 'bookme_pro'),
                'intlTelInput' => array(
                    'country' => get_option('bookme_pro_cst_phone_default_country'),
                    'enabled' => get_option('bookme_pro_cst_phone_default_country') != 'disabled',
                    'utils' => plugins_url('intlTelInput.utils.js', Lib\Plugin::getDirectory() . '/frontend/assets/js/intlTelInput.utils.js'),
                )
            )
        );
        $cron_uri = plugins_url('lib/utils/send_notifications_cron.php', Lib\Plugin::getMainFile());
        $statuses = Lib\Entities\CustomerAppointment::getStatuses();
        $this->render('index', compact('form', 'cron_uri', 'statuses'));
    }

    public function executeSaveSms()
    {
        update_option('bookme_pro_twillio_account_sid', $this->getParameter('bookme_pro_twillio_account_sid'));
        update_option('bookme_pro_twillio_auth_token', $this->getParameter('bookme_pro_twillio_auth_token'));
        update_option('bookme_pro_twillio_phone_number', $this->getParameter('bookme_pro_twillio_phone_number'));
        update_option('bookme_pro_sms_admin_phone', $this->getParameter('bookme_pro_sms_admin_phone'));

        $cron_reminder = (array)get_option('bookme_pro_cron_reminder_times');
        $form = new \BookmePro\Backend\Controllers\Notifications\Forms\Notifications('sms');
        $form->bind($this->getPostParameters());
        $form->save();

        foreach (array('staff_agenda', 'client_follow_up', 'client_reminder', 'client_birthday_greeting') as $type) {
            $cron_reminder[$type] = $this->getParameter($type . '_cron_hour');
        }
        foreach (array('client_reminder_1st', 'client_reminder_2nd', 'client_reminder_3rd',) as $type) {
            $cron_reminder[$type] = $this->getParameter($type . '_cron_before_hour');
        }
        update_option('bookme_pro_cron_reminder_times', $cron_reminder);

        wp_send_json_success(array('message' => esc_html__('Setting saved.', 'bookme_pro')));
    }


    public function executeSendTestSms()
    {
        $sms = new Lib\SMS();

        $response = array('success' => $sms->sendSms(
            $this->getParameter('phone_number'),
            'Bookme Pro test SMS.'
        ));

        if ($response['success']) {
            $response['message'] = esc_html__('SMS has been sent successfully.', 'bookme_pro');
        } else {
            $response['message'] = implode(' ', $sms->getErrors());
        }

        wp_send_json($response);
    }


}