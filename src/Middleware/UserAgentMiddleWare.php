<?php

namespace xqus\BadBot\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use xqus\BadBot\BadBotLog as Log;

class UserAgentMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isKnownBadBot = $this->isKnownBadUserAgent($request);

        if ($isKnownBadBot === false) {
            return $next($request);
        }

        Log::notice('Request blocked based on user agent');
        abort(429);

        return $next($request);
    }

    private function isKnownBadUserAgent(Request $request): bool
    {
        $userAgent = $request->header('User-Agent');

        $knownUserAgents = collect(config('bad-bot.deny-list'));

        $isKnown = $knownUserAgents->contains(function ($value) use ($userAgent) {
            return str_contains(strtolower($userAgent), strtolower($value));
        });

        return $isKnown;
    }
}
