<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Http\Request;

class TwitterController extends Controller
{
    protected $apiKey;
    protected $apiSecret;
    protected $token;
    protected $tokenSecret;

    public function __construct()
    {
        $this->apiKey = env('X_API_KEY');
        $this->apiSecret = env('X_API_KEY_SECRET');
        $this->token = env('X_TOKEN');
        $this->tokenSecret = env('X_TOKEN_SECRET');
    }

    public function postTweet($text)
    {
        $connection = new TwitterOAuth(
            $this->apiKey,
            $this->apiSecret,
            $this->token,
            $this->tokenSecret
        );

        $content = $connection->post("statuses/update", ["status" => $text]);

        return $content;
    }
}
