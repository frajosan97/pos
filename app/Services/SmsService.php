<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /*
    |--------------------------------------------------------------------------
    | SMS Service Properties
    |--------------------------------------------------------------------------
    |
    | These properties hold the configuration values needed to send SMS messages
    | using the SMS service provider. The values are loaded from the `smsservice.php`
    | configuration file. The properties include:
    | - Partner ID
    | - API Key
    | - Sender ID
    | - API URL
    | - Guzzle HTTP Client
    |
    */
    protected $partnerId;
    protected $apiKey;
    protected $senderId;
    protected $apiUrl;
    protected $client;

    /*
    |--------------------------------------------------------------------------
    | Constructor Method
    |--------------------------------------------------------------------------
    |
    | The constructor method is used to initialize the SMS service with the
    | necessary configuration settings. It retrieves the values from the 
    | `smsservice.php` configuration file and initializes the Guzzle HTTP client.
    |
    */
    public function __construct()
    {
        $this->partnerId = config('sms.partner_id');
        $this->apiKey = config('sms.api_key');
        $this->senderId = config('sms.sender_id');
        $this->apiUrl = rtrim(config('sms.api_url'), '/'); // Ensure no trailing slash
        $this->client = new Client(); // Initialize Guzzle client
    }

    /*
    |--------------------------------------------------------------------------
    | Send SMS Method
    |--------------------------------------------------------------------------
    |
    | This method sends an SMS message to a given recipient. It constructs a
    | JSON payload and sends a POST request to the SMS service provider's API.
    |
    | Parameters:
    | - $recipient: The recipient's phone number.
    | - $message: The message text to be sent.
    |
    | Returns:
    | - true: If the SMS was sent successfully.
    | - false: If the SMS failed to send.
    |
    */
    public function sendSms($recipient, $message)
    {
        try {
            // Construct the JSON payload
            $payload = [
                'apikey' => $this->apiKey,
                'partnerID' => $this->partnerId,
                'message' => $message,
                'shortcode' => $this->senderId,
                'mobile' => formatPhoneNumber($recipient),
            ];

            // Construct the full API endpoint URL for sending SMS
            $url = $this->apiUrl . '/sendsms';

            // Send the POST request using Guzzle
            $response = $this->client->post($url, [
                'json' => $payload,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            // Return response
            return $response;
        } catch (\Exception $e) {
            // Log any exceptions that occur during the process
            Log::error('Error while sending SMS', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
