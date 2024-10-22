<?php
namespace App\Services\Contracts\Authentication;

use Illuminate\Http\Request;

interface RegisterInterface {
    public function register(Request $request) : mixed;
}