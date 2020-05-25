<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\InputValidation;
use App\Services\Clients\OpenTriviaDBClient;
use App\Services\QuestionService;
use Illuminate\Console\Command;

class PopulateQA extends Command
{
    use InputValidation;

    protected $signature = 'qanda:populate {locale=en}';
    protected $description = 'Populate database with Q&A automatically';
    private $openTriviaDBClient;
    private $questionService;
    public $locale;

    public function __construct(OpenTriviaDBClient $openTriviaDBClient, QuestionService $questionService)
    {
        parent::__construct();
        $this->openTriviaDBClient = $openTriviaDBClient;
        $this->questionService = $questionService;
    }

    public function handle()
    {
        $this->locale = $this->argument('locale');
        if ($this->locale !== 'en') {
            $this->error(__('qa.populate_error', [], $this->locale));
        } else {
            $totalQuestions = $this->askAmountQaToInsert();
            $items = $this->openTriviaDBClient->fetchQAndA($totalQuestions);
            $this->questionService->insertBatch($items, $this->locale);
            $this->info(__('qa.success'));
        }
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
