<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use xqus\BadBot\Events\RequestRateLimited;
use xqus\BadBot\Middleware\ThrottleMiddleware;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\Support\Models\User;

pest()->use(RefreshDatabase::class);

test('throttle middleware serves requests', function () {
    $middleware = new ThrottleMiddleware;
    $request = new Request;

    $next = function () {
        return response('This is a secret place');
    };


    $response = $middleware->handle($request, $next);
    $this->assertEquals('This is a secret place', $response->getContent());
    $this->assertEquals(200,  $response->getStatusCode());

});

test('requests are throttled', function () {
    Event::fake();

    $response = $this->get('/rate-limit');
    $limit = 10;

    for($i=0; $i<$limit; $i++) {
        $response = $this->get('/rate-limit');    
    }

    $this->assertEquals(429,  $response->getStatusCode());
});

test('authenticated requests can skip rate limit', function () {
    Event::fake();

    Artisan::call('migrate', ['--path' => __DIR__ . '/Support/database/migrations/create_users_table.php', '--realpath' => true]);
    $user = User::factory()->create();
    Config::set('rate-limit-authenticated-requests', false);

    $response = $this->actingAs($user)->get('/rate-limit');
    $limit = 10;

    for($i=0; $i<$limit; $i++) {
        $response = $this->actingAs($user)->get('/rate-limit');    
    }

    $this->assertEquals(200,  $response->getStatusCode());
});

test('authenticated requests can be rate limited', function () {
    Event::fake();

    Artisan::call('migrate', ['--path' => __DIR__ . '/Support/database/migrations/create_users_table.php', '--realpath' => true]);
    $user = User::factory()->create();
    Config::set('rate-limit-authenticated-requests', true);

    $response = $this->actingAs($user)->get('/rate-limit');
    $limit = 10;

    for($i=0; $i<$limit; $i++) {
        $response = $this->actingAs($user)->get('/rate-limit');    
    }

    $this->assertEquals(429,  $response->getStatusCode());
});

test('an event is dispatched when a request is rate limited', function () {
    Event::fake();

    $response = $this->get('/rate-limit');
    $limit = 10;

    for($i=0; $i<$limit; $i++) {
        $response = $this->get('/rate-limit');    
    }

    Event::assertDispatched(RequestRateLimited::class);
});

test('whtelisted user agents can skip rate limit', function() {});
test('event is fired when whtelisted user agents skip rate limit', function() {});
test('failing reverse dns lookup enables rate limit', function() {});
test('failing reverse dns lookup fires an event', function() {});
test('post requests are not rate limited', function() {});

