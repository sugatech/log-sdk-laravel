<?php

return [
    'api_url' => env('LOG_API_URL'),
    'access_token' => env('LOG_ACCESS_TOKEN'),
    'delimiter' => env('LOG_DELIMITER', '||'),
    'dry_run' => env('LOG_DRY_RUN', false),
];
