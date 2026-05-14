<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
});

Route::get('/home', function () {
    return Inertia::render('Home');
});

Route::post('/authenticate', [AuthController::class, 'checkAuthPassword']);
