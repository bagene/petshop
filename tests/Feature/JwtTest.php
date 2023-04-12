<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\JwtServices;
use Database\Factories\UserFactory;
use Tests\TestCase;

class JwtTest extends TestCase
{
    /** @test */
    public function it_can_issue_token()
    {
        $user = User::factory()
            ->create()
            ->first();

        $token = JwtServices::issue($user);

        $this->assertIsString($token);
    }

    /** @test */
    public function it_can_validate_jwt()
    {
        User::factory()
            ->create([
                'email' => 'example@example.org',
            ]);

        $user = User::latest()->first();

        $token = JwtServices::issue($user);

        $res = JwtServices::validate($token);

        $this->assertEquals($user, $res);
    }
}