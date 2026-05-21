<?php

return [
    'api_url'           => env('ZIWO_API_URL', 'https://api.ziwo.io'),
    'account_name'      => env('ZIWO_ACCOUNT_NAME'),
    'username'          => env('ZIWO_USERNAME'),
    'password'          => env('ZIWO_PASSWORD'),
    'webhook_secret'    => env('ZIWO_WEBHOOK_SECRET'),
    'default_caller_id' => env('ZIWO_DEFAULT_CALLER_ID'),
    'recording_disk'    => env('ZIWO_RECORDING_DISK', 'local'),
];
