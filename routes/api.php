<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlockController;
use App\Http\Controllers\Api\DislikeController;
use App\Http\Controllers\Api\FilterController;
use App\Http\Controllers\Api\LangController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\PetController;
use App\Http\Controllers\Api\RaceController;
use App\Http\Controllers\Api\SpeciesController;
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

    Route::put('remove-avatar/{id}', [AuthController::class, 'removeAvatar']);
    Route::delete('disable-account/{id}', [AuthController::class, 'disable']);
    Route::put('enable-account/{id}', [AuthController::class, 'enable']);

    Route::prefix('preferences')->group(function () {
        Route::put('langs/{lang}', [LangController::class, 'set']);
        Route::get('langs/current', [LangController::class, 'current']);
        Route::get('langs', [LangController::class, 'index']);
    });

    Route::put('/pets/restore/{id}', [PetController::class, 'restore']);
    Route::resources(['pets' => PetController::class]);

    Route::put('/races/restore/{id}', [RaceController::class, 'restore']);
    Route::resources(['races' => RaceController::class]);

    Route::put('/species/restore/{id}', [SpeciesController::class, 'restore']);
    Route::resources(['species' => SpeciesController::class]);

    Route::put('/locations/restore/{id}', [LocationController::class, 'restore']);
    Route::post('/locations/nears/', [LocationController::class, 'near']);
    Route::post('/locations/filters/', [LocationController::class, 'filter']);
    Route::resources(['locations' => LocationController::class]);

    Route::resources(['dislikes' => DislikeController::class]);
    Route::resources(['likes' => LikeController::class]);

    Route::get('matches', [MatchController::class, 'matches']);
    Route::get('mismatches', [MatchController::class, 'mismatches']);

    Route::post('blocks', [BlockController::class, 'store']);
    Route::get('blocks', [BlockController::class, 'index']);

    Route::put('/messages/restore/{id}', [MessageController::class, 'restore']);
    Route::resources(['messages' => MessageController::class]);

    Route::get('filters',[FilterController::class, 'index']);
});
