<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\postJson;
uses(RefreshDatabase::class);

it('sends reset password email for valid user', function () {
    Notification::fake();

    $user = User::factory()->create();

    $response = postJson('/api/v1/forgot-password', [
        'email' => $user->email,
    ]);

    $response->assertStatus(200);
    Notification::assertSentTo($user, \Illuminate\Auth\Notifications\ResetPassword::class);
});

