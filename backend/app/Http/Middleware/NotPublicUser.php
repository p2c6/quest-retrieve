<?php

namespace App\Http\Middleware;

use App\Enums\PostType;
use App\Enums\UserType;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotPublicUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !in_array(auth()->user()->role_id, [UserType::ADMINISTRATOR, UserType::MODERATOR])) {
            return response()->json(['message' => 'You are not allowed to access this action'], 403);
        }
        
        return $next($request);
    }
}
