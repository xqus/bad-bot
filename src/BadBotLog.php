<?php

namespace xqus\BadBot;

use Illuminate\Support\Facades\Log;

class BadBotLog
{
    public static function debug($message, $context = [])
    {
        Log::debug($message, self::addContext($context));
    }

    public static function notice($message, $context = [])
    {
        Log::notice($message, self::addContext($context));
    }

    public static function warning($message, $context = [])
    {
        Log::warning($message, self::addContext($context));
    }

    public static function withContext($context)
    {
        Log::withContext($context);
    }

    private static function addContext($context)
    {
        $context['user-agent'] = request()->header('User-Agent');
        $context['ip-address'] = request()->ip();
        $context['reported-by'] = 'BadBot';

        return $context;
    }
}
