<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class InfobipSmsService
{
    private $httpClient;
    private $apiUrl = 'https://1gww19.api.infobip.com/sms/2/text/advanced';
    private $apiKey = 'fb39899f57e17d5334dee3610f5f6238-d81c8524-c4bf-419e-986d-5f07cda6eab3';

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function sendSms(string $to, string $message)
    {
        $response = $this->httpClient->request('POST', $this->apiUrl, [
            'headers' => [
                'Authorization' => 'App ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'messages' => [
                    [
                        'destinations' => [
                            ['to' => $to]
                        ],
                        'from' => '447491163443',
                        'text' => $message,
                    ]
                ]
            ]
        ]);

        if ($response->getStatusCode() === 200) {
            return $response->getContent();
        } else {
            return 'Error: ' . $response->getStatusCode() . ' ' . $response->getStatusText();
        }
    }
}