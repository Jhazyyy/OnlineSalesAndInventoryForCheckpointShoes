<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'postal_code' => $this->faker->postcode(),
            'country' => $this->faker->randomElement([
                'Philippines', 
                'United States', 
                'Canada', 
                'Australia', 
                'Singapore', 
                'Japan', 
                'South Korea',
                'Germany',
                'United Kingdom',
                'France'
            ]),
            'date_of_birth' => $this->faker->optional(0.8)->dateTimeBetween('-80 years', '-18 years'),
            'avatar' => $this->faker->optional(0.3)->imageUrl(200, 200, 'people'),
            'notes' => $this->faker->optional(0.2)->paragraph(2),
            'status' => $this->faker->randomElement(['active', 'inactive'], [90, 10]), // 90% active, 10% inactive
            'customer_type' => $this->faker->randomElement(['individual', 'business'], [70, 30]), // 70% individual, 30% business
            'company_name' => null,
            'tax_id' => null,
        ];
    }

    /**
     * Indicate that the customer is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the customer is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the customer is an individual.
     */
    public function individual(): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_type' => 'individual',
            'company_name' => null,
            'tax_id' => null,
        ]);
    }

    /**
     * Indicate that the customer is a business.
     */
    public function business(): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_type' => 'business',
            'company_name' => $this->faker->company(),
            'tax_id' => $this->faker->optional(0.8)->numerify('###-###-###'),
        ]);
    }

    /**
     * Indicate that the customer is from Philippines.
     */
    public function philippines(): static
    {
        return $this->state(fn (array $attributes) => [
            'country' => 'Philippines',
            'city' => $this->faker->randomElement([
                'Manila', 'Quezon City', 'Makati', 'Pasig', 'Taguig', 'Paranaque',
                'Cebu City', 'Davao City', 'Iloilo City', 'Bacolod', 'Cagayan de Oro',
                'Zamboanga City', 'Antipolo', 'Bacoor', 'General Santos'
            ]),
            'state' => $this->faker->randomElement([
                'Metro Manila', 'Cebu', 'Davao del Sur', 'Iloilo', 'Negros Occidental',
                'Misamis Oriental', 'Zamboanga del Sur', 'Rizal', 'Cavite', 'South Cotabato'
            ]),
            'postal_code' => $this->faker->numerify('####'),
        ]);
    }

    /**
     * Indicate that the customer is from Metro Manila.
     */
    public function metroManila(): static
    {
        return $this->state(fn (array $attributes) => [
            'country' => 'Philippines',
            'city' => $this->faker->randomElement([
                'Manila', 'Quezon City', 'Makati', 'Pasig', 'Taguig', 'Paranaque', 
                'Las Pinas', 'Muntinlupa', 'Caloocan', 'Malabon', 'Navotas', 
                'Valenzuela', 'Marikina', 'Pasay', 'San Juan', 'Mandaluyong'
            ]),
            'state' => 'Metro Manila',
            'postal_code' => $this->faker->numerify('####'),
        ]);
    }

    /**
     * Create a young adult customer (18-30 years old).
     */
    public function youngAdult(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_of_birth' => $this->faker->dateTimeBetween('-30 years', '-18 years'),
            'customer_type' => 'individual',
        ]);
    }

    /**
     * Create a senior customer (60+ years old).
     */
    public function senior(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_of_birth' => $this->faker->dateTimeBetween('-80 years', '-60 years'),
        ]);
    }

    /**
     * Create customer with complete business information.
     */
    public function withBusinessInfo(): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_type' => 'business',
            'company_name' => $this->faker->company(),
            'tax_id' => $this->faker->numerify('###-###-###'),
            'notes' => $this->faker->realText(200),
        ]);
    }

    /**
     * Create customer with avatar.
     */
    public function withAvatar(): static
    {
        return $this->state(fn (array $attributes) => [
            'avatar' => $this->faker->imageUrl(200, 200, 'people'),
        ]);
    }

    /**
     * Create VIP customer (business type with complete info).
     */
    public function vip(): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_type' => 'business',
            'company_name' => $this->faker->company() . ' ' . $this->faker->randomElement(['Corp', 'Inc', 'LLC', 'Ltd']),
            'tax_id' => $this->faker->numerify('###-###-###'),
            'status' => 'active',
            'notes' => 'VIP Customer - ' . $this->faker->sentence(),
            'avatar' => $this->faker->imageUrl(200, 200, 'business'),
        ]);
    }
}