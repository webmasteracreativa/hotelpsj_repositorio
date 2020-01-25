<?php
namespace BookmePro\Backend\Controllers\Staff;

use BookmePro\Lib;
use BookmePro\Backend\Controllers\Staff\Forms\Widgets\TimeChoice;
use BookmePro\Backend\Controllers\Notices\Components;

/**
 * Class Controller
 * @package BookmePro\Backend\Controllers\Staff
 */
class Controller extends Lib\Base\Controller
{
    const page_slug = 'bookme-pro-staff';

    protected function getPermissions()
    {
        return get_option('bookme_pro_gen_allow_staff_edit_profile') ? array('_this' => 'user') : array();
    }

    public function index()
    {
        wp_enqueue_media();
        $this->enqueueStyles(array(
            'frontend' => array_merge(
                array('css/ladda.min.css',),
                get_option('bookme_pro_cst_phone_default_country') == 'disabled'
                    ? array()
                    : array('css/intlTelInput.css')
            ),
            'backend' => array_merge(array('bootstrap/css/bootstrap-theme.min.css', 'css/jquery-ui-theme/jquery-ui.min.css', 'css/slidePanel.min.css', 'css/tooltipster.bundle.min.css', 'css/tooltipster-sideTip-borderless.min.css'),
                (is_rtl() ? array('bootstrap/css/bootstrap-rtl.css') : array()))
        ));

        $this->enqueueScripts(array(
            'backend' => array(
                'bootstrap/js/bootstrap.min.js' => array('jquery'),
                'js/tooltipster.bundle.min.js' => array('jquery'),
                'js/help.js' => array('jquery'),
                'js/jCal.js' => array('jquery'),
                'js/alert.js' => array('jquery'),
                'js/range_tools.js' => array('jquery'),
                'js/jquery-slidePanel.min.js' => array('jquery'),
                'js/custom.js' => array('jquery'),
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
            'module' => array(
                'js/staff-details.js' => array('bookme-pro-alert.js',),
                'js/staff-services.js' => array('bookme-pro-staff-details.js'),
                'js/staff-schedule.js' => array('bookme-pro-staff-services.js'),
                'js/staff-days-off.js' => array('bookme-pro-staff-schedule.js'),
                'js/staff.js' => array('jquery-ui-sortable', 'jquery-ui-datepicker', 'bookme-pro-range_tools.js', 'bookme-pro-staff-days-off.js'),
            ),
        ));

        wp_localize_script('bookme-pro-staff.js', 'BookmeProL10n', array(
            'are_you_sure' => __('Are you sure?', 'bookme_pro'),
            'saved' => __('Settings saved.', 'bookme_pro'),
            'capacity_error' => __('Min capacity should not be greater than max capacity.', 'bookme_pro'),
            'selector' => array('all_selected' => __('All locations', 'bookme_pro'), 'nothing_selected' => __('No locations selected', 'bookme_pro'),),
            'intlTelInput' => array(
                'enabled' => get_option('bookme_pro_cst_phone_default_country') != 'disabled',
                'utils' => plugins_url('intlTelInput.utils.js', Lib\Plugin::getDirectory() . '/frontend/assets/js/intlTelInput.utils.js'),
                'country' => get_option('bookme_pro_cst_phone_default_country'),
            ),
            'csrf_token' => Lib\Utils\Common::getCsrfToken(),
        ));

        wp_localize_script('bookme-pro-custom.js', 'BookmePro_rtl', array('is_rtl' => is_rtl()));

        // Allow add-ons to enqueue their assets.
        Lib\Proxy\Shared::enqueueAssetsForStaffProfile();

        $staff_members = Lib\Utils\Common::isCurrentUserAdmin()
            ? Lib\Entities\Staff::query()->sortBy('position')->fetchArray()
            : Lib\Entities\Staff::query()->where('wp_user_id', get_current_user_id())->fetchArray();

        if ($this->hasParameter('staff_id')) {
            $active_staff_id = $this->getParameter('staff_id');
        } else {
            $active_staff_id = empty ($staff_members) ? 0 : $staff_members[0]['id'];
        }

        $form = new Forms\StaffMemberEdit();
        $users_for_staff = $form->getUsersForStaff();

        $edit_panel_url = admin_url('admin-ajax.php?action=bookme_pro_staff_panel&csrf_token=' . Lib\Utils\Common::getCsrfToken());
        $add_panel_url = admin_url('admin-ajax.php?action=bookme_pro_add_staff_panel&csrf_token=' . Lib\Utils\Common::getCsrfToken());

        $this->render('index', compact('staff_members', 'users_for_staff', 'active_staff_id', 'edit_panel_url', 'add_panel_url'));
    }

    public function executeAddStaffPanel()
    {
        $form = new Forms\StaffMemberEdit();
        $users_for_staff = $form->getUsersForStaff();
        $this->render('_new', compact('users_for_staff'));
        wp_die();
    }

    public function executeCreateStaff()
    {
        if (Lib\Entities\Staff::query()->count() >= 2 ) {
            wp_send_json_error(array( 'error' => Components::getInstance()->getLimitationHtml() ));
        }
        $form = new Forms\StaffMemberNew();
        $form->bind($this->getPostParameters());

        $staff = $form->save();
        if ($staff) {
            $edit_panel_url = admin_url('admin-ajax.php?action=bookme_pro_staff_panel&csrf_token=' . Lib\Utils\Common::getCsrfToken());
            wp_send_json_success(array('html' => $this->render('_list_item', array('staff' => $staff->getFields(), 'edit_panel_url' => $edit_panel_url), false)));
        }
    }

    public function executeGetStaff()
    {
        $staff_members = Lib\Utils\Common::isCurrentUserAdmin()
            ? Lib\Entities\Staff::query()->sortBy('position')->fetchArray()
            : Lib\Entities\Staff::query()->where('wp_user_id', get_current_user_id())->fetchArray();
        $edit_panel_url = admin_url('admin-ajax.php?action=bookme_pro_staff_panel&csrf_token=' . Lib\Utils\Common::getCsrfToken());
        $html = '';
        foreach ($staff_members as $staff) {
            $html .= $this->render('_list_item', compact('staff', 'edit_panel_url'), false);
        }
        wp_send_json_success(array('html' => $html));
    }

    public function executeUpdateStaffPosition()
    {
        $staff_sorts = $this->getParameter('position');
        foreach ($staff_sorts as $position => $staff_id) {
            $staff_sort = new Lib\Entities\Staff();
            $staff_sort->load($staff_id);
            $staff_sort->setPosition($position);
            $staff_sort->save();
        }
    }

    public function executeGetStaffServices()
    {
        $form = new Forms\StaffServices();
        $staff_id = $this->getParameter('staff_id');
        $form->load($staff_id);
        $categories = $form->getCategories();
        $services_data = $form->getServicesData();
        $uncategorized_services = $form->getUncategorizedServices();

        $html = $this->render('services', compact('categories', 'services_data', 'uncategorized_services', 'staff_id'), false);
        wp_send_json_success(compact('html'));
    }

    public function executeGetStaffSchedule()
    {
        $staff_id = $this->getParameter('staff_id');
        $staff = new Lib\Entities\Staff();
        $staff->load($staff_id);
        $schedule_items = $staff->getScheduleItems();
        $html = $this->render('schedule', compact('schedule_items', 'staff_id'), false);
        wp_send_json_success(compact('html'));
    }

    public function executeStaffScheduleUpdate()
    {
        $form = new Forms\StaffSchedule();
        $form->bind($this->getPostParameters());
        $form->save();
        wp_send_json_success();
    }

    /**
     *
     * @throws \Exception
     */
    public function executeResetBreaks()
    {
        $breaks = $this->getParameter('breaks');
        $html_breaks = array();

        // Remove all breaks for staff member.
        $break = new Lib\Entities\ScheduleItemBreak();
        $break->removeBreaksByStaffId($breaks['staff_id']);

        // Restore previous breaks.
        if (isset($breaks['breaks']) && is_array($breaks['breaks'])) {
            foreach ($breaks['breaks'] as $day) {
                $schedule_item_break = new Lib\Entities\ScheduleItemBreak();
                $schedule_item_break->setFields($day);
                $schedule_item_break->save();
            }
        }

        $staff = new Lib\Entities\Staff();
        $staff->load($breaks['staff_id']);

        // Make array with breaks (html) for each day.
        foreach ($staff->getScheduleItems() as $item) {
            /** @var Lib\Entities\StaffScheduleItem $item */
            $html_breaks[$item->getId()] = $this->render('_breaks', array(
                'day_is_not_available' => null === $item->getStartTime(),
                'item' => $item,
                'break_start' => new TimeChoice(array('use_empty' => false, 'type' => 'break_from')),
                'break_end' => new TimeChoice(array('use_empty' => false, 'type' => 'to')),
            ), false);
        }

        wp_send_json($html_breaks);
    }

    public function executeStaffScheduleHandleBreak()
    {
        $start_time = $this->getParameter('start_time');
        $end_time = $this->getParameter('end_time');
        $working_start = $this->getParameter('working_start');
        $working_end = $this->getParameter('working_end');

        if (Lib\Utils\DateTime::timeToSeconds($start_time) >= Lib\Utils\DateTime::timeToSeconds($end_time)) {
            wp_send_json_error(array('message' => __('The start time must be less than the end one', 'bookme_pro'),));
        }

        $res_schedule = new Lib\Entities\StaffScheduleItem();
        $res_schedule->load($this->getParameter('staff_schedule_item_id'));

        $break_id = $this->getParameter('break_id', 0);

        $in_working_time = $working_start <= $start_time && $start_time <= $working_end
            && $working_start <= $end_time && $end_time <= $working_end;
        if (!$in_working_time || !$res_schedule->isBreakIntervalAvailable($start_time, $end_time, $break_id)) {
            wp_send_json_error(array('message' => __('The requested interval is not available', 'bookme_pro'),));
        }

        $formatted_start = Lib\Utils\DateTime::formatTime(Lib\Utils\DateTime::timeToSeconds($start_time));
        $formatted_end = Lib\Utils\DateTime::formatTime(Lib\Utils\DateTime::timeToSeconds($end_time));
        $formatted_interval = $formatted_start . ' - ' . $formatted_end;

        if ($break_id) {
            $break = new Lib\Entities\ScheduleItemBreak();
            $break->load($break_id);
            $break->setStartTime($start_time)
                ->setEndTime($end_time)
                ->save();

            wp_send_json_success(array('interval' => $formatted_interval,));
        } else {
            $form = new Forms\StaffScheduleItemBreak();
            $form->bind($this->getPostParameters());

            $res_schedule_break = $form->save();
            if ($res_schedule_break) {
                $breakStart = new TimeChoice(array('use_empty' => false, 'type' => 'break_from'));
                $breakEnd = new TimeChoice(array('use_empty' => false, 'type' => 'to'));
                wp_send_json(array(
                    'success' => true,
                    'item_content' => $this->render('_break', array(
                        'staff_schedule_item_break_id' => $res_schedule_break->getId(),
                        'formatted_interval' => $formatted_interval,
                        'break_start_choices' => $breakStart->render('', $start_time, array('class' => 'break-start form-control')),
                        'break_end_choices' => $breakEnd->render('', $end_time, array('class' => 'break-end form-control')),
                    ), false),
                ));
            } else {
                wp_send_json_error(array('message' => __('Error adding the break interval', 'bookme_pro'),));
            }
        }
    }

    public function executeDeleteStaffScheduleBreak()
    {
        $break = new Lib\Entities\ScheduleItemBreak();
        $break->setId($this->getParameter('id', 0));
        $break->delete();

        wp_send_json_success();
    }

    public function executeStaffServicesUpdate()
    {
        $form = new Forms\StaffServices();
        $form->bind($this->getPostParameters());
        $form->save();
        wp_send_json_success();
    }

    public function executeStaffPanel()
    {
        $alert = array('error' => array());
        $staff = new Lib\Entities\Staff();
        $staff->load($this->getParameter('id'));

        $form = new Forms\StaffMemberEdit();

        $users_for_staff = Lib\Utils\Common::isCurrentUserAdmin() ? $form->getUsersForStaff($staff->getId()) : array();

        wp_send_json_success(array(
            'html' => array(
                'sidepanel' => $this->render('staff-panel', compact('staff'), false),
                'details' => $this->render('_details', compact('staff', 'users_for_staff'), false)
            ),
            'alert' => $alert,
        ));
    }

    public function executeEditStaff()
    {
        $alert = array('error' => array());
        $staff = new Lib\Entities\Staff();
        $staff->load($this->getParameter('id'));

        $form = new Forms\StaffMemberEdit();

        $users_for_staff = Lib\Utils\Common::isCurrentUserAdmin() ? $form->getUsersForStaff($staff->getId()) : array();

        wp_send_json_success(array(
            'html' => array(
                'edit' => $this->render('edit', compact('staff'), false),
                'details' => $this->render('_details', compact('staff', 'users_for_staff'), false)
            ),
            'alert' => $alert,
        ));
    }


    /**
     * Update staff from POST request.
     */
    public function executeUpdateStaff()
    {
        if (!Lib\Utils\Common::isCurrentUserAdmin()) {
            // Check permissions to prevent one staff member from updating profile of another staff member.
            do {
                if (get_option('bookme_pro_gen_allow_staff_edit_profile')) {
                    $staff = new Lib\Entities\Staff();
                    $staff->load($this->getParameter('id'));
                    if ($staff->getWpUserId() == get_current_user_id()) {
                        unset ($_POST['wp_user_id']);
                        break;
                    }
                }
                do_action('admin_page_access_denied');
                wp_die('Bookme Pro: ' . __('You do not have sufficient permissions to access this page.'));
            } while (0);
        }
        $form = new Forms\StaffMemberEdit();

        $form->bind($this->getPostParameters(), $_FILES);
        $employee = $form->save();

        Lib\Proxy\Shared::updateStaff($this->getPostParameters());

        if ($employee === false && array_key_exists('google_calendar_error', $form->getErrors())) {
            $errors = $form->getErrors();
            wp_send_json_error(array('error' => $errors['google_calendar_error']));
        } else {
            $wp_users = array();
            if (Lib\Utils\Common::isCurrentUserAdmin()) {
                $form = new Forms\StaffMember();
                $wp_users = $form->getUsersForStaff();
            }

            wp_send_json_success(compact('wp_users'));
        }
    }

    public function executeDeleteStaff()
    {
        $wp_users = array();

        if (Lib\Utils\Common::isCurrentUserAdmin()) {
            $ids = $this->getParameter('ids', array());
            if (is_array($ids) && !empty ($ids)) {
                Lib\Entities\Staff::query('s')->delete()->whereIn('s.id', $ids)->execute();
            }
            /*if ($staff = Lib\Entities\Staff::find($this->getParameter('id'))) {
                $staff->delete();
            }*/
            $form = new Forms\StaffMember();
            $wp_users = $form->getUsersForStaff();
        }

        wp_send_json_success(compact('wp_users'));
    }

    public function executeDeleteStaffAvatar()
    {
        $staff = new Lib\Entities\Staff();
        $staff->load($this->getParameter('id'));
        $staff->setAttachmentId(null);
        $staff->save();

        wp_send_json_success();
    }

    public function executeStaffHolidays()
    {
        /** @var \WP_Locale $wp_locale */
        global $wp_locale;

        $staff_id = $this->getParameter('id', 0);
        $holidays = $this->getHolidays($staff_id);
        $loading_img = plugins_url('bookme-pro-free-appointment-booking-system/backend/assets/images/loading.gif');
        $start_of_week = (int)get_option('start_of_week');
        $days = array_values($wp_locale->weekday_abbrev);
        $months = array_values($wp_locale->month);
        $close = __('Close', 'bookme_pro');
        $repeat = __('Repeat every year', 'bookme_pro');
        $we_are_not_working = __('We are not working on this day', 'bookme_pro');
        $html = $this->render('holidays', array(), false);
        wp_send_json_success(compact('html', 'holidays', 'days', 'months', 'start_of_week', 'loading_img', 'we_are_not_working', 'repeat', 'close'));
    }

    public function executeStaffHolidaysUpdate()
    {
        global $wpdb;

        $id = $this->getParameter('id');
        $holiday = $this->getParameter('holiday') == 'true';
        $repeat = $this->getParameter('repeat') == 'true';
        $day = $this->getParameter('day', false);
        $staff_id = $this->getParameter('staff_id');
        if ($staff_id) {
            // Update or delete the event.
            if ($id) {
                if ($holiday) {
                    $wpdb->update(Lib\Entities\Holiday::getTableName(), array('repeat_event' => (int)$repeat), array('id' => $id), array('%d'));
                } else {
                    Lib\Entities\Holiday::query()->delete()->where('id', $id)->execute();
                }
                // Add the new event.
            } elseif ($holiday && $day) {
                $wpdb->insert(Lib\Entities\Holiday::getTableName(), array('date' => $day, 'repeat_event' => (int)$repeat, 'staff_id' => $staff_id), array('%s', '%d', '%d'));
            }
            // And return refreshed events.
            echo json_encode($this->getHolidays($staff_id));
        }
        exit;
    }

    // Protected methods.

    protected function getHolidays($staff_id)
    {
        $collection = Lib\Entities\Holiday::query('h')->where('h.staff_id', $staff_id)->fetchArray();
        $holidays = array();
        foreach ($collection as $holiday) {
            list ($Y, $m, $d) = explode('-', $holiday['date']);
            $holidays[$holiday['id']] = array(
                'm' => (int)$m,
                'd' => (int)$d,
            );
            // if not repeated holiday, add the year
            if (!$holiday['repeat_event']) {
                $holidays[$holiday['id']]['y'] = (int)$Y;
            }
        }

        return $holidays;
    }

    /**
     * Extend parent method to control access on staff member level.
     *
     * @param string $action
     * @return bool
     */
    protected function hasAccess($action)
    {
        if (parent::hasAccess($action)) {
            if (!Lib\Utils\Common::isCurrentUserAdmin()) {
                $staff = new Lib\Entities\Staff();

                switch ($action) {
                    case 'executeGetStaff':
                        return true;
                    case 'executeEditStaff':
                    case 'executeDeleteStaffAvatar':
                    case 'executeStaffSchedule':
                    case 'executeStaffHolidays':
                    case 'executeUpdateStaff':
                    case 'executeGetStaffDetails':
                    case 'executeStaffPanel':
                        $staff->load($this->getParameter('id'));
                        break;
                    case 'executeGetStaffServices':
                    case 'executeGetStaffSchedule':
                    case 'executeStaffServicesUpdate':
                    case 'executeStaffHolidaysUpdate':
                        $staff->load($this->getParameter('staff_id'));
                        break;
                    case 'executeStaffScheduleHandleBreak':
                        $res_schedule = new Lib\Entities\StaffScheduleItem();
                        $res_schedule->load($this->getParameter('staff_schedule_item_id'));
                        $staff->load($res_schedule->getStaffId());
                        break;
                    case 'executeDeleteStaffScheduleBreak':
                        $break = new Lib\Entities\ScheduleItemBreak();
                        $break->load($this->getParameter('id'));
                        $res_schedule = new Lib\Entities\StaffScheduleItem();
                        $res_schedule->load($break->getStaffScheduleItemId());
                        $staff->load($res_schedule->getStaffId());
                        break;
                    case 'executeStaffScheduleUpdate':
                        if ($this->hasParameter('days')) {
                            foreach ($this->getParameter('days') as $id => $day_index) {
                                $res_schedule = new Lib\Entities\StaffScheduleItem();
                                $res_schedule->load($id);
                                $staff = new Lib\Entities\Staff();
                                $staff->load($res_schedule->getStaffId());
                                if ($staff->getWpUserId() != get_current_user_id()) {
                                    return false;
                                }
                            }
                        }
                        break;
                    default:
                        return false;
                }

                return $staff->getWpUserId() == get_current_user_id();
            }

            return true;
        }

        return false;
    }
}