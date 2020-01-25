<?php
namespace BookmePro\Backend\Controllers\Appointments;

use BookmePro\Lib;
use BookmePro\Lib\DataHolders\Booking as DataHolders;

/**
 * Class Controller
 * @package BookmePro\Backend\Controllers\Appointments
 */
class Controller extends Lib\Base\Controller
{
    const page_slug = 'bookme-pro-appointments';

    public function index()
    {
        /** @var \WP_Locale $wp_locale */
        global $wp_locale;

        $this->enqueueStyles(array(
            'frontend' => array('css/ladda.min.css',),
            'backend' => array_merge(array(
                'css/select2.min.css',
                'bootstrap/css/bootstrap-theme.min.css',
                'css/daterangepicker.css',
                'css/slidePanel.min.css',
                'css/tooltipster.bundle.min.css',
                'css/tooltipster-sideTip-borderless.min.css'
            ), (is_rtl() ? array('bootstrap/css/bootstrap-rtl.css') : array())),
        ));

        $this->enqueueScripts(array(
            'backend' => array(
                'bootstrap/js/bootstrap.min.js' => array('jquery'),
                'js/tooltipster.bundle.min.js' => array('jquery'),
                'js/help.js' => array('jquery'),
                'js/datatables.min.js' => array('jquery', 'moment'),
                'js/daterangepicker.js' => array('jquery', 'moment'),
                'js/select2.full.min.js' => array('jquery'),
                'js/sidePanel.js' => array('jquery')
            ),
            'frontend' => array(
                'js/spin.min.js' => array('jquery'),
                'js/ladda.min.js' => array('jquery'),
            ),
            'module' => array('js/appointments.js' => array('bookme-pro-datatables.min.js'),)
        ));

        // Custom fields without captcha field.
        $custom_fields = array_filter(json_decode(get_option('bookme_pro_custom_fields')), function ($field) {
            return !in_array($field->type, array('captcha', 'text-content'));
        });

        wp_localize_script('bookme-pro-appointments.js', 'BookmeProL10n', array(
            'csrf_token' => Lib\Utils\Common::getCsrfToken(),
            'tomorrow' => esc_html__('Tomorrow', 'bookme_pro'),
            'today' => esc_html__('Today', 'bookme_pro'),
            'yesterday' => esc_html__('Yesterday', 'bookme_pro'),
            'last_7' => esc_html__('Last 7 Days', 'bookme_pro'),
            'last_30' => esc_html__('Last 30 Days', 'bookme_pro'),
            'this_month' => esc_html__('This Month', 'bookme_pro'),
            'next_month' => esc_html__('Next Month', 'bookme_pro'),
            'custom_range' => esc_html__('Custom Range', 'bookme_pro'),
            'apply' => esc_html__('Apply', 'bookme_pro'),
            'cancel' => esc_html__('Cancel', 'bookme_pro'),
            'to' => esc_html__('To', 'bookme_pro'),
            'from' => esc_html__('From', 'bookme_pro'),
            'calendar' => array(
                'longMonths' => array_values($wp_locale->month),
                'shortMonths' => array_values($wp_locale->month_abbrev),
                'longDays' => array_values($wp_locale->weekday),
                'shortDays' => array_values($wp_locale->weekday_abbrev),
            ),
            'mjsDateFormat' => Lib\Utils\DateTime::convertFormat('date', Lib\Utils\DateTime::FORMAT_MOMENT_JS),
            'startOfWeek' => (int)get_option('start_of_week'),
            'are_you_sure' => esc_html__('Are you sure?', 'bookme_pro'),
            'zeroRecords' => esc_html__('No bookings for selected period.', 'bookme_pro'),
            'processing' => esc_html__('Processing...', 'bookme_pro'),
            'edit' => esc_html__('Edit', 'bookme_pro'),
            'cf_columns' => array_map(function ($custom_field) {
                return $custom_field->id;
            }, $custom_fields),
            'filter' => (array)get_user_meta(get_current_user_id(), 'bookme_pro_filter_appointments_list', true),
            'no_result_found' => esc_html__('No result found', 'bookme_pro'),
            'panel_url' => admin_url('admin-ajax.php?action=bookme_pro_booking_panel&csrf_token=' . Lib\Utils\Common::getCsrfToken())
        ));

        // Filters data
        $staff_members = Lib\Entities\Staff::query('s')->select('s.id, s.full_name')->fetchArray();
        $customers = Lib\Entities\Customer::query('c')->select('c.id, c.full_name, c.first_name, c.last_name')->fetchArray();
        $services = Lib\Entities\Service::query('s')->select('s.id, s.title')->where('type', Lib\Entities\Service::TYPE_SIMPLE)->fetchArray();

        $this->render('index', compact('custom_fields', 'staff_members', 'customers', 'services'));
    }

    /**
     * Get list of appointments.
     */
    public function executeGetAppointments()
    {
        $columns = $this->getParameter('columns');
        $order = $this->getParameter('order');
        $filter = $this->getParameter('filter');
        $postfix_any = sprintf(' (%s)', get_option('bookme_pro_l10n_option_employee'));

        $query = Lib\Entities\CustomerAppointment::query('ca')
            ->select('a.id,
                ca.payment_id,
                ca.status,
                ca.id        AS ca_id,
                ca.extras,
                a.start_date,
                a.staff_any,
                a.extras_duration,
                c.full_name  AS customer_full_name,
                c.phone      AS customer_phone,
                c.email      AS customer_email,
                s.title      AS service_title,
                s.duration   AS service_duration,
                st.full_name AS staff_name,
                p.paid       AS payment,
                p.total      AS payment_total,
                p.type       AS payment_type,
                p.status     AS payment_status')
            ->leftJoin('Appointment', 'a', 'a.id = ca.appointment_id')
            ->leftJoin('Service', 's', 's.id = a.service_id')
            ->leftJoin('Customer', 'c', 'c.id = ca.customer_id')
            ->leftJoin('Payment', 'p', 'p.id = ca.payment_id')
            ->leftJoin('Staff', 'st', 'st.id = a.staff_id')
            ->leftJoin('StaffService', 'ss', 'ss.staff_id = st.id AND ss.service_id = s.id');

        $total = $query->count();

        if ($filter['id'] != '') {
            $query->where('a.id', $filter['id']);
        }

        list ($start, $end) = explode(' - ', $filter['date'], 2);
        $end = date('Y-m-d', strtotime('+1 day', strtotime($end)));
        $query->whereBetween('a.start_date', $start, $end);

        if ($filter['staff'] != '') {
            $query->where('a.staff_id', $filter['staff']);
        }

        if ($filter['customer'] != '') {
            $query->where('ca.customer_id', $filter['customer']);
        }

        if ($filter['service'] != '') {
            $query->where('a.service_id', $filter['service']);
        }

        if ($filter['status'] != '') {
            $query->where('ca.status', $filter['status']);
        }

        foreach ($order as $sort_by) {
            $query->sortBy(str_replace('.', '_', $columns[$sort_by['column']]['data']))
                ->order($sort_by['dir'] == 'desc' ? Lib\Query::ORDER_DESCENDING : Lib\Query::ORDER_ASCENDING);
        }

        $custom_fields = array();
        $fields_data = array_filter(json_decode(get_option('bookme_pro_custom_fields')), function ($field) {
            return !in_array($field->type, array('captcha', 'text-content'));
        });
        foreach ($fields_data as $field_data) {
            $custom_fields[$field_data->id] = '';
        }

        $data = array();
        foreach ($query->fetchArray() as $row) {
            // Service duration.
            $service_duration = Lib\Utils\DateTime::secondsToInterval($row['service_duration']);
            if ($row['extras_duration'] > 0) {
                $service_duration .= ' + ' . Lib\Utils\DateTime::secondsToInterval($row['extras_duration']);
            }
            // Appointment status.
            $row['status'] = Lib\Entities\CustomerAppointment::statusToString($row['status']);

            // Payment title.
            $payment_title = '';
            if ($row['payment'] !== null) {
                $payment_title = Lib\Utils\Price::format($row['payment']);
                if ($row['payment'] != $row['payment_total']) {
                    $payment_title = sprintf(esc_html__('%s of %s', 'bookme_pro'), $payment_title, Lib\Utils\Price::format($row['payment_total']));
                }
                $payment_title .= sprintf(
                    ' %s <span%s>%s</span>',
                    Lib\Entities\Payment::typeToString($row['payment_type']),
                    $row['payment_status'] == Lib\Entities\Payment::STATUS_PENDING ? ' class="text-danger"' : '',
                    Lib\Entities\Payment::statusToString($row['payment_status'])
                );
            }
            // Custom fields
            $customer_appointment = new Lib\Entities\CustomerAppointment();
            $customer_appointment->load($row['ca_id']);
            foreach ($customer_appointment->getCustomFieldsData() as $custom_field) {
                $custom_fields[$custom_field['id']] = $custom_field['value'];
            }

            $data[] = array(
                'id' => $row['id'],
                'start_date' => Lib\Utils\DateTime::formatDateTime($row['start_date']),
                'staff' => array(
                    'name' => $row['staff_name'] . ($row['staff_any'] ? $postfix_any : ''),
                ),
                'customer' => array(
                    'full_name' => $row['customer_full_name'],
                    'phone' => $row['customer_phone'],
                    'email' => $row['customer_email'],
                ),
                'service' => array(
                    'title' => $row['service_title'],
                    'duration' => $service_duration,
                    'extras' => (array)Lib\Proxy\ServiceExtras::getInfo(json_decode($row['extras'], true), false),
                ),
                'status' => $row['status'],
                'payment' => $payment_title,
                'custom_fields' => $custom_fields,
                'ca_id' => $row['ca_id'],
                'payment_id' => $row['payment_id'],
            );

            $custom_fields = array_map(function () {
                return '';
            }, $custom_fields);
        }

        unset($filter['date']);
        update_user_meta(get_current_user_id(), 'bookme_pro_filter_appointments_list', $filter);

        wp_send_json(array(
            'draw' => (int)$this->getParameter('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => count($data),
            'data' => $data,
        ));
    }

    /**
     * Delete customer appointments.
     */
    public function executeDeleteCustomerAppointments()
    {
        /** @var Lib\Entities\CustomerAppointment $ca */
        foreach (Lib\Entities\CustomerAppointment::query()->whereIn('id', $this->getParameter('data', array()))->find() as $ca) {
            if ($this->getParameter('notify')) {
                switch ($ca->getStatus()) {
                    case Lib\Entities\CustomerAppointment::STATUS_PENDING:
                    case Lib\Entities\CustomerAppointment::STATUS_WAITLISTED:
                        $ca->setStatus(Lib\Entities\CustomerAppointment::STATUS_REJECTED);
                        break;
                    case Lib\Entities\CustomerAppointment::STATUS_APPROVED:
                        $ca->setStatus(Lib\Entities\CustomerAppointment::STATUS_CANCELLED);
                        break;
                }
                Lib\NotificationSender::sendSingle(
                    DataHolders\Simple::create($ca),
                    null,
                    array('cancellation_reason' => $this->getParameter('reason'))
                );
            }
            $ca->deleteCascade();
        }
        wp_send_json_success();
    }
}