<?php

use App\Http\Controllers\Api\AuthController;
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

Auth::routes();


Route::get('login/{provider}', [AuthController::class, 'redirectToProvider']);
Route::get('{provider}/callback', [AuthController::class, 'handleProviderCallback']);




Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/about', App\Http\Controllers\Web\AboutController::class)->name('about');
Route::get('/contact', [App\Http\Controllers\Web\ContactController::class, 'index'])->name('contact');
Route::post('/contact', [App\Http\Controllers\Web\ContactController::class, 'store'])->name('contact.store');
Route::get('/blogs', App\Http\Controllers\Web\BlogController::class)->name('blogs');
Route::get('/faq', App\Http\Controllers\Web\FaqController::class)->name('faq');
Route::get('/search', [App\Http\Controllers\Web\EngineController::class, 'index'])->name('engine');
