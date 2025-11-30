<?php

namespace xqus\BadBot\Commands;

use Illuminate\Console\Command;

class AddOpenAiIPsToIPTable extends Command
{
    public $signature = 'badbot:update-openai-ips';

    public $description = 'Update the list of known OpenAI IP addresses';

    public function handle(): int
    {

        return self::SUCCESS;
    }
}
