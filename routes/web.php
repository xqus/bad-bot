<?php

use Illuminate\Support\Facades\Route;

Route::get('robots.txt', function() {
    return response()
        ->view('bad-bot::robots')
        ->header('Content-Type', 'text/plain');
});