<?php

namespace App\Console\Commands;


use App\Console\Commands\Traits\AskQuestion;
use App\Console\Commands\Traits\PlayQA;
use App\Services\AnswerService;
use App\Services\Clients\OpenTriviaDBClient;
use App\Services\QuestionService;
use Illuminate\Console\Command;

class QAndA extends Command
{
    use AskQuestion;
    use PlayQA;

    protected $signature = 'qanda:interactive';
    protected $description = 'Runs an interactive command line based Q And A system.';

    private const LOCALES = [
        'en',
        'pt-br'
    ];
    private $locale;
    private $questionService;
    private $answerService;
    private $openTriviaDBClient;

    public function __construct(
        QuestionService $questionService,
        AnswerService $answerService,
        OpenTriviaDBClient $openTriviaDBClient
    ) {
        parent::__construct();
        $this->questionService = $questionService;
        $this->answerService = $answerService;
        $this->openTriviaDBClient = $openTriviaDBClient;
    }

    public function handle(): void
    {
        $running = true;
        $this->locale = self::LOCALES[0];

        while ($running) {
            $option = $this->choice(__('qa.choose_option', [], $this->locale), $this->getInitialOptions(), 1);

            switch ($option) {
                case $this->getInitialOptions()[0]: // Insert Question Manually
                    $this->addQuestion($this->questionService, $this->answerService);
                    break;
                case $this->getInitialOptions()[1]: // Play
                    $this->playQA($this->questionService, $this->answerService);
                    break;
                case $this->getInitialOptions()[2]: // Language options
                    $this->locale = $this->choice(__('qa.choose_option', [], $this->locale), self::LOCALES);
                    break;
                case $this->getInitialOptions()[3]: // Populate automatically
                    if ($this->locale !== 'en') {
                        $this->error(__('qa.populate_error', [], $this->locale));
                        break;
                    }
                    $totalQuestions = $this->askAmountQaToInsert();
                    $this->openTriviaDBClient->fetchQAndA($totalQuestions);
                    $this->info(__('qa.success'));
                    break;
                default:
                    $this->info(__('qa.message_thanks', [], $this->locale));
                    $running = false;
            }
        }
    }

    private function getInitialOptions(): array
    {
        return [
            __('qa.add_question', [], $this->locale),
            __('qa.practice', [], $this->locale),
            __('qa.locale', [], $this->locale),
            __('qa.populate', [], $this->locale),
            __('qa.exit', [], $this->locale),
        ];
    }

    private function askAmountQaToInsert(): int
    {
        return (int)$this->askValid(
            __('qa.amount_question', [], $this->locale),
            __('qa.amount', [], $this->locale),
            ['required', 'numeric', 'min:1', 'max:50'],
            $this->locale
        );
    }
}
