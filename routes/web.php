<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
});

Route::get('/owner', function () {
    return ['owner' => 'Mauro van der Duim', 'status' => 'success'];
});
