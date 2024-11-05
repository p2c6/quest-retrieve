<?php

namespace App\Http\Controllers\API\v1\Approval;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filters\DateFilter;
use Spatie\QueryBuilder\AllowedFilter;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
         $posts = QueryBuilder::for(Post::class)
            ->allowedIncludes('profile')
            ->allowedFilters([
                AllowedFilter::custom('search', new DateFilter),
            ])
            ->get();
        
        return response()->json($posts);
    }
}
