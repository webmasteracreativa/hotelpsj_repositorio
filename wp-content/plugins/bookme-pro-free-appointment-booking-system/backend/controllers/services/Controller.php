<?php
namespace BookmePro\Backend\Controllers\Services;

use BookmePro\Backend\Controllers\Notices\Components;
use BookmePro\Lib;

/**
 * Class Controller
 * @package BookmePro\Backend\Controllers\Services
 */
class Controller extends Lib\Base\Controller
{
    const page_slug = 'bookme-pro-services';

    /**
     * Index page.
     */
    public function index()
    {
        wp_enqueue_media();
        $this->enqueueStyles(array(
            'wp' => array('wp-color-picker'),
            'frontend' => array('css/ladda.min.css'),
            'backend' => array_merge(array('bootstrap/css/bootstrap-theme.min.css', 'css/slidePanel.min.css', 'css/tooltipster.bundle.min.css', 'css/tooltipster-sideTip-borderless.min.css', 'css/jquery.multiselect.css'),
                (is_rtl() ? array('bootstrap/css/bootstrap-rtl.css') : array())
            ),
        ));

        $this->enqueueScripts(array(
            'wp' => array('wp-color-picker'),
            'backend' => array(
                'bootstrap/js/bootstrap.min.js' => array('jquery'),
                'js/tooltipster.bundle.min.js' => array('jquery'),
                'js/help.js' => array('jquery'),
                'js/alert.js' => array('jquery'),
                'js/range_tools.js' => array('jquery'),
                'js/jquery-slidePanel.min.js' => array('jquery'),
                'js/custom.js' => array('jquery'),
                'js/jquery.multiselect.js' => array('jquery'),
            ),
            'module' => array('js/service.js' => array('jquery-ui-sortable', 'jquery')),
            'frontend' => array(
                'js/spin.min.js' => array('jquery'),
                'js/ladda.min.js' => array('bookme-pro-spin.min.js', 'jquery'),
            )
        ));

        $data = $this->getCaSeStSpCollections();
        $staff = array();
        foreach ($data['staff_collection'] as $employee) {
            $staff[$employee['id']] = $employee['full_name'];
        }

        wp_localize_script('bookme-pro-service.js', 'BookmeProL10n', array(
            'csrf_token' => Lib\Utils\Common::getCsrfToken(),
            'capacity_error' => esc_html__('Min capacity should not be greater than max capacity.', 'bookme_pro'),
            'are_you_sure' => esc_html__('Are you sure?', 'bookme_pro'),
            'service_special_day' => Lib\Config::specialDaysEnabled() && Lib\Config::specialDaysEnabled(),
            'reorder' => esc_html__('Reorder', 'bookme_pro'),
            'staff' => $staff
        ));
        wp_localize_script('bookme-pro-custom.js', 'BookmePro_rtl', array('is_rtl' => is_rtl()));

        // Allow add-ons to enqueue their assets.
        Lib\Proxy\Shared::enqueueAssetsForServices();

        $data['panel_url'] = admin_url('admin-ajax.php?action=bookme_pro_service_panel&csrf_token=' . Lib\Utils\Common::getCsrfToken());

        $this->render('index', $data);
    }

    /**
     *
     */
    public function executeGetCategoryServices()
    {
        $category_id = $this->getParameter('category_id', 0);
        wp_send_json_success($this->render('_list', array(
            'service_collection' => $this->getServiceCollection($category_id),
            'panel_url' => admin_url('admin-ajax.php?action=bookme_pro_service_panel&csrf_token=' . Lib\Utils\Common::getCsrfToken())
        ), false));
    }

    /**
     *
     */
    public function executeAddCategory()
    {
        $html = '';
        if (!empty ($_POST)) {
            if ($this->csrfTokenValid()) {
                $form = new Forms\Category();
                $form->bind($this->getPostParameters());
                if ($category = $form->save()) {
                    $html = $this->render('_category_item', array('category' => $category->getFields()), false);
                }
            }
        }
        wp_send_json_success(compact('html'));
    }

    /**
     * Update category.
     */
    public function executeUpdateCategory()
    {
        $form = new Forms\Category();
        $form->bind($this->getPostParameters());
        $form->save();
    }

    /**
     * Update category position.
     */
    public function executeUpdateCategoryPosition()
    {
        $category_sorts = $this->getParameter('position');
        foreach ($category_sorts as $position => $category_id) {
            $category_sort = new Lib\Entities\Category();
            $category_sort->load($category_id);
            $category_sort->setPosition($position);
            $category_sort->save();
        }
    }

    /**
     * Update services position.
     */
    public function executeUpdateServicesPosition()
    {
        $services_sorts = $this->getParameter('position');
        foreach ($services_sorts as $position => $service_ids) {
            $services_sort = new Lib\Entities\Service();
            $services_sort->load($service_ids);
            $services_sort->setPosition($position);
            $services_sort->save();
        }
    }

    /**
     * Reorder staff preferences for service
     */
    public function executeUpdateServiceStaffPreferenceOrders()
    {
        $service_id = $this->getParameter('service_id');
        $positions = (array)$this->getParameter('positions');
        /** @var Lib\Entities\StaffPreferenceOrder[] $staff_preferences */
        $staff_preferences = Lib\Entities\StaffPreferenceOrder::query()
            ->where('service_id', $service_id)
            ->indexBy('staff_id')
            ->find();
        foreach ($positions as $position => $staff_id) {
            if (array_key_exists($staff_id, $staff_preferences)) {
                $staff_preferences[$staff_id]->setPosition($position)->save();
            } else {
                $preference = new Lib\Entities\StaffPreferenceOrder();
                $preference
                    ->setServiceId($service_id)
                    ->setStaffId($staff_id)
                    ->setPosition($position)
                    ->save();
            }
        }

        wp_send_json_success();
    }

    /**
     * Delete category.
     */
    public function executeDeleteCategory()
    {
        $category = new Lib\Entities\Category();
        $category->setId($this->getParameter('id', 0));
        $category->delete();
    }

    public function executeServicePanel()
    {
        $data = $this->getCaStSpCollections();
        if ($this->hasParameter('service_id')) {
            $service = $this->getServiceData($this->getParameter('service_id'));
            if ($service = reset($service))
                $data['service'] = $service;
        }
        $this->render('service-panel', $data);
        wp_die();
    }

    public function executeRemoveServices()
    {
        $service_ids = $this->getParameter('service_ids', array());
        if (is_array($service_ids) && !empty ($service_ids)) {
            foreach ($service_ids as $service_id) {
                Lib\Proxy\Shared::serviceDeleted($service_id);
            }
            Lib\Entities\Service::query('s')->delete()->whereIn('s.id', $service_ids)->execute();
        }
        wp_send_json_success();
    }

    /**
     * Update service parameters and assign staff
     */
    public function executeUpdateService()
    {

        if (Lib\Entities\Service::query()->count() > 4 && !$this->getParameter('id') ) {
            wp_send_json_error(array( 'message' => Components::getInstance()->getLimitationHtml() ));
        }
        /** @var \wpdb $wpdb */
        global $wpdb;

        $form = new Forms\Service();
        $form->bind($this->getPostParameters());
        $service = $form->save();
        $service_id = $service->getId();

        $staff_ids = $this->getParameter('staff_ids', array());
        if (empty ($staff_ids)) {
            Lib\Entities\StaffService::query()->delete()->where('service_id', $service_id)->execute();
        } else {
            Lib\Entities\StaffService::query()->delete()->where('service_id', $service_id)->whereNotIn('staff_id', $staff_ids)->execute();
            if ($this->getParameter('update_staff', false)) {
                if ($service->getType() == Lib\Entities\Service::TYPE_PACKAGE && !$this->getParameter('package_service_changed', false)) {
                    $data = array(
                        'price' => $this->getParameter('price'),
                    );
                } else {
                    $data = array(
                        'price' => $this->getParameter('price'),
                        'capacity_min' => $service->getCapacityMin(),
                        'capacity_max' => $service->getCapacityMax(),
                    );
                }
                $wpdb->update(
                    Lib\Entities\StaffService::getTableName(),
                    $data,
                    array('service_id' => $this->getParameter('id'))
                );
            }
            // Create records for newly linked staff.
            if ($service->getType() != Lib\Entities\Service::TYPE_PACKAGE) {
                $existing_staff_ids = array();
                $res = Lib\Entities\StaffService::query()
                    ->select('staff_id')
                    ->where('service_id', $service_id)
                    ->fetchArray();
                foreach ($res as $staff) {
                    $existing_staff_ids[] = $staff['staff_id'];
                }
                foreach ($staff_ids as $staff_id) {
                    if (!in_array($staff_id, $existing_staff_ids)) {
                        $staff_service = new Lib\Entities\StaffService();
                        $staff_service->setStaffId($staff_id)
                            ->setServiceId($service_id)
                            ->setPrice($service->getPrice())
                            ->setCapacityMin($service->getCapacityMin())
                            ->setCapacityMax($service->getCapacityMax())
                            ->save();
                    }
                }
            }
        }

        // Update services in addons.
        $alert = Lib\Proxy\Shared::updateService(array('success' => array(esc_html__('Settings saved.', 'bookme_pro'))), $service, $this->getPostParameters());

        $price = Lib\Utils\Price::format($service->getPrice());
        $nice_duration = Lib\Utils\DateTime::secondsToInterval($service->getDuration());
        $title = $service->getTitle();
        $colors = array_fill(0, 3, $service->getColor());
        wp_send_json_success(Lib\Proxy\Shared::prepareUpdateServiceResponse(compact('title', 'price', 'colors', 'nice_duration', 'alert', 'service_id'), $service, $this->getPostParameters()));
    }

    /**
     * Array for rendering service list.
     *
     * @param int $category_id
     * @return array
     */
    private function getCaSeStSpCollections($category_id = 0)
    {
        if (!$category_id) {
            $category_id = $this->getParameter('category_id', 0);
        }

        return array(
            'service_collection' => $this->getServiceCollection($category_id),
            'staff_collection' => $this->getStaffCollection(),
            'category_collection' => $this->getCategoryCollection(),
            'staff_preference' => array(
                Lib\Entities\Service::PREFERRED_ORDER => esc_html__('Specified order', 'bookme_pro'),
                Lib\Entities\Service::PREFERRED_LEAST_OCCUPIED => esc_html__('Least occupied that day', 'bookme_pro'),
                Lib\Entities\Service::PREFERRED_MOST_OCCUPIED => esc_html__('Most occupied that day', 'bookme_pro'),
                Lib\Entities\Service::PREFERRED_LEAST_EXPENSIVE => esc_html__('Least expensive', 'bookme_pro'),
                Lib\Entities\Service::PREFERRED_MOST_EXPENSIVE => esc_html__('Most expensive', 'bookme_pro'),
            )
        );
    }

    private function getCaStSpCollections()
    {
        return array(
            'staff_collection' => $this->getStaffCollection(),
            'category_collection' => $this->getCategoryCollection(),
            'staff_preference' => array(
                Lib\Entities\Service::PREFERRED_ORDER => esc_html__('Specified order', 'bookme_pro'),
                Lib\Entities\Service::PREFERRED_LEAST_OCCUPIED => esc_html__('Least occupied that day', 'bookme_pro'),
                Lib\Entities\Service::PREFERRED_MOST_OCCUPIED => esc_html__('Most occupied that day', 'bookme_pro'),
                Lib\Entities\Service::PREFERRED_LEAST_EXPENSIVE => esc_html__('Least expensive', 'bookme_pro'),
                Lib\Entities\Service::PREFERRED_MOST_EXPENSIVE => esc_html__('Most expensive', 'bookme_pro'),
            ),
        );
    }

    /**
     * @return array
     */
    private function getCategoryCollection()
    {
        return Lib\Entities\Category::query()->sortBy('position')->fetchArray();
    }

    /**
     * @return array
     */
    private function getStaffCollection()
    {
        return Lib\Entities\Staff::query()->fetchArray();
    }

    /**
     * @param int $id
     * @return array
     */
    private function getServiceCollection($id = 0)
    {
        $services = Lib\Entities\Service::query('s')
            ->select('s.*, COUNT(staff.id) AS total_staff, GROUP_CONCAT(DISTINCT staff.id) AS staff_ids, GROUP_CONCAT(DISTINCT sp.staff_id ORDER BY sp.position ASC) AS pref_staff_ids')
            ->leftJoin('StaffService', 'ss', 'ss.service_id = s.id')
            ->leftJoin('StaffPreferenceOrder', 'sp', 'sp.service_id = s.id')
            ->leftJoin('Staff', 'staff', 'staff.id = ss.staff_id')
            ->whereRaw('s.category_id = %d OR !%d', array($id, $id))
            ->groupBy('s.id')
            ->indexBy('id')
            ->sortBy('s.position');
        if (!Lib\Config::packagesActive()) {
            $services->whereNot('s.type', Lib\Entities\Service::TYPE_PACKAGE);
        }
        $result = $services->fetchArray();
        foreach ($result as &$service) {
            $service['sub_services'] = Lib\Entities\SubService::query()
                ->where('service_id', $service['id'])
                ->sortBy('position')
                ->fetchArray();
            $service['colors'] = Lib\Proxy\Shared::prepareServiceColors(array_fill(0, 3, $service['color']), $service['id'], $service['type']);
        }

        return $result;
    }

    /**
     * Get service data by id
     * @param $id
     * @return array
     */
    private function getServiceData($id)
    {
        $services = Lib\Entities\Service::query('s')
            ->select('s.*, COUNT(staff.id) AS total_staff, GROUP_CONCAT(DISTINCT staff.id) AS staff_ids, GROUP_CONCAT(DISTINCT sp.staff_id ORDER BY sp.position ASC) AS pref_staff_ids')
            ->leftJoin('StaffService', 'ss', 'ss.service_id = s.id')
            ->leftJoin('StaffPreferenceOrder', 'sp', 'sp.service_id = s.id')
            ->leftJoin('Staff', 'staff', 'staff.id = ss.staff_id')
            ->whereRaw('s.id = %d', array($id))
            ->groupBy('s.id')
            ->indexBy('id')
            ->sortBy('s.position');
        if (!Lib\Config::packagesActive()) {
            $services->whereNot('s.type', Lib\Entities\Service::TYPE_PACKAGE);
        }
        $result = $services->fetchArray();
        foreach ($result as &$service) {
            $service['sub_services'] = Lib\Entities\SubService::query()
                ->where('service_id', $service['id'])
                ->sortBy('position')
                ->fetchArray();
            $service['colors'] = Lib\Proxy\Shared::prepareServiceColors(array_fill(0, 3, $service['color']), $service['id'], $service['type']);
        }

        return $result;
    }

    public function executeUpdateExtraPosition()
    {
        Lib\Proxy\ServiceExtras::reorder($this->getParameter('position'));
        wp_send_json_success();
    }
}