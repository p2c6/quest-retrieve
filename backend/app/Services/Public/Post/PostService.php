<?php

namespace App\Services\Public\Post;

use App\Enums\PostStatus;
use App\Filters\FilterPublicPost;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
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
        ->paginate(5)
        ->appends($keyword);
                
        return response()->json($posts);
    }
}
