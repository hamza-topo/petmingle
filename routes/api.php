<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PetController;
use App\Http\Controllers\Api\RaceController;
use App\Http\Controllers\Api\SpeciesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v.0')->group(function () {
    Route::post('/sign-in', [AuthController::class, 'signIn']);
    Route::post('/sign-up', [AuthController::class, 'signUp']);
    Route::post('/sign-out', [AuthController::class, 'signOut']);
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API Ayth routes for app. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('v.0')->middleware('jwt.verify')->group(function () {
    Route::resources(['pets' => PetController::class]);
    Route::resources(['races' => RaceController::class]);
    Route::put('/races/restore/{id}', [RaceController::class, 'restore']);
    Route::resources(['species' => SpeciesController::class]);
    Route::put('/species/restore/{id}', [SpeciesController::class, 'restore']);
});
