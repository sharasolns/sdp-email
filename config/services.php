<?php

return [
    'sdp' => [
        'key' => env('SDP_EMAIL_KEY', env('SDP_EMAIL_API_KEY')),
        'endpoint' => env('SDP_EMAIL_ENDPOINT', 'https://email.sdp-platform.com'),
        'timeout' => (float) env('SDP_EMAIL_TIMEOUT', 10),
    ],
];
