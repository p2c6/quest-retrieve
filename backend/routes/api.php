<?php

use App\Http\Controllers\API\v1\Authentication\LoginController;
use App\Http\Controllers\API\V1\Authentication\LogoutController;
use App\Http\Controllers\API\v1\Authentication\RegisterController;
use App\Http\Controllers\API\v1\Authentication\ResetPasswordController;
use App\Http\Controllers\API\v1\Authentication\SendEmailVerificationController;
use App\Http\Controllers\API\v1\Authentication\SendResetPasswordLinkController;
use App\Http\Controllers\API\v1\Authentication\VerifyController;
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
        Route::post('/email/verification-notification', [SendEmailVerificationController::class,'sendEmailVerification'])
            ->middleware('throttle:6,1')->name('send');

        Route::get('/email/verify/{id}/{hash}', [VerifyController::class,'verify'])
            ->middleware('signed')->name('verify');
})->middleware('auth:sanctum');

Route::post('/forgot-password', [SendResetPasswordLinkController::class, 'sendResetPasswordLink'])->middleware('throttle:20,1'); // Allow 10 requests per minute

Route::get('/reset-password/{token}', function ($token) {
    if (!$token) {
        return response()->json(['error' => 'Token not provided'], 400);
    }
    return response()->json(['token' => $token], 200);
})->name('password.reset');


Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])->name('password.update');



