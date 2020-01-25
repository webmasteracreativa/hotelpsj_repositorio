<?php
namespace BookmePro\Lib;

/**
 * Class SMS
 * @package BookmePro\Lib
 */
class SMS
{
    private $acc_sid,
        $auth_token,
        $twilio_no,
        $errors = array();

    public function __construct()
    {
        require_once Plugin::getDirectory() . '/lib/Twilio/autoload.php';
        $this->acc_sid = get_option('bookme_pro_twillio_account_sid');
        $this->auth_token = get_option('bookme_pro_twillio_auth_token');
        $this->twilio_no = get_option('bookme_pro_twillio_phone_number');
    }

    /**
     * Send SMS.
     *
     * @param string $phone_number
     * @param string $message
     * @param int $type_id
     * @return bool
     */
    public function sendSms($phone_number, $message)
    {
        if (!empty($this->acc_sid) && !empty($this->auth_token)) {
            $phone_number = $this->normalizePhoneNumber($phone_number);
            if (!empty($phone_number)) {
                try {
                    $client = new \Twilio\Rest\Client($this->acc_sid, $this->auth_token);
                    // Use the client to do fun stuff like send text messages!
                    $client->messages->create(
                    // the number you'd like to send the message to
                        $phone_number,
                        array(
                            // A Twilio phone number you purchased at twilio.com/console
                            'from' => $this->twilio_no,
                            // the body of the text message you'd like to send
                            'body' => $message
                        )
                    );
                    return true;
                } catch (\Exception $e) {
                    $this->clearErrors();
                    $this->errors[] = $e->getMessage();
                    return false;
                }
            }
        }
        $this->errors[] = __("Unexpected error, please try again.", 'bookme_pro');
        return false;
    }

    /**
     * Return phone_number in international format
     *
     * @param $phone_number
     * @return string
     */
    public function normalizePhoneNumber($phone_number)
    {
        // Remove everything except numbers and "+".
        $phone_number = preg_replace('/[^\d\+]/', '', $phone_number);

        if (strpos($phone_number, '+') === 0) {
            // ok.
        } elseif (strpos($phone_number, '00') === 0) {
            $phone_number = ltrim($phone_number, '0');
        } else {
            // Default country code can contain not permitted characters. Remove everything except numbers.
            $phone_number = ltrim(preg_replace('/\D/', '', get_option('bookme_pro_cst_default_country_code', '')), '0') . ltrim($phone_number, '0');
        }

        return $phone_number;
    }


    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    public function clearErrors()
    {
        $this->errors = array();
    }

}