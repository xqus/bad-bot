<?php

namespace xqus\BadBot\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use PhpIP\IPBlock;

class BlockIP extends Command
{
    public $signature = 'badbot:mark-ip {ip : IP or range to block. For example 132.196.86.0/24}';

    public $description = 'Mark an IP addresses or IP range as a bad bot.';

    public function handle(): int
    {
        $inputIp = $this->argument('ip');

        if (strpos($inputIp, '/') === false) {
            $inputIp = $inputIp.'/32';
        }

        $ipBlock = IPBlock::create($inputIp);

        $blockedIPRanges = collect(Cache::get('badbot-blocked-ips', []));

        $blockedIPRanges->put($inputIp, $inputIp);
        Cache::put('badbot-blocked-ips', $blockedIPRanges);

        return self::SUCCESS;
    }
}
