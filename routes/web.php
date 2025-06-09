<?php

use Dedoc\Scramble\Scramble;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('docs')->group(function () {
    Scramble::registerUiRoute('/api/v1'); // serves the docs UI
    Scramble::registerJsonSpecificationRoute('/api/v1/api.json'); // serves api.json
});