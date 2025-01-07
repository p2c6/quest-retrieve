<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Enums\UserType;
use App\Models\Post;
use App\Models\User;
use App\Policies\Moderator\Post\PostPolicy;
use App\Policies\PublicUserPostPolicy;
use App\Policies\UserPolicy;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Post::class => PublicUserPostPolicy::class,
        Post::class => PostPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->verifyEmailNotification();
    }

    public function verifyEmailNotification()
    {
        VerifyEmail::toMailUsing(function($notifiable, $url) {
            $spaUrl = "http://localhost:3000/verify-email?verification_url=" . urlencode($url);

            return (new MailMessage)
                    ->subject('Verify Email Address')
                    ->line('Click the button below to verify your email address.')
                    ->action('Verify Email Address', $spaUrl);
        });
    }
}
