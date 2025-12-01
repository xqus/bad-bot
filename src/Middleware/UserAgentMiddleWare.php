<?php

namespace xqus\BadBot\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use xqus\BadBot\Events\RequestBlockedByUserAgent;

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
        RequestBlockedByUserAgent::dispatch($request);
        abort(403);
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
