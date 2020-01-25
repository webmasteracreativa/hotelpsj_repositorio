<?php
namespace BookmePro\Backend\Controllers\Staff\Forms;

use BookmePro\Lib;

/**
 * Class StaffScheduleItemBreak
 * @package BookmePro\Backend\Controllers\Staff\Forms
 */
class StaffScheduleItemBreak extends Lib\Base\Form
{
    protected static $entity_class = 'ScheduleItemBreak';

    public function configure()
    {
        $this->setFields( array(
            'staff_schedule_item_id',
            'start_time',
            'end_time'
        ) );
    }

}