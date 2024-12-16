<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\School;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /*
    |--------------------------------------------------------------------------
    | SMS Service Properties
    |--------------------------------------------------------------------------
    |
    | These properties hold the configuration values needed to send SMS messages
    | using the SMS service provider. The values are loaded from the `services.php`
    | configuration file. The properties include:
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
    | `smsservice.php` configuration file:
    |
    | It also initializes the Guzzle HTTP client for making requests.
    |
    */
    public function __construct()
    {
        $this->partnerId = config('smsservice.sms.partner_id');
        $this->apiKey = config('smsservice.sms.api_key');
        $this->senderId = config('smsservice.sms.sender_id');
        $this->apiUrl = config('smsservice.sms.api_url');  // New API URL
        $this->client = new Client();  // Initialize Guzzle client
    }

    public function sendSms($recipient, $message)
    {
        try {
            // Construct the JSON payload
            $payload = json_encode([
                'apikey' => $this->apiKey,
                'partnerID' => $this->partnerId,
                'message' => $message,
                'shortcode' => $this->senderId,
                'mobile' => formatPhoneNumber($recipient), 
            ]);

            // Construct the full API endpoint URL for sending SMS
            $url = rtrim($this->apiUrl, '/') . '/sendsms';

            // Send the POST request using Guzzle
            $response = $this->client->post($url, [
                'body' => $payload,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            // Check the response status code
            if ($response->getStatusCode() === 200) {
                // Log the successful response
                Log::info('SMS sent successfully', ['response' => $response->getBody()->getContents()]);
                return true;  // SMS sent successfully
            } else {
                // Log the error response if SMS sending fails
                Log::error('SMS sending failed', ['response' => $response->getBody()->getContents()]);
                return false;
            }
        } catch (\Exception $e) {
            // Log any exceptions that occur during the process
            Log::error('Error while sending SMS', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
