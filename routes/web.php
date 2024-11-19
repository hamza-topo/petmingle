<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Web\AuthController as WebAuthController;
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



Route::get('login/{provider}', [AuthController::class, 'redirectToProvider']);
Route::get('{provider}/callback', [AuthController::class, 'handleProviderCallback']);
Route::get('user/login/', [WebAuthController::class, 'login'])->name('user.login');
Route::get('user/register/', [WebAuthController::class, 'register'])->name('user.register');
Route::post('user/login/', [WebAuthController::class, 'signIn'])->name('user.login.signIn');
Route::post('user/register/', [WebAuthController::class, 'signUp'])->name('user.register.signUp');


// Route::get('/', function () {
//     return view('welcome');
// });

Route::group([], function ($router) {
    $router->get('/about', App\Http\Controllers\Web\AboutController::class)->name('about');
    $router->get('/contact', [App\Http\Controllers\Web\ContactController::class, 'index'])->name('contact');
    $router->post('/contact', [App\Http\Controllers\Web\ContactController::class, 'store'])->name('contact.store');
    $router->get('/blogs', [App\Http\Controllers\Web\BlogController::class, 'index'])->name('blogs');
    $router->get('/blogs/{slug}', [App\Http\Controllers\Web\BlogController::class, 'read'])->name('blogs.read');
    $router->get('/faq', App\Http\Controllers\Web\FaqController::class)->name('faq');
    $router->get('/privacy-policy', App\Http\Controllers\Web\PrivacyPolicyController::class)->name('privacy-policy');
    $router->get('/search', [App\Http\Controllers\Web\EngineController::class, 'index'])->name('engine');
    $router->get('/search/{slug}/{id}', [App\Http\Controllers\Web\EngineController::class, 'show'])->name('engine.detail');
    $router->get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    $router->post('', [App\Http\Controllers\Web\NewsLetterController::class, 'subscribe'])->name('news-letter.subscribe');
});
