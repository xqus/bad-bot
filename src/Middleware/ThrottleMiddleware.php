<?php

namespace xqus\BadBot\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;
use xqus\BadBot\BadBotLog as Log;

class ThrottleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $perMinute = 5): Response
    {
        if ($request->getRealMethod() === 'POST') {
            return $next($request);
        }

        $rateLimiterkey = 'bad-bot:'.hash('sha256', $request->ip());
        if (RateLimiter::tooManyAttempts($rateLimiterkey, $perMinute)) {
            if (! $this->isAllowedToSkipThrottleLimits($request)) {
                Log::notice('Rate limit reached. Returning error 429');
                abort(429);
            }

            Log::notice('Rate limited request allowed. Reason: Whitelisted user-agent.');

            return $next($request);

        }

        RateLimiter::increment($rateLimiterkey);

        return $next($request);
    }

    private function isAllowedToSkipThrottleLimits(Request $request): bool
    {
        if (! $this->userAgentWhiteListed($request->header('User-Agent'))) {
            return false;
        }

        if (! $this->userAgentIpValidated($request->header('User-Agent'), $request->ip())) {
            Log::warning('Hostname does not match expected hostname for whitelisted user-agent.');

            return false;
        }

        return true;
    }

    private function userAgentWhiteListed($userAgent): bool
    {
        $allowedUserAgents = collect(config('bad-bot.allow-list'));

        return $allowedUserAgents->contains(function ($value, $key) use ($userAgent) {
            return str_contains(strtolower($userAgent), strtolower($key));
        });
    }

    private function userAgentIpValidated($userAgent, $ipAddress): bool
    {
        $allowedUserAgents = collect(config('bad-bot.allow-list'));
        $allowedHostname = $allowedUserAgents->filter(function (string $value, string $key) use ($userAgent) {
            return str_contains(strtolower($userAgent), strtolower($key));
        })->first();

        $resolvedHostname = gethostbyaddr($ipAddress);

        return str_contains(strtolower($resolvedHostname), strtolower($allowedHostname));
    }
}
