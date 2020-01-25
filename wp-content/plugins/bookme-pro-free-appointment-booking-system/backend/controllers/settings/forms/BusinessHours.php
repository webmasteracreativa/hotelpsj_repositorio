<?php
namespace BookmePro\Backend\Controllers\Settings\Forms;

use BookmePro\Lib;

/**
 * Class BusinessHours
 * @package BookmePro\Backend\Controllers\Settings\Forms
 */
class BusinessHours extends Lib\Base\Form
{
    public function __construct()
    {
        $this->setFields( array(
            'bookme_pro_bh_monday_start',
            'bookme_pro_bh_monday_end',
            'bookme_pro_bh_tuesday_start',
            'bookme_pro_bh_tuesday_end',
            'bookme_pro_bh_wednesday_start',
            'bookme_pro_bh_wednesday_end',
            'bookme_pro_bh_thursday_start',
            'bookme_pro_bh_thursday_end',
            'bookme_pro_bh_friday_start',
            'bookme_pro_bh_friday_end',
            'bookme_pro_bh_saturday_start',
            'bookme_pro_bh_saturday_end',
            'bookme_pro_bh_sunday_start',
            'bookme_pro_bh_sunday_end',
        ) );
    }

    public function save()
    {
        foreach ( $this->data as $field => $value ) {
            update_option( $field, $value );
        }
    }

    /**
     * @param string $field_name
     * @param bool $is_start
     * @return string
     */
    public function renderField( $field_name = 'bookme_pro_bh_monday', $is_start = true )
    {
        $ts_length      = Lib\Config::getTimeSlotLength();
        $time_output    = 0;
        $time_end       = DAY_IN_SECONDS;
        $option_name    = $field_name . ( $is_start ? '_start' : '_end' );
        $class_name     = $is_start ? 'select_start' : 'select_end bookme-pro-hide-on-off';
        $selected_value = get_option( $option_name );
        $selected_seconds = Lib\Utils\DateTime::timeToSeconds( $selected_value );
        $output         = "<select style='display:inline-block' class='form-control {$class_name}' name={$option_name}>";

        if ( $is_start ) {
            $output .= '<option value="">' . __( 'OFF', 'bookme_pro' ) . '</option>';
            $time_end -= $ts_length;
        }
        $value_added = false;
        while ( $time_output <= $time_end ) {
            if ( $value_added === false ) {
                if ( $selected_seconds == $time_output ) {
                    $value_added = true;
                } elseif ( $selected_seconds < $time_output ) {
                    $output .= sprintf( '<option value="%s" selected="selected">%s</option>', $selected_value, Lib\Utils\DateTime::formatTime( $selected_value ) );
                    $value_added = true;
                }
            }

            $value    = Lib\Utils\DateTime::buildTimeString( $time_output, false );
            $op_name  = Lib\Utils\DateTime::formatTime( $time_output );
            $output  .= "<option value='{$value}'" . selected( $value, $selected_value, false ) . ">{$op_name}</option>";
            $time_output += $ts_length;
        }

        $output .= '</select>';

        return $output;
    }

}