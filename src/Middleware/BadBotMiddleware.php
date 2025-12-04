<?php

namespace xqus\BadBot\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use PhpIP\IPBlock;
use Symfony\Component\HttpFoundation\Response;
use xqus\BadBot\Exceptions\IpAddressBlockedException;

class BadBotMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isKnownBadBot = $this->isKnownBadIPAddress($request);

        if ($isKnownBadBot === false) {
            return $next($request);
        }

        throw new IpAddressBlockedException(429);
    }

    private function isKnownBadIPAddress(Request $request): bool
    {
        $blockedIPRanges = collect(Cache::get('badbot-blocked-ips', []));

        $isKnown = $blockedIPRanges->contains(function ($value) use ($request) {
            $ipBlock = IPBlock::create($value);

            return $ipBlock->contains($request->ip());
        });

        return $isKnown;
    }
}
