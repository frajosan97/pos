<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration manages cross-origin resource sharing (CORS) settings.
    | CORS determines what cross-origin operations are permitted in browsers.
    | You can adjust these settings as necessary for your application's needs.
    |
    | For more information: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    /*
    |--------------------------------------------------------------------------
    | CORS Paths
    |--------------------------------------------------------------------------
    |
    | Define the URL paths within your application that should be accessible
    | via cross-origin requests. You can specify wildcard patterns as needed
    | to match multiple endpoints dynamically.
    |
    */
    'paths' => [
        'payments/*',
        'sanctum/csrf-cookie',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed HTTP Methods
    |--------------------------------------------------------------------------
    |
    | Specify the HTTP methods that are permitted for cross-origin requests.
    | You can allow specific methods or use "*" to permit all methods.
    |
    */
    'allowed_methods' => [
        'GET',
        'POST',
        'PUT',
        'DELETE',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | List the origins that are allowed to make cross-origin requests to your
    | application. You can use explicit URLs or wildcard patterns to define
    | the allowed origins.
    |
    */
    'allowed_origins' => [
        'https://example.com',
        'https://another-example.com',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins Patterns
    |--------------------------------------------------------------------------
    |
    | Provide patterns for matching origins dynamically. These patterns are
    | evaluated against the `Origin` header of incoming requests, allowing
    | for flexible matching of allowed origins.
    |
    */
    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    |
    | Specify the headers that are allowed in cross-origin requests. This is
    | useful for ensuring only required headers are passed from clients to
    | your application during a request.
    |
    */
    'allowed_headers' => [
        'Content-Type',
        'X-Requested-With',
        'Authorization',
    ],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | Define the headers that should be exposed to the client in the response.
    | This is useful for allowing clients to read certain headers that are
    | not available by default in cross-origin responses.
    |
    */
    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | Set the maximum age (in seconds) for the CORS preflight response to be
    | cached by the browser. This improves performance by reducing the need
    | for repeated preflight requests.
    |
    */
    'max_age' => 3600,

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    |
    | Indicate whether cookies or HTTP authentication credentials are allowed
    | to be included in cross-origin requests. This is often required when
    | handling sessions or authorization via cookies.
    |
    */
    'supports_credentials' => true,

];
