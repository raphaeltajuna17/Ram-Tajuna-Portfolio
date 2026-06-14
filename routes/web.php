<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatController;

Route::get('/', [CatController::class, 'index']);
Route::post('/adopt', [CatController::class, 'adopt']);
Route::post('/interact', [CatController::class, 'interact']);