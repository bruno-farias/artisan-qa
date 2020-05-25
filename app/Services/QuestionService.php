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
        return Question::all(['id', 'question', 'locale'])->random($limit);
    }

    public function insertBatch(array $items, string $locale = 'en'): void
    {
        $answerService = new AnswerService();
        foreach ($items as $item) {
            $question = $this->insert(base64_decode($item->question), $locale);
            $answerService->insert(base64_decode($item->correct_answer), true, $locale, $question);
            foreach ($item->incorrect_answers as $incorrect_answer) {
                $answerService->insert(base64_decode($incorrect_answer), false, $locale, $question);
            }
        }
    }
}
