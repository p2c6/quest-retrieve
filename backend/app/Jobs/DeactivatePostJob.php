<?php

namespace App\Jobs;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeactivatePostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $posts = Post::where('expiration_date', '<' , now()->format('Y-m-d'))
                    ->where('status', PostStatus::PENDING)
                    ->get();

        foreach ($posts as $post) {
            $post->update(['status' => PostStatus::DEACTIVATED]);

            $user = $post->user;

            PostDeactivatedEmailJob::dispatch($user, $post);
        }
    
    }
}
