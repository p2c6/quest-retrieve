<?php

namespace App\Http\Controllers\API\v1\PostApproval;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filters\GlobalFilter;
use App\Http\Resources\Post\PostCollection;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\AllowedFilter;

class PostApprovalController extends Controller
{
    /**
     * List of all posts for approval.
     * 
     * @return App\Http\Resources\Post\PostCollection
     */
    public function index(): PostCollection
    {
        $posts = QueryBuilder::for(Post::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new GlobalFilter),
            ])
            ->where('status', PostStatus::PENDING)
            ->paginate(10);
        
        return new PostCollection($posts);
    }

    /**
     * Handle approve post.
     * 
     * @param App\Models\Post $post The model of the post which needs to be updated.
     * @return Illuminate\Http\JsonResponse
     */
    public function approve(Post $post) : JsonResponse
    {
        try {
            $post->update([
                'status' => PostStatus::ON_PROCESSING
            ]);

            return response()->json(['message' => 'Successfully Post Approved'], 200);
        } catch(\Throwable $th) {
            info("Error on post approve: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during post approve.'
            ], 500);
        }
    }

    /**
     * Handle reject post.
     * 
     * @param App\Models\Post $post The model of the post which needs to be updated.
     * @return Illuminate\Http\JsonResponse
     */
    public function reject(Post $post) : JsonResponse
    {
        try {
            $post->update([
                'status' => PostStatus::REJECT
            ]);

            return response()->json(['message' => 'Successfully Post Rejected'], 200);
        } catch(\Throwable $th) {
            info("Error on post reject: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during post reject.'
            ], 500);
        }
    }
}
