<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>
<form method="post" action="<?php echo esc_url(add_query_arg('tab', 'calendar')) ?>">
    <div class="form-group"><label for="bookme_pro_appointment_participants"><?php esc_html_e('Calendar', 'bookme_pro') ?></label>
        <p class="help-block"><?php esc_html_e('Set order of the fields in calendar for', 'bookme_pro') ?></p>
        <select class="form-control" id="bookme_pro_appointment_participants">
            <option value="bookme_pro_cal_one_participant"><?php esc_html_e('Appointment with one participant', 'bookme_pro') ?></option>
            <option value="bookme_pro_cal_many_participants"><?php esc_html_e('Appointment with many participants', 'bookme_pro') ?></option>
        </select>
    </div>
    <div class="form-group" id="bookme_pro_cal_one_participant">
        <textarea class="form-control" rows="9" name="bookme_pro_cal_one_participant"
                  placeholder="<?php esc_html_e('Enter a value', 'bookme_pro') ?>"><?php echo esc_textarea(get_option('bookme_pro_cal_one_participant')) ?></textarea><br/>
        <?php $this->render('_calendar_codes', array('participants' => 'one')) ?>
    </div>
    <div class="form-group" id="bookme_pro_cal_many_participants">
        <textarea class="form-control" rows="9" name="bookme_pro_cal_many_participants"
                  placeholder="<?php esc_html_e('Enter a value', 'bookme_pro') ?>"><?php echo esc_textarea(get_option('bookme_pro_cal_many_participants')) ?></textarea><br/>
        <?php $this->render('_calendar_codes', array('participants' => 'many')) ?>
    </div>

    <div class="panel-footer">
        <?php \BookmePro\Lib\Utils\Common::csrf() ?>
        <?php \BookmePro\Lib\Utils\Common::submitButton() ?>
        <?php \BookmePro\Lib\Utils\Common::resetButton() ?>
    </div>
</form>