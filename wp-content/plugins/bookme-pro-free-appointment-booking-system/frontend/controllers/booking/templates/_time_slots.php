<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>
    <button class="bookme-pro-day" value="<?php echo esc_attr($group) ?>">
        <?php echo date_i18n(($duration_in_days ? 'M' : 'D, M d'), strtotime($group)) ?>
    </button>
<?php foreach ($slots as $slot) :
    /** @var \BookmePro\Lib\Slots\Range $slot */
    $data = $slot->buildSlotData();
    $service = \BookmePro\Lib\Entities\Service::find($slot->serviceId());
    $slot = $slot->resize($service->getDuration());
    $time = $slot->start()->toClientTz()->formatI18n($duration_in_days ? 'D, M d' : get_option('time_format')) . ' - ' . $slot->end()->toClientTz()->formatI18n($duration_in_days ? 'D, M d' : get_option('time_format'));
    printf('<button value="%s" data-group="%s" class="bookme-pro-hour%s" %s>
        <span class="bookme-pro-hour-time %s">%s</span><span class="ladda-label"><span class="bookme-pro-button-time">%s</span><span class="bookme-pro-button-text">%s</span></span>
    </button>',
        esc_attr(json_encode($data)),
        $group,
        $slot->fullyBooked() ? ' booked' : '',
        disabled($slot->fullyBooked(), true, false),
        $data[0][2] == $selected_date ? ' bookme-pro-bold' : '',
        $time,
        $time,
        \BookmePro\Lib\Utils\Common::getTranslatedOption('bookme_pro_l10n_button_book_now')
    );
endforeach ?>