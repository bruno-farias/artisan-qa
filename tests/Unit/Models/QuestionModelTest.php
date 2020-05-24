<?php


namespace Tests\Unit\Models;


use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestHelper;

class QuestionModelTest extends TestCase
{
    use TestHelper;
    use RefreshDatabase;

    public function testCreateQuestionSucceeds()
    {
        $text = TestHelper::text();
        $locale = TestHelper::locale();

        $question = new Question();
        $question
            ->setQuestion($text)
            ->setLocale($locale)
            ->save();

        $this->assertDatabaseHas($question->getTable(), [
            'question' => $text,
            'locale' => $locale
        ]);
    }

    public function testCreateQuestionWithMoreThan255CharsSucceeds()
    {
        $text = TestHelper::text(1000);
        $locale = TestHelper::locale();

        $question = new Question();
        $question
            ->setQuestion($text)
            ->setLocale($locale)
            ->save();

        $this->assertDatabaseHas($question->getTable(), [
            'question' => $text,
            'locale' => $locale
        ]);
    }
}
