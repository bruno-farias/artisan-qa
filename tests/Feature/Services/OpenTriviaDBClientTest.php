<?php


namespace Tests\Feature\Services;


use App\Services\Clients\OpenTriviaDBClient;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Tests\TestHelper;

class OpenTriviaDBClientTest extends TestCase
{
    use TestHelper;

    private $openTriviaDBClient;

    public function setUp(): void
    {
        parent::setUp();
        $this->openTriviaDBClient = new OpenTriviaDBClient();
    }

    public function testGetTokenFromCacheSucceeds()
    {
        $token = TestHelper::token();
        Cache::put(OpenTriviaDBClient::TOKEN_KEY, $token, now()->addSeconds(5));

        $this->assertEquals($token, $this->openTriviaDBClient->getToken());
    }

    public function testFetchQAndASucceeds()
    {
        $quantity = TestHelper::quantity(1, 3);
        $response = $this->openTriviaDBClient->fetchQAndA($quantity);

        $this->assertIsArray($response);
        for ($x = 0; $x < $quantity; $x++) {
            $obj = $response[$x];
            $this->assertIsObject($obj);
            $this->assertObjectHasAttribute('question', $obj);
            $this->assertObjectHasAttribute('correct_answer', $obj);
            $this->assertObjectHasAttribute('incorrect_answers', $obj);
        }
    }

}
