<?php
namespace BookmePro\Frontend\Controllers\Booking;

use BookmePro\Lib;
use BookmePro\Lib\DataHolders\Booking as DataHolders;
use BookmePro\Lib\Slots\DatePoint;

/**
 * Class Controller
 * @package BookmePro\Frontend\Controllers\Booking
 */
class Controller extends Lib\Base\Controller
{
    private $info_text_codes = array();

    protected function getPermissions()
    {
        return array('_this' => 'anonymous');
    }

    /**
     * Render Bookme Pro shortcode.
     *
     * @param $attributes
     * @return string
     */
    public function renderShortCode($attributes)
    {
        global $sitepress;

        // Disable caching.
        Lib\Utils\Common::noCache();

        $assets = '';

        if (get_option('bookme_pro_gen_link_assets_method') == 'print') {
            $print_assets = !wp_script_is('bookme_pro', 'done');
            if ($print_assets) {
                ob_start();

                // The styles and scripts are registered in Frontend.php
                wp_print_styles('bookme-pro-intlTelInput');
                wp_print_styles('bookme-pro-ladda-min');
                wp_print_styles('bookme-pro-picker');
                wp_print_styles('bookme-pro-picker-date');
                wp_print_styles('bookme-pro-scroll');
                wp_print_styles('bookme-pro');
                wp_print_styles('bookme-pro-rtl');

                wp_print_scripts('bookme-pro-spin');
                wp_print_scripts('bookme-pro-ladda');
                wp_print_scripts('bookme-pro-picker');
                wp_print_scripts('bookme-pro-picker-date');
                wp_print_scripts('bookme-pro-hammer');
                wp_print_scripts('bookme-pro-jq-hammer');
                wp_print_scripts('bookme-pro-scroll');
                wp_print_scripts('bookme-pro-intlTelInput');
                wp_print_scripts('bookme-pro-tooltip');
                // Android animation.
                if (stripos(strtolower($_SERVER['HTTP_USER_AGENT']), 'android') !== false) {
                    wp_print_scripts('bookme-pro-jquery-animate-enhanced');
                }
                Lib\Proxy\Shared::printBookingAssets();
                wp_print_scripts('bookme-pro');

                $assets = ob_get_clean();
            }
        } else {
            $print_assets = true; // to print CSS in template.
        }

        // Generate unique form id.
        $form_id = uniqid();


        // Find bookings with any of payment statuses ( PayPal, 2Checkout, PayU Latam ).
        $status = array('booking' => 'new');
        foreach (Lib\Session::getAllFormsData() as $saved_form_id => $data) {
            if (isset ($data['payment'])) {
                if (!isset ($data['payment']['processed'])) {
                    switch ($data['payment']['status']) {
                        case 'success':
                        case 'processing':
                            $form_id = $saved_form_id;
                            $status = array('booking' => 'finished');
                            break;
                        case 'cancelled':
                        case 'error':
                            $form_id = $saved_form_id;
                            end($data['cart']);
                            $status = array('booking' => 'cancelled', 'cart_key' => key($data['cart']));
                            break;
                    }
                    // Mark this form as processed for cases when there are more than 1 booking form on the page.
                    $data['payment']['processed'] = true;
                    Lib\Session::setFormVar($saved_form_id, 'payment', $data['payment']);
                }
            } elseif ($data['last_touched'] + 30 * MINUTE_IN_SECONDS < time()) {
                // Destroy forms older than 30 min.
                Lib\Session::destroyFormData($saved_form_id);
            }
        }

        // Handle shortcode attributes.
        $fields_to_hide = isset ($attributes['hide']) ? explode(',', $attributes['hide']) : array();
        $staff_member_id = (int)(@$_GET['staff_id'] ?: @$attributes['staff_member_id']);

        $attrs = array(
            'location_id' => (int)(@$_GET['loc_id'] ?: @$attributes['location_id']),
            'category_id' => (int)(@$_GET['cat_id'] ?: @$attributes['category_id']),
            'service_id' => (int)(@$_GET['service_id'] ?: @$attributes['service_id']),
            'staff_member_id' => $staff_member_id,
            'hide_categories' => in_array('categories', $fields_to_hide) ? true : (bool)@$attributes['hide_categories'],
            'hide_services' => in_array('services', $fields_to_hide) ? true : (bool)@$attributes['hide_services'],
            'hide_calendar' => in_array('calendar', $fields_to_hide) ? true : (bool)@$attributes['hide_calendar'],
            'hide_staff_members' => (in_array('staff_members', $fields_to_hide) ? true : (bool)@$attributes['hide_staff_members'])
                && (get_option('bookme_pro_app_required_employee') ? $staff_member_id : true),
            'show_number_of_persons' => (bool)@$attributes['show_number_of_persons'],
            'show_service_duration' => (bool)get_option('bookme_pro_app_service_name_with_duration'),
            // Add-ons.
            'hide_locations' => true,
        );
        // Set service step attributes for Add-ons.
        if (Lib\Config::locationsEnabled()) {
            $attrs['hide_locations'] = in_array('locations', $fields_to_hide);
        }

        $service_part1 = (
            !$attrs['show_number_of_persons'] &&
            $attrs['hide_categories'] &&
            $attrs['hide_services'] &&
            $attrs['service_id'] &&
            $attrs['hide_staff_members'] &&
            $attrs['hide_locations']
        );
        $service_part2 = (int)$attrs['hide_calendar'];

        if ($service_part1 && $service_part2) {
            // Store attributes in session for later use in Time step.
            Lib\Session::setFormVar($form_id, 'attrs', $attrs);
            Lib\Session::setFormVar($form_id, 'last_touched', time());
        }

        $skip_steps = array(
            'service_part1' => (int)$service_part1,
            'service_part2' => (int)$service_part2,
        );
        // Prepare URL for AJAX requests.
        $ajax_url = admin_url('admin-ajax.php');
        // Support WPML.
        if ($sitepress instanceof \SitePress) {
            $ajax_url .= (strpos($ajax_url, '?') ? '&' : '?') . 'lang=' . $sitepress->get_current_language();
        }
        $woocommerce_enabled = (int)Lib\Config::wooCommerceEnabled();
        $options = array(
            'intlTelInput' => array('enabled' => 0),
            'woocommerce' => array('enabled' => $woocommerce_enabled, 'cart_url' => $woocommerce_enabled ? wc_get_cart_url() : ''),
            'cart' => array('enabled' => $woocommerce_enabled ? 0 : (int)Lib\Config::showStepCart()),
            'show_calendar_availability' => array('enabled' => (int)Lib\Config::showCalendarAvailability()),
            'auto_calendar_size' => array('enabled' => (int)Lib\Config::autoCalendarSize())
        );
        if (get_option('bookme_pro_cst_phone_default_country') != 'disabled') {
            $options['intlTelInput']['enabled'] = 1;
            $options['intlTelInput']['utils'] = plugins_url('intlTelInput.utils.js', Lib\Plugin::getDirectory() . '/frontend/assets/js/intlTelInput.utils.js');
            $options['intlTelInput']['country'] = get_option('bookme_pro_cst_phone_default_country');
        }
        $required = array(
            'staff' => (int)get_option('bookme_pro_app_required_employee')
        );
        if (Lib\Config::locationsEnabled()) {
            $required['location'] = (int)get_option('bookme_pro_app_required_location');
        }

        // Custom CSS.
        $custom_css = get_option('bookme_pro_app_custom_styles');

        return $assets . $this->render(
                'short_code',
                compact('attrs', 'options', 'required', 'print_assets', 'form_id', 'ajax_url', 'status', 'skip_steps', 'custom_css'),
                false
            );
    }

    /**
     * 1. Step service.
     *
     * response JSON
     */
    public function executeRenderService()
    {
        $response = null;
        $form_id = $this->getParameter('form_id');

        if ($form_id) {
            $userData = new Lib\UserBookingData($form_id);
            $userData->load();

            if ($this->hasParameter('new_chain')) {
                $userData->resetChain();
            }

            if ($this->hasParameter('edit_cart_item')) {
                $cart_key = $this->getParameter('edit_cart_item');
                $userData->set('edit_cart_keys', array($cart_key));
                $userData->setChainFromCartItem($cart_key);
            }

            if (Lib\Config::useClientTimeZone()) {
                // Client time zone.
                $userData->set('time_zone', $this->getParameter('time_zone'));
                $userData->set('time_zone_offset', $this->getParameter('time_zone_offset'));
                $userData->applyTimeZone();
                $userData->set(
                    'date_from',
                    Lib\Slots\DatePoint::now()
                        ->modify(Lib\Config::getMinimumTimePriorBooking())
                        ->toClientTz()
                        ->format('Y-m-d')
                );
            }

            $progress_tracker = $this->_prepareProgressTracker(1);
            $info_text = $this->_prepareInfoText(1, Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_info_service_step'), $userData);

            // Available days and times.
            $days_times = Lib\Config::getDaysAndTimes();

            $bounding = Lib\Config::getBoundingDaysForPickadate();

            $casest = Lib\Config::getCaSeSt();

            if (class_exists('\BookmeProLocations\Lib\Plugin', false)) {
                $locasest = $casest['locations'];
            } else {
                $locasest = array();
            }

            $response = array(
                'success' => true,
                'csrf_token' => Lib\Utils\Common::getCsrfToken(),
                'html' => $this->render('1_service', array(
                    'progress_tracker' => $progress_tracker,
                    'info_text' => $info_text,
                    'userData' => $userData,
                    'times' => $days_times['times'],
                    'show_cart_btn' => $this->_showCartButton($userData),
                    'layout' => (int)get_option('bookme_pro_app_layout')
                ), false),
                'categories' => $casest['categories'],
                'chain' => $userData->chain->getItemsData(),
                'date_max' => $bounding['date_max'],
                'date_min' => $bounding['date_min'],
                'locations' => $locasest,
                'services' => $casest['services'],
                'staff' => $casest['staff'],
            );
        } else {
            $response = array('success' => false, 'error_code' => 2, 'error' => esc_html__('Form ID error.', 'bookme_pro'));
        }

        // Output JSON response.
        wp_send_json($response);
    }

    /**
     * Get number of time slots for calendar
     *
     * response JSON
     */
    public function executeRenderAvailability()
    {
        $this->executeSessionSave(true);
        $response = null;
        $userData = new Lib\UserBookingData($this->getParameter('form_id'));
        $loaded = $userData->load();

        if (!$loaded && Lib\Session::hasFormVar($this->getParameter('form_id'), 'attrs')) {
            $loaded = true;
        }
        if ($loaded) {
            $start = DatePoint::fromStr($userData->get('date_from'));
            $max_end = $start->modify('first day of next month');
            $finder = new Lib\Slots\Finder($userData, null, array($this, 'stopAvailability'));
            $finder->prepare();
            $finder->end_dp = $max_end;
            $finder->client_end_dp = $max_end->toClientTz();
            $finder->load();
            $slots = array();
            foreach ($finder->getSlots() as $date => $slot) {
                $slots[$date] = count($slot) . ' ' . Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_calendar_availability');
            }

            // Set response.
            $response = array(
                'success' => true,
                'csrf_token' => Lib\Utils\Common::getCsrfToken(),
                'slots' => $slots,
                'start' => $start->format('Y-m-d'),
                'end' => $max_end->format('Y-m-d')
            );

        } else {
            $response = array('success' => false, 'error_code' => 1, 'error' => esc_html__('Session error.', 'bookme_pro'));
        }

        // Output JSON response.
        wp_send_json($response);
    }

    /**
     * 2. Step time.
     *
     * response JSON
     */
    public function executeRenderTime()
    {
        $response = null;
        $userData = new Lib\UserBookingData($this->getParameter('form_id'));
        $loaded = $userData->load();

        if (!$loaded && Lib\Session::hasFormVar($this->getParameter('form_id'), 'attrs')) {
            $loaded = true;
        }

        if ($loaded) {
            if ($this->hasParameter('new_chain')) {
                $this->_setDataForSkippedServiceStep($userData);
            }

            if ($this->hasParameter('edit_cart_item')) {
                $cart_key = $this->getParameter('edit_cart_item');
                $userData->set('edit_cart_keys', array($cart_key));
                $userData->setChainFromCartItem($cart_key);
            }

            $finder = new Lib\Slots\Finder($userData);
            if ($this->hasParameter('selected_date')) {
                $finder->setSelectedDate($this->getParameter('selected_date'));
            } else {
                $finder->setSelectedDate($userData->get('date_from'));
            }
            $finder->prepare()->load();

            $progress_tracker = $this->_prepareProgressTracker(2);
            $info_text = $this->_prepareInfoText(2, Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_info_time_step'), $userData);

            // Render slots by groups (day or month).
            $slots = $userData->get('slots');
            $selected_date = isset ($slots[0][2]) ? $slots[0][2] : null;
            $slots = array();
            foreach ($finder->getSlots() as $group => $group_slots) {
                $slots[$group] = preg_replace('/>\s+</', '><', $this->render('_time_slots', array(
                    'group' => $group,
                    'slots' => $group_slots,
                    'duration_in_days' => $finder->isServiceDurationInDays(),
                    'selected_date' => $selected_date,
                ), false));
            }

            // Set response.
            $response = array(
                'success' => true,
                'csrf_token' => Lib\Utils\Common::getCsrfToken(),
                'has_slots' => !empty ($slots),
                'has_more_slots' => $finder->hasMoreSlots(),
                'slots' => $slots,
                'html' => $this->render('2_time', array(
                    'progress_tracker' => $progress_tracker,
                    'info_text' => $info_text,
                    'has_slots' => !empty ($slots),
                    'arrow_img' => plugins_url('frontend/assets/images/calendar-arrow.png', Lib\Plugin::getMainFile()),
                    'show_cart_btn' => $this->_showCartButton($userData)
                ), false),
            );
        } else {
            $response = array('success' => false, 'error_code' => 1, 'error' => esc_html__('Session error.', 'bookme_pro'));
        }

        // Output JSON response.
        wp_send_json($response);
    }

    /**
     * Render next time for step Time.
     *
     * response JSON
     */
    public function executeRenderNextTime()
    {
        $userData = new Lib\UserBookingData($this->getParameter('form_id'));

        if ($userData->load()) {
            $finder = new Lib\Slots\Finder($userData);
            $finder->setLastFetchedSlot($this->getParameter('last_slot'));
            $finder->prepare()->load();

            $slots = $userData->get('slots');
            $selected_date = isset ($slots[0][2]) ? $slots[0][2] : null;
            $html = '';
            foreach ($finder->getSlots() as $group => $group_slots) {
                $html .= $this->render('_time_slots', array(
                    'group' => $group,
                    'slots' => $group_slots,
                    'duration_in_days' => $finder->isServiceDurationInDays(),
                    'selected_date' => $selected_date,
                ), false);
            }

            // Set response.
            $response = array(
                'success' => true,
                'html' => preg_replace('/>\s+</', '><', $html),
                'has_slots' => $html != '',
                'has_more_slots' => $finder->hasMoreSlots(), // show/hide the next button
            );
        } else {
            $response = array('success' => false, 'error_code' => 1, 'error' => esc_html__('Session error.', 'bookme_pro'));
        }

        // Output JSON response.
        wp_send_json($response);
    }

    /**
     * 3. Step cart.
     *
     * response JSON
     */
    public function executeRenderCart()
    {
        $userData = new Lib\UserBookingData($this->getParameter('form_id'));
        $deposit = array('show' => false,);

        if ($userData->load()) {
            if ($this->hasParameter('add_to_cart')) {
                $userData->addChainToCart();
            }
            $progress_tracker = $this->_prepareProgressTracker(3);
            $info_text = $this->_prepareInfoText(3, Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_info_cart_step'), $userData);
            $items_data = array();
            $cart_columns = get_option('bookme_pro_cart_show_columns', array());
            foreach ($userData->cart->getItems() as $cart_key => $cart_item) {
                $nop_prefix = ($cart_item->get('number_of_persons') > 1 ? '<i class="bookme-pro-icon-user"></i>' . $cart_item->get('number_of_persons') . ' &times; ' : '');
                $slots = $cart_item->get('slots');
                $service_dp = Lib\Slots\DatePoint::fromStr($slots[0][2])->toClientTz();

                foreach ($cart_columns as $column => $attr) {
                    if ($attr['show']) {
                        switch ($column) {
                            case 'service':
                                $items_data[$cart_key][] = $cart_item->getService()->getTranslatedTitle();
                                break;
                            case 'date':
                                $items_data[$cart_key][] = $service_dp->formatI18nDate();;
                                break;
                            case 'time':
                                if ($cart_item->getService()->getDuration() < DAY_IN_SECONDS) {
                                    $items_data[$cart_key][] = $service_dp->formatI18nTime();
                                } else {
                                    $items_data[$cart_key][] = '';
                                }
                                break;
                            case 'employee':
                                $items_data[$cart_key][] = $cart_item->getStaff()->getTranslatedName();
                                break;
                            case 'price':
                                if ($cart_item->get('number_of_persons') > 1) {
                                    $items_data[$cart_key][] = $nop_prefix . Lib\Utils\Price::format($cart_item->getServicePrice() - $cart_item->getExtrasAmount()) . ' = ' . Lib\Utils\Price::format(($cart_item->getServicePrice() - $cart_item->getExtrasAmount()) * $cart_item->get('number_of_persons'));
                                } else {
                                    $items_data[$cart_key][] = Lib\Utils\Price::format($cart_item->getServicePrice() - $cart_item->getExtrasAmount());
                                }
                                break;
                        }
                    }
                }
            }

            $columns = array();
            $position = 0;
            $positions = array();
            foreach ($cart_columns as $column => $attr) {
                if ($attr['show']) {
                    if ($column != 'deposit' || $deposit['show']) {
                        $positions[$column] = $position;
                    }
                    switch ($column) {
                        case 'service':
                            $columns[] = Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_label_service');
                            $position++;
                            break;
                        case 'date':
                            $columns[] = esc_html__('Date', 'bookme_pro');
                            $position++;
                            break;
                        case 'time':
                            $columns[] = esc_html__('Time', 'bookme_pro');
                            $position++;
                            break;
                        case 'employee':
                            $columns[] = Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_label_employee');
                            $position++;
                            break;
                        case 'price':
                            $columns[] = esc_html__('Price', 'bookme_pro');
                            $position++;
                            break;
                    }
                }
            }
            list($total, $amount_to_pay) = $userData->cart->getInfo(false);   // without coupon
            $deposit['to_pay'] = $amount_to_pay;
            $response = array(
                'success' => true,
                'html' => $this->render('3_cart', array(
                    'progress_tracker' => $progress_tracker,
                    'info_text' => $info_text,
                    'items_data' => $items_data,
                    'columns' => $columns,
                    'deposit' => $deposit,
                    'positions' => $positions,
                    'total' => $total,
                    'cart_items' => $userData->cart->getItems(),
                ), false),
            );
        } else {
            $response = array('success' => false, 'error_code' => 1, 'error' => esc_html__('Session error.', 'bookme_pro'));
        }

        // Output JSON response.
        wp_send_json($response);
    }

    /**
     * 4. Step details & payment.
     *
     * response JSON
     */
    public function executeRenderDetails()
    {
        $form_id = $this->getParameter('form_id');
        $userData = new Lib\UserBookingData($form_id);

        if ($userData->load()) {
            if (!Lib\Config::showStepCart()) {
                $userData->addChainToCart();
                $userData->saveInSession();
            }

            $cf_data = array();
            if (Lib\Config::customFieldsPerService()) {
                // Prepare custom fields data per service.
                foreach ($userData->cart->getItems() as $cart_key => $cart_item) {
                    $data = array();
                    if ($cart_item->getService()->getType() == Lib\Entities\Service::TYPE_COMPOUND) {
                        $service_id = current($cart_item->getService()->getSubServices())->getId();
                    } else {
                        $service_id = $cart_item->get('service_id');
                    }
                    $key = get_option('bookme_pro_custom_fields_merge_repetitive') ? $service_id : $cart_key;

                    if (!isset($cf_data[$key])) {
                        foreach ($cart_item->get('custom_fields') as $field) {
                            $data[$field['id']] = $field['value'];
                        }
                        $cf_data[$key] = array(
                            'service_title' => Lib\Entities\Service::find($cart_item->get('service_id'))->getTranslatedTitle(),
                            'custom_fields' => Lib\Utils\Common::getTranslatedCustomFields($service_id),
                            'data' => $data,
                        );
                    }
                }
            } else {
                $cart_items = $userData->cart->getItems();
                $cart_item = array_pop($cart_items);
                $data = array();
                foreach ($cart_item->get('custom_fields') as $field) {
                    $data[$field['id']] = $field['value'];
                }
                $cf_data[] = array(
                    'custom_fields' => Lib\Utils\Common::getTranslatedCustomFields(null),
                    'data' => $data,
                );
            }


            $info_text = Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_info_details_step');
            $info_text_guest = !get_current_user_id() ? Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_info_details_step_guest') : '';

            $booking_data = array();
            /** @var Lib\CartItem $cart_item */
            foreach ($userData->cart->getItems() as $cart_item) {
                $service = $cart_item->getService();
                $slot = $cart_item->get('slots');
                $service_dp = Lib\Slots\DatePoint::fromStr($slot[0][2])->toClientTz();
                $b_data = array();
                $b_data['category_name'] = $service->getTranslatedCategoryName();
                $b_data['number_of_persons'] = $cart_item->get('number_of_persons');
                $b_data['service_date'] = $service_dp->formatI18nDate();
                $b_data['service_info'] = $service->getTranslatedInfo();
                $b_data['service_name'] = $service->getTranslatedTitle();
                $b_data['service_price'] = Lib\Utils\Price::format($cart_item->getServicePrice() * $cart_item->get('number_of_persons'));
                $b_data['service_time'] = $service_dp->formatI18nTime();
                $b_data['staff_info'] = $cart_item->getStaff()->getTranslatedInfo();
                $b_data['staff_name'] = $cart_item->getStaff()->getTranslatedName();
                $booking_data[] = $b_data;
            }

            $payment_disabled = Lib\Config::paymentStepDisabled();
            list ($total, $deposit, , $sub_total, $discount_price) = $userData->cart->getInfo();
            if ($deposit <= 0) {
                $payment_disabled = true;
            }

            if ($payment_disabled == false) {
                // Render main template.
                $html = $this->render('4_details', array(
                    'disabled' => false,
                    'progress_tracker' => $this->_prepareProgressTracker(4),
                    'info_text' => $this->_prepareInfoText(4, $info_text, $userData),
                    'info_text_guest' => $this->_prepareInfoText(4, $info_text_guest, $userData),
                    'userData' => $userData,
                    'cf_data' => $cf_data,

                    'form_id' => $form_id,
                    'booking_data' => $booking_data,
                    'total' => Lib\Utils\Price::format($total),
                    'sub_total' => Lib\Utils\Price::format($sub_total),
                    'discount_price' => Lib\Utils\Price::format($discount_price),
                    'pay_local' => Lib\Config::paymentTypeEnabled(Lib\Entities\Payment::TYPE_LOCAL),
                    'page_url' => $this->getParameter('page_url'),
                ), false);
            } else {
                // Render main template.
                $html = $this->render('4_details', array(
                    'disabled' => true,
                    'progress_tracker' => $this->_prepareProgressTracker(4),
                    'info_text' => $this->_prepareInfoText(4, $info_text, $userData),
                    'info_text_guest' => $this->_prepareInfoText(4, $info_text_guest, $userData),
                    'userData' => $userData,
                    'cf_data' => $cf_data,
                ), false);
            }

            // Render additional templates.
            $html .= $this->render('_customer_duplicate_msg', array(), false);
            if (
                !get_current_user_id() && (
                    get_option('bookme_pro_app_show_login_button') ||
                    strpos($info_text . $info_text_guest, '{login_form}') !== false
                )
            ) {
                $html .= $this->render('_login_form', array(), false);
            }

            $response = array(
                'success' => true,
                'disabled' => $payment_disabled,
                'html' => $html,
            );
        } else {
            $response = array('success' => false, 'error_code' => 1, 'error' => esc_html__('Session error.', 'bookme_pro'));
        }

        // Output JSON response.
        wp_send_json($response);
    }

    /**
     * 5. Step done ( complete ).
     *
     * response JSON
     */
    public function executeRenderComplete()
    {
        $userData = new Lib\UserBookingData($this->getParameter('form_id'));
        $errors = $this->getParameter('errors', array());
        if ($userData->load()) {
            $progress_tracker = $this->_prepareProgressTracker(5);
            if (empty($errors)) {
                $payment = $userData->extractPaymentStatus();
                do {
                    if ($payment) {
                        switch ($payment['status']) {
                            case 'processing':
                                $info_text = $this->_prepareInfoText(5, Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_info_complete_step_processing'), $userData);
                                break (2);
                        }
                    }
                    $info_text = $this->_prepareInfoText(5, Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_info_complete_step'), $userData);
                } while (0);

                $response = array(
                    'success' => true,
                    'html' => $this->render('5_complete', array(
                        'progress_tracker' => $progress_tracker,
                        'info_text' => $info_text,
                    ), false),
                );
            } else {
                $response = array(
                    'success' => true,
                    'html' => $this->render('5_complete', array(
                        'progress_tracker' => $progress_tracker,
                        'info_text' => $this->_prepareInfoText(5, Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_info_complete_step_limit_error'), $userData),
                    ), false),
                );
            }
        } else {
            $response = array('success' => false, 'error_code' => 1, 'error' => esc_html__('Session error.', 'bookme_pro'));
        }

        // Output JSON response.
        wp_send_json($response);
    }


    /**
     * Custom stop function for time slots
     * @param DatePoint $dp
     * @param $srv_duration_days
     * @param $slots_count
     * @return int
     */
    public function stopAvailability(DatePoint $dp, $srv_duration_days, $slots_count)
    {
        $userData = new Lib\UserBookingData($this->getParameter('form_id'));
        $userData->load();
        $end = DatePoint::fromStr($userData->get('date_from'));
        return $dp->gt($end->modify('first day of next month')->toClientTz()) ? 1 : 0;
    }

    /**
     * Save booking data in session.
     * @param bool $only_save
     */
    public function executeSessionSave($only_save = false)
    {
        $form_id = $this->getParameter('form_id');
        $errors = array();
        $userData = null;
        if ($form_id) {
            $userData = new Lib\UserBookingData($form_id);
            $userData->load();
            $parameters = $this->getParameters();
            $errors = $userData->validate($parameters);
            if (empty ($errors)) {
                if ($this->hasParameter('extras')) {
                    $parameters['chain'] = $userData->chain->getItemsData();
                    foreach ($parameters['chain'] as $key => &$item) {
                        // Decode extras.
                        $item['extras'] = json_decode($parameters['extras'][$key], true);
                    }
                } elseif ($this->hasParameter('slots')) {
                    // Decode slots.
                    $parameters['slots'] = json_decode($parameters['slots'], true);
                } elseif ($this->hasParameter('captcha_ids')) {
                    $parameters['captcha_ids'] = json_decode($parameters['captcha_ids'], true);
                    foreach ($parameters['cart'] as &$service) {
                        // Remove captcha from custom fields.
                        $custom_fields = array_filter(json_decode($service['custom_fields'], true), function ($field) use ($parameters) {
                            return !in_array($field['id'], $parameters['captcha_ids']);
                        });
                        // Index the array numerically.
                        $service['custom_fields'] = array_values($custom_fields);
                    }
                    $merge_cf = (int)get_option('bookme_pro_custom_fields_merge_repetitive');
                    // Copy custom fields to all cart items.
                    $cart = array();
                    foreach ($userData->cart->getItems() as $cart_key => $_cart_item) {
                        $cart[$cart_key] = Lib\Config::customFieldsPerService()
                            ? $parameters['cart'][$merge_cf ? $_cart_item->getService()->getId() : $cart_key]
                            : $parameters['cart'][0];
                    }
                    $parameters['cart'] = $cart;
                }
                $userData->fillData($parameters);
            }
        }
        if ($only_save) {
            $userData->saveInSession();
        } else {
            $errors['success'] = empty($errors);
            wp_send_json($errors);
        }
    }

    /**
     * Save cart appointments.
     */
    public function executeSaveAppointment()
    {
        $userData = new Lib\UserBookingData($this->getParameter('form_id'));

        if ($userData->load()) {
            $failed_cart_key = $userData->cart->getFailedKey();
            if ($failed_cart_key === null) {
                list($total, $deposit) = $userData->cart->getInfo();
                $is_payment_disabled = Lib\Config::paymentStepDisabled();
                $is_pay_locally_enabled = Lib\Config::paymentTypeEnabled(Lib\Entities\Payment::TYPE_LOCAL);
                if ($is_payment_disabled || $is_pay_locally_enabled || $deposit <= 0) {
                    // Handle coupon.
                    $coupon = $userData->getCoupon();
                    if ($coupon) {
                        $coupon->claim();
                        $coupon->save();
                    }
                    // Handle payment.
                    $payment = null;
                    if (!$is_payment_disabled) {
                        if ($coupon && $deposit <= 0) {
                            // Create fake payment record for 100% discount coupons.
                            $payment = new Lib\Entities\Payment();
                            $payment
                                ->setStatus(Lib\Entities\Payment::STATUS_COMPLETED)
                                ->setPaidType(Lib\Entities\Payment::PAY_IN_FULL)
                                ->setCreated(current_time('mysql'))
                                ->setType(Lib\Entities\Payment::TYPE_COUPON)
                                ->setTotal(0)
                                ->setPaid(0)
                                ->save();
                        } elseif ($is_pay_locally_enabled && $deposit > 0) {
                            // Create record for local payment.
                            $payment = new Lib\Entities\Payment();
                            $payment
                                ->setStatus(Lib\Entities\Payment::STATUS_PENDING)
                                ->setPaidType(Lib\Entities\Payment::PAY_IN_FULL)
                                ->setCreated(current_time('mysql'))
                                ->setType(Lib\Entities\Payment::TYPE_LOCAL)
                                ->setTotal($total)
                                ->setPaid(0)
                                ->save();
                        }
                    }
                    // Save cart.
                    $order = $userData->save($payment);
                    // Send notifications.
                    Lib\NotificationSender::sendFromCart($order);
                    if ($payment !== null) {
                        $payment->setDetails($order, $coupon)->save();
                    }
                    $response = array(
                        'success' => true,
                    );
                } else {
                    $response = array(
                        'success' => false,
                        'error_code' => 4,
                        'error' => esc_html__('Pay locally is not available.', 'bookme_pro'),
                    );
                }
            } else {
                $response = array(
                    'success' => false,
                    'failed_cart_key' => $failed_cart_key,
                    'error_code' => 3,
                    'error' => Lib\Utils\Common::getTranslatedOption(Lib\Config::showStepCart() ? 'bookme_pro_l10n_step_cart_slot_not_available' : 'bookme_pro_l10n_step_time_slot_not_available'),
                );
            }
        } else {
            $response = array('success' => false, 'error_code' => 1, 'error' => esc_html__('Session error.', 'bookme_pro'));
        }

        wp_send_json($response);
    }

    /**
     * Save cart items as pending appointments.
     */
    public function executeSavePendingAppointment()
    {
        if (
            Lib\Config::paymentTypeEnabled(Lib\Entities\Payment::TYPE_PAYULATAM) ||
            Lib\Config::getPaymentTypeOption(Lib\Entities\Payment::TYPE_PAYPAL) == Lib\Payment\PayPal::TYPE_PAYMENTS_STANDARD
        ) {
            $userData = new Lib\UserBookingData($this->getParameter('form_id'));
            if ($userData->load()) {
                $failed_cart_key = $userData->cart->getFailedKey();
                if ($failed_cart_key === null) {
                    $coupon = $userData->getCoupon();
                    if ($coupon) {
                        $coupon->claim();
                        $coupon->save();
                    }
                    list ($total, $deposit) = $userData->cart->getInfo();
                    $payment = new Lib\Entities\Payment();
                    $payment
                        ->setType($this->getParameter('payment_type'))
                        ->setStatus(Lib\Entities\Payment::STATUS_PENDING)
                        ->setTotal($total)
                        ->setPaid($deposit)
                        ->setCreated(current_time('mysql'))
                        ->save();
                    $payment_id = $payment->getId();
                    $order = $userData->save($payment);
                    $payment->setDetails($order, $coupon)->save();
                    $response = array(
                        'success' => true,
                        'payment_id' => $payment_id,
                    );
                } else {
                    $response = array(
                        'success' => false,
                        'failed_cart_key' => $failed_cart_key,
                        'error_code' => 3,
                        'error' => Lib\Utils\Common::getTranslatedOption(Lib\Config::showStepCart() ? 'bookme_pro_l10n_step_cart_slot_not_available' : 'bookme_pro_l10n_step_time_slot_not_available'),
                    );
                }
            } else {
                $response = array('success' => false, 'error_code' => 1, 'error' => esc_html__('Session error.', 'bookme_pro'));
            }
        } else {
            $response = array('success' => false, 'error_code' => 5, 'error' => esc_html__('Invalid gateway.', 'bookme_pro'));
        }

        wp_send_json($response);
    }

    public function executeCheckCart()
    {
        $userData = new Lib\UserBookingData($this->getParameter('form_id'));

        if ($userData->load()) {
            $failed_cart_key = $userData->cart->getFailedKey();
            if ($failed_cart_key === null) {
                $response = array('success' => true);
            } else {
                $response = array(
                    'success' => false,
                    'failed_cart_key' => $failed_cart_key,
                    'error_code' => 3,
                    'error' => Lib\Config::showStepCart()
                        ? Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_step_time_slot_not_available')
                        : Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_step_cart_slot_not_available')
                );
            }
        } else {
            $response = array('success' => false, 'error_code' => 5, 'error' => esc_html__('Invalid gateway.', 'bookme_pro'));
        }

        wp_send_json($response);
    }

    /**
     * Cancel Appointment using token.
     */
    public function executeCancelAppointment()
    {
        $customer_appointment = new Lib\Entities\CustomerAppointment();

        $allow_cancel = true;
        if ($customer_appointment->loadBy(array('token' => $this->getParameter('token')))) {
            $appointment = new Lib\Entities\Appointment();
            $minimum_time_prior_cancel = (int)get_option('bookme_pro_gen_min_time_prior_cancel', 0);
            if ($minimum_time_prior_cancel > 0
                && $appointment->load($customer_appointment->getAppointmentId())
            ) {
                $allow_cancel_time = strtotime($appointment->getStartDate()) - $minimum_time_prior_cancel * HOUR_IN_SECONDS;
                if (current_time('timestamp') > $allow_cancel_time) {
                    $allow_cancel = false;
                }
            }
            if ($allow_cancel) {
                $customer_appointment->cancel();
            }
        }

        if ($url = $allow_cancel ? get_option('bookme_pro_url_cancel_page_url') : get_option('bookme_pro_url_cancel_denied_page_url')) {
            wp_redirect($url);
            $this->render('redirection', compact('url'));
            exit;
        }

        $url = home_url();
        if (isset ($_SERVER['HTTP_REFERER'])) {
            if (parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) == parse_url($url, PHP_URL_HOST)) {
                // Redirect back if user came from our site.
                $url = $_SERVER['HTTP_REFERER'];
            }
        }
        wp_redirect($url);
        $this->render('redirection', compact('url'));
        exit;
    }

    /**
     * Approve appointment using token.
     */
    public function executeApproveAppointment()
    {
        $url = get_option('bookme_pro_url_approve_denied_page_url');

        // Decode token.
        $token = Lib\Utils\Common::xorDecrypt($this->getParameter('token'), 'approve');
        $ca_to_approve = new Lib\Entities\CustomerAppointment();
        if ($ca_to_approve->loadBy(array('token' => $token))) {
            $success = true;
            $updates = array();
            /** @var Lib\Entities\CustomerAppointment[] $ca_list */
            if ($ca_to_approve->getCompoundToken() != '') {
                $ca_list = Lib\Entities\CustomerAppointment::query()
                    ->where('compound_token', $ca_to_approve->getCompoundToken())
                    ->find();
            } else {
                $ca_list = array($ca_to_approve);
            }
            // Check that all items can be switched to approved.
            foreach ($ca_list as $ca) {
                $ca_status = $ca->getStatus();
                if ($ca_status != Lib\Entities\CustomerAppointment::STATUS_APPROVED) {
                    if ($ca_status != Lib\Entities\CustomerAppointment::STATUS_CANCELLED &&
                        $ca_status != Lib\Entities\CustomerAppointment::STATUS_REJECTED
                    ) {
                        $appointment = new Lib\Entities\Appointment();
                        $appointment->load($ca->getAppointmentId());
                        if ($ca_status == Lib\Entities\CustomerAppointment::STATUS_WAITLISTED) {
                            $info = $appointment->getNopInfo();
                            if ($info['total_nop'] + $ca->getNumberOfPersons() > $info['capacity_max']) {
                                $success = false;
                                break;
                            }
                        }
                        $updates[] = array($ca, $appointment);
                    } else {
                        $success = false;
                        break;
                    }
                }
            }

            if ($success) {
                foreach ($updates as $update) {
                    /** @var Lib\Entities\CustomerAppointment $ca */
                    /** @var Lib\Entities\Appointment $appointment */
                    list ($ca, $appointment) = $update;
                    $ca->setStatus(Lib\Entities\CustomerAppointment::STATUS_APPROVED)->save();
                    $appointment->handleGoogleCalendar();
                }

                if (!empty ($updates)) {
                    $ca_to_approve->setStatus(Lib\Entities\CustomerAppointment::STATUS_APPROVED);
                    Lib\NotificationSender::sendSingle(DataHolders\Simple::create($ca_to_approve));
                }

                $url = get_option('bookme_pro_url_approve_page_url');
            }
        }

        wp_redirect($url);
        $this->render('redirection', compact('url'));
        exit (0);
    }

    /**
     * Reject appointment using token.
     */
    public function executeRejectAppointment()
    {
        $url = get_option('bookme_pro_url_reject_denied_page_url');

        // Decode token.
        $token = Lib\Utils\Common::xorDecrypt($this->getParameter('token'), 'reject');
        $ca_to_reject = new Lib\Entities\CustomerAppointment();
        if ($ca_to_reject->loadBy(array('token' => $token))) {
            $updates = array();
            /** @var Lib\Entities\CustomerAppointment[] $ca_list */
            if ($ca_to_reject->getCompoundToken() != '') {
                $ca_list = Lib\Entities\CustomerAppointment::query()
                    ->where('compound_token', $ca_to_reject->getCompoundToken())
                    ->find();
            } else {
                $ca_list = array($ca_to_reject);
            }
            // Check that all items can be switched to rejected.
            foreach ($ca_list as $ca) {
                $ca_status = $ca->getStatus();
                if ($ca_status != Lib\Entities\CustomerAppointment::STATUS_REJECTED &&
                    $ca_status != Lib\Entities\CustomerAppointment::STATUS_CANCELLED
                ) {
                    $appointment = new Lib\Entities\Appointment();
                    $appointment->load($ca->getAppointmentId());
                    $updates[] = array($ca, $appointment);
                }
            }

            foreach ($updates as $update) {
                /** @var Lib\Entities\CustomerAppointment $ca */
                /** @var Lib\Entities\Appointment $appointment */
                list ($ca, $appointment) = $update;
                $ca->setStatus(Lib\Entities\CustomerAppointment::STATUS_REJECTED)->save();
                $appointment->handleGoogleCalendar();
            }

            if (!empty ($updates)) {
                $ca_to_reject->setStatus(Lib\Entities\CustomerAppointment::STATUS_REJECTED);
                Lib\NotificationSender::sendSingle(DataHolders\Simple::create($ca_to_reject));
                $url = get_option('bookme_pro_url_reject_page_url');
            }
        }

        wp_redirect($url);
        $this->render('redirection', compact('url'));
        exit (0);
    }

    /**
     * Apply coupon
     */
    public function executeApplyCoupon()
    {
        if (!get_option('bookme_pro_pmt_coupons')) {
            wp_send_json_error();
        }

        $response = null;
        $userData = new Lib\UserBookingData($this->getParameter('form_id'));

        if ($userData->load()) {
            $coupon_code = $this->getParameter('coupon');

            $coupon = new Lib\Entities\Coupon();
            $coupon->loadBy(array(
                'code' => $coupon_code,
            ));


            if ($coupon->isLoaded() && $coupon->getUsed() < $coupon->getUsageLimit()) {
                $service_ids = array();
                foreach ($userData->cart->getItems() as $item) {
                    $service_ids[] = $item->get('service_id');
                }
                if ($coupon->valid($service_ids)) {
                    $userData->fillData(array('coupon' => $coupon_code));
                    list ($total, $deposit, , $sub_total, $discount_price) = $userData->cart->getInfo();
                    $response = array(
                        'success' => true,
                        'total_simple' => $deposit,
                        'total' => Lib\Utils\Price::format($deposit),
                        'discount' => Lib\Utils\Price::format($discount_price)
                    );
                } else {
                    $userData->fillData(array('coupon' => null));
                    $response = array(
                        'success' => false,
                        'error_code' => 6,
                        'error' => esc_html__('This coupon code is invalid or has been used', 'bookme_pro'),
                    );
                }
            } else {
                $userData->fillData(array('coupon' => null));
                $response = array(
                    'success' => false,
                    'error_code' => 6,
                    'error' => esc_html__('This coupon code is invalid or has been used', 'bookme_pro'),
                );
            }
        } else {
            $response = array('success' => false, 'error_code' => 1, 'error' => esc_html__('Session error.', 'bookme_pro'));
        }

        // Output JSON response.
        wp_send_json($response);
    }

    /**
     * Log in to WordPress in the Details step.
     */
    public function executeWpUserLogin()
    {
        $response = null;
        $userData = new Lib\UserBookingData($this->getParameter('form_id'));

        if ($userData->load()) {
            add_action('set_logged_in_cookie', function ($logged_in_cookie) {
                $_COOKIE[LOGGED_IN_COOKIE] = $logged_in_cookie;
            });
            /** @var \WP_User $user */
            $user = wp_signon();
            if (is_wp_error($user)) {
                $response = array('success' => false, 'error_code' => 8, 'error' => esc_html__('Incorrect username or password.'));
            } else {
                wp_set_current_user($user->ID, $user->user_login);
                $customer = new Lib\Entities\Customer();
                if ($customer->loadBy(array('wp_user_id' => $user->ID))) {
                    $user_info = array(
                        'email' => $customer->getEmail(),
                        'full_name' => $customer->getFullName(),
                        'first_name' => $customer->getFirstName(),
                        'last_name' => $customer->getLastName(),
                        'phone' => $customer->getPhone(),
                        'csrf_token' => Lib\Utils\Common::getCsrfToken(),
                    );
                } else {
                    $user_info = array(
                        'email' => $user->user_email,
                        'full_name' => $user->display_name,
                        'first_name' => $user->user_firstname,
                        'last_name' => $user->user_lastname,
                        'csrf_token' => Lib\Utils\Common::getCsrfToken(),
                    );
                }
                $userData->fillData($user_info);
                $response = array(
                    'success' => true,
                    'data' => $user_info,
                );
            }
        } else {
            $response = array('success' => false, 'error_code' => 1, 'error' => esc_html__('Session error.', 'bookme_pro'));
        }

        // Output JSON response.
        wp_send_json($response);
    }

    /**
     * Drop cart item.
     */
    public function executeCartDropItem()
    {
        $userData = new Lib\UserBookingData($this->getParameter('form_id'));
        $total = $deposit = 0;
        if ($userData->load()) {
            $cart_key = $this->getParameter('cart_key');
            $edit_cart_keys = $userData->get('edit_cart_keys');

            $userData->cart->drop($cart_key);
            if (($idx = array_search($cart_key, $edit_cart_keys)) !== false) {
                unset ($edit_cart_keys[$idx]);
                $userData->set('edit_cart_keys', $edit_cart_keys);
            }

            list($total, $deposit) = $userData->cart->getInfo();
        }
        wp_send_json_success(
            array(
                'total_price' => Lib\Utils\Price::format($total),
                'total_deposit_price' => Lib\Utils\Price::format($deposit)
            )
        );
    }

    /**
     * Render progress tracker into a variable.
     *
     * @param int $step
     * @return string
     */
    private function _prepareProgressTracker($step)
    {
        $result = '';

        if (get_option('bookme_pro_app_show_progress_tracker')) {
            $result = $this->render('_progress_tracker', array(
                'step' => $step,
                'show_cart' => Lib\Config::showStepCart(),
                'skip_service_step' => Lib\Session::hasFormVar($this->getParameter('form_id'), 'attrs')
            ), false);
        }

        return $result;
    }

    /**
     * Render info text into a variable.
     *
     * format codes {code}
     *
     * @param integer $step
     * @param string $text
     * @param Lib\UserBookingData $userData
     * @return string
     */
    private function _prepareInfoText($step, $text, $userData)
    {
        if (empty ($this->info_text_codes)) {
            if ($step == 1) {
                // No replacements.
            } elseif ($step < 3) {
                $data = array(
                    'category_names' => array(),
                    'numbers_of_persons' => array(),
                    'service_date' => '',
                    'service_info' => array(),
                    'service_names' => array(),
                    'service_prices' => array(),
                    'service_time' => '',
                    'staff_info' => array(),
                    'staff_names' => array(),
                    'total_price' => 0,
                );

                /** @var Lib\ChainItem $chain_item */
                foreach ($userData->chain->getItems() as $chain_item) {
                    $data['numbers_of_persons'][] = $chain_item->get('number_of_persons');
                    /** @var Lib\Entities\Service $service */
                    $service = Lib\Entities\Service::find($chain_item->get('service_id'));
                    $data['service_names'][] = $service->getTranslatedTitle();
                    $data['service_info'][] = $service->getTranslatedInfo();
                    $data['category_names'][] = $service->getTranslatedCategoryName();
                    /** @var Lib\Entities\Staff $staff */
                    $staff = null;
                    $staff_ids = $chain_item->get('staff_ids');
                    if (count($staff_ids) == 1) {
                        $staff = Lib\Entities\Staff::find($staff_ids[0]);
                    }
                    if ($staff) {
                        $data['staff_names'][] = $staff->getTranslatedName();
                        $data['staff_info'][] = $staff->getTranslatedInfo();
                        if ($service->getType() == Lib\Entities\Service::TYPE_COMPOUND) {
                            $price = $service->getPrice();
                        } else {
                            $staff_service = new Lib\Entities\StaffService();
                            $staff_service->loadBy(array(
                                'staff_id' => $staff->getId(),
                                'service_id' => $service->getId(),
                            ));
                            $price = $staff_service->getPrice();
                        }
                    } else {
                        $data['staff_names'][] = esc_html__('Any', 'bookme_pro');
                        $price = false;
                    }
                    $data['service_prices'][] = $price !== false ? Lib\Utils\Price::format($price) : '-';
                    $data['total_price'] += $price * $chain_item->get('number_of_persons');
                }

                $this->info_text_codes = array(
                    '{category_name}' => '<b>' . implode(', ', $data['category_names']) . '</b>',
                    '{number_of_persons}' => '<b>' . implode(', ', $data['numbers_of_persons']) . '</b>',
                    '{service_date}' => '<b>' . $data['service_date'] . '</b>',
                    '{service_info}' => '<b>' . implode(', ', $data['service_info']) . '</b>',
                    '{service_name}' => '<b>' . implode(', ', $data['service_names']) . '</b>',
                    '{service_price}' => '<b>' . implode(', ', $data['service_prices']) . '</b>',
                    '{service_time}' => '<b>' . $data['service_time'] . '</b>',
                    '{staff_info}' => '<b>' . implode(', ', $data['staff_info']) . '</b>',
                    '{staff_name}' => '<b>' . implode(', ', $data['staff_names']) . '</b>',
                    '{total_price}' => '<b>' . Lib\Utils\Price::format($data['total_price']) . '</b>',
                );
            } else {
                $data = array(
                    'booking_number' => $userData->getBookingNumbers(),
                    'category_name' => array(),
                    'extras' => array(),
                    'number_of_persons' => array(),
                    'service' => array(),
                    'service_date' => array(),
                    'service_info' => array(),
                    'service_name' => array(),
                    'service_price' => array(),
                    'service_time' => array(),
                    'staff_info' => array(),
                    'staff_name' => array(),
                );
                /** @var Lib\CartItem $cart_item */
                foreach ($userData->cart->getItems() as $cart_item) {
                    $service = $cart_item->getService();
                    $slot = $cart_item->get('slots');
                    $service_dp = Lib\Slots\DatePoint::fromStr($slot[0][2])->toClientTz();

                    $data['category_name'][] = $service->getTranslatedCategoryName();
                    $data['number_of_persons'][] = $cart_item->get('number_of_persons');
                    $data['service_date'][] = $service_dp->formatI18nDate();
                    $data['service_info'][] = $service->getTranslatedInfo();
                    $data['service_name'][] = $service->getTranslatedTitle();
                    $data['service_price'][] = Lib\Utils\Price::format($cart_item->getServicePrice());
                    $data['service_time'][] = $service_dp->formatI18nTime();
                    $data['staff_info'][] = $cart_item->getStaff()->getTranslatedInfo();
                    $data['staff_name'][] = $cart_item->getStaff()->getTranslatedName();

                }

                list ($total) = $userData->cart->getInfo($step >= 4);  // >= step payment

                $this->info_text_codes = array(
                    '{appointments_count}' => '<b>' . count($userData->cart->getItems()) . '</b>',
                    '{booking_number}' => '<b>' . implode(', ', $data['booking_number']) . '</b>',
                    '{category_name}' => '<b>' . implode(', ', $data['category_name']) . '</b>',
                    '{number_of_persons}' => '<b>' . implode(', ', $data['number_of_persons']) . '</b>',
                    '{service_date}' => '<b>' . implode(', ', $data['service_date']) . '</b>',
                    '{service_info}' => '<b>' . implode(', ', $data['service_info']) . '</b>',
                    '{service_name}' => '<b>' . implode(', ', $data['service_name']) . '</b>',
                    '{service_price}' => '<b>' . implode(', ', $data['service_price']) . '</b>',
                    '{service_time}' => '<b>' . implode(', ', $data['service_time']) . '</b>',
                    '{staff_info}' => '<b>' . implode(', ', $data['staff_info']) . '</b>',
                    '{staff_name}' => '<b>' . implode(', ', $data['staff_name']) . '</b>',
                    '{total_price}' => '<b>' . Lib\Utils\Price::format($total) . '</b>',
                );
                if ($step == 4) {
                    $this->info_text_codes['{login_form}'] = !get_current_user_id()
                        ? sprintf('<a class="bookme-pro-js-login-show" href="#">%s</a>', esc_html__('Log In'))
                        : '';
                }
            }
        }

        return strtr(nl2br($text), $this->info_text_codes);
    }

    /**
     * Check if cart button should be shown.
     *
     * @param Lib\UserBookingData $userData
     * @return bool
     */
    private function _showCartButton(Lib\UserBookingData $userData)
    {
        return Lib\Config::showStepCart() && count($userData->cart->getItems());
    }

    /**
     * Add data for the skipped Service step.
     *
     * @param Lib\UserBookingData $userData
     */
    private function _setDataForSkippedServiceStep(Lib\UserBookingData $userData)
    {
        // Staff ids.
        $attrs = Lib\Session::getFormVar($this->getParameter('form_id'), 'attrs');
        if ($attrs['staff_member_id'] == 0) {
            $staff_ids = array_map(function ($staff) {
                return $staff['id'];
            }, Lib\Entities\StaffService::query()
                ->select('staff_id AS id')
                ->where('service_id', $attrs['service_id'])
                ->fetchArray()
            );
        } else {
            $staff_ids = array($attrs['staff_member_id']);
        }
        // Date.
        $date_from = Lib\Slots\DatePoint::now()->modify(Lib\Config::getMinimumTimePriorBooking());
        if (Lib\Config::useClientTimeZone()) {
            // Client time zone.
            $userData->set('time_zone', $this->getParameter('time_zone'));
            $userData->set('time_zone_offset', $this->getParameter('time_zone_offset'));
            $userData->applyTimeZone();
            $date_from = $date_from->toClientTz();
        }
        // Days and times.
        $days_times = Lib\Config::getDaysAndTimes();
        $time_from = key($days_times['times']);
        end($days_times['times']);

        $userData->chain->clear();
        $chain_item = new Lib\ChainItem();
        $chain_item->set('number_of_persons', 1);
        $chain_item->set('quantity', 1);
        $chain_item->set('service_id', $attrs['service_id']);
        $chain_item->set('staff_ids', $staff_ids);
        $chain_item->set('location_id', $attrs['location_id'] ?: null);
        $userData->chain->add($chain_item);

        $userData->fillData(array(
            'date_from' => $date_from->format('Y-m-d'),
            'days' => array_keys($days_times['days']),
            'edit_cart_keys' => array(),
            'slots' => array(),
            'time_from' => $time_from,
            'time_to' => key($days_times['times']),
        ));
    }

    /**
     * Override parent method to register 'wp_ajax_nopriv_' actions too.
     *
     * @param bool $with_nopriv
     */
    protected function registerWpAjaxActions($with_nopriv = false)
    {
        parent::registerWpAjaxActions(true);
    }

    /**
     * Override parent method to exclude actions from CSRF token verification.
     *
     * @param string $action
     * @return bool
     */
    protected function csrfTokenValid($action = null)
    {
        $excluded_actions = array(
            'executeApproveAppointment',
            'executeCancelAppointment',
            'executeRejectAppointment',
            'executeRenderService',
            'executeRenderExtras',
            'executeRenderTime',
        );

        return in_array($action, $excluded_actions) || parent::csrfTokenValid($action);
    }
}