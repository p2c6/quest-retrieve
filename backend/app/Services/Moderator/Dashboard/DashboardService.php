<?php
namespace App\Services\Moderator\Dashboard;

use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardService {
    /**
     * Get total posts count per month.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function getPostsPerMonth(): JsonResponse
    {
        try {
        $rawData = Post::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
        ->whereYear('created_at', now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $monthlyPosts = array_fill(0, 12, 0);

        foreach ($rawData as $row) {
            $monthlyPosts[$row->month - 1] = $row->total;
        }

        return response()->json([
            'message' => 'Posts per month count successfully retrieved.',
            'data' => $monthlyPosts
        ], 200);
        } catch(\Throwable $th) {
            info("Error on retrieving monthly posts count: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during retrieving of monthly posts count.'
            ], 500);
        }
    }

    /**
     * Get total posts count.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function getTotalPost(): JsonResponse
    {
        try {
            $posts = Post::all()->count();

            return response()->json([
                'message' => 'Total posts count successfully retrieved.',
                'data' => $posts
            ], 200);
        } catch(\Throwable $th) {
            info("Error on retrieving total posts count: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during retrieval of total posts count.'
            ], 500);
        }
    }

    /**
     * Get total pending posts count.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function getTotalPendingPost(): JsonResponse
    {
        try {
            $posts = Post::query()
                    ->where('status', PostStatus::PENDING)
                    ->count();

            return response()->json([
                'message' => 'Total pending posts count successfully retrieved.',
                'data' => $posts
            ], 200);
        } catch(\Throwable $th) {
            info("Error on retrieving total pending posts count: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during retrieval of total pending posts count.'
            ], 500);
        }
    }

    /**
     * Get approved posts count.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function getApprovedPost(): JsonResponse
    {
        try {
            $posts = Post::query()
                    ->where('status', PostStatus::ON_PROCESSING)
                    ->count();

            return response()->json([
                'message' => 'Total approved posts count successfully retrieved.',
                'data' => $posts
            ], 200);
        } catch(\Throwable $th) {
            info("Error on retrieving total approved posts count: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during retrieval of total approved posts count.'
            ], 500);
        }
    }

    /**
     * Get rejected posts count.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function getRejectedPost(): JsonResponse
    {
        try {
            $posts = Post::query()
                    ->where('status', PostStatus::REJECT)
                    ->count();

            return response()->json([
                'message' => 'Total rejected posts count successfully retrieved.',
                'data' => $posts
            ], 200);
        } catch(\Throwable $th) {
            info("Error on retrieving total rejected posts count: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during retrieval of total rejected posts count.'
            ], 500);
        }
    }

    /**
     * Get completed posts count.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function getCompletedPost(): JsonResponse
    {
        try {
            $posts = Post::query()
                    ->where('status', PostStatus::FINISHED)
                    ->count();

            return response()->json([
                'message' => 'Total pending completed count successfully retrieved.',
                'data' => $posts
            ], 200);
        } catch(\Throwable $th) {
            info("Error on retrieving total completed posts count: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during retrieval of total completed posts count.'
            ], 500);
        }
    }

    /**
     * Get verified & not yet verified users count.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function getPostCountPerStatus(): JsonResponse
    {
        $counts = Post::query()
        ->selectRaw('status, COUNT(*) as total')
        ->groupBy('status')
        ->pluck('total', 'status');

        return response()->json([
            'message' => 'Post summary retrieved successfully.',
            'data' => [
                $counts[PostStatus::PENDING] ?? 0,
                $counts[PostStatus::ON_PROCESSING] ?? 0,
                $counts[PostStatus::REJECT] ?? 0,
                $counts[PostStatus::FINISHED] ?? 0,
            ]
        ], 200);
    }
}