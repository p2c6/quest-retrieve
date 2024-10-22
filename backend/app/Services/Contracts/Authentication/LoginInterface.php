<?php
namespace App\Services\Contracts\Authentication;

use Illuminate\Http\Request;

interface LoginInterface {
    public function login(Request $request) : mixed;
}