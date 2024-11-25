<?php

namespace App\Http\Controllers\API\v1\Approval;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filters\GlobalFilter;
use App\Http\Resources\Post\PostCollection;
use Spatie\QueryBuilder\AllowedFilter;

class ApprovalController extends Controller
{
    public function index()
    {
        $posts = QueryBuilder::for(Post::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new GlobalFilter),
            ])
            ->where('status', PostStatus::PENDING)
            ->paginate(10);
        
        return new PostCollection($posts);
    }

    public function approve(Post $post)
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

    public function reject(Post $post)
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
