<?php
namespace BookmePro\Backend\Controllers\Settings;

use BookmePro\Lib;

/**
 * Class Controller
 * @package BookmePro\Backend\Controllers\Settings
 */
class Controller extends Lib\Base\Controller
{
    const page_slug = 'bookme-pro-settings';

    public function index()
    {
        /** @var \WP_Locale $wp_locale */
        global $wp_locale;

        wp_enqueue_media();
        $this->enqueueStyles(array(
            'frontend' => array('css/ladda.min.css'),
            'backend' => array_merge(array(
                'bootstrap/css/bootstrap-theme.min.css',
                'css/tooltipster.bundle.min.css',
                'css/tooltipster-sideTip-borderless.min.css'
            ), (is_rtl() ? array('bootstrap/css/bootstrap-rtl.css') : array()))
        ));

        $this->enqueueScripts(array(
            'backend' => array(
                'bootstrap/js/bootstrap.min.js' => array('jquery'),
                'js/tooltipster.bundle.min.js' => array('jquery'),
                'js/help.js' => array('jquery'),
                'js/jCal.js' => array('jquery'),
                'js/alert.js' => array('jquery'),
            ),
            'module' => array('js/settings.js' => array('jquery', 'bookme-pro-intlTelInput.min.js', 'jquery-ui-sortable')),
            'frontend' => array(
                'js/intlTelInput.min.js' => array('jquery'),
                'js/spin.min.js' => array('jquery'),
                'js/ladda.min.js' => array('jquery'),
            )
        ));

        $current_tab = $this->hasParameter('tab') ? $this->getParameter('tab') : 'general';
        $alert = array('success' => array(), 'error' => array());

        // Save the settings.
        if (!empty ($_POST)) {
            if ($this->csrfTokenValid()) {
                switch ($this->getParameter('tab')) {
                    case 'calendar':  // Calendar form.
                        update_option('bookme_pro_cal_one_participant', $this->getParameter('bookme_pro_cal_one_participant'));
                        update_option('bookme_pro_cal_many_participants', $this->getParameter('bookme_pro_cal_many_participants'));
                        $alert['success'][] = esc_html__('Settings saved.', 'bookme_pro');
                        break;
                    case 'payments':  // Payments form.
                        update_option( 'bookme_pro_pmt_currency', $this->getParameter( 'bookme_pro_pmt_currency' ) );
                        update_option( 'bookme_pro_pmt_price_format', $this->getParameter( 'bookme_pro_pmt_price_format' ) );
                        update_option( 'bookme_pro_pmt_local', $this->getParameter( 'bookme_pro_pmt_local' ) );
                        $alert['success'][] = __( 'Settings saved.', 'bookme_pro' );
                        break;
                    case 'business_hours':  // Business hours form.
                        $form = new Forms\BusinessHours();
                        $form->bind($this->getPostParameters(), $_FILES);
                        $form->save();
                        $alert['success'][] = esc_html__('Settings saved.', 'bookme_pro');
                        break;
                    case 'general':  // General form.
                        $bookme_pro_gen_time_slot_length = $this->getParameter('bookme_pro_gen_time_slot_length');
                        if (in_array($bookme_pro_gen_time_slot_length, array(5, 10, 12, 15, 20, 30, 45, 60, 90, 120, 180, 240, 360))) {
                            update_option('bookme_pro_gen_time_slot_length', $bookme_pro_gen_time_slot_length);
                        }
                        update_option('bookme_pro_gen_service_duration_as_slot_length', (int)$this->getParameter('bookme_pro_gen_service_duration_as_slot_length'));
                        update_option('bookme_pro_gen_allow_staff_edit_profile', (int)$this->getParameter('bookme_pro_gen_allow_staff_edit_profile'));
                        update_option('bookme_pro_gen_default_appointment_status', $this->getParameter('bookme_pro_gen_default_appointment_status'));
                        update_option('bookme_pro_gen_link_assets_method', $this->getParameter('bookme_pro_gen_link_assets_method'));
                        update_option('bookme_pro_gen_max_days_for_booking', (int)$this->getParameter('bookme_pro_gen_max_days_for_booking'));
                        update_option('bookme_pro_gen_use_client_time_zone', (int)$this->getParameter('bookme_pro_gen_use_client_time_zone'));

                        $alert['success'][] = esc_html__('Settings saved.', 'bookme_pro');
                        break;
                    case 'url': // URL settings form.
                        update_option('bookme_pro_url_approve_page_url', $this->getParameter('bookme_pro_url_approve_page_url'));
                        update_option('bookme_pro_url_approve_denied_page_url', $this->getParameter('bookme_pro_url_approve_denied_page_url'));
                        update_option('bookme_pro_url_cancel_page_url', $this->getParameter('bookme_pro_url_cancel_page_url'));
                        update_option('bookme_pro_url_cancel_denied_page_url', $this->getParameter('bookme_pro_url_cancel_denied_page_url'));
                        update_option('bookme_pro_url_reject_denied_page_url', $this->getParameter('bookme_pro_url_reject_denied_page_url'));
                        update_option('bookme_pro_url_reject_page_url', $this->getParameter('bookme_pro_url_reject_page_url'));
                        $alert['success'][] = esc_html__('Settings saved.', 'bookme_pro');
                        break;
                    case 'customers':  // Customers form.
                        update_option('bookme_pro_cst_cancel_action', $this->getParameter('bookme_pro_cst_cancel_action'));
                        update_option('bookme_pro_cst_combined_notifications', $this->getParameter('bookme_pro_cst_combined_notifications'));
                        update_option('bookme_pro_cst_create_account', $this->getParameter('bookme_pro_cst_create_account'));
                        update_option('bookme_pro_cst_default_country_code', $this->getParameter('bookme_pro_cst_default_country_code'));
                        update_option('bookme_pro_cst_new_account_role', $this->getParameter('bookme_pro_cst_new_account_role'));
                        update_option('bookme_pro_cst_phone_default_country', $this->getParameter('bookme_pro_cst_phone_default_country'));
                        update_option('bookme_pro_cst_remember_in_cookie', $this->getParameter('bookme_pro_cst_remember_in_cookie'));
                        update_option('bookme_pro_cst_show_update_details_dialog', $this->getParameter('bookme_pro_cst_show_update_details_dialog'));
                        $alert['success'][] = esc_html__('Settings saved.', 'bookme_pro');
                        break;
                    case 'company':  // Company form.
                        update_option('bookme_pro_co_address', $this->getParameter('bookme_pro_co_address'));
                        update_option('bookme_pro_co_logo_attachment_id', $this->getParameter('bookme_pro_co_logo_attachment_id'));
                        update_option('bookme_pro_co_name', $this->getParameter('bookme_pro_co_name'));
                        update_option('bookme_pro_co_phone', $this->getParameter('bookme_pro_co_phone'));
                        update_option('bookme_pro_co_website', $this->getParameter('bookme_pro_co_website'));
                        $alert['success'][] = esc_html__('Settings saved.', 'bookme_pro');
                        break;
                }

                // Let Add-ons save their settings.
                $alert = Lib\Proxy\Shared::saveSettings($alert, $this->getParameter('tab'), $this->getPostParameters());
            }
        }

        $candidates = $this->getCandidatesBookmeProProduct();

        // Check if WooCommerce cart exists.
        if (get_option('bookme_pro_wc_enabled') && class_exists('WooCommerce', false)) {
            $post = get_post(wc_get_page_id('cart'));
            if ($post === null || $post->post_status != 'publish') {
                $alert['error'][] = sprintf(
                    esc_html__('WooCommerce cart is not set up. Follow the <a href="%s">link</a> to correct this problem.', 'bookme_pro'),
                    Lib\Utils\Common::escAdminUrl('wc-status', array('tab' => 'tools'))
                );
            }
        }
        $cart_columns = array(
            'service' => Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_label_service'),
            'date' => esc_html__('Date', 'bookme_pro'),
            'time' => esc_html__('Time', 'bookme_pro'),
            'employee' => Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_label_employee'),
            'price' => esc_html__('Price', 'bookme_pro'),
            'deposit' => esc_html__('Deposit', 'bookme_pro'),
        );

        wp_localize_script('bookme-pro-jCal.js', 'BookmeProL10n', array(
            'alert' => $alert,
            'current_tab' => $current_tab,
            'csrf_token' => Lib\Utils\Common::getCsrfToken(),
            'default_country' => get_option('bookme_pro_cst_phone_default_country'),
            'holidays' => $this->getHolidays(),
            'loading_img' => plugins_url('bookme-pro-free-appointment-booking-system/backend/assets/images/loading.gif'),
            'start_of_week' => get_option('start_of_week'),
            'days' => array_values($wp_locale->weekday_abbrev),
            'months' => array_values($wp_locale->month),
            'close' => esc_html__('Close', 'bookme_pro'),
            'repeat' => esc_html__('Repeat every year', 'bookme_pro'),
            'we_are_not_working' => esc_html__('We are not working on this day', 'bookme_pro'),
            'sample_price' => number_format_i18n(10, 3),
        ));
        $values = array(
            'bookme_pro_gc_limit_events' => array(array('0', esc_html__('Disabled', 'bookme_pro')), array(25, 25), array(50, 50), array(100, 100), array(250, 250), array(500, 500), array(1000, 1000), array(2500, 2500)),
            'bookme_pro_gen_min_time_prior_booking' => array(array('0', esc_html__('Disabled', 'bookme_pro'))),
            'bookme_pro_gen_min_time_prior_cancel' => array(array('0', esc_html__('Disabled', 'bookme_pro'))),
        );
        $wp_roles = new \WP_Roles();
        foreach ($wp_roles->get_names() as $role => $name) {
            $values['bookme_pro_cst_new_account_role'][] = array($role, $name);
        }
        foreach (array(5, 10, 12, 15, 20, 30, 45, 60, 90, 120, 180, 240, 360) as $duration) {
            $values['bookme_pro_gen_time_slot_length'][] = array($duration, Lib\Utils\DateTime::secondsToInterval($duration * MINUTE_IN_SECONDS));
        }
        foreach (array_merge(array(0.5), range(1, 12), range(24, 144, 24), range(168, 672, 168)) as $hour) {
            $values['bookme_pro_gen_min_time_prior_booking'][] = array($hour, Lib\Utils\DateTime::secondsToInterval($hour * HOUR_IN_SECONDS));
        }
        foreach (array_merge(array(1), range(2, 12, 2), range(24, 168, 24)) as $hour) {
            $values['bookme_pro_gen_min_time_prior_cancel'][] = array($hour, Lib\Utils\DateTime::secondsToInterval($hour * HOUR_IN_SECONDS));
        }


        $this->render('index', compact('candidates', 'cart_columns', 'values'));
    }

    /**
     * Ajax request for Holidays calendar
     */
    public function executeSettingsHoliday()
    {
        global $wpdb;

        $id = $this->getParameter('id', false);
        $day = $this->getParameter('day', false);
        $holiday = $this->getParameter('holiday') == 'true';
        $repeat = (int)($this->getParameter('repeat') == 'true');

        // update or delete the event
        if ($id) {
            if ($holiday) {
                $wpdb->update(Lib\Entities\Holiday::getTableName(), array('repeat_event' => $repeat), array('id' => $id), array('%d'));
                $wpdb->update(Lib\Entities\Holiday::getTableName(), array('repeat_event' => $repeat), array('parent_id' => $id), array('%d'));
            } else {
                Lib\Entities\Holiday::query()->delete()->where('id', $id)->where('parent_id', $id, 'OR')->execute();
            }
            // add the new event
        } elseif ($holiday && $day) {
            $holiday = new Lib\Entities\Holiday();
            $holiday
                ->setDate($day)
                ->setRepeatEvent($repeat)
                ->save();
            foreach (Lib\Entities\Staff::query()->fetchArray() as $employee) {
                $staff_holiday = new Lib\Entities\Holiday();
                $staff_holiday
                    ->setDate($day)
                    ->setRepeatEvent($repeat)
                    ->setStaffId($employee['id'])
                    ->setParent($holiday)
                    ->save();
            }
        }

        // and return refreshed events
        echo json_encode($this->getHolidays());
        exit;
    }

    /**
     * @return string
     */
    protected function getHolidays()
    {
        $collection = Lib\Entities\Holiday::query()->where('staff_id', null)->fetchArray();
        $holidays = array();
        if (count($collection)) {
            foreach ($collection as $holiday) {
                $holidays[$holiday['id']] = array(
                    'm' => (int)date('m', strtotime($holiday['date'])),
                    'd' => (int)date('d', strtotime($holiday['date'])),
                );
                // If not repeated holiday, add the year
                if (!$holiday['repeat_event']) {
                    $holidays[$holiday['id']]['y'] = (int)date('Y', strtotime($holiday['date']));
                }
            }
        }

        return $holidays;
    }

    /**
     * @return array
     */
    protected function getCandidatesBookmeProProduct()
    {
        /** @global \wpdb $wpdb */
        global $wpdb;

        $goods = array(array('id' => 0, 'name' => esc_html__('Select product', 'bookme_pro')));
        $query = 'SELECT ID, post_title FROM ' . $wpdb->posts . ' WHERE post_type = \'product\' AND post_status = \'publish\' ORDER BY post_title';
        $products = $wpdb->get_results($query);

        foreach ($products as $product) {
            $goods[] = array('id' => $product->ID, 'name' => $product->post_title);
        }

        return $goods;
    }
}