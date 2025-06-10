<?php
use App\Models\Ad;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('shows a single ad with category, user, and images', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $category = Category::factory()->create();

    $ad = Ad::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'View Test Ad',
        'slug'=>'view-test-ad',
        'location' => 'Lahore',
        'price' => 1500,
    ]);

    $thumbnailPath = 'ads/thumbnails/thumb.jpg';
    $galleryPath1 = 'ads/gallery/gallery1.jpg';
    $galleryPath2 = 'ads/gallery/gallery2.jpg';

    Storage::disk('public')->put($thumbnailPath, 'fake content');
    Storage::disk('public')->put($galleryPath1, 'fake content');
    Storage::disk('public')->put($galleryPath2, 'fake content');

    $ad->images()->createMany([
        ['path' => $thumbnailPath, 'type' => 'thumbnail'],
        ['path' => $galleryPath1, 'type' => 'gallery'],
        ['path' => $galleryPath2, 'type' => 'gallery'],
    ]);

    $response = $this->getJson(route('ads.show', $ad->slug));

    $response->assertOk();
    $response->assertJsonFragment([
        'title' => 'View Test Ad',
        'location' => 'Lahore',
        'price' => 1500,
        'category' => $category->name,
        'created_by' => $user->name,
    ]);

    $response->assertJsonStructure([
        'data' => [
            'id',
            'title',
            'slug',
            'description',
            'location',
            'price',
            'category',
            'created_by',
            'contact_email',
            'contact_phone',
            'is_active',
            'thumbnail',
            'gallery',
            'created_at',
        ]
    ]);
});
