<?php


namespace App\Console\Commands\Traits;


use Illuminate\Validation\Rule;

trait PlayQA
{
    public function playQA()
    {
        $this->info('Welcome to Q&A playground');

        $totalQuestions = $this->askValid(
            __('qa.play_amount_question', [], $this->locale),
            __('qa.amount', [], $this->locale),
            ['required', 'numeric', 'min:1', 'max:5'],
            $this->locale
        );

        $correctAnswers = 0;
        $questions = $this->questionService->selectBatch((int)$totalQuestions);

        while (count($questions) > 0) {

            $this->table(['ID', 'Question'], $questions->toArray());

            $selectedQuestion = $this->askValid(
                __('qa.play_amount_id', [], $this->locale),
                __('qa.id', [], $this->locale),
                ['required', Rule::in($questions->pluck('id'))],
                $this->locale
            );

            $answers = $this->answerService->getAnswers($selectedQuestion);

            $this->table(['ID', 'Option'], $answers->map->only(['id', 'option'])->toArray());

            $selectedAnswer = $this->askValid(
                __('qa.play_select_answer', [], $this->locale),
                __('qa.option', [], $this->locale),
                ['required', Rule::in($answers->pluck('id'))],
                $this->locale
            );

            $chooseOption = $answers->firstWhere('id', '=', $selectedAnswer);
            if ($chooseOption->correct) {
                $correctAnswers++;
            }

            $questions = $questions->filter(function ($item) use ($selectedQuestion) {
                return $item->id != $selectedQuestion;
            });
        }

        $this->info("Correct answers: $correctAnswers/$totalQuestions");
    }
}
