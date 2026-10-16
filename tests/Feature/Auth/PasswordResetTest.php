<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_user_can_verify_email_for_password_reset(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/forgot-password', ['email' => $user->email]);

        $response->assertRedirect(route('password.captcha'));
        $this->assertEquals($user->email, session('pwreset.email'));
    }

    public function test_captcha_screen_requires_verified_email(): void
    {
        $response = $this->get('/forgot-password/captcha');

        $response->assertRedirect(route('password.request'));
    }
}
