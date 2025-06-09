<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

it('returns the authenticated user data', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = getJson('/api/v1/profile');

    $response->assertOk()
        ->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
});

it('returns unauthorized if not authenticated', function () {
    $response = getJson('/api/v1/profile');

    $response->assertUnauthorized();
});