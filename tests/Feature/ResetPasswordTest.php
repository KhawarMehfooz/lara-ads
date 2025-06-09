<?php
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('sends a password reset email', function () {
    Notification::fake();

    $user = User::factory()->create();

    $response = $this->postJson('/api/v1/forgot-password', [
        'email' => $user->email,
    ]);

    $response->assertStatus(200);
    Notification::assertSentTo($user, ResetPassword::class);
});

it('resets password with valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->postJson('/api/v1/forgot-password', [
        'email' => $user->email,
    ]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use (&$token) {
        $token = $notification->token;
        return true;
    });

    $response = $this->postJson('/api/v1/reset-password', [
        'email' => $user->email,
        'token' => $token,
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ]);

    $response->assertStatus(200);
    expect(Hash::check('new-password-123', $user->fresh()->password))->toBeTrue();
});

it('fails to reset password with invalid token', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/v1/reset-password', [
        'email' => $user->email,
        'token' => 'invalid-token',
        'password' => 'another-password',
        'password_confirmation' => 'another-password',
    ]);

    $response->assertStatus(400);
});
