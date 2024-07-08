<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Organisation;
use App\Models\Organization;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrganisationAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_access_other_organisations()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $organisation = Organization::factory()->create();

        $organisation->users()->attach($user1->userId);

        // Generate JWT token for $user2
        $token = JWTAuth::fromUser($user2);

        // Set Authorization header with JWT token
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Attempt to access the organisation endpoint as $user2
        $response = $this->withHeaders($headers)->getJson('/api/organisations/' . $organisation->orgId);

        // Assert that access is forbidden (HTTP status code 403)
        $response->assertStatus(403);
    }
}
