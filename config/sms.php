<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SMS API URL
    |--------------------------------------------------------------------------
    |
    | This option determines the base URL for the SMS API. This is used for
    | sending SMS messages through the provider's service. Ensure the URL
    | is correct and corresponds to your provider's API documentation.
    |
    */

    'api_url' => env('SMS_API_URL', 'https://sms.frajosantech.co.ke/api/services'),

    /*
    |--------------------------------------------------------------------------
    | SMS API Key
    |--------------------------------------------------------------------------
    |
    | The API key is required to authenticate requests to the SMS API.
    | Ensure that the key is kept secure and is only accessible to
    | authorized services within your application.
    |
    */

    'api_key' => env('SMS_API_KEY', '2JZ7sTav5RRqyK5xKqwulWwjYEtG1OI4'),

    /*
    |--------------------------------------------------------------------------
    | SMS Partner ID
    |--------------------------------------------------------------------------
    |
    | The partner ID identifies your account with the SMS provider.
    | This ID is necessary for tracking and managing your SMS transactions.
    |
    */

    'partner_id' => env('SMS_PARTNER_ID', '12302'),

    /*
    |--------------------------------------------------------------------------
    | SMS Partner Sender ID
    |--------------------------------------------------------------------------
    |
    | The sender ID is the identifier shown as the sender of the SMS message.
    | You can set this to a custom value that aligns with your brand.
    |
    */

    'sender_id' => env('SMS_PARTNER_SENDER_ID', 'JuaMobile'),

];
