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
        $this->apiKey = config('services.x.key');
        $this->apiSecret = config('services.x.secret');
        $this->token = config('services.x.token');
        $this->tokenSecret = config('services.x.token_secret');
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
