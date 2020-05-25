<?php

namespace App\Console\Commands;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Console\Command;

class ResetQA extends Command
{
    protected $signature = 'qanda:reset';
    protected $description = 'Reset game database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->call('migrate:fresh');
        $this->info('Game was refreshed');
    }
}
