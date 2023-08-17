<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\GoogleMeetController;
use App\Http\Controllers\MeetController;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/admin',[LoginController::class,'showAdminLoginForm'])->name('admin.login-view');
Route::post('/admin',[LoginController::class,'adminLogin'])->name('admin.login');

Route::get('/admin/register',[RegisterController::class,'showAdminRegisterForm'])->name('admin.register-view');
Route::post('/admin/register',[RegisterController::class,'createAdmin'])->name('admin.register');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// Route::get('/checkResponse', [MeetController::class,'redirectToGoogle'])->name('google.login');

// Route::get('/checkResponse', function () {
//     return Socialite::driver('google')
//     ->scopes(['https://www.googleapis.com/auth/calendar.events'])
//     ->redirect();
// });
Route::get('/checkResponse', function () {
    return Socialite::driver('google')
    ->scopes(['https://www.googleapis.com/auth/calendar.events'])
    ->redirect();
});

Route::get('/google/meet', [MeetController::class,'handleGoogleCallback'])->name('google.meet');



Route::get('/admin/dashboard',function(){
    return view('admin.admin');
})->middleware('auth:admin');
