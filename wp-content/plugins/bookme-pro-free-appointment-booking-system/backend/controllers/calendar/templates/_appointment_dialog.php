<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
use BookmePro\Lib\Entities\CustomerAppointment;
use BookmePro\Lib\Config;
use BookmePro\Lib\Proxy;
use BookmePro\Lib\Utils\Common;

?>

<div ng-app="appointmentDialog" ng-controller="appointmentDialogCtrl">
    <div id="appointmentPanel" class="slidePanel <?php echo is_rtl() ? 'slidePanel-left' : 'slidePanel-right'; ?>">
        <div class="slidePanel-scrollable">
            <div>
                <div class="slidePanel-content">
                    <header class="slidePanel-header">
                        <div class="slidePanel-overlay-panel">
                            <div class="slidePanel-heading">
                                <h2><?php esc_html_e('New Booking', 'bookme_pro'); ?></h2>
                            </div>
                            <div class="slidePanel-actions">
                                <div class="btn-group-flat">
                                    <button type="button"
                                            class="btn btn-primary btn-sm ajax-service-send"
                                            ng-click=processForm()><i class="glyphicon glyphicon-ok"></i></button>
                                    <button type="button" class="btn btn-default btn-sm slidePanel-close"><i
                                                class="glyphicon glyphicon-remove"></i></button>
                                </div>
                            </div>
                        </div>
                    </header>
                    <div class="slidePanel-inner">
                        <div class="panel-body">
                            <div id="bookme-pro-appointment-dialog" tabindex=-1>
                                <form ng-submit=processForm()>
                                    <div ng-show=loading class="modal-body">
                                        <div class="bookme-pro-loading"></div>
                                    </div>
                                    <div ng-hide="loading || form.screen != 'main'" class="modal-body">
                                        <div class=form-group>
                                            <label for="bookme-pro-provider"><?php esc_html_e('Staff Member', 'bookme_pro') ?></label>
                                            <select id="bookme-pro-provider" class="form-control" ng-model="form.staff"
                                                    ng-options="s.full_name + (form.staff_any == s ? ' (' + dataSource.l10n.staff_any + ')' : '') for s in dataSource.data.staff"
                                                    ng-change="onStaffChange()"></select>
                                        </div>

                                        <div class=form-group>
                                            <label for="bookme-pro-service"><?php esc_html_e('Service', 'bookme_pro') ?></label>
                                            <select id="bookme-pro-service" class="form-control" ng-model="form.service"
                                                    ng-options="s.title for s in form.staff.services"
                                                    ng-change="onServiceChange()">
                                                <option value=""><?php esc_html_e('-- Select a service --', 'bookme_pro') ?></option>
                                            </select>
                                            <p class="text-danger" my-slide-up="errors.service_required">
                                                <?php esc_html_e('Please select a service', 'bookme_pro') ?>
                                            </p>
                                        </div>

                                        <?php if (Config::locationsActive()): ?>
                                            <div class="form-group">
                                                <label for="bookme-pro-appointment-location"><?php esc_html_e('Location', 'bookme_pro') ?></label>
                                                <select id="bookme-pro-appointment-location" class="form-control"
                                                        ng-model="form.location"
                                                        ng-options="l.name for l in form.staff.locations">
                                                    <option value=""></option>
                                                </select>
                                            </div>
                                        <?php endif ?>

                                        <div class=form-group>
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <label for="bookme-pro-date"><?php esc_html_e('Date', 'bookme_pro') ?></label>
                                                    <input id="bookme-pro-date" class="form-control" type=text
                                                           ng-model=form.date ui-date="dateOptions"
                                                           autocomplete="off"
                                                           ng-change=onDateChange()>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div ng-hide="form.service.duration >= 86400">
                                                        <label for="bookme-pro-period"><?php esc_html_e('Period', 'bookme_pro') ?></label>
                                                        <div class="bookme-pro-flexbox">
                                                            <div class="bookme-pro-flex-cell">
                                                                <select id="bookme-pro-period" class="form-control"
                                                                        ng-model=form.start_time
                                                                        ng-options="t.title for t in dataSource.data.start_time"
                                                                        ng-change=onStartTimeChange()></select>
                                                            </div>
                                                            <div class="bookme-pro-flex-cell" style="width: 4%">
                                                                <div class="bookme-pro-margin-horizontal-md"><?php esc_html_e('to', 'bookme_pro') ?></div>
                                                            </div>
                                                            <div class="bookme-pro-flex-cell" style="width: 48%">
                                                                <select class="form-control" ng-model=form.end_time
                                                                        ng-options="t.title for t in dataSource.getDataForEndTime()"
                                                                        ng-change=onEndTimeChange()></select>
                                                            </div>
                                                        </div>
                                                        <p class="text-success"
                                                           my-slide-up=errors.date_interval_warning
                                                           id=date_interval_warning_msg>
                                                            <?php esc_html_e('Selected period doesn\'t match service duration', 'bookme_pro') ?>
                                                        </p>
                                                        <p class="text-success" my-slide-up="errors.time_interval"
                                                           ng-bind="errors.time_interval"></p>
                                                    </div>
                                                </div>
                                                <div class="text-success col-sm-12"
                                                     my-slide-up=errors.date_interval_not_available
                                                     id=date_interval_not_available_msg>
                                                    <?php esc_html_e('The selected period is occupied by another appointment', 'bookme_pro') ?>
                                                </div>
                                            </div>
                                        </div>

                                        <?php Proxy\RecurringAppointments::renderRecurringSubForm() ?>

                                        <div class=form-group>
                                            <label for="bookme-pro-select2"><?php esc_html_e('Customers', 'bookme_pro') ?></label>
                                            <span ng-show="form.service"
                                                  title="<?php esc_attr_e('Selected / maximum', 'bookme_pro') ?>">
                                ({{dataSource.getTotalNumberOfPersons()}}/{{form.service.capacity_max}})
                            </span>
                                            <span ng-show="form.customers.length > 5"
                                                  ng-click="form.expand_customers_list = !form.expand_customers_list"
                                                  role="button">
                                <i class="dashicons"
                                   ng-class="{'dashicons-arrow-down-alt2':!form.expand_customers_list, 'dashicons-arrow-up-alt2':form.expand_customers_list}"></i>
                            </span>
                                            <p class="text-success" ng-show=form.service
                                               my-slide-up="form.service.capacity_min > 1 && form.service.capacity_min > dataSource.getTotalNumberOfPersons()">
                                                <?php esc_html_e('Minimum capacity', 'bookme_pro') ?>:
                                                {{form.service.capacity_min}}
                                            </p>
                                            <ul class="bookme-pro-flexbox">
                                                <li ng-repeat="customer in form.customers" class="bookme-pro-flex-row"
                                                    ng-hide="$index > 4 && !form.expand_customers_list">
                                                    <a ng-click="editCustomerDetails(customer)"
                                                       title="<?php esc_attr_e('Edit booking details', 'bookme_pro') ?>"
                                                       class="bookme-pro-flex-cell bookme-pro-padding-bottom-sm"
                                                       href>{{customer.name}}</a>
                                                    <span class="bookme-pro-flex-cell text-right text-nowrap bookme-pro-padding-bottom-sm">
                                        <?php Proxy\Shared::renderAppointmentDialogCustomerList() ?>
                                                        <span class="dropdown">
                                            <button type="button" class="btn btn-sm btn-default bookme-pro-margin-left-xs"
                                                    data-toggle="dropdown">
                                                <span ng-class="{'dashicons': true, 'dashicons-clock': customer.status == 'pending', 'dashicons-yes': customer.status == 'approved', 'dashicons-no': customer.status == 'cancelled', 'dashicons-dismiss': customer.status == 'rejected', 'dashicons-list-view': customer.status == 'waitlisted'}"></span>
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href ng-click="customer.status = 'pending'">
                                                        <span class="dashicons dashicons-clock"></span>
                                                        <?php echo esc_html(CustomerAppointment::statusToString(CustomerAppointment::STATUS_PENDING)) ?>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href ng-click="customer.status = 'approved'">
                                                        <span class="dashicons dashicons-yes"></span>
                                                        <?php echo esc_html(CustomerAppointment::statusToString(CustomerAppointment::STATUS_APPROVED)) ?>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href ng-click="customer.status = 'cancelled'">
                                                        <span class="dashicons dashicons-no"></span>
                                                        <?php echo esc_html(CustomerAppointment::statusToString(CustomerAppointment::STATUS_CANCELLED)) ?>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href ng-click="customer.status = 'rejected'">
                                                        <span class="dashicons dashicons-dismiss"></span>
                                                        <?php echo esc_html(CustomerAppointment::statusToString(CustomerAppointment::STATUS_REJECTED)) ?>
                                                    </a>
                                                </li>
                                                <?php if (Config::waitingListActive()): ?>
                                                    <li>
                                                        <a href ng-click="customer.status = 'waitlisted'">
                                                            <span class="dashicons dashicons-list-view"></span>
                                                            <?php echo esc_html(CustomerAppointment::statusToString(CustomerAppointment::STATUS_WAITLISTED)) ?>
                                                        </a>
                                                    </li>
                                                <?php endif ?>
                                            </ul>
                                        </span>
                                        <button type="button" class="btn btn-sm btn-default bookme-pro-margin-left-xs"
                                                id="bookme-pro-payment-dialog-show"
                                                data-payment_id="{{customer.payment_id}}" ng-show="customer.payment_id"
                                                popover="<?php esc_attr_e('Payment', 'bookme_pro') ?>: {{customer.payment_title}}">
                                            <span ng-class="{'bookme-pro-js-toggle-popover dashicons': true, 'dashicons-thumbs-up': customer.payment_type == 'full', 'dashicons-warning': customer.payment_type == 'partial'}"></span>
                                        </button>
                                        <span class="btn btn-sm btn-default disabled bookme-pro-margin-left-xs"
                                              style="opacity:1;cursor:default;"><i class="glyphicon glyphicon-user"></i>&times;{{customer.number_of_persons}}</span>
                                        <button type="button" class="btn btn-sm btn-default bookme-pro-margin-left-xs"
                                                ng-click="editPackageSchedule(customer)" ng-show="customer.package_id"
                                                popover="<?php esc_attr_e('Package schedule', 'bookme_pro') ?>">
                                            <span class="dashicons dashicons-calendar"></span>
                                        </button>
                                        <a ng-click="removeCustomer(customer)"
                                           class="dashicons dashicons-trash text-danger bookme-pro-vertical-middle"
                                           href="#"></a>
                                    </span>
                                                </li>
                                            </ul>
                                            <span class="btn btn-default"
                                                  ng-show="form.customers.length > 5 && !form.expand_customers_list"
                                                  ng-click="form.expand_customers_list = !form.expand_customers_list"
                                                  style="width: 100%; line-height: 0; padding-top: 0; padding-bottom: 8px; margin-bottom: 10px;"
                                                  role="button">...</span>
                                            <div
                                                <?php if (!Config::waitingListActive()): ?>ng-show="!form.service || dataSource.getTotalNumberOfNotCancelledPersons() < form.service.capacity_max"<?php endif ?>>
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <select id="bookme-pro-select2" multiple
                                                                data-placeholder="<?php esc_attr_e('-- Search customers --', 'bookme_pro') ?>"
                                                                class="form-control"
                                                                ng-model="form.customers"
                                                                ng-options="c.name for c in dataSource.data.customers"
                                                                ng-change="onCustomersChange({{form.customers.length}}, {{dataSource.getTotalNumberOfNotCancelledPersons()}})">
                                                        </select>
                                                        <span class="input-group-btn">
                                            <a class="btn btn-primary" ng-click="openNewCustomerDialog()">
                                                <i class="glyphicon glyphicon-plus"></i>
                                                <?php esc_html_e('New customer', 'bookme_pro') ?>
                                            </a>
                                        </span>
                                                    </div>
                                                    <p class="text-danger" my-slide-up="errors.customers_required">
                                                        <?php esc_html_e('Please select a customer', 'bookme_pro') ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <p class="text-danger" my-slide-up="errors.overflow_capacity"
                                               ng-bind="errors.overflow_capacity"></p>
                                            <p class="text-success"
                                               my-slide-up="errors.customers_appointments_limit"
                                               ng-repeat="customer_error in errors.customers_appointments_limit">
                                                {{customer_error}}
                                            </p>
                                        </div>

                                        <div class=form-group>
                                            <label for="bookme-pro-notification"><?php esc_html_e('Send notifications', 'bookme_pro') ?></label>
                                            <p class="help-block"><?php is_admin() ?
                                                    esc_html_e('If email or SMS notifications are enabled and you want customers or staff member to be notified about this appointment after saving, select appropriate option before clicking Save. With "If status changed" the notifications are sent to those customers whose status has just been changed. With "To all customers" the notifications are sent to everyone in the list.', 'bookme_pro') :
                                                    esc_html_e('If email or SMS notifications are enabled and you want customers or yourself to be notified about this appointment after saving, select appropriate option before clicking Save. With "If status changed" the notifications are sent to those customers whose status has just been changed. With "To all customers" the notifications are sent to everyone in the list.', 'bookme_pro') ?></p>
                                            <select class="form-control" style="margin-top: 0"
                                                    ng-model=form.notification id="bookme-pro-notification"
                                                    ng-init="form.notification = '<?php echo get_user_meta(get_current_user_id(), 'bookme_pro_appointment_form_send_notifications', true) ?>' || 'no'">
                                                <option value="no"><?php esc_html_e('Don\'t send', 'bookme_pro') ?></option>
                                                <option value="changed_status"><?php esc_html_e('If status changed', 'bookme_pro') ?></option>
                                                <option value="all"><?php esc_html_e('To all customers', 'bookme_pro') ?></option>
                                            </select>
                                        </div>

                                        <div class=form-group>
                                            <label for="bookme-pro-internal-note"><?php esc_html_e('Internal note', 'bookme_pro') ?></label>
                                            <textarea class="form-control" ng-model=form.internal_note
                                                      id="bookme-pro-internal-note"></textarea>
                                        </div>
                                    </div>
                                    <?php Proxy\RecurringAppointments::renderSchedule() ?>
                                </form>
                            </div><!-- /.modal -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div customer-dialog=createCustomer(customer)></div>
    <div payment-details-dialog="completePayment(payment_id, payment_title)"></div>

    <?php $this->render('_customer_details_dialog', compact('custom_fields')) ?>
    <?php \BookmePro\Backend\Controllers\Customers\Components::getInstance()->renderCustomerDialog() ?>
    <?php \BookmePro\Backend\Controllers\Payments\Components::getInstance()->renderPaymentDetailsDialog() ?>
</div>