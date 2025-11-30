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

        /** @var view-string $viewName */
        $viewName = 'bad-bot::robots';

        $robotsTxt = view($viewName)->render();

        file_put_contents($publicRobotsTxtFilePath, $robotsTxt, LOCK_EX);

        return self::SUCCESS;
    }
}
