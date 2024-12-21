<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SMS Partner ID
    |--------------------------------------------------------------------------
    |
    | This value represents the partner ID provided by your SMS service provider.
    | It is used to authenticate your application when sending SMS messages.
    |
    */
    'partner_id' => env('SMS_PARTNER_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | SMS API Key
    |--------------------------------------------------------------------------
    |
    | This value is the API key issued by your SMS service provider. It is
    | required to securely authenticate API requests made to the SMS service.
    |
    */
    'api_key' => env('SMS_PARTNER_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | SMS Sender ID
    |--------------------------------------------------------------------------
    |
    | The sender ID represents the name or number displayed as the sender of
    | the SMS messages. Ensure this value is approved by your SMS provider.
    |
    */
    'sender_id' => env('SMS_PARTNER_SENDER_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | SMS API URL
    |--------------------------------------------------------------------------
    |
    | This value specifies the base URL of the SMS service API. It is the
    | endpoint your application will use to send SMS messages via the API.
    |
    */
    'api_url' => env('SMS_API_URL', ''),

];
