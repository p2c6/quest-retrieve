<?php

namespace App\Http\Controllers\API\v1\Moderator\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Moderator\Dashboard\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * The DashboardController constructor.
     * 
     * @param DashboardService $service The instance of DashboardService
     */
    public function __construct(protected DashboardService $service) { }

    /**
     * Get total posts count per month.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function getPostsPerMonth(): JsonResponse
    {
        return $this->service->getPostsPerMonth();
    }
    
    /**
     * Get total posts count per month.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function getPostCountPerStatus(): JsonResponse
    {
        return $this->service->getPostCountPerStatus();
    }

    /**
     * Get total posts count.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function getTotalPost(): JsonResponse
    {
        return $this->service->getTotalPost();
    }

    /**
     * Get total pending posts count.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function getTotalPendingPost(): JsonResponse
    {
        return $this->service->getTotalPendingPost();
    }

    /**
     * Get approved posts count.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function getApprovedPost(): JsonResponse
    {
        return $this->service->getApprovedPost();
    }

    /**
     * Get rejected posts count.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function getRejectedPost(): JsonResponse
    {
        return $this->service->getRejectedPost();
    }

    /**
     * Get completed posts count.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function getCompletedPost(): JsonResponse
    {
        return $this->service->getCompletedPost();
    }
}
