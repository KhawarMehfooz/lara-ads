<?php

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns paginated categories with correct structure', function () {
    Category::factory()->count(10)->create();

    $response = $this->getJson(route('categories.index'));

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                ],
            ],
            'links',
            'meta',
        ]);

    expect($response['meta'])->toHaveKeys(['current_page', 'last_page', 'per_page', 'total']);
});
