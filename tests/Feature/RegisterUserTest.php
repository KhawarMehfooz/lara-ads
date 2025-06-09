<?php
use App\Models\User;
use function Pest\Laravel\postJson;

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('registers a user successfully',function(){
    $response = postJson('/api/v1/register',[
        'name'=>'Khawar Mehfooz',
        'email'=>'khawar@example.com',
        'password'=>'password1234'
    ]);

    $response->assertCreated();
    $response->assertJsonStructure([
        'message',
        'access_token',
        'token_type',
        'user'=>['id','name','email']
    ]);

    expect(User::where('email','khawar@example.com')->exists())->toBeTrue();
});

it('fails when required fields are missing',function(){
    $response = postJson('/api/v1/register',[]);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name','email','password']);
});

it('fails when email format is invalid',function(){
    $response = postJson('api/v1/register',[
        'name'=>'Khawar',
        'email'=>'dirty-email.com',
        'password'=>'password1234'
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
});

it('fails when password is too short',function(){
    $response = postJson('api/v1/register',[
        'name'=>'Khawar',
        'email'=>'example@gmail.com',
        'password'=>'passw'
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['password']);
});

it('fails when email is already taken', function () {
    User::factory()->create([
        'email' => 'duplicate@example.com',
    ]);

    $response = postJson('/api/v1/register', [
        'name' => 'Another User',
        'email' => 'duplicate@example.com',
        'password' => 'password1234',
    ]);

    $response->assertStatus(422);

    $response->assertJsonValidationErrors('email');
});