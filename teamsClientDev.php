<?php

$client = new \GuzzleHttp\Client();
$res = $client->request('GET', 'https://api.ciscospark.com/v1/rooms', [
    'headers' => [
        'Authorization' => 'Bearer ' . env('TEAMS_BOT_TOKEN'),
    ],
    'verify' => false
]);

echo (string) $res->getBody();

$client = new \GuzzleHttp\Client();
$res = $client->request('POST', 'https://api.ciscospark.com/v1/messages', [
    'headers' => [
        'Authorization' => 'Bearer ' . env('TEAMS_BOT_TOKEN'),
    ],
    'verify' => false,
    \GuzzleHttp\RequestOptions::JSON => [
        'toPersonEmail' => 'masloan@cisco.com',
        'text' => 'A test from the Guzzle client'
    ]
]);

