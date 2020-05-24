<?php


namespace App\Services\Clients;


use App\Services\AnswerService;
use App\Services\QuestionService;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class OpenTriviaDBClient implements TriviaClientInterface
{
    private $baseUrl = 'https://opentdb.com/';
    private $client;
    private const TOKEN_KEY = 'open_trivia_token';
    private $questionService;
    private $answerService;

    public function __construct(QuestionService $questionService, AnswerService $answerService)
    {
        $this->questionService = $questionService;
        $this->answerService = $answerService;
    }

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

    public function fetchQAndA(int $amount = 1): void
    {
        $response = $this->getClient()->get("{$this->baseUrl}api.php?amount=$amount&token={$this->getToken()}&encode=base64");
        $decodedResponse = json_decode($response->getBody());
        $results = $decodedResponse->results;

        foreach ($results as $result) {
            $question = $this->questionService->insert(base64_decode($result->question), 'en');
            $this->answerService->insert(base64_decode($result->correct_answer), true, 'en', $question);
            foreach ($result->incorrect_answers as $incorrect_answer) {
                $this->answerService->insert(base64_decode($incorrect_answer), false, 'en', $question);
            }
        }
    }

}
