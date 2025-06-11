<?php

use App\Models\Ad;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('updates an ad successfully with new thumbnail and gallery images', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $category = Category::factory()->create();
    $oldCategory = Category::factory()->create();

    $ad = Ad::factory()->for($user)->create([
        'category_id' => $oldCategory->id,
    ]);

    $oldThumbnail = UploadedFile::fake()->image('old-thumb.jpg');
    $oldThumbPath = $oldThumbnail->store('ads/thumbnails', 'public');
    $ad->images()->create([
        'path' => $oldThumbPath,
        'type' => 'thumbnail',
    ]);

    $newThumbnail = UploadedFile::fake()->image('new-thumb.jpg');
    $newGallery = [
        UploadedFile::fake()->image('new-gallery1.jpg'),
        UploadedFile::fake()->image('new-gallery2.jpg'),
    ];

    $payload = [
        'title' => 'Updated Ad',
        'description' => 'Updated description.',
        'category_id' => $category->id,
        'location' => 'Lahore',
        'price' => 1500,
        'contact_email' => 'updated@example.com',
        'contact_phone' => '03123456789',
        'thumbnail' => $newThumbnail,
        'gallery' => $newGallery,
    ];

    $response = $this
        ->actingAs($user)
        ->put(route('ads.update', $ad), $payload);

    $response->assertOk();
    $response->assertJsonFragment(['message' => 'Ad updated successfully.']);

    $ad->refresh();

    expect($ad->title)->toBe('Updated Ad');
    expect($ad->category_id)->toBe($category->id);

    Storage::disk('public')->assertMissing($oldThumbPath);
    Storage::disk('public')->assertExists('ads/thumbnails/' . $newThumbnail->hashName());

    foreach ($newGallery as $image) {
        Storage::disk('public')->assertExists('ads/gallery/' . $image->hashName());
    }

    expect($ad->images()->where('type', 'thumbnail')->count())->toBe(1);
    expect($ad->images()->where('type', 'gallery')->count())->toBe(count($newGallery));
});
