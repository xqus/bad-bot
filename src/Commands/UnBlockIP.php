<?php

namespace xqus\BadBot\Commands;

use Illuminate\Console\Command;

class UnBlockIP extends Command
{
    public $signature = 'badbot:unblock {ip : IP or range to block. For example 132.196.86.0/24}';

    public $description = 'Unmark an IP addresses or IP range as a bad bot.';

    public function handle(): int
    {
        return self::SUCCESS;
    }
}
