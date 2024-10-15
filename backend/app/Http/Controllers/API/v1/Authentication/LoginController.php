<?php

namespace App\Http\Controllers\API\v1\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\LoginRequest;
use App\Services\Authentication\LoginService;

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
     * @param App\Http\Requests\Authentication\LoginRequest $request The HTTP request object containing user data.
     * @return mixed
     */
    public function login(LoginRequest $request): mixed
    {
        return $this->service->login($request);
    }
}
