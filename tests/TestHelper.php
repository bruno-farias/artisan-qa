<?php


namespace Tests;


use App\Models\Question;
use Faker\Factory;

trait TestHelper
{
    public static function question(): string
    {
        return Factory::create()->words(10, true);
    }

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

    public static function createQuestion(array $params = []): Question
    {
        return \factory(Question::class)->create($params);
    }
}
