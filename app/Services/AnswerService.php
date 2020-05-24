<?php


namespace App\Services;


use App\Models\Answer;
use App\Models\Question;

class AnswerService
{
    public function insert(string $text, bool $correct, string $locale, Question $question): void
    {
        (new Answer())
            ->setOption($text)
            ->setCorrect($correct)
            ->setLocale($locale)
            ->question()->associate($question)
            ->save();
    }
}
