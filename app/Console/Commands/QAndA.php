<?php

namespace App\Console\Commands;


use App\Console\Commands\Traits\InputValidation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class QAndA extends Command
{
    use InputValidation;

    protected $signature = 'qanda:interactive';
    protected $description = 'Runs an interactive command line based Q And A system.';
    private $locale;
    private const LOCALES = [
        'en',
        'pt-br'
    ];

    public function handle(): void
    {
        $running = true;
        $this->locale = self::LOCALES[0];

        while ($running) {
            $option = $this->choice(__('qa.choose_option', [], $this->locale), $this->getInitialOptions(), 1);

            switch ($option) {
                case $this->getInitialOptions()[0]: // Insert Question Manually
                    $this->call('qanda:add', ['locale' => $this->locale]);
                    break;
                case $this->getInitialOptions()[1]: // Play
                    $this->call('qanda:play', ['locale' => $this->locale]);
                    break;
                case $this->getInitialOptions()[2]: // Language options
                    $this->locale = $this->choice(__('qa.choose_option', [], $this->locale), self::LOCALES);
                    break;
                case $this->getInitialOptions()[3]: // Populate automatically
                    $this->call('qanda:populate', ['locale' => $this->locale]);
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
}
