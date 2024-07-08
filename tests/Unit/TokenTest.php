<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Tymon\JWTAuth\Facades\JWTAuth;

// use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_token_generation()
    {
        // Create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        // Attempt to login
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Ensure the response status is 200
        $response->assertStatus(200);

        // Ensure the response has access token and user details
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'accessToken',
                'user' => [
                    'userId',
                    'firstName',
                    'lastName',
                    'email',
                    'phone'
                ]
            ]
        ]);

        // Ensure token is not null
        $this->assertNotNull($response->json('data.accessToken'));
    }
}
