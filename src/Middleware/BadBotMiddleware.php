<?php

namespace xqus\BadBot\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use PhpIP\IPBlock;
use Symfony\Component\HttpFoundation\Response;
use xqus\BadBot\BadBotLog as Log;

class BadBotMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (strtolower($request->getRequestUri()) == '/robots.txt') {
            return $next($request);
        }

        $requestId = (string) Str::uuid();

        Log::withContext([
            'badbot-request-id' => $requestId,
        ]);

        $startTime = microtime(true);
        $isKnownBadBot = $this->isKnownBadBot($request);

        $endTime = microtime(true);
        Log::debug('Handled incoming request in '.round($endTime - $startTime, 5).' seconds.');

        if (! $isKnownBadBot) {
            return $next($request);
        }

        if (config('bad-bot.block-known-bad-bots')) {
            Log::notice('Bad bot blocked');
            abort(429);
        }

        Log::warning('Bad bot identified but was not blocked');

        return $next($request);
    }

    private function isKnownBadBot(Request $request): bool
    {
        if ($this->isKnownBadUserAgent($request)) {
            return true;
        }

        if ($this->isKnownBadIPAddress($request)) {
            return true;
        }

        return false;
    }

    private function isKnownBadUserAgent(Request $request): bool
    {
        $userAgent = $request->header('User-Agent');

        $knownUserAgents = collect(config('bad-bot.user-agents'));

        $isKnown = $knownUserAgents->contains(function ($value) use ($userAgent) {
            return str_contains(strtolower($userAgent), strtolower($value));
        });

        if ($isKnown) {
            Log::notice('A bad bot was identified by User-Agent: {user-agent}');
        }

        return $isKnown;
    }

    private function isKnownBadIPAddress(Request $request): bool
    {
        $blockedIPRanges = collect(Cache::get('badbot-blocked-ips', []));

        $isKnown = $blockedIPRanges->contains(function ($value) use ($request) {
            $ipBlock = IPBlock::create($value);

            return $ipBlock->contains($request->ip());
        });

        if ($isKnown) {
            Log::notice('A bad bot was identified by IP address: {ip-address}');
        }

        return $isKnown;
    }
}
