<?php


namespace Tests;


use App\Models\Answer;
use App\Models\Question;
use Faker\Factory;

trait TestHelper
{
    public static function text(int $maxNumOfChars = 200): string
    {
        return Factory::create()->text($maxNumOfChars);
    }

    public static function locale(): string
    {
        return Factory::create()->locale;
    }

    public static function bool(): bool
    {
        return Factory::create()->boolean();
    }

    public static function quantity(int $min = 1, int $max = 10): int
    {
        return Factory::create()->numberBetween($min, $max);
    }

    public static function createQuestion(array $params = []): Question
    {
        return \factory(Question::class)->create($params);
    }

    public static function createAnswer(Question $question)
    {
        return \factory(Answer::class)->create([
            'question_id' => $question->id
        ]);
    }
}
