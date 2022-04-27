<?php

namespace App\Console\Commands;

use App\Models\Config;
use Illuminate\Console\Command;

class CacheConfig extends Command
{
    protected $signature = 'admin:cache-config';
    protected $description = 'Refresh app-config cache';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Config::clearConfigCache();
        Config::getDottedConfigFromCache();

        $this->info('Config re-cached!');

        return 0;
    }
}
