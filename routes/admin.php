<?php

use App\Http\Controllers\Admin\AdoptionController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\PetController;
use App\Http\Controllers\Admin\RaceController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\SpeciesController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\Web\MainController;
use Illuminate\Support\Facades\Auth;
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

Route::prefix('/admin')->name('admin.')->group(function () {
    //toggle trashed 

    Route::get('/home', [HomeController::class, 'index']);
    Route::resource('pets', PetController::class);
    Route::resource('species', SpeciesController::class);
    Route::resource('races', RaceController::class);
    Route::resource('adoptions', AdoptionController::class);
    Route::resource('users', UserController::class);
    Route::resource('seo', SeoController::class);

    Route::post('/trashed/toggle', [ConfigController::class, 'toggleShowTrashed'])->name('trash.toggle');
    Route::put('/species/restore/{id}', [SpeciesController::class, 'restore'])->name('species.restore');

});

Route::prefix('/admin')->name('admin.')->group(function () {
    Auth::routes(['register' => false]);
});
