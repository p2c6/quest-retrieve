<?php
namespace App\Services\Contracts;

use Illuminate\Http\Request;

interface LoginInterface {
    public function login(Request $request) : mixed;
}