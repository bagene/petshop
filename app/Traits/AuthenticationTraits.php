<?php

namespace App\Traits;

use App\Models\User;
use App\Services\JwtServices;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait AuthenticationTraits
{
    protected function checkAuth(array $credentials): ?User
    {
        $user = User::whereEmail($credentials['email'])->first();
        return ($user && Hash::check($credentials['password'], $user->password)) ? $user : null;
    }

    protected function saveToken(User $user): string
    {
        $token = JwtServices::issue($user);

        $user->jwt_tokens()
            ->updateOrCreate([
                'token_title' => 'jwt',
                'unique_id' => $token,
                'expires_at' => Carbon::now()->addHour(),
                'last_used_at' => null,
            ]);

        return $token;
    }
}