<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Google OAuth (Socialite compatible)
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', env('APP_URL') . '/oauth/google/callback'),
    ],

    // GitHub OAuth (Socialite compatible)
    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_REDIRECT_URI', env('APP_URL') . '/oauth/github/callback'),
    ],

    // X (Twitter) OAuth 1.0a - Manual implementation
    'x' => [
        'api_key' => env('X_API_KEY'),
        'api_secret' => env('X_API_KEY_SECRET'),
        'redirect_uri' => env('X_REDIRECT_URI', env('APP_URL') . '/oauth/x/callback'),
    ],

    // Discord OAuth 2.0 - Manual implementation
    'discord' => [
        'client_id' => env('DISCORD_CLIENT_ID'),
        'client_secret' => env('DISCORD_CLIENT_SECRET'),
        'redirect_uri' => env('DISCORD_REDIRECT_URI', env('APP_URL') . '/oauth/discord/callback'),
    ],

    // Reddit OAuth 2.0 - Manual implementation
    'reddit' => [
        'client_id' => env('REDDIT_CLIENT_ID'),
        'client_secret' => env('REDDIT_CLIENT_SECRET'),
        'redirect_uri' => env('REDDIT_REDIRECT_URI', env('APP_URL') . '/oauth/reddit/callback'),
        'user_agent' => env('REDDIT_USER_AGENT', env('APP_NAME') . '/1.0'),
    ],

    // Telegram Bot Widget - Manual implementation
    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'bot_username' => env('TELEGRAM_BOT_USERNAME'),
    ],
];
