<?php


namespace Tests\Feature\Commands;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Emoji\Emoji;
use Tests\TestCase;
use Tests\TestHelper;

class AddQuestionTest extends TestCase
{
    use RefreshDatabase;
    use TestHelper;

    public function testAddQuestionSucceeds()
    {
        $question = TestHelper::text();
        $option1 = TestHelper::text(50);
        $option2 = TestHelper::text(50);
        $this->artisan('qanda:add', ['locale' => 'en'])
            ->expectsQuestion('What is the new question to add to the database?', $question)
            ->expectsQuestion('How many options, including the correct answer will be provided? (numeric)', 2)
            ->expectsQuestion('Enter option 1', $option1)
            ->expectsQuestion("Do you confirm option: $option1", 'yes')
            ->expectsQuestion('Is this the correct option?', 'yes')
            ->expectsQuestion('Enter option 2', $option2)
            ->expectsQuestion("Do you confirm option: $option2", 'yes')
            ->expectsQuestion('Add other question?', false)
            ->assertExitCode(0);
    }

    public function testAddQuestionWithoutCorrectAnswerFails()
    {
        $question = TestHelper::text();
        $option1 = TestHelper::text(50);
        $option2 = TestHelper::text(50);
        $this->artisan('qanda:add', ['locale' => 'en'])
            ->expectsQuestion('What is the new question to add to the database?', $question)
            ->expectsQuestion('How many options, including the correct answer will be provided? (numeric)', 2)
            ->expectsQuestion('Enter option 1', $option1)
            ->expectsQuestion("Do you confirm option: $option1", true)
            ->expectsQuestion('Is this the correct option?', false)
            ->expectsQuestion('Enter option 2', $option2)
            ->expectsQuestion("Do you confirm option: $option2", true)
            ->expectsQuestion('Is this the correct option?', false)
            ->expectsOutput('Question should have at least one true answer! Add it now! ' . Emoji::CHARACTER_ANGRY_FACE)
            ->expectsQuestion('Enter option 2', $option2)
            ->expectsQuestion("Do you confirm option: $option2", true)
            ->expectsQuestion('Is this the correct option?', true)
            ->expectsQuestion('Add other question?', false)
            ->assertExitCode(0);
    }
}
