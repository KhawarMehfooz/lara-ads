<?php

use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeds all default categories', function () {
    $this->seed(CategorySeeder::class);

    $expected = [
        'Vehicles',
        'Real Estate',
        'Electronics',
        'Furniture',
        'Jobs',
        'Services',
        'Fashion',
        'Pets',
        'Books',
        'Others',
    ];

    foreach ($expected as $name) {
        $this->assertDatabaseHas('categories', ['name' => $name]);
    }
});
