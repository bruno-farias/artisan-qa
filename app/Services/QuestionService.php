<?php


namespace App\Services;


use App\Models\Question;

class QuestionService
{
    public function insert(string $text, string $locale): Question
    {
        $question = new Question();
        $question
            ->setQuestion($text)
            ->setLocale($locale)
            ->save();
        return $question;
    }
}
