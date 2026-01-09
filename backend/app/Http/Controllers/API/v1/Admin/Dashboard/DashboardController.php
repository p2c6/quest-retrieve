<?php

namespace App\Http\Controllers\API\v1\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Admin\Dashboard\DashboardService;
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
     * Get total users per month count.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function getUsersPerMonth(): JsonResponse
    {
        return $this->service->getUsersPerMonth();
    }

    /**
     * Get verified & not yet verified users count.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function getVerifiedUsers(): JsonResponse
    {
        return $this->service->getVerifiedUsers();
    }
}
