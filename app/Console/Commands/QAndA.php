<?php

namespace App\Console\Commands;


use App\Models\Question;
use App\Services\AnswerService;
use App\Services\QuestionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class QAndA extends Command
{
    use InputValidation;

    protected $signature = 'qanda:interactive';
    protected $description = 'Runs an interactive command line based Q And A system.';

    private const LOCALES = [
        'en',
        'pt-br'
    ];
    private $locale;
    private $questionService;
    private $answerService;

    public function __construct(QuestionService $questionService, AnswerService $answerService)
    {
        parent::__construct();
        $this->questionService = $questionService;
        $this->answerService = $answerService;
    }

    public function handle(): void
    {
        $running = true;
        $this->locale = self::LOCALES[0];

        while ($running) {
            $option = $this->choice(__('qa.choose_option', [], $this->locale), $this->getInitialOptions(), 0);

            switch ($option) {
                case $this->getInitialOptions()[0]:
                    $this->addQuestion();
                    break;
                case $this->getInitialOptions()[1]:
                    $this->info('Answers');
                    break;
                case $this->getInitialOptions()[2]:
                    $this->locale = $this->choice(__('qa.choose_option', [], $this->locale), self::LOCALES);
                    break;
                default:
                    $this->info(__('qa.message_thanks', [], $this->locale));
                    $running = false;
            }
        }
    }

    private function addQuestion(): void
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

    private function validateQuestionInput(): Question
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

    private function validateAnswer(int $counter): string
    {
        return $this->askValid(
            __('qa.enter_option', ['counter' => $counter], $this->locale),
            __('qa.answer', [], $this->locale),
            ['required'],
            $this->locale
        );
    }

    private function getInitialOptions(): array
    {
        return [
            __('qa.add_question', [], $this->locale),
            __('qa.practice', [], $this->locale),
            __('qa.locale', [], $this->locale),
            __('qa.exit', [], $this->locale),
        ];
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
}
