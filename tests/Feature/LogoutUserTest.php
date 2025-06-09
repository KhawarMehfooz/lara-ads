<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('logs out an authenticated user', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = postJson('/api/v1/logout');

    $response->assertOk()
        ->assertJson([
            'message' => 'Logged out successfully',
        ]);

    expect($user->tokens()->count())->toBe(0);
});
