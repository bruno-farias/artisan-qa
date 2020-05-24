<?php


namespace App\Console\Commands\Traits;


use App\Models\Question;
use Illuminate\Support\Facades\DB;

trait AskQuestion
{
    use InputValidation;

    public function addQuestion(): void
    {
        $keepAsking = true;

        while ($keepAsking) {
            DB::transaction(function () {
                $question = $this->validateQuestionInput();
                $options = $this->validateOptionsQuantityInput();
                $counter = 1;
                $this->insertOption($counter, $options, $question);
            });
            $keepAsking = $this->confirm(__('qa.keep_adding_new_question', [], $this->locale), true);
        }
    }

    public function validateQuestionInput(): Question
    {
        $text = $this->askValid(
            __('qa.new_question', [], $this->locale),
            __('qa.question', [], $this->locale),
            ['required', 'min:10'],
            $this->locale
        );
        return $this->questionService->insert($text, $this->locale);
    }

    private function validateOptionsQuantityInput(): int
    {
        return $this->askValid(
            __('qa.how_many_options', [], $this->locale),
            __('qa.options', [], $this->locale),
            ['required', 'numeric', 'between:1,10'],
            $this->locale
        );
    }

    private function insertOption(int $counter, int $options, Question $question): void
    {
        while ($counter <= $options) {
            $correct = false;
            $text = $this->validateAnswer($counter);
            if ($this->confirm(__('qa.confirm_option', ['option' => $text], $this->locale), true)) {

                if (!$question->hasCorrectOption()) {
                    $correct = $this->confirm(__('qa.correct_option', [], $this->locale), true);
                }

                $this->answerService->insert($text, $correct, $this->locale, $question);

                $counter++;
            }
        }
    }

    private function validateAnswer(int $counter): string
    {
        return $this->askValid(
            __('qa.enter_option', ['counter' => $counter], $this->locale),
            __('qa.answer', [], $this->locale),
            ['required'],
            $this->locale
        );
    }
}
