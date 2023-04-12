<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /** @test */
    public function user_can_login()
    {
        User::factory()
            ->create([
                'email' => 'admin@example.org',
            ]);

        $res = $this->post('/api/login', [
            'email' => 'admin@example.org',
            'password' => 'userpassword',
        ]);

        $res->assertStatus(200);
        $res->assertJsonStructure(['token']);
    }
}