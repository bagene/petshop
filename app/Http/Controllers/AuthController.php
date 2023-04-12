<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Models\User;
use App\Services\JwtServices;
use App\Traits\AuthenticationTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use AuthenticationTraits;

    public function login(AuthRequest $request)
    {
        $credentials = $request->only([
            'email',
            'password',
        ]);

        if (!($user = $this->checkAuth($credentials))) {
            return response()->json([
                'message' => 'Incorrect Email/Password',
            ], 401);
        }

        return response()->json([
            'token' => $this->saveToken($user),
        ]);
    }
}
