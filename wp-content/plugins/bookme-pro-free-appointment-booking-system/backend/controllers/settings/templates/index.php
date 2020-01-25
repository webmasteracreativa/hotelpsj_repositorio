<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>
<div id="bookme-pro-tbs" class="wrap">
    <div class="bookme-pro-tbs-body">
        <?php \BookmePro\Lib\Utils\Common::pageHeader(esc_html__('Settings', 'bookme_pro')); ?>
        <div class="panel panel-default bookme-pro-main">
            <div class="panel-body">
                <div class="row">
                    <div id="bookme-pro-sidebar" class="col-sm-2">
                        <ul class="bookme-pro-nav bookme-pro-vertical-nav" role="tablist">
                            <li class="bookme-pro-nav-item" data-target="#bookme_pro_settings_general" data-toggle="tab">
                                <?php esc_html_e('General', 'bookme_pro') ?>
                            </li>
                            <li class="bookme-pro-nav-item" data-target="#bookme_pro_settings_url" data-toggle="tab">
                                <?php esc_html_e('URL Settings', 'bookme_pro') ?>
                            </li>
                            <li class="bookme-pro-nav-item" data-target="#bookme_pro_settings_company" data-toggle="tab">
                                <?php esc_html_e('Company', 'bookme_pro') ?>
                            </li>
                            <li class="bookme-pro-nav-item" data-target="#bookme_pro_settings_calendar" data-toggle="tab">
                                <?php esc_html_e('Calendar', 'bookme_pro') ?>
                            </li>
                            <li class="bookme-pro-nav-item" data-target="#bookme_pro_settings_customers" data-toggle="tab">
                                <?php esc_html_e('Customers', 'bookme_pro') ?>
                            </li>
                            <?php \BookmePro\Lib\Proxy\Shared::renderSettingsMenu() ?>
                            <li class="bookme-pro-nav-item" data-target="#bookme_pro_settings_payments" data-toggle="tab">
                                <?php esc_html_e('Payments', 'bookme_pro') ?>
                            </li>
                            <li class="bookme-pro-nav-item" data-target="#bookme_pro_settings_business_hours"
                                data-toggle="tab">
                                <?php esc_html_e('Business Hours', 'bookme_pro') ?>
                            </li>
                            <li class="bookme-pro-nav-item" data-target="#bookme_pro_settings_holidays" data-toggle="tab">
                                <?php esc_html_e('Holidays', 'bookme_pro') ?>
                            </li>
                        </ul>
                    </div>

                    <div id="bookme_pro_settings_controls" class="col-sm-10">
                        <div class="tab-content">
                            <div class="tab-pane active" id="bookme_pro_settings_general">
                                <?php include '_generalForm.php' ?>
                            </div>
                            <div class="tab-pane" id="bookme_pro_settings_url">
                                <?php include '_urlForm.php' ?>
                            </div>
                            <div class="tab-pane" id="bookme_pro_settings_company">
                                <?php include '_companyForm.php' ?>
                            </div>
                            <div class="tab-pane active" id="bookme_pro_settings_calendar">
                                <?php include '_calendarForm.php' ?>
                            </div>
                            <div class="tab-pane" id="bookme_pro_settings_customers">
                                <?php include '_customers.php' ?>
                            </div>
                            <?php \BookmePro\Lib\Proxy\Shared::renderSettingsForm() ?>
                            <div class="tab-pane" id="bookme_pro_settings_payments">
                                <?php include '_paymentsForm.php' ?>
                            </div>
                            <div class="tab-pane" id="bookme_pro_settings_business_hours">
                                <?php include '_hoursForm.php' ?>
                            </div>
                            <div class="tab-pane" id="bookme_pro_settings_holidays">
                                <?php include '_holidaysForm.php' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>