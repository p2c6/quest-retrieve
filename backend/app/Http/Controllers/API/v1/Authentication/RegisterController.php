<?php

namespace App\Http\Controllers\API\v1\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\RegisterRequest;
use App\Services\Authentication\RegisterService;
use Illuminate\Http\JsonResponse;

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
     * @param App\Http\Requests\Authentication\RegisterRequest $request The HTTP request object containing user data.
     * @return Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->service->register($request);
    }
}
