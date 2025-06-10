<?php

use App\Models\Ad;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('creates an ad successfully with thumbnail and gallery images', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $category = Category::factory()->create();

    $thumbnail = UploadedFile::fake()->image('thumb.jpg');
    $gallery = [
        UploadedFile::fake()->image('gallery1.jpg'),
        UploadedFile::fake()->image('gallery2.jpg'),
    ];

    $payload = [
        'title' => 'Test Ad',
        'description' => 'This is a test ad.',
        'category_id' => $category->id,
        'location' => 'Karachi',
        'price' => 999.99,
        'contact_email' => 'test@example.com',
        'contact_phone' => '03001234567',
        'thumbnail' => $thumbnail,
        'gallery' => $gallery,
    ];

    $response = $this
        ->actingAs($user)
        ->post(route('ads.store'), $payload);

    $response->assertCreated();
    $response->assertJsonFragment(['title' => 'Test Ad']);

    $this->assertDatabaseHas('ads', [
        'title' => 'Test Ad',
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    Storage::disk('public')->assertExists('ads/thumbnails/' . $thumbnail->hashName());

    foreach ($gallery as $image) {
        Storage::disk('public')->assertExists('ads/gallery/' . $image->hashName());
    }

    $ad = Ad::where('title', 'Test Ad')->first();
    expect($ad->images()->where('type', 'thumbnail')->count())->toBe(1);
    expect($ad->images()->where('type', 'gallery')->count())->toBe(count($gallery));
});
