<?php

namespace App\Services\Public\Post;

use App\Enums\PostStatus;
use App\Filters\FilterPublicPost;
use App\Mail\ClaimRequested;
use App\Mail\ReturnRequested;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PostService
{
    /**
     * List of all posts.
     * 
     * @param Illuminate\Http\Request $keyword The HTTP request object containing user data.
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
        ->where('status', PostStatus::ON_PROCESSING)
        ->allowedFilters([
            AllowedFilter::custom('keyword', new FilterPublicPost)
        ])
        ->paginate(10)
        ->appends($keyword);
                
        return response()->json($posts);
    }

    /**
     * Handle claim post request.
     * 
     * @param App\Models\Post $post The model of the post which needs to be retrieve.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function claim(Post $post, $request)
    {
        try {
            Mail::to($post->user->email)->send(new ClaimRequested($post, $request));
            
            return response()->json([
                'message' => 'Successfully Claim Requested.'
            ], 200);
        } catch (\Throwable $th) {
            info("Error on claiming post: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during claiming post.'
            ], 500);
        }
    }

    /**
     * Handle return post request.
     * 
     * @param App\Models\Post $post The model of the post which needs to be retrieve.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function return(Post $post, $request)
    {
        try {
            Mail::to($post->user->email)->send(new ReturnRequested($post, $request));
            
            return response()->json([
                'message' => 'Successfully Return Requested.'
            ], 200);
        } catch (\Throwable $th) {
            info("Error on returning post: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during returning post.'
            ], 500);
        }
    }
}
