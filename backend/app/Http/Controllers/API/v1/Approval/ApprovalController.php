<?php

namespace App\Http\Controllers\API\v1\Approval;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $posts = Post::with(['user' => function($query) {
            $query->select('id');
        }, 'user.profile' => function($query) {
            $query->select('user_id', 'last_name', 'first_name');
        },'subcategory' => function($query) {
            $query->select('id', 'name', 'category_id');
        }, 'subcategory.category' => function($query) {
            $query->select('id', 'name');
        },])
        ->select(
            'posts.user_id', 
            'posts.type', 
            'posts.subcategory_id', 
            'posts.incident_location', 
            'posts.incident_date', 
            'posts.finish_transaction_date',
            'posts.expiration_date',
            'posts.status'
        );

        if ($q !== null) {
            
            $posts->where('posts.status', 'like', '%' . $q . '%')
            ->orWhereHas('user.profile', function($query) use ($q) {
                $query->where('first_name', 'like', '%' . $q . '%')
                        ->orWhere('last_name', 'like', '%' . $q . '%')
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $q . '%'])
                        ->select('first_name', 'last_name');
            });
        }
        $posts = $posts->get();
        
        return response()->json($posts, 200);

    }
}
