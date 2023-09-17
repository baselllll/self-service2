<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessQueue extends Command
{
    protected $signature = 'process:queue';
    protected $description = 'Process the Laravel queue';

    public function handle()
    {
        $this->info('Running queue worker...');
        \Artisan::call('queue:work');
        $this->info('Queue worker finished.');
    }
}
