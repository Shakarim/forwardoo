<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AllocController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/alloc', [AllocController::class, 'index']);
