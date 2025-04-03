<?php

namespace App\Services;

use GuzzleHttp\Client;

class TwilioService
{
    protected $accountSid;
    protected $authToken;
    protected $from;
    protected $client;

    public function __construct()
    {
        $this->accountSid = env('TWILIO_SID');
        $this->authToken = env('TWILIO_AUTH_TOKEN');
        $this->from = env('TWILIO_FROM');
        $this->client = new Client([
            'base_uri' => 'https://api.twilio.com/2010-04-01/',
            'auth' => [$this->accountSid, $this->authToken],
        ]);
    }

    public function sendSms($to, $message)
    {
        $response = $this->client->post("Accounts/{$this->accountSid}/Messages.json", [
            'form_params' => [
                'From' => $this->from,
                'To' => $to,
                'Body' => $message,
            ]
        ]);

        return json_decode($response->getBody()->getContents());
    }
}
