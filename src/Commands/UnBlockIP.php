<?php

namespace xqus\BadBot\Commands;

use Exception;
use Illuminate\Console\Command;

class UnBlockIP extends Command
{
    public $signature = 'badbot:update-openai-ips';

    public $description = 'Update the list of known OpenAI IP addresses';

    public function handle(): int
    {
        
    }
}
