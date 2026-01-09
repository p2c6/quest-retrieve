<?php
namespace App\Services\Moderator\Dashboard;

use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Models\Post;
use Illuminate\Http\JsonResponse;

class DashboardService {
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
}