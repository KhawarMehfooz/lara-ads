<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\postJson;
uses(RefreshDatabase::class);

it('throttles login attempts', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);

    for ($i = 0; $i < 5; $i++) {
        postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
    }

    $response = postJson('/api/v1/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(429);
});
