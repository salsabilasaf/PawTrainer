<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | Semua key dikonfigurasi di .env agar tidak hardcode.
    |
    */

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // ── PawTrainer External APIs ─────────────────────────────────────────────

    /**
     * The Cat API
     * Daftar gratis di: https://thecatapi.com/signup
     * Free tier: 10.000 request/bulan
     */
    'thecatapi' => [
        'key' => env('CAT_API_KEY', ''),
    ],

    /**
     * YouTube Data API v3
     * Aktifkan di Google Cloud Console:
     * https://console.cloud.google.com/apis/library/youtube.googleapis.com
     * Free tier: 10.000 unit/hari
     */
    'youtube' => [
        'key' => env('YOUTUBE_API_KEY', ''),
    ],

    /**
     * Cat Fact Ninja
     * https://catfact.ninja — tidak perlu API key
     */

];
