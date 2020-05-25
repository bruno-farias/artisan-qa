<?php


namespace Tests\Feature\Commands;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Emoji\Emoji;
use Tests\TestCase;
use Tests\TestHelper;

class PlayCommandTest extends TestCase
{
    use RefreshDatabase;
    use TestHelper;

    public function testPlayFlowSucceeds()
    {
        $questionText = TestHelper::text();
        $locale = TestHelper::locale();
        $question = TestHelper::createQuestion(['question' => $questionText, 'locale' => $locale]);
        TestHelper::createAnswer($question, true);
        $this->artisan('qanda:play', ['locale' => 'en'])
            ->expectsOutput('Welcome to Q&A playground')
            ->expectsQuestion('How many questions do you wanna try?', null)
            ->expectsOutput('The amount field is required.')
            ->expectsQuestion('How many questions do you wanna try?', 1)
            ->expectsQuestion('Which question ID do you want to try?', 1)
            ->expectsQuestion('Select the correct option', 1)
            ->expectsOutput('Correct answers: 1/1 100% ' . Emoji::CHARACTER_BRAIN)
            ->assertExitCode(0);
    }
}
