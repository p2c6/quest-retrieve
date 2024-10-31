<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestSchedulerCommand extends Command
{
    protected $signature = 'test:scheduler';
    protected $description = 'Logs a test message to confirm scheduler is working';

    public function handle()
    {
        Log::info('Scheduler is working as expected!');
        $this->info('Scheduler test ran successfully.');
    }
}
