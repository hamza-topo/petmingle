<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlockController;
use App\Http\Controllers\Api\ConversationController;
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

Route::prefix('v.0')->group(function () {
    Route::post('/sign-in', [AuthController::class, 'signIn']);
    Route::post('/sign-up', [AuthController::class, 'signUp']);
    Route::get('/login/{provider}', [AuthController::class, 'redirectToProvider']);
    Route::get('/login/{provider}/callback', [AuthController::class, 'handleProviderCallback']);
});

Route::prefix('v.0')->middleware('auth:sanctum')->group(function () {

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/sign-out', [AuthController::class, 'signOut']);

    Route::put('remove-avatar/{id}', [AuthController::class, 'removeAvatar']);
    Route::delete('disable-account/{id}', [AuthController::class, 'disable']);
    Route::put('enable-account/{id}', [AuthController::class, 'enable']);

    Route::prefix('preferences')->group(function () {
        Route::put('langs/{lang}', [LangController::class, 'set']);
        Route::get('langs/current', [LangController::class, 'current']);
        Route::get('langs', [LangController::class, 'index']);
    });

    Route::put('/pets/restore/{id}', [PetController::class, 'restore']);
    Route::get('/pets/{id}/statistics', [PetController::class, 'statistics']);
    Route::apiResource('pets', PetController::class);

    Route::put('/races/restore/{id}', [RaceController::class, 'restore']);
    Route::apiResource('races', RaceController::class);

    Route::put('/species/restore/{id}', [SpeciesController::class, 'restore']);
    Route::apiResource('species', SpeciesController::class);

    Route::put('/locations/restore/{id}', [LocationController::class, 'restore']);
    Route::post('/locations/nears/', [LocationController::class, 'near']);
    Route::post('/locations/filters/', [LocationController::class, 'filter']);
    Route::apiResource('locations', LocationController::class);

    Route::apiResource('dislikes', DislikeController::class)->only(['index', 'store']);
    Route::apiResource('likes', LikeController::class)->only(['index', 'store']);

    Route::get('matches', [MatchController::class, 'matches']);
    Route::get('mismatches', [MatchController::class, 'mismatches']);

    Route::post('blocks', [BlockController::class, 'store']);
    Route::get('blocks', [BlockController::class, 'index']);

    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::put('/conversations/{conversation}/seen', [ConversationController::class, 'markSeen']);

    Route::post('/messages/typing', [MessageController::class, 'typing']);
    Route::put('/messages/restore/{id}', [MessageController::class, 'restore']);
    Route::apiResource('messages', MessageController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('filters', [FilterController::class, 'index']);
});
