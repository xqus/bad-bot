<?php

namespace xqus\BadBot\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \xqus\BadBot\BadBot
 */
class BadBot extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \xqus\BadBot\BadBot::class;
    }
}
