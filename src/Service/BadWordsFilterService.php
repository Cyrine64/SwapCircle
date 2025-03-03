<?php
namespace App\Service;

use GuzzleHttp\Client;

class BadWordsFilterService
{
    private $client;
    private $apiUrl = 'https://www.purgomalum.com/service/containsprofanity';

    public function __construct()
    {
        $this->client = new Client();
    }

    public function containsBadWords(string $text): bool
    {
        $response = $this->client->request('GET', $this->apiUrl, [
            'query' => ['text' => $text]
        ]);

        $result = $response->getBody()->getContents();

        return $result === 'true';
    }
}
