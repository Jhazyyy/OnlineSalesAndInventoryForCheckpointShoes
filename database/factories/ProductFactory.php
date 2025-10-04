<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = $this->faker->randomFloat(2, 10, 1000);
        $discount = $this->faker->optional(0.3)->randomFloat(2, 1, $price * 0.3); // max 30% discount

        return [
            'product_name'   => $this->faker->words(3, true), // "Wireless Bluetooth Speaker"
            'product_brand'  => $this->faker->company(),
            'quantity'       => $this->faker->numberBetween(0, 500),
            'price'          => $price,
            // 'discount_price' => $discount ? round($price - $discount, 2) : null,
            'image'          => $this->faker->imageUrl(640, 480, 'technics', true, 'Product'),
            'description'    => $this->faker->paragraphs(2, true),
            // 'tags'           => $this->faker->optional(0.5)->words($this->faker->numberBetween(2, 5)), // array of tags
            // 'warranty_months'=> $this->faker->optional(0.4)->numberBetween(6, 36),
            // 'status'         => $this->faker->randomElement(['active', 'inactive']),
            // 'featured'       => $this->faker->boolean(20), // 20% chance to be featured
            'product_category'       => $this->faker->randomElement([
                'Electronics', 'Home Appliances', 'Accessories', 'Gadgets', 'Office Supplies'
            ]),
        ];
    }

    /**
     * Product is marked as featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'featured' => true,
        ]);
    }

    /**
     * Product is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Product with low stock.
     */
    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => $this->faker->numberBetween(1, 5),
        ]);
    }

    /**
     * Product with a discount.
     */
    public function discounted(): static
    {
        return $this->state(function (array $attributes) {
            $discount = $this->faker->randomFloat(2, 1, $attributes['price'] * 0.3);
            return [
                'discount_price' => round($attributes['price'] - $discount, 2),
            ];
        });
    }

    /**
     * Product without stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => 0,
            'status' => 'inactive',
        ]);
    }

    /**
     * Product with full warranty.
     */
    public function withWarranty(): static
    {
        return $this->state(fn (array $attributes) => [
            'warranty_months' => $this->faker->numberBetween(12, 36),
        ]);
    }
}
