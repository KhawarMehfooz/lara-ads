<?php
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('sends verification email on registration', function () {
    Notification::fake();

    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123'
    ];

    $response = $this->postJson('/api/v1/register', $userData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'access_token',
            'token_type',
            'user' => ['id', 'name', 'email']
        ]);

    $user = User::where('email', 'john@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->email_verified_at)->toBeNull();

    Notification::assertSentTo($user, VerifyEmailNotification::class);
});

it('completes full registration and verification flow', function () {
    Notification::fake();

    $userData = [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password123'
    ];

    $registerResponse = $this->postJson('/api/v1/register', $userData);
    $registerResponse->assertStatus(201);

    $user = User::where('email', 'jane@example.com')->first();
    expect($user->email_verified_at)->toBeNull();

    $hash = sha1($user->getEmailForVerification());
    $expires = now()->addMinutes(60)->timestamp;

    $verifyResponse = $this->getJson("/api/v1/email/verify/{$user->id}/{$hash}?expires={$expires}");
    
    $verifyResponse->assertStatus(200)
                  ->assertJson([
                      'message' => 'Email verified successfully.'
                  ]);

    $user->refresh();
    expect($user->email_verified_at)->not->toBeNull();
});