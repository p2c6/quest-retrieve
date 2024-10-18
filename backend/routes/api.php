<?php

use App\Http\Controllers\API\v1\Authentication\LoginController;
use App\Http\Controllers\API\V1\Authentication\LogoutController;
use App\Http\Controllers\API\v1\Authentication\RegisterController;
use App\Http\Controllers\API\v1\EmailVerification\EmailVerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->name('api.v1.')->group(function() {
    //Authentication
    Route::prefix('authentication')->name('authentiaction.')->group(function() {
        Route::post('/login', [LoginController::class, 'login'])->name('login');
        Route::post('/logout', [LogoutController::class, 'logout'])->name('logout')->middleware('auth:sanctum');
        Route::post('/register', [RegisterController::class, 'register'])->name('register');
    });
});

 //E-mail Verification
Route::prefix('verification')->name('verification.')->group(function() {
    Route::controller(EmailVerificationController::class)->group(function() {
        Route::post('/email/verification-notification','sendEmailVerification')
            ->middleware('throttle:6,1')->name('send');

        Route::get('/email/verify/{id}/{hash}', 'verify')
            ->middleware('signed')->name('verify');
    });
})->middleware('auth:sanctum');





