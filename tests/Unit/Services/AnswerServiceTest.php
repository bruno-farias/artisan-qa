<?php


namespace Tests\Unit\Services;


use App\Services\AnswerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Tests\TestHelper;

class AnswerServiceTest extends TestCase
{
    use RefreshDatabase;
    use TestHelper;

    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = new AnswerService();
    }

    public function testCreateAnswerSucceeds()
    {
        $text = TestHelper::text();
        $correct = TestHelper::bool();
        $locale = TestHelper::locale();
        $question = TestHelper::createQuestion();

        $answer = $this->service->insert($text, $correct, $locale, $question);

        $this->assertEquals($text, $answer->getOption());
        $this->assertEquals($correct, $answer->getCorrect());
        $this->assertEquals($locale, $answer->getLocale());
        $this->assertEquals($question, $answer->getQuestion());
    }

    public function testGetQuestionOptions()
    {
        $question = TestHelper::createQuestion();
        $quantity = TestHelper::quantity();
        Collection::times($quantity, function () use ($question) {
            TestHelper::createAnswer($question);
        });

        $this->service->getAnswersByQuestionId($question->id);

        $this->assertCount($quantity, $question->getOptions());
    }

}
