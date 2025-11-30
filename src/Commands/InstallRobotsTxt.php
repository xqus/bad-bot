<?php

namespace xqus\BadBot\Commands;

use Illuminate\Console\Command;

class InstallRobotsTxt extends Command
{
    public $signature = 'badbot:update-txt';

    public $description = 'Update your public\robots.txt file.';

    public function handle(): int
    {
        $publicRobotsTxtFilePath = public_path('robots.txt');
        $robotsTxt = view('bad-bot::robots')->render();

        file_put_contents($publicRobotsTxtFilePath, $robotsTxt, LOCK_EX);

        return self::SUCCESS;
    }
}
