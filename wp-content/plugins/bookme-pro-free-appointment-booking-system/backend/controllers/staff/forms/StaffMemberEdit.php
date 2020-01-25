<?php
namespace BookmePro\Backend\Controllers\Staff\Forms;

use BookmePro\Lib;

/**
 * Class StaffMemberEdit
 * @package BookmePro\Backend\Controllers\Staff\Forms
 */
class StaffMemberEdit extends StaffMember
{
    private $errors = array();

    public function configure()
    {
        $this->setFields( array(
            'wp_user_id',
            'full_name',
            'email',
            'phone',
            'attachment_id',
            'google_calendar_id',
            'position',
            'info',
            'visibility',
        ) );
    }

    /**
     * @return Lib\Entities\Staff|false
     */
    public function save()
    {
        return parent::save();
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

}
