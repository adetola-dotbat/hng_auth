<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Organisation;

class RegisterEndpointTest extends TestCase
{

    use RefreshDatabase;

    public function test_register_user_successfully()
    {
        $response = $this->postJson('/api/auth/register', [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'phone' => '1234567890'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
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

        $this->assertDatabaseHas('organizations', [
            'name' => "John's Organisation"
        ]);
    }

    public function test_login_user_successfully()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
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
    }

    public function test_registration_fails_if_required_fields_are_missing()
    {
        $response = $this->postJson('/api/auth/register', [
            'lastName' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'phone' => '1234567890'
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    [
                        'field',
                        'message'
                    ]
                ]
            ]);
    }

    public function test_registration_fails_on_duplicate_email()
    {
        User::factory()->create([
            'email' => 'john@example.com',
        ]);

        $response = $this->postJson('/api/auth/register', [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'phone' => '1234567890'
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    [
                        'field',
                        'message'
                    ]
                ]
            ]);
    }
}
