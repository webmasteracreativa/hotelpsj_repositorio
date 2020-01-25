<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
use BookmePro\Lib\Plugin;
use BookmePro\Lib\Utils\Common;
use BookmePro\Lib\Entities\CustomerAppointment;

?>
<form method="post" action="<?php echo esc_url(add_query_arg('tab', 'general')) ?>">
    <?php Common::optionToggle('bookme_pro_gen_time_slot_length', esc_html__('Time slot length', 'bookme_pro'), esc_html__('Select a time interval which will be used as a step when building all time slots in the system.', 'bookme_pro'),
        $values['bookme_pro_gen_time_slot_length']);
    Common::optionToggle('bookme_pro_gen_service_duration_as_slot_length', esc_html__('Service duration as slot length', 'bookme_pro'), esc_html__('Enable this option to make slot length equal to service duration at the Time step of booking form.', 'bookme_pro'));
    Common::optionToggle('bookme_pro_gen_default_appointment_status', esc_html__('Default appointment status', 'bookme_pro'), esc_html__('Select status for newly booked appointments.', 'bookme_pro'), array(array(CustomerAppointment::STATUS_PENDING, esc_html__('Pending', 'bookme_pro')), array(CustomerAppointment::STATUS_APPROVED, esc_html__('Approved', 'bookme_pro')),));
    Common::optionNumeric('bookme_pro_gen_max_days_for_booking', esc_html__('Number of days available for booking', 'bookme_pro'), esc_html__('Set how far in the future the clients can book appointments.', 'bookme_pro'), 1, 1);
    Common::optionToggle('bookme_pro_gen_use_client_time_zone', esc_html__('Display available time slots in client\'s time zone', 'bookme_pro'), esc_html__('The value is taken from client’s browser.', 'bookme_pro'));
    Common::optionToggle('bookme_pro_gen_allow_staff_edit_profile', esc_html__('Allow staff members to edit their profiles', 'bookme_pro'), esc_html__('If this option is enabled then all staff members who are associated with WordPress users will be able to edit their own profiles, services, schedule and days off.', 'bookme_pro'));
    Common::optionToggle('bookme_pro_gen_link_assets_method', esc_html__('Method to include Bookme Pro JavaScript and CSS files on the page', 'bookme_pro'), esc_html__('With "Enqueue" method the JavaScript and CSS files of Bookme Pro will be included on all pages of your website. This method should work with all themes. With "Print" method the files will be included only on the pages which contain Bookme Pro booking form. This method may not work with all themes.', 'bookme_pro'),
        array(array('enqueue', 'Enqueue'), array('print', 'Print')));
    ?>
    <div class="panel-footer">
        <?php Common::csrf() ?>
        <?php Common::submitButton() ?>
        <?php Common::resetButton() ?>
    </div>
</form>