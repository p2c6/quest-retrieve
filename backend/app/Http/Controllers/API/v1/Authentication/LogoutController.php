<?php

namespace App\Http\Controllers\API\V1\Authentication;

use App\Http\Controllers\Controller;
use App\Services\Authentication\LogoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
     * The log-out service instance.
     * 
     * @var LogoutService
     */
    private $service;

    /**
     * LogoutController constructor.
     * 
     * @param LogoutService $service The instance of LogoutService
     */
    public function __construct(LogoutService $service)
    {
        $this->service = $service;
    }
    
    /**
     * Handle log-out request.
     * 
     * @param \Illuminate\Http\Request $request The HTTP request object containing user data.
     * @return Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        return $this->service->logout($request);
    }
}
