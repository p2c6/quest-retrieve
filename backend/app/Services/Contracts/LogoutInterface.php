<?php
namespace App\Services\Contracts;

use Illuminate\Http\Request;

interface LogoutInterface {
    public function logout(Request $request) : mixed;
}