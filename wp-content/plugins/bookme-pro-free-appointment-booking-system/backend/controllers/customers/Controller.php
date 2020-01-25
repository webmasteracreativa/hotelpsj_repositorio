<?php
namespace BookmePro\Backend\Controllers\Customers;

use BookmePro\Lib;

/**
 * Class Controller
 * @package BookmePro\Backend\Controllers\Customers
 */
class Controller extends Lib\Base\Controller
{
    const page_slug = 'bookme-pro-customers';

    protected function getPermissions()
    {
        return array(
            'executeSaveCustomer' => 'user',
        );
    }

    public function index()
    {
        if ($this->hasParameter('import-customers')) {
            $this->importCustomers();
        }

        $this->enqueueStyles(array(
            'backend' => array_merge(array(
                'bootstrap/css/bootstrap-theme.min.css',
                'css/slidePanel.min.css'
            ), (is_rtl() ? array('bootstrap/css/bootstrap-rtl.css') : array())),
            'frontend' => array('css/ladda.min.css',),
        ));

        $this->enqueueScripts(array(
            'backend' => array(
                'bootstrap/js/bootstrap.min.js' => array('jquery'),
                'js/datatables.min.js' => array('jquery'),
                'js/sidePanel.js' => array('jquery'),
            ),
            'frontend' => array(
                'js/spin.min.js' => array('jquery'),
                'js/ladda.min.js' => array('jquery'),
            ),
            'module' => array(
                'js/customers.js' => array('bookme-pro-datatables.min.js', 'bookme-pro-ng-customer_dialog.js'),
            ),
        ));

        wp_localize_script('bookme-pro-customers.js', 'BookmeProL10n', array(
            'csrf_token' => Lib\Utils\Common::getCsrfToken(),
            'first_last_name' => (int)Lib\Config::showFirstLastName(),
            'edit' => esc_html__('Edit', 'bookme_pro'),
            'are_you_sure' => esc_html__('Are you sure?', 'bookme_pro'),
            'wp_users' => get_users(array('fields' => array('ID', 'display_name'), 'orderby' => 'display_name')),
            'zeroRecords' => esc_html__('No customers found.', 'bookme_pro'),
            'processing' => esc_html__('Processing...', 'bookme_pro'),
            'edit_customer' => esc_html__('Edit customer', 'bookme_pro'),
            'new_customer' => esc_html__('New customer', 'bookme_pro'),
            'create_customer' => esc_html__('Create customer', 'bookme_pro'),
            'save' => esc_html__('Save', 'bookme_pro'),
            'search' => esc_html__('Quick search customer', 'bookme_pro'),
        ));

        $this->render('index');
    }

    /**
     * Get list of customers.
     */
    public function executeGetCustomers()
    {
        global $wpdb;

        $columns = $this->getParameter('columns');
        $order = $this->getParameter('order');
        $filter = $this->getParameter('filter');

        $query = Lib\Entities\Customer::query('c');

        $total = $query->count();

        $query
            ->select('SQL_CALC_FOUND_ROWS c.*,
                (
                    SELECT MAX(a.start_date) FROM ' . Lib\Entities\Appointment::getTableName() . ' a
                        LEFT JOIN ' . Lib\Entities\CustomerAppointment::getTableName() . ' ca ON ca.appointment_id = a.id
                            WHERE ca.customer_id = c.id
                ) AS last_appointment,
                (
                    SELECT COUNT(DISTINCT ca.appointment_id) FROM ' . Lib\Entities\CustomerAppointment::getTableName() . ' ca
                        WHERE ca.customer_id = c.id
                ) AS total_appointments,
                (
                    SELECT SUM(p.total) FROM ' . Lib\Entities\Payment::getTableName() . ' p
                        WHERE p.id IN (
                            SELECT DISTINCT ca.payment_id FROM ' . Lib\Entities\CustomerAppointment::getTableName() . ' ca
                                WHERE ca.customer_id = c.id
                        )
                ) AS payments,
                wpu.display_name AS wp_user')
            ->tableJoin($wpdb->users, 'wpu', 'wpu.ID = c.wp_user_id')
            ->groupBy('c.id');

        if ($filter != '') {
            $search_value = Lib\Query::escape($filter);
            $query
                ->whereLike('c.full_name', "%{$search_value}%")
                ->whereLike('c.phone', "%{$search_value}%", 'OR')
                ->whereLike('c.email', "%{$search_value}%", 'OR');
        }

        foreach ($order as $sort_by) {
            $query->sortBy(str_replace('.', '_', $columns[$sort_by['column']]['data']))
                ->order($sort_by['dir'] == 'desc' ? Lib\Query::ORDER_DESCENDING : Lib\Query::ORDER_ASCENDING);
        }

        $query->limit($this->getParameter('length'))->offset($this->getParameter('start'));

        $data = array();
        foreach ($query->fetchArray() as $row) {
            $data[] = array(
                'id' => $row['id'],
                'full_name' => $row['full_name'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'wp_user' => $row['wp_user'],
                'wp_user_id' => $row['wp_user_id'],
                'phone' => $row['phone'],
                'email' => $row['email'],
                'notes' => $row['notes'],
                'birthday' => $row['birthday'],
                'last_appointment' => $row['last_appointment'] ? Lib\Utils\DateTime::formatDateTime($row['last_appointment']) : '',
                'total_appointments' => $row['total_appointments'],
                'payments' => Lib\Utils\Price::format($row['payments']),
            );
        }

        wp_send_json(array(
            'draw' => ( int )$this->getParameter('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => ( int )$wpdb->get_var('SELECT FOUND_ROWS()'),
            'data' => $data,
        ));
    }

    /**
     * Create or edit a customer.
     */
    public function executeSaveCustomer()
    {
        $response = array();
        $form = new Forms\Customer();

        do {
            if ((get_option('bookme_pro_cst_first_last_name') && $this->getParameter('first_name') !== '' && $this->getParameter('last_name') !== '') || (!get_option('bookme_pro_cst_first_last_name') && $this->getParameter('full_name') !== '')) {
                $params = $this->getPostParameters();
                if (!$params['wp_user_id']) {
                    $params['wp_user_id'] = null;
                }
                if (!$params['birthday']) {
                    $params['birthday'] = null;
                }
                $form->bind($params);
                /** @var Lib\Entities\Customer $customer */
                $customer = $form->save();
                if ($customer) {
                    $response['success'] = true;
                    $response['customer'] = array(
                        'id' => $customer->getId(),
                        'wp_user_id' => $customer->getWpUserId(),
                        'full_name' => $customer->getFullName(),
                        'first_name' => $customer->getFirstName(),
                        'last_name' => $customer->getLastName(),
                        'phone' => $customer->getPhone(),
                        'email' => $customer->getEmail(),
                        'notes' => $customer->getNotes(),
                        'birthday' => $customer->getBirthday(),
                    );
                    break;
                }
            }
            $response['success'] = false;
            $response['errors'] = array();
            if (get_option('bookme_pro_cst_first_last_name')) {
                if ($this->getParameter('first_name') == '') {
                    $response['errors']['first_name'] = array('required');
                }
                if ($this->getParameter('last_name') == '') {
                    $response['errors']['last_name'] = array('required');
                }
            } else {
                $response['errors'] = array('full_name' => array('required'));
            }
        } while (0);

        wp_send_json($response);
    }

    /**
     * Delete customers.
     */
    public function executeDeleteCustomers()
    {
        foreach ($this->getParameter('data', array()) as $id) {
            $customer = new Lib\Entities\Customer();
            $customer->load($id);
            $customer->deleteWithWPUser((bool)$this->getParameter('with_wp_user'));
        }
        wp_send_json_success();
    }
}