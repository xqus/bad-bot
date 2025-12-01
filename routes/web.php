<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use xqus\BadBot\Middleware\ThrottleMiddleware;

if (App::environment() != 'testing') {
    return;
}

Route::get('/', function () {
    return 'hello-world';
});

Route::get('/rate-limit', function () {
    return 'hello-world';
})->middleware(ThrottleMiddleware::class);
