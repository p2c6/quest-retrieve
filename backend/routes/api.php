<?php

use App\Http\Controllers\API\v1\Admin\Entity\Category\CategoryController;
use App\Http\Controllers\API\v1\Admin\Entity\Role\RoleController;
use App\Http\Controllers\API\v1\Admin\Entity\SubCategory\SubCategoryController;
use App\Http\Controllers\API\v1\Admin\Entity\User\UserController;
use App\Http\Controllers\API\v1\Authentication\LoginController;
use App\Http\Controllers\API\V1\Authentication\LogoutController;
use App\Http\Controllers\API\v1\Authentication\RegisterController;
use App\Http\Controllers\API\v1\Authentication\ResetPasswordController;
use App\Http\Controllers\API\v1\Authentication\SendEmailVerificationController;
use App\Http\Controllers\API\v1\Authentication\SendResetPasswordLinkController;
use App\Http\Controllers\API\v1\Authentication\VerifyController;
use App\Http\Controllers\API\v1\FileUpload\TemporaryFileUploadController;
use App\Http\Controllers\API\v1\Moderator\Post\PostController as PostPostController;
use App\Http\Controllers\API\v1\Public\Post\PostController as PublicPostController;
use App\Http\Controllers\API\v1\User\Post\PostController;
use App\Http\Controllers\API\v1\User\UserProfile\UserProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->name('api.v1.')->group(function() {
    //Authentication
    Route::prefix('authentication')->name('authentiaction.')->group(function() {
        Route::post('/login', [LoginController::class, 'login'])->name('login');
        Route::post('/logout', [LogoutController::class, 'logout'])->name('logout')->middleware('auth:sanctum');
        Route::post('/register', [RegisterController::class, 'register'])->name('register');
    });

    //Public Post
    Route::prefix('public/posts')->name('public.')
    ->controller(PublicPostController::class)
    ->group(function() {
            Route::get('/', 'index')->name('index');
            Route::post('/{post}/claim',  'claim')->name('claim');
            Route::post('/{post}/return',  'return')->name('return');
    });

    //Authenticated User
    Route::middleware('auth:sanctum')->group(function() {
        //Get Current User
        Route::get('/user', function(Request $request) {
            $user = auth()->user();
            if ($user) {
                $user->load('profile');
            }
            return $user; 
        });
        
        //User
        Route::apiResource('users', UserController::class);
        
        //Roles
        Route::get('/roles/dropdown', [RoleController::class, 'dropdownRoles'])
            ->name('roles.dropdown');
        Route::apiResource('roles', RoleController::class);

        //Categories
        Route::get('/categories/dropdown', [CategoryController::class, 'dropdownCategories'])
            ->name('categories.dropdown');
        Route::apiResource('categories', CategoryController::class);

        //Subcategories
        Route::get('/subcategories/dropdown', [SubCategoryController::class, 'dropdownSubcategories'])
        ->name('subcategories.dropdown');
        Route::apiResource('subcategories', SubCategoryController::class)
            ->parameters(['subcategories' => 'subCategory']);
            
        //Post
        Route::put('/posts/{post}/mark-as-done', [PostController::class, 'markAsDone'])->name('posts.markAsDone');
        Route::post('/posts/{post}/claim', [PostController::class, 'claim'])->name('posts.claim');
        Route::apiResource('posts', PostController::class);

        //User Profile
        Route::put('/profile/{user}', [UserProfileController::class, 'update'])->name('profile.update');

        //Temporary File Upload
        Route::prefix('temporary-file')->name('temporary-file.')->group(function() {
            Route::post('/upload', [TemporaryFileUploadController::class, 'upload'])->name('upload');
            Route::post('/revert', [TemporaryFileUploadController::class, 'revert'])->name('revert');
        });

        //For Approval
        Route::prefix('approval/posts')->name('for-approval.')->group(function() {
            Route::middleware('not_public_user')->group(function() {
                Route::get('/', [PostPostController::class, 'index'])->name('index');
                Route::put('/{post}/approve', [PostPostController::class, 'approve'])->name('approve');
                Route::put('/{post}/reject', [PostPostController::class, 'reject'])->name('reject');
            });
        });
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

//Reset Password
Route::get('/reset-password/{token}', function ($token) {
    if (!$token) {
        return response()->json(['error' => 'Token not provided'], 400);
    }
    return response()->json(['token' => $token], 200);
})->name('password.reset');

Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])->name('password.update');



