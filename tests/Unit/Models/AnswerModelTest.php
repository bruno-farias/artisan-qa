<?php


namespace Tests\Unit\Models;


use App\Models\Answer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestHelper;

class AnswerModelTest extends TestCase
{
    use TestHelper;
    use RefreshDatabase;

    public function testCreateAnswerSucceeds()
    {
        $question = TestHelper::createQuestion();

        $option = TestHelper::text();
        $correct = TestHelper::bool();
        $locale = TestHelper::locale();

        $answer = new Answer();
        $answer
            ->setOption($option)
            ->setCorrect($correct)
            ->setLocale($locale)
            ->question()
            ->associate($question)
            ->save();

        $this->assertDatabaseHas($answer->getTable(), [
            'question_id' => $question->id,
            'option' => $option,
            'correct' => $correct,
            'locale' => $locale
        ]);
    }
}
