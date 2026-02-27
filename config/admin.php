<?php

return [
    'password_hash' => env('ADMIN_PASSWORD_HASH'),
    'password_plain' => env('ADMIN_PASSWORD'),
    'unlock_timeout_seconds' => (int) env('ADMIN_UNLOCK_TIMEOUT', 3600),
    'session_key' => env('ADMIN_UNLOCK_SESSION_KEY', 'admin_unlocked_at'),
];
