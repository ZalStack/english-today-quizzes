<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create(['role' => 'employee']);

        $response = $this
            ->actingAs($user)
            ->get('/employee/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create(['role' => 'employee']);

        $response = $this
            ->actingAs($user)
            ->put('/employee/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'full_name' => 'Test Full Name',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $user->refresh();

        $this->assertSame('Test Full Name', $user->name);
        $this->assertSame('Test Full Name', $user->full_name);
    }
}
