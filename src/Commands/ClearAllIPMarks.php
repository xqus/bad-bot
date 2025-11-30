<?php

namespace xqus\BadBot\Commands;

use Illuminate\Support\Facades\Cache;
use Illuminate\Console\Command;

class ClearAllIPMarks extends Command
{
    public $signature = 'badbot:clear-all-ip-marks';

    public $description = 'Clear all bad bot IPs';

    public function handle(): int
    {
        Cache::put('badbot-blocked-ips', []);

        return self::SUCCESS;
    }
}
