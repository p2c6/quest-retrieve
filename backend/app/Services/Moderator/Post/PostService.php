<?php

namespace App\Services\Moderator\Post;

use App\Enums\PostStatus;
use App\Filters\FilterForApprovalPost;
use App\Models\Post;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\AllowedFilter;

class PostService
{
    /**
     * List of all posts for approval.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function index($keyword): JsonResponse
    {
        $posts = QueryBuilder::for(Post::class)
        ->with(['subcategory' => function($q) {
            $q->select('id', 'name');
        }])
        ->select(
            'id',
            'type',
            'subcategory_id', 
            'incident_location', 
            'incident_date',
            'finish_transaction_date',
            'status',
        )
        ->where('status', PostStatus::PENDING)
        ->allowedFilters([
            AllowedFilter::custom('keyword', new FilterForApprovalPost)
        ])
        ->paginate(5)
        ->appends($keyword);
                
        return response()->json($posts);
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
