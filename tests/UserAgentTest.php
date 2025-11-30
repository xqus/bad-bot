<?php

use Illuminate\Http\Request;
use xqus\BadBot\Middleware\UserAgentMiddleWare;



test('requests are handled', function () {
    $middleware = new UserAgentMiddleWare;
    $request = new Request;

    $next = function () {
        return response('This is a secret place');
    };

    $response = $middleware->handle($request, $next);

    $this->assertEquals('This is a secret place', $response->getContent());
 
});

test('a user agent on the deny list is blocked', function () {
    $middleware = new UserAgentMiddleWare;
    $request = new Request;
    $request->headers->set('User-Agent', 'Omgilibot');

    $next = function () {
        return response('This is a secret place');
    };

    try {
        $response = $middleware->handle($request, $next);        
    } catch(Exception $e) {
        dump($e);
        $this->assertEquals(503, $e->getStatusCode());
    }
});

test('a user agent is matched properly', function () {
    $middleware = new UserAgentMiddleWare;
    $request = new Request;
    $request->headers->set('User-Agent', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Omgilibot/0.1; +https://developer.amazon.com/support/amazonbot) Chrome/119.0.6045.214 Safari/537.3 ');

    $next = function () {
        return response('This is a secret place');
    };

    try {
        $response = $middleware->handle($request, $next);        
    } catch(Exception $e) {
        dump($e);
        $this->assertEquals(503, $e->getStatusCode());
    }
});