<?php

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GoogleController;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
// Route::get('/gmail', [GoogleController::class, 'view'])->name('gmail')->name('google');
Route::get('/google', [GoogleController::class, 'signInwithGoogle']);
Route::get('/google/callback', [GoogleController::class, 'callbackToGoogle']);
Route::post('/password/reset', [UserController::class, 'sendResetLinkEmail']);
Route::get('/password/reset/{token}', [UserController::class, 'getemail_fromtoken'])->name('resetlink');
Route::post('/password/update', [UserController::class, 'submitResetPasswordForm'])->name('updatePassword');
Route::get('/reset-password/{email}/{token}', [UserController::class, 'showResetForm'])->name('resetPasswordForm');
