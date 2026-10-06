<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Web\AboutController;
use App\Http\Controllers\Web\AuthController as WebAuthController;
use App\Http\Controllers\Web\BlogController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\EngineController;
use App\Http\Controllers\Web\FaqController;
use App\Http\Controllers\Web\NewsLetterController;
use App\Http\Controllers\Web\PrivacyPolicyController;
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
    $router->get('/about', AboutController::class)->name('about');
    $router->get('/contact', [ContactController::class, 'index'])->name('contact');
    $router->post('/contact', [ContactController::class, 'store'])->name('contact.store');
    $router->get('/blogs', [BlogController::class, 'index'])->name('blogs');
    $router->get('/blogs/{slug}', [BlogController::class, 'read'])->name('blogs.read');
    $router->get('/faq', FaqController::class)->name('faq');
    $router->get('/privacy-policy', PrivacyPolicyController::class)->name('privacy-policy');
    $router->get('/search', [EngineController::class, 'index'])->name('engine');
    $router->get('/search/{slug}/{id}', [EngineController::class, 'show'])->name('engine.detail');
    $router->get('/', [HomeController::class, 'index'])->name('home');
    $router->post('', [NewsLetterController::class, 'subscribe'])->name('news-letter.subscribe');
});
