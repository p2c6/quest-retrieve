<?php

namespace App\Http\Controllers\API\v1\Authentication;

use App\Http\Controllers\Controller;
use App\Services\Authentication\LoginService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    private $service;

    public function __construct(LoginService $service)
    {
        $this->service = $service;
    }

    public function login(Request $request)
    {
        return $this->service->login($request);
    }
}
