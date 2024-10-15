<?php

namespace App\Http\Controllers\API\v1\Authentication;

use App\Http\Controllers\Controller;
use App\Services\Authentication\RegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * The register service instance.
     * 
     * @var RegisterService
     */
    private $service;
    
    /**
     * The RegisterController constructor.
     * 
     * @param RegisterService $service The instance of RegisterService
     */
    public function __construct(RegisterService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle register request.
     * 
     * @param Illuminate\Http\Request $request The HTTP request object containing user data.
     * @return Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        return $this->service->register($request);
    }
}
