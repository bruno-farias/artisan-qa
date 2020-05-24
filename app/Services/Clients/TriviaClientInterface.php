<?php


namespace App\Services\Clients;


use GuzzleHttp\Client;

interface TriviaClientInterface
{
    public function getClient(): Client;

    public function setClient(Client $client): void;

    public function getToken(): string;

    public function fetchQAndA(int $amount = 1): void;
}
