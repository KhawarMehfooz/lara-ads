<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ad>
 */
class AdFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'=>$this->faker->sentence(5),
            'description'=>$this->faker->paragraph(),
            'category_id'=>Category::factory(),
            'user_id'=>User::factory(),
            'location'=>$this->faker->city(),
            'price'=>$this->faker->randomFloat(1,20,1000),
            'contact_email'=>$this->faker->safeEmail(),
            'contact_phone'=>$this->faker->phoneNumber(),
            'is_active'=>true,
            'created_at'=>now()
        ];
    }
}
