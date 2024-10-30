<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\User;
use App\Observers\PostObserver;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->modifiedResetPasswordUrl();
        $this->registerObserver();
        
    }

    public function modifiedResetPasswordUrl(): void
    {
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return 'http://localhost:3000/reset-password?token='.$token;
        });
    }

    public function registerObserver(): void
    {
        Post::observe(PostObserver::class);
    }
}
