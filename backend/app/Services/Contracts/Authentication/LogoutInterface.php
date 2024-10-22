<?php
namespace App\Services\Contracts\Authentication;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface LogoutInterface {
    public function logout(Request $request) : JsonResponse;
}