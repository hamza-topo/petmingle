<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\PetController;
use App\Http\Controllers\Api\RaceController;
use App\Http\Controllers\Api\SpeciesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('v.0')->group(function () {
    Route::post('/sign-in', [AuthController::class, 'signIn']);
    Route::post('/sign-up', [AuthController::class, 'signUp']);
    Route::post('/sign-out', [AuthController::class, 'signOut']);
});

/*
|--------------------------------------------------------------------------
| Api Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API Ayth routes for app. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('v.0')->middleware('jwt.verify')->group(function () {

    Route::put('/locations/restore/{id}', [LocationController::class, 'restore']);
    Route::post('/locations/nears/', [LocationController::class, 'near']);
    Route::resources(['locations' => LocationController::class]);

    Route::put('/pets/restore/{id}', [PetController::class, 'restore']);
    Route::resources(['pets' => PetController::class]);

    Route::put('/races/restore/{id}', [RaceController::class, 'restore']);
    Route::resources(['races' => RaceController::class]);

    Route::put('/species/restore/{id}', [SpeciesController::class, 'restore']);
    Route::resources(['species' => SpeciesController::class]);
});
