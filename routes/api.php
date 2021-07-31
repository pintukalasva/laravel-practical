<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [RegisterController::class, 'login']);
Route::post('signup', [RegisterController::class, 'signup']);
Route::post('otp_verify', [RegisterController::class, 'otpverify']);

Route::middleware(['auth:sanctum','admin'])->group( function () {
    Route::post('send_signup_link', [RegisterController::class, 'sendSignupLink']);
    Route::post('logout', [RegisterController::class, 'logout']);
});
Route::middleware(['auth:sanctum','user'])->group( function () {
    Route::put('update_profile', [RegisterController::class, 'updateProfile']);
    Route::post('logout', [RegisterController::class, 'logout']);
});
