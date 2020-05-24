<?php


namespace App\Services;


use App\Models\Question;
use Illuminate\Support\Collection;

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

    public function selectBatch(int $limit = 5): Collection
    {
        return Question::all(['id', 'question'])->random($limit);
    }
}
