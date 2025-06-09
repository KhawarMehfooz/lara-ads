<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates an ad successfully', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    $payload = [
        'title' => 'Test Ad',
        'description' => 'This is a test ad.',
        'category_id' => $category->id,
        'location' => 'Karachi',
        'price' => 999.99,
        'contact_email' => 'test@example.com',
        'contact_phone' => '03001234567',
    ];

    $response = $this
        ->actingAs($user)
        ->postJson(route('ads.store'), $payload);

    $response->assertCreated();
    $response->assertJsonFragment([
        'title' => 'Test Ad',
        'category_id' => $category->id,
    ]);

    $this->assertDatabaseHas('ads', [
        'title' => 'Test Ad',
        'user_id' => $user->id,
    ]);
});

