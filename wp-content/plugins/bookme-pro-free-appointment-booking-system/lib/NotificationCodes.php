<?php
namespace BookmePro\Lib;

use BookmePro\Lib\DataHolders\Booking\Order;

/**
 * Class NotificationCodes
 * @package BookmePro\Lib
 */
class NotificationCodes
{
    public $amount_due;
    public $amount_paid;
    public $appointment_end;
    public $appointment_end_info;
    public $appointment_start;
    public $appointment_start_info;
    public $appointment_token;
    public $booking_number;
    public $cancellation_reason;
    public $cart_info;
    public $category_name;
    public $client_email;
    public $client_name;
    public $client_first_name;
    public $client_last_name;
    public $client_phone;
    public $custom_fields;
    public $custom_fields_2c;
    public $google_calendar_url;
    public $location_info;
    public $location_name;
    public $new_password;
    public $new_username;
    public $next_day_agenda;
    public $number_of_persons;
    public $payment_type;
    public $schedule;
    public $series_token;
    public $service_info;
    public $service_name;
    public $service_price;
    public $service_duration;
    public $site_address;
    public $staff_email;
    public $staff_info;
    public $staff_name;
    public $staff_phone;
    public $staff_photo;
    public $total_price;
    public $extras;
    public $extras_total_price;
    public $appointment_schedule;
    public $appointment_schedule_c;
    public $appointment_waiting_list;
    public $package_name;
    public $package_size;
    public $package_price;
    public $package_life_time;

    /** @var DataHolders\Booking\Order */
    protected $order;
    /** @var DataHolders\Booking\Item */
    protected $item;

    /**
     * Get order.
     *
     * @return DataHolders\Booking\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Get item.
     *
     * @return DataHolders\Booking\Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Do replacements.
     *
     * format codes {code}
     *
     * @param string $text
     * @param string $format
     * @return string
     */
    public function replace( $text, $format = 'text' )
    {
        $company_logo = '';
        $staff_photo  = '';
        $cart_info_c  = $cart_info = '';

        if ( $format == 'html' ) {
            $img = wp_get_attachment_image_src( get_option( 'bookme_pro_co_logo_attachment_id' ), 'full' );
            // Company logo as <img> tag.
            if ( $img ) {
                $company_logo = sprintf(
                    '<img src="%s" alt="%s" />',
                    esc_attr( $img[0] ),
                    esc_attr( get_option( 'bookme_pro_co_name' ) )
                );
            }
            if ( $this->staff_photo != '' ) {
                // Staff photo as <img> tag.
                $staff_photo = sprintf(
                    '<img src="%s" alt="%s" />',
                    esc_attr( $this->staff_photo ),
                    esc_attr( $this->staff_name )
                );
            }
        }

        // Cart info.
        $cart_info_data = $this->cart_info;
        if ( ! empty ( $cart_info_data ) ) {
            $cart_columns = get_option( 'bookme_pro_cart_show_columns', array() );
            $ths = array();
            foreach ( $cart_columns as $column => $attr ) {
                if ( $attr['show'] ) {
                    switch ( $column ) {
                        case 'service':
                            $ths[] = Utils\Common::getTranslatedOption( 'bookme_pro_l10n_label_service' );
                            break;
                        case 'date':
                            $ths[] = __( 'Date', 'bookme_pro' );
                            break;
                        case 'time':
                            $ths[] = __( 'Time', 'bookme_pro' );
                            break;
                        case 'employee':
                            $ths[] = Utils\Common::getTranslatedOption( 'bookme_pro_l10n_label_employee' );
                            break;
                        case 'price':
                            $ths[] = __( 'Price', 'bookme_pro' );
                            break;
                    }
                }
            }
            $trs = array();
            foreach ( $cart_info_data as $codes ) {
                $tds = array();
                foreach ( $cart_columns as $column => $attr ) {
                    if ( $attr['show'] ) {
                        switch ( $column ) {
                            case 'service':
                                $service_name = $codes['service_name'];
                                if ( ! empty ( $codes['extras'] ) ) {
                                    $extras = '';
                                    if ( $format == 'html' ) {
                                        foreach ( $codes['extras'] as $extra ) {
                                            $extras .= '<li>' . $extra['title'] . '</li>';
                                        }
                                        $extras = '<ul>' . $extras . '</ul>';
                                    } else {
                                        foreach ( $codes['extras'] as $extra ) {
                                            $extras .= ', ' . str_replace( '&nbsp;&times;&nbsp;', ' x ', $extra['title'] );
                                        }
                                    }
                                    $service_name .= $extras;
                                }
                                $tds[] = $service_name;
                                break;
                            case 'date':
                                $tds[] = Utils\DateTime::formatDate( $codes['appointment_start'] );
                                break;
                            case 'time':
                                if ( $codes['appointment_start_info'] !== null ) {
                                    $tds[] = $codes['appointment_start_info'];
                                } else {
                                    $tds[] = Utils\DateTime::formatTime( $codes['appointment_start'] );
                                }
                                break;
                            case 'employee':
                                $tds[] = $codes['staff_name'];
                                break;
                            case 'price':
                                $tds[] = Utils\Price::format( $codes['appointment_price'] );
                                break;
                        }
                    }
                }
                $tds[] = $codes['cancel_url'];
                $trs[] = $tds;
            }
            if ( $format == 'html' ) {
                $cart_info   = '<table cellspacing="1" border="1" cellpadding="5"><thead><tr><th>' . implode( '</th><th>', $ths ) . '</th></tr></thead><tbody>';
                $cart_info_c = '<table cellspacing="1" border="1" cellpadding="5"><thead><tr><th>' . implode( '</th><th>', $ths ) . '</th><th>' . __( 'Cancel', 'bookme_pro' ) . '</th></tr></thead><tbody>';
                foreach ( $trs as $tr ) {
                    $cancel_url   = array_pop( $tr );
                    $cart_info   .= '<tr><td>' . implode( '</td><td>', $tr ) . '</td></tr>';
                    $cart_info_c .= '<tr><td>' . implode( '</td><td>', $tr ) . '</td><td><a href="' . $cancel_url . '">' . __( 'Cancel', 'bookme_pro' ) . '</a></td></tr>';
                }
                $cart_info   .= '</tbody></table>';
                $cart_info_c .= '</tbody></table>';
            } else {
                foreach ( $trs as $tr ) {
                    $cancel_url = array_pop( $tr );
                    foreach ( $ths as $position => $column ) {
                        $cart_info   .= $column . ' ' . $tr[ $position ] . "\r\n";
                        $cart_info_c .= $column . ' ' . $tr[ $position ] . "\r\n";
                    }
                    $cart_info .= "\r\n";
                    $cart_info_c .= __( 'Cancel', 'bookme_pro' )  . ' ' . $cancel_url . "\r\n\r\n";
                }
            }
        }

        // Codes.
        $codes = array(
            '{amount_due}'             => Utils\Price::format( $this->amount_due ),
            '{amount_paid}'            => Utils\Price::format( $this->amount_paid ),
            '{appointment_date}'       => Utils\DateTime::formatDate( $this->appointment_start ),
            '{appointment_time}'       => $this->service_duration < DAY_IN_SECONDS ? Utils\DateTime::formatTime( $this->appointment_start ) : $this->appointment_start_info,
            '{appointment_end_date}'   => Utils\DateTime::formatDate( $this->appointment_end ),
            '{appointment_end_time}'   => $this->service_duration < DAY_IN_SECONDS ? Utils\DateTime::formatTime( $this->appointment_end ) : $this->appointment_end_info,
            '{approve_appointment_url}'=> $this->appointment_token ? admin_url( 'admin-ajax.php?action=bookme_pro_approve_appointment&token=' . urlencode( Utils\Common::xorEncrypt( $this->appointment_token, 'approve' ) ) ) : '',
            '{booking_number}'         => $this->booking_number,
            '{cancel_appointment_url}' => $this->appointment_token ? admin_url( 'admin-ajax.php?action=bookme_pro_cancel_appointment&token=' . $this->appointment_token ) : '',
            '{cart_info}'              => $cart_info,
            '{cart_info_c}'            => $cart_info_c,
            '{category_name}'          => $this->category_name,
            '{client_email}'           => $this->client_email,
            '{client_name}'            => $this->client_name,
            '{client_first_name}'      => $this->client_first_name,
            '{client_last_name}'       => $this->client_last_name,
            '{client_phone}'           => $this->client_phone,
            '{company_address}'        => $format == 'html' ? nl2br( get_option( 'bookme_pro_co_address' ) ) : get_option( 'bookme_pro_co_address' ),
            '{company_logo}'           => $company_logo,
            '{company_name}'           => get_option( 'bookme_pro_co_name' ),
            '{company_phone}'          => get_option( 'bookme_pro_co_phone' ),
            '{company_website}'        => get_option( 'bookme_pro_co_website' ),
            '{custom_fields}'          => $this->custom_fields,
            '{custom_fields_2c}'       => $format == 'html' ? $this->custom_fields_2c : $this->custom_fields,
            '{google_calendar_url}'    => sprintf( 'https://calendar.google.com/calendar/render?action=TEMPLATE&text=%s&dates=%s/%s&details=%s',
                urlencode( $this->service_name ),
                date( 'Ymd\THis', strtotime( $this->appointment_start ) ),
                date( 'Ymd\THis', strtotime( $this->appointment_end ) ),
                urlencode( sprintf( "%s\n%s", $this->service_name, $this->staff_name ) )
            ),
            '{new_password}'           => $this->new_password,
            '{new_username}'           => $this->new_username,
            '{next_day_agenda}'        => $this->next_day_agenda,
            '{number_of_persons}'      => $this->number_of_persons,
            '{payment_type}'           => $this->payment_type,
            '{reject_appointment_url}' => $this->appointment_token ? admin_url( 'admin-ajax.php?action=bookme_pro_reject_appointment&token=' . urlencode( Utils\Common::xorEncrypt( $this->appointment_token, 'reject' ) ) ) : '',
            '{service_info}'           => $format == 'html' ? nl2br( $this->service_info ) : $this->service_info,
            '{service_name}'           => $this->service_name,
            '{service_price}'          => Utils\Price::format( $this->service_price ),
            '{service_duration}'       => Utils\DateTime::secondsToInterval( $this->service_duration ),
            '{site_address}'           => $this->site_address,
            '{staff_email}'            => $this->staff_email,
            '{staff_info}'             => $format == 'html' ? nl2br( $this->staff_info ) : $this->staff_info,
            '{staff_name}'             => $this->staff_name,
            '{staff_phone}'            => $this->staff_phone,
            '{staff_photo}'            => $staff_photo,
            '{tomorrow_date}'          => Utils\DateTime::formatDate( $this->appointment_start ),
            '{total_price}'            => Utils\Price::format( $this->total_price ),
            '{cancellation_reason}'    => $this->cancellation_reason,
        );
        $codes['{cancel_appointment}'] = $format == 'html'
            ? sprintf( '<a href="%1$s">%1$s</a>', $codes['{cancel_appointment_url}'] )
            : $codes['{cancel_appointment_url}'];

        $codes = Proxy\Shared::prepareReplaceCodes( $codes, $this, $format );

        // Support deprecated codes [[CODE]]
        foreach ( array_keys( $codes ) as $code_key ) {
            if ( $code_key{1} == '[' ) {
                $codes[ '{' . strtolower( substr( $code_key, 2, -2 ) ) . '}' ] = $codes[ $code_key ];
            } else {
                $codes[ '[[' . strtoupper( substr( $code_key, 1, -1 ) ) . ']]' ] = $codes[ $code_key ];
            }
        }

        return strtr( $text, $codes );
    }

    public function refresh()
    {
        $order = $this->getOrder();
        $item = $this->getItem();

        $this->category_name    = $item->getService()->getTranslatedCategoryName();
        $this->custom_fields    = $item->getCA()->getFormattedCustomFields( 'text' );
        $this->custom_fields_2c = $item->getCA()->getFormattedCustomFields( 'html' );
        $this->service_info     = $item->getService()->getTranslatedInfo();
        $this->service_name     = $item->getService()->getTranslatedTitle();
        $this->staff_info       = $item->getStaff()->getTranslatedInfo();
        $this->staff_name       = $item->getStaff()->getTranslatedName();

        if ( $order->hasPayment() ) {
            $this->payment_type = Entities\Payment::typeToString( $order->getPayment()->getType() );
        }

        Proxy\Shared::prepareNotificationCodesForOrder( $this );
    }

    /**
     * Create for order.
     *
     * @param DataHolders\Booking\Order $order
     * @param DataHolders\Booking\Item $item
     * @return static
     */
    public static function createForOrder( DataHolders\Booking\Order $order, DataHolders\Booking\Item $item )
    {
        $codes = new static();

        $codes->order = $order;
        $codes->item  = $item;

        if ( $item->getService()->getType() == Entities\Service::TYPE_COMPOUND ) {
            $price = $item->getService()->getPrice();
            // The appointment ends when the last service ends in the compound service.
            $bounding = Entities\Appointment::query( 'a' )
                ->select( 'MIN(a.start_date) AS start, MAX(DATE_ADD(a.end_date, INTERVAL a.extras_duration SECOND)) AS end' )
                ->leftJoin( 'CustomerAppointment', 'ca', 'ca.appointment_id = a.id' )
                ->where( 'ca.compound_token', $item->getCA()->getCompoundToken() )
                ->groupBy( 'ca.compound_token' )
                ->fetchRow();
            $appointment_start = $bounding['start'];
            $appointment_end   = $bounding['end'];
        } else {
            $staff_service = new Entities\StaffService();
            $staff_service->loadBy( array( 'staff_id' => $item->getStaff()->getId(), 'service_id' => $item->getService()->getId() ) );
            $price = $staff_service->getPrice();
            // Normal start and end.
            $appointment_start = $item->getAppointment()->getStartDate();
            $appointment_end   = date_create( $item->getAppointment()->getEndDate() )
                ->modify( '+' . $item->getAppointment()->getExtrasDuration() . ' sec' )
                ->format( 'Y-m-d H:i:s' );
        }

        $staff_photo = wp_get_attachment_image_src( $item->getStaff()->getAttachmentId(), 'full' );

        $codes->appointment_end        = $appointment_end;
        $codes->appointment_start      = $appointment_start;
        $codes->appointment_token      = $item->getCA()->getToken();
        $codes->booking_number         = $item->getAppointment()->getId();
        $codes->client_email           = $order->getCustomer()->getEmail();
        $codes->client_name            = $order->getCustomer()->getFullName();
        $codes->client_first_name      = $order->getCustomer()->getFirstName();
        $codes->client_last_name       = $order->getCustomer()->getLastName();
        $codes->client_phone           = $order->getCustomer()->getPhone();
        $codes->number_of_persons      = $item->getCA()->getNumberOfPersons();
        $codes->service_price          = $price;
        $codes->service_duration       = $item->getService()->getDuration();
        $codes->staff_email            = $item->getStaff()->getEmail();
        $codes->staff_phone            = $item->getStaff()->getPhone();
        $codes->staff_photo            = $staff_photo ? $staff_photo[0] : '';
        $codes->appointment_start_info = $item->getService()->getStartTimeInfo();
        $codes->appointment_end_info   = $item->getService()->getEndTimeInfo();

        if ( $order->hasPayment() ) {
            $codes->amount_paid  = $order->getPayment()->getPaid();
            $codes->amount_due   = $order->getPayment()->getTotal() - $order->getPayment()->getPaid();
            $codes->total_price  = $order->getPayment()->getTotal();
        } else {
            $price = Proxy\SpecialHours::preparePrice(
                $price,
                $item->getStaff()->getId(),
                $item->getService()->getId(),
                $item->getAppointment()->getStartDate()
            );
            $codes->amount_paid = '';
            $codes->amount_due  = '';
            $codes->total_price = $price * $item->getCA()->getNumberOfPersons();
        }

        $codes->refresh();

        if ( ! $order->hasPayment() && $codes->extras_total_price ) {
            $codes->total_price = ( $price + $codes->extras_total_price ) * $item->getCA()->getNumberOfPersons();
        }

        return $codes;
    }

    /**
     * Create for test
     *
     * @return NotificationCodes
     */
    public static function createForTest()
    {
        $codes = new static();
        $customer = new Entities\Customer();

        $customer
            ->setPhone( '12345678' )
            ->setEmail( 'client@example.com' )
            ->setNotes( 'Client notes' )
            ->setFullName( 'Client Name' )
            ->setFirstName( 'Client First Name' )
            ->setLastName( 'Client Last Name' )
            ->setBirthday( '2000-01-01' );

        $codes->order = new Order( $customer );

        $codes->item;

        $start_date  = date_create( '-1 month' );
        $event_start = $start_date->format( 'Y-m-d 12:00:00' );
        $event_end = $start_date->format( 'Y-m-d 13:00:00' );
        $cart_info = array( array(
            'service_name'      => 'Service Name',
            'appointment_start' => $event_start,
            'staff_name'        => 'Staff Name',
            'appointment_price' => 24,
            'cancel_url'        => '#',
        ) );

        $codes->amount_due          = '';
        $codes->amount_paid         = '';
        $codes->appointment_end     = $event_end;
        $codes->appointment_start   = $event_start;
        $codes->cart_info           = $cart_info;
        $codes->category_name       = 'Category Name';
        $codes->client_email        = $customer->getEmail();
        $codes->client_name         = $customer->getFullName();
        $codes->client_first_name   = $customer->getFirstName();
        $codes->client_last_name    = $customer->getLastName();
        $codes->client_phone        = $customer->getPhone();
        $codes->extras              = 'Extras 1, Extras 2';
        $codes->extras_total_price  = '4';
        $codes->new_password        = 'New Password';
        $codes->new_username        = 'New User';
        $codes->next_day_agenda     = '';
        $codes->number_of_persons   = '1';
        $codes->payment_type        = Entities\Payment::typeToString( Entities\Payment::TYPE_LOCAL );
        $codes->service_info        = 'Service info text';
        $codes->service_name        = 'Service Name';
        $codes->service_price       = '10';
        $codes->service_duration    = '3600';
        $codes->staff_email         = 'staff@example.com';
        $codes->staff_info          = 'Staff info text';
        $codes->staff_name          = 'Staff Name';
        $codes->staff_phone         = '23456789';
        $codes->staff_photo         = 'https://dummyimage.com/100/dddddd/000000';
        $codes->total_price         = '24';
        $codes->cancellation_reason = 'Some Reason';

        $codes = Proxy\Shared::prepareTestNotificationCodes( $codes );

        return $codes;
    }
}