<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface LoginInteface {
    public function login(Request $request) : JsonResponse;
}