<?php

use App\Models\Ad;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('deletes an ad and its associated images successfully', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $category = Category::factory()->create();

    $ad = Ad::factory()->for($user)->create([
        'category_id' => $category->id,
    ]);

    // Add thumbnail and gallery images
    $thumbnail = UploadedFile::fake()->image('thumb.jpg')->store('ads/thumbnails', 'public');
    $gallery1 = UploadedFile::fake()->image('gallery1.jpg')->store('ads/gallery', 'public');
    $gallery2 = UploadedFile::fake()->image('gallery2.jpg')->store('ads/gallery', 'public');

    $ad->images()->createMany([
        ['path' => $thumbnail, 'type' => 'thumbnail'],
        ['path' => $gallery1, 'type' => 'gallery'],
        ['path' => $gallery2, 'type' => 'gallery'],
    ]);

    // Ensure the images exist in storage
    Storage::disk('public')->assertExists($thumbnail);
    Storage::disk('public')->assertExists($gallery1);
    Storage::disk('public')->assertExists($gallery2);

    // Delete the ad
    $response = $this->actingAs($user)->delete(route('ads.destroy', $ad));

    // Assertions
    $response->assertOk();
    $response->assertJsonFragment(['message' => 'Ad deleted successfully.']);

    $this->assertDatabaseMissing('ads', ['id' => $ad->id]);

    Storage::disk('public')->assertMissing($thumbnail);
    Storage::disk('public')->assertMissing($gallery1);
    Storage::disk('public')->assertMissing($gallery2);
});
