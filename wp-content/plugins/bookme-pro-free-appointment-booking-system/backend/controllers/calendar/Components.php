<?php
namespace BookmePro\Backend\Controllers\Calendar;

use BookmePro\Lib;

/**
 * Class Components
 * @package BookmePro\Backend\Controllers\Calendar
 */
class Components extends Lib\Base\Components
{
    /**
     * Render appointment dialog.
     * @throws \Exception
     */
    public function renderAppointmentDialog()
    {
        global $wp_locale;

        $this->enqueueStyles(array(
            'backend' => array('css/jquery-ui-theme/jquery-ui.min.css', 'css/select2.min.css'),
            'frontend' => array('css/ladda.min.css',),
        ));

        $this->enqueueScripts(array(
            'backend' => array(
                'js/angular.min.js' => array('jquery', 'moment'),
                'js/angular-ui-date-0.0.8.js' => array('bookme-pro-angular.min.js'),
                'js/select2.full.min.js' => array('jquery'),
                'js/help.js' => array('jquery'),
            ),
            'frontend' => array(
                'js/spin.min.js' => array('jquery'),
                'js/ladda.min.js' => array('jquery'),
            ),
            'module' => array(
                'js/ng-appointment_dialog.js' => array('bookme-pro-angular-ui-date-0.0.8.js', 'jquery-ui-datepicker'),
            )
        ));

        wp_localize_script('bookme-pro-ng-appointment_dialog.js', 'BookmeProL10nAppDialog', array(
            'csrf_token' => Lib\Utils\Common::getCsrfToken(),
            'dateOptions' => array(
                'dateFormat' => Lib\Utils\DateTime::convertFormat('date', Lib\Utils\DateTime::FORMAT_JQUERY_DATEPICKER),
                'monthNamesShort' => array_values($wp_locale->month_abbrev),
                'monthNames' => array_values($wp_locale->month),
                'dayNamesMin' => array_values($wp_locale->weekday_abbrev),
                'longDays' => array_values($wp_locale->weekday),
                'firstDay' => (int)get_option('start_of_week'),
            ),
            'cf_per_service' => (int)Lib\Config::customFieldsPerService(),
            'no_result_found' => esc_html__('No result found', 'bookme_pro'),
            'staff_any' => get_option('bookme_pro_l10n_option_employee'),
            'title' => array(
                'edit_appointment' => esc_html__('Edit booking', 'bookme_pro'),
                'new_appointment' => esc_html__('New booking', 'bookme_pro'),
            ),
        ));

        // Custom fields without captcha field.
        $custom_fields = array_filter(
            json_decode(get_option('bookme_pro_custom_fields')),
            function ($field) {
                return !in_array($field->type, array('captcha', 'text-content'));
            }
        );

        $this->render('_appointment_dialog', compact('custom_fields'));
    }

    /**
     * Render delete appointment dialog
     */
    public function renderDeleteDialog()
    {
        $this->enqueueStyles(array(
            'frontend' => array('css/ladda.min.css',),
        ));

        $this->enqueueScripts(array(
            'frontend' => array(
                'js/spin.min.js' => array('jquery'),
                'js/ladda.min.js' => array('jquery'),
            ),
            'module' => array(
                'js/delete_dialog.js' => array('jquery'),
            ),
        ));
        $this->render('_delete_dialog');
    }

}