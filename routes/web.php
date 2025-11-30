<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;



if(App::environment() != 'testing') {
    return;
}

Route::get('/', function () {
    return 'hello-world';
});

