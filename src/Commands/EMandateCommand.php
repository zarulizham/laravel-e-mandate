<?php

namespace ZarulIzham\EMandate\Commands;

use Illuminate\Console\Command;

class EMandateCommand extends Command
{
    public $signature = 'laravel-e-mandate';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
