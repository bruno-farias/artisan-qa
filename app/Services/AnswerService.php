<?php


namespace App\Services;


use App\Models\Answer;
use App\Models\Question;
use Illuminate\Support\Collection;

class AnswerService
{
    public function insert(string $text, bool $correct, string $locale, Question $question): Answer
    {
        $answer = new Answer();
        $answer
            ->setOption($text)
            ->setCorrect($correct)
            ->setLocale($locale)
            ->question()->associate($question)
            ->save();

        return $answer;
    }

    public function getAnswersByQuestionId(int $id): Collection
    {
        return Answer::where('question_id', '=', $id)
            ->inRandomOrder()
            ->get(['id', 'option', 'correct']);
    }
}
