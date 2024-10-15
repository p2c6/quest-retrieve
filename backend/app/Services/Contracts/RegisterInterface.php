<?php
namespace App\Services\Contracts;

use Illuminate\Http\Request;

interface RegisterInterface {
    public function register(Request $request) : mixed;
}