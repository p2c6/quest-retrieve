<?php

namespace App\Http\Controllers\API\v1\Authentication;

use App\Http\Controllers\Controller;
use App\Services\Authentication\LoginService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * The login service instance.
     * 
     * @var LoginService
     */
    private $service;
    
    /**
     * LoginController contructor.
     * 
     * @param LoginService $service The instance of LoginService.
     */
    public function __construct(LoginService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle user log-in request.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        return $this->service->login($request);
    }
}
