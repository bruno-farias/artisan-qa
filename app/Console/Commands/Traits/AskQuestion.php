<?php


namespace App\Console\Commands\Traits;


use App\Models\Question;
use App\Services\AnswerService;
use App\Services\QuestionService;
use Illuminate\Support\Facades\DB;
use Spatie\Emoji\Emoji;

trait AskQuestion
{
    use InputValidation;

    public function addQuestion(QuestionService $questionService, AnswerService $answerService): void
    {
        $keepAsking = true;

        while ($keepAsking) {
            DB::transaction(function () use ($questionService, $answerService) {
                $text = $this->validateQuestionInput();
                $question = $questionService->insert($text, $this->locale);
                $options = $this->validateOptionsQuantityInput();
                $counter = 1;
                $this->insertOption($answerService, $counter, $options, $question);
            });
            $keepAsking = $this->confirm(__('qa.keep_adding_new_question', [], $this->locale), true);
        }
    }

    public function validateQuestionInput(): string
    {
        return $this->askValid(
            __('qa.new_question', [], $this->locale),
            __('qa.question', [], $this->locale),
            ['required', 'min:10'],
            $this->locale
        );
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

    private function insertOption(AnswerService $answerService, int $counter, int $options, Question $question): void
    {
        while ($counter <= $options) {
            $correct = false;
            $text = $this->validateAnswer($counter);
            if ($this->confirm(__('qa.confirm_option', ['option' => $text], $this->locale), true)) {

                if (!$question->hasCorrectOption()) {
                    $correct = $this->confirm(__('qa.correct_option', [], $this->locale), true);
                }

                $answerService->insert($text, $correct, $this->locale, $question);

                $counter++;
                if ($counter > $options && !$question->hasCorrectOption()) {
                    $this->error(__('qa.add_question_should_have_true', ['emoji' => Emoji::CHARACTER_ANGRY_FACE],
                        $this->locale));
                    $counter--;
                }
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
