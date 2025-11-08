<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory>
 */
class InventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'sku' => fake()->unique()->bothify('SKU-####-???'),
            'quantity' => fake()->numberBetween(0, 1000),
            'price' => fake()->randomFloat(2, 1, 1000),
            'category' => fake()->randomElement(['Electronics', 'Clothing', 'Food', 'Books', 'Toys', 'Tools', 'Furniture', 'Other']),
            'location' => fake()->randomElement(['Warehouse A', 'Warehouse B', 'Store Front', 'Back Room', 'Storage Unit 1']),
            'is_active' => true,
        ];
    }
}
