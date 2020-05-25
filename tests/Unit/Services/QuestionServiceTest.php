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

    public function setUp(): void
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

    public function testInsertBatchSucceeds()
    {
        $question = 'Which rock band released the album "The Bends" in March 1995?';
        $correctAnswer = 'Radiohead';
        $option1 = 'Nirvana';
        $option2 = 'Coldplay';
        $option3 = 'U2';

        $items = [
            $this->buildItemPayload($question, $correctAnswer, $option1, $option2, $option3),
            $this->buildItemPayload()
        ];

        $this->service->insertBatch($items);

        $this->assertDatabaseHas('questions', [
            'question' => $question
        ]);

        foreach ($items as $item) {
            $this->assertDatabaseHasCorrectAnswer($item);
            $this->assertDatabaseHasIncorrectAnswers($item);
        }

    }

    private function buildItemPayload(
        string $question = null,
        string $correctAnswer = null,
        string $op1 = null,
        string $op2 = null,
        string $op3 = null
    ): \stdClass {
        $question = $question ?? TestHelper::text();
        $correctAnswer = $correctAnswer ?? TestHelper::text(50);
        $op1 = $op1 ?? TestHelper::text(10);
        $op2 = $op2 ?? TestHelper::text(10);
        $op3 = $op3 ?? TestHelper::text(10);
        $item = [
            'category' => base64_encode(TestHelper::text()),
            'type' => base64_encode('multiple'),
            'difficulty' => base64_encode('medium'),
            'question' => base64_encode($question),
            'correct_answer' => base64_encode($correctAnswer),
            'incorrect_answers' => [
                base64_encode($op1),
                base64_encode($op2),
                base64_encode($op3),
            ]
        ];
        return json_decode(json_encode($item));
    }

    private function assertDatabaseHasCorrectAnswer($item): void
    {
        $this->assertDatabaseHas('answers', [
            'option' => base64_decode($item->correct_answer),
            'correct' => true
        ]);
    }

    private function assertDatabaseHasIncorrectAnswers($item): void
    {
        foreach ($item->incorrect_answers as $incorrect_answer) {
            $this->assertDatabaseHas('answers', [
                'option' => base64_decode($incorrect_answer),
                'correct' => false
            ]);
        }
    }

}
