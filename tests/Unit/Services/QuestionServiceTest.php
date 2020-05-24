<?php


namespace Tests\Unit\Services;


use App\Services\QuestionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestHelper;

class QuestionServiceTest extends TestCase
{
    use RefreshDatabase;
    use TestHelper;

    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = new QuestionService();
    }

    public function testInsertSucceeds()
    {
        $text = TestHelper::text();
        $locale = TestHelper::locale();

        $question = $this->service->insert($text, $locale);

        $this->assertEquals($text, $question->getQuestion());
        $this->assertEquals($locale, $question->getLocale());
    }

    public function testGetQuestionBatchSucceeds()
    {
        $firstQuestion = TestHelper::text();
        $firstLocale = TestHelper::locale();
        $secondQuestion = TestHelper::text();
        $secondLocale = TestHelper::locale();
        TestHelper::createQuestion(['question' => $firstQuestion, 'locale' => $firstLocale]);
        TestHelper::createQuestion(['question' => $secondQuestion, 'locale' => $secondLocale]);

        $result = $this->service->selectBatch(2);

        $this->assertEquals($firstQuestion, $result->first()->question);
        $this->assertEquals($firstLocale, $result->first()->locale);
        $this->assertEquals($secondQuestion, $result->last()->question);
        $this->assertEquals($secondLocale, $result->last()->locale);
    }
}
