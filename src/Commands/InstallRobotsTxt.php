<?php

namespace xqus\BadBot\Commands;

use Exception;
use Illuminate\Console\Command;

class InstallRobotsTxt extends Command
{
    public $signature = 'badbot:clear-ip';

    public $description = 'Remove IP mark';

    public function handle(): int
    {
        $publicRobotsTxtFilePath = public_path('robots.txt');
        if(! file_exists($publicRobotsTxtFilePath)) {
            $this->info('Static robots.txt not found. Dynamic robots.txt enabled.');
            return self::SUCCESS;
        }

        try {
            unlink($publicRobotsTxtFilePath);
            $this->info('Static robots.txt removed. Dynamic robots.txt enabled.');
            return self::SUCCESS;
        }
        catch(Exception $error) {
            $this->error('Unable to remove robots.txt: '. $error->getMessage());
            return self::FAILURE;
        }      
    }
}
