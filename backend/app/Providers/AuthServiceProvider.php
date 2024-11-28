<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Enums\UserType;
use App\Models\Post;
use App\Models\User;
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
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->gates();
        $this->verifyEmailNotification();
    }

    public function verifyEmailNotification()
    {
        VerifyEmail::toMailUsing(function($notifiable, $url) {
            $spaUrl = "http://localhost:3000/email_verify_url=" . $url;

            return (new MailMessage)
                    ->subject('Verify Email Address')
                    ->line('Click the button below to verify your email address.')
                    ->action('Verify Email Address', $spaUrl);
        });
    }

    public function gates()
    {
        Gate::define('approve-post', function (User $user, Post $post) {
            return in_array($user->role_id, [UserType::ADMINISTRATOR, UserType::MODERATOR]);
        });

        Gate::define('reject-post', function (User $user, Post $post) {
            return in_array($user->role_id, [UserType::ADMINISTRATOR, UserType::MODERATOR]);
        });
    }
}
