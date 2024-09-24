<?php

use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\PetController;
use App\Http\Controllers\Admin\RaceController;
use App\Http\Controllers\Admin\SpeciesController;
use App\Http\Controllers\Web\MainController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// ->middleware('auth')
Route::prefix('/admin')->name('admin.')->group(function () {
    Route::get('/', [HomeController::class, 'index']);
    Route::resource('pets', PetController::class);
    Route::resource('species', SpeciesController::class);
    Route::resource('races', RaceController::class);
});
