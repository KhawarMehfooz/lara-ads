<?php
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\postJson;
uses(RefreshDatabase::class);

test('logs in successfully with valid credentials', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password123')
    ]);

    $response = postJson('/api/v1/login', [
        'email' => $user->email,
        'password' => 'password123'
    ]);

    $response->assertOk();

    $response->assertJsonStructure([
        'message',
        'access_token',
        'token_type',
        'user' => ['id', 'name', 'email'],
    ]);

});

it('fails login with incorrect password', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);

    $response = postJson('/api/v1/login', [
        'email' => $user->email,
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('message');
});

it('fails login with non-existent email', function () {
    $response = postJson('/api/v1/login', [
        'email' => 'notfound@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('message');
});

it('fails login when required fields missing', function () {
    $response = postJson('/api/v1/login', []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email', 'password']);
});