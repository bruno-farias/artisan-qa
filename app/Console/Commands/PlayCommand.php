<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\InputValidation;
use App\Services\AnswerService;
use App\Services\QuestionService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Spatie\Emoji\Emoji;

class PlayCommand extends Command
{
    use InputValidation;

    protected $signature = 'qanda:play {locale=en}';
    protected $description = 'Play Q&A game';
    private $questionService;
    private $answerService;
    public $locale;

    private $index = 1;

    public function __construct(QuestionService $questionService, AnswerService $answerService)
    {
        parent::__construct();
        $this->questionService = $questionService;
        $this->answerService = $answerService;
    }

    public function handle()
    {
        $this->locale = $this->argument('locale');
        $correctAnswers = 0;
        $this->info(__('qa.play_welcome', [], $this->locale));

        $totalQuestions = $this->askAmountOfQuestions();
        $questions = $this->remapQuestionKeys($this->questionService->selectBatch($totalQuestions));

        while (count($questions) > 0) {
            $this->table([__('qa.option'), __('qa.question')],
                $questions->map->only(['selection', 'question'])->toArray());

            $selectedOption = $this->chooseQuestion($questions);
            $question = $questions->firstWhere('selection', '=', $selectedOption);
            $answers = $this->remapAnswersKeys($this->answerService->getAnswersByQuestionId($question['id']));
            $this->table([__('qa.option'), __('qa.answer')], $answers->map->only(['selection', 'option'])->toArray());
            $selectedAnswer = $this->selectAnswer($answers);

            $chooseOption = $answers->firstWhere('selection', '=', $selectedAnswer);
            if ($chooseOption['correct']) {
                $correctAnswers++;
            }

            $questions = $questions->filter(function ($item) use ($selectedOption) {
                return $item['selection'] != $selectedOption;
            });
        }

        $this->showResults($correctAnswers, $totalQuestions);
    }

    public function remapQuestionKeys(Collection $collection): Collection
    {
        $this->index = 1;
        return $collection->map(function ($item) {
            return collect([
                'selection' => $this->index++,
                'id' => $item->id,
                'question' => $item->question
            ]);
        });
    }

    public function remapAnswersKeys(Collection $collection): Collection
    {
        $this->index = 1;
        return $collection->map(function ($item) {
            return collect([
                'selection' => $this->index++,
                'id' => $item->id,
                'option' => $item->option,
                'correct' => $item->correct
            ]);
        });
    }

    private function askAmountOfQuestions(): int
    {
        return (int)$this->askValid(
            __('qa.play_amount_question', [], $this->locale),
            __('qa.amount', [], $this->locale),
            ['required', 'numeric', 'min:1', 'max:5'],
            $this->locale
        );
    }

    private function chooseQuestion(Collection $questions): int
    {
        return (int)$this->askValid(
            __('qa.play_amount_id', [], $this->locale),
            __('qa.id', [], $this->locale),
            ['required', Rule::in($questions->pluck('selection'))],
            $this->locale
        );
    }

    private function selectAnswer(Collection $answers): int
    {
        return (int)$this->askValid(
            __('qa.play_select_answer', [], $this->locale),
            __('qa.option', [], $this->locale),
            ['required', Rule::in($answers->pluck('selection'))],
            $this->locale
        );
    }

    private function showResults(int $correctAnswers, int $totalQuestions)
    {
        $result = round(($correctAnswers * 100) / $totalQuestions);
        switch (true) {
            case $result <= 50:
                $emoji = Emoji::CHARACTER_LOUDLY_CRYING_FACE;
                break;
            case $result > 50 && $result < 70:
                $emoji = Emoji::CHARACTER_RELIEVED_FACE;
                break;
            case $result > 70 && $result < 100:
                $emoji = Emoji::CHARACTER_SMILING_FACE;
                break;
            default:
                $emoji = Emoji::CHARACTER_BRAIN;
                break;
        }

        $this->info(__('qa.play_result', [
            'correct' => $correctAnswers,
            'total' => $totalQuestions,
            'result' => $result,
            'emoji' => $emoji
        ]));
    }
}
