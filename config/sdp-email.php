<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SDP Email API
    |--------------------------------------------------------------------------
    |
    | Create an API key in SDP Email and enable outbound sending for the
    | domain used by your mailables. The endpoint normally needs no changes.
    |
    */
    'api_key' => env('SDP_EMAIL_API_KEY'),

    'endpoint' => env('SDP_EMAIL_ENDPOINT', 'https://email.sdp-platform.com'),

    'timeout' => (float) env('SDP_EMAIL_TIMEOUT', 10),
];
