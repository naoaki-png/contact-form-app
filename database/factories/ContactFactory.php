<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->lastName(),
            'last_name' => $this->faker->firstName(),
            'gender' => $this->faker->randomElement([1, 2]),
            'email' => $this->faker->safeEmail(),
            'tel' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'detail' => $this->faker->realText(50),
            'category_id' => Category::factory(),
        ];
    }
}
