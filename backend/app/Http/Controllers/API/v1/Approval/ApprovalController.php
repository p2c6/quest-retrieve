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
}
