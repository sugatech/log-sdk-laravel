<?php

return [
    'api_url' => env('LOG_API_URL'),
    'oauth' => [
        'url' => env('LOG_OAUTH_URL', env('LOG_API_URL').'/oauth/token'),
        'client_id' => env('LOG_OAUTH_CLIENT_ID'),
        'client_secret' => env('LOG_OAUTH_CLIENT_SECRET'),
    ],
    'dry_run' => env('LOG_DRY_RUN', false),
];
