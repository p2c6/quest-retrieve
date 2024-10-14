<?php

use App\Http\Controllers\API\v1\Authentication\LoginController;
use App\Http\Controllers\API\V1\Authentication\LogoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->name('api.v1.')->group(function() {

    //Authentication
    Route::prefix('authentication')->name('authentiaction.')->group(function() {
        Route::post('/login', [LoginController::class, 'login'])->name('login');
        Route::post('/logout', [LogoutController::class, 'logout'])->name('logout')->middleware('auth:sanctum');
    });

});
