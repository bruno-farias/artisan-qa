<?php


namespace App\Services\Clients;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class OpenTriviaDBClient implements TriviaClientInterface
{
    private $baseUrl = 'https://opentdb.com/';
    private $client;
    const TOKEN_KEY = 'open_trivia_token';

    public function getClient(): Client
    {
        if (!$this->client) {
            $this->client = new Client();
        }
        return $this->client;
    }

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    public function getToken(): string
    {
        return Cache::remember(self::TOKEN_KEY, now()->addHours(6), function () {
            $response = $this->getClient()->get("{$this->baseUrl}api_token.php?command=request");
            $decodedResponse = json_decode($response->getBody());
            return $decodedResponse->token;
        });
    }

    public function fetchQAndA(int $amount = 1): array
    {
        $response = $this->getClient()->get("{$this->baseUrl}api.php?amount=$amount&token={$this->getToken()}&encode=base64");
        $decodedResponse = json_decode($response->getBody());
        return $decodedResponse->results;
    }

}
