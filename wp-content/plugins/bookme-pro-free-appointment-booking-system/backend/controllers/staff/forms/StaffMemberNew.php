<?php
namespace BookmePro\Backend\Controllers\Staff\Forms;

/**
 * Class StaffMemberNew
 * @package BookmePro\Backend\Controllers\Staff\Forms
 */
class StaffMemberNew extends StaffMember
{
    public function configure()
    {
        $this->setFields(array('wp_user_id', 'full_name', 'email', 'phone', 'attachment_id', 'info', 'visibility'));
    }

}