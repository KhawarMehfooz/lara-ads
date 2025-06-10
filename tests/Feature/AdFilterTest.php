<?php

use App\Models\Ad;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->category1 = Category::factory()->create(['name' => 'Electronics']);
    $this->category2 = Category::factory()->create(['name' => 'Vehicles']);

    Ad::factory()->create([
        'title' => 'iPhone 14 Pro',
        'category_id' => $this->category1->id,
        'user_id' => $this->user->id,
        'location' => 'Lahore',
        'price' => 1500,
    ]);

    Ad::factory()->create([
        'title' => 'Honda Civic',
        'category_id' => $this->category2->id,
        'user_id' => $this->user->id,
        'location' => 'Karachi',
        'price' => 30000,
    ]);
});

it('returns ads filtered by category_id', function () {
    $response = $this->getJson(route('ads.index', ['category_id' => $this->category1->id]));

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['title' => 'iPhone 14 Pro']);
});

it('returns ads filtered by location', function () {
    $response = $this->getJson(route('ads.index', ['location' => 'Karachi']));

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['title' => 'Honda Civic']);
});

it('returns ads filtered by min and max price', function () {
    $response = $this->getJson(route('ads.index', [
        'min_price' => 1000,
        'max_price' => 2000,
    ]));

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['title' => 'iPhone 14 Pro']);
});

it('returns ads based on full text search in title or description', function () {
    $response = $this->getJson(route('ads.index', ['q' => 'honda']));

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['title' => 'Honda Civic']);
});

