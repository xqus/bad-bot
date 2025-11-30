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
        $requestId = (string) Str::uuid();

        Log::withContext([
            'badbot-request-id' => $requestId,
        ]);

        $startTime = microtime(true);

        $isBadBot = false;

        $isKnownBadBot = $this->isKnownBadBot($request);
        if($isKnownBadBot) {
            $isBadBot = true;
        } else {
            $isAllowedBot = $this->isAllowedBot($request);
        }
        

        $endTime = microtime(true);
        Log::debug('Handled incoming request in '.round($endTime - $startTime, 5).' seconds.');

        if ($isBadBot === false) {
            return $next($request);
        }

        if (config('bad-bot.block-known-bad-bots')) {
            Log::notice('Bad bot blocked');
            abort(429);
        }

        Log::warning('Bad bot identified but was not blocked');

        return $next($request);
    }

    private function isAllowedBot(Request $request): bool
    {
        $userAgent = $request->header('User-Agent');
        $allowedUserAgents = collect(config('bad-bot.allow-list'));

        $isKnown = $allowedUserAgents->contains(function ($value, $key) use ($request, $userAgent) {
            if(! str_contains(strtolower($userAgent), strtolower($key))) {
                return false;
            }

            $hostname = gethostbyaddr($request->ip());
            $isRealBot = str_contains(strtolower($hostname), strtolower($value));

            if(! $isRealBot) {
                Log::warning('Fake bot identified.', ['hostname' => $hostname]);
                return false;
            }

            Log::notice('Whitelisted bot identified.', ['hostname' => $hostname]);
            return true;
        });

        return false;
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

        $knownUserAgents = collect(config('bad-bot.deny-list'));

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
