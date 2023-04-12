<?php

namespace App\Http\Middleware;

use App\Services\JwtServices;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class AuthenticateApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = Str::after($request->header('Authorization'), 'Bearer ');

        // return 401 response if failed
        if (!($user = JwtServices::validate($token))) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        auth()->loginUsingId($user->id);

        // update last_used_at
        $user->jwt_tokens()
            ->whereUniqueId($token)
            ->update([
                'last_used_at' => Carbon::now(),
            ]);

        return $next($request);
    }
}
