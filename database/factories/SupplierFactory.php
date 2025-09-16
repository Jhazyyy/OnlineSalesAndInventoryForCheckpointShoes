<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_name' => $this->faker->company(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'supplier_type' => $this->faker->randomElement([
                'local', 
                'distributor', 
                'manufacturer', 
                'service_provider'
            ]),
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
            'tax_id' => $this->faker->optional(0.7)->numerify('###-###-###'),
            'payment_terms' => $this->faker->randomElement([
                'Net 30', 
                'Net 15', 
                'COD', 
                'Prepaid', 
                'Net 60', 
                '2/10 Net 30',
                'Upon Receipt',
                'Net 45'
            ]),
            'status' => $this->faker->randomElement(['active', 'inactive'], [90, 10]), // 90% active, 10% inactive
            'notes' => $this->faker->optional(0.3)->paragraph(2),
        ];
    }

    /**
     * Indicate that the supplier is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the supplier is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the supplier is local.
     */
    public function local(): static
    {
        return $this->state(fn (array $attributes) => [
            'supplier_type' => 'local',
            'country' => 'Philippines',
            'city' => $this->faker->randomElement([
                'Manila', 'Quezon City', 'Makati', 'Pasig', 'Taguig', 'Paranaque', 
                'Las Pinas', 'Muntinlupa', 'Caloocan', 'Malabon', 'Navotas', 
                'Valenzuela', 'Marikina', 'Pasay', 'San Juan', 'Mandaluyong'
            ]),
            'state' => 'Metro Manila',
        ]);
    }

    /**
     * Indicate that the supplier is a distributor.
     */
    public function distributor(): static
    {
        return $this->state(fn (array $attributes) => [
            'supplier_type' => 'distributor',
            'supplier_name' => $this->faker->company() . ' Distribution',
        ]);
    }

    /**
     * Indicate that the supplier is a manufacturer.
     */
    public function manufacturer(): static
    {
        return $this->state(fn (array $attributes) => [
            'supplier_type' => 'manufacturer',
            'supplier_name' => $this->faker->company() . ' Manufacturing',
            'tax_id' => $this->faker->numerify('###-###-###'),
        ]);
    }

    /**
     * Indicate that the supplier is a service provider.
     */
    public function serviceProvider(): static
    {
        return $this->state(fn (array $attributes) => [
            'supplier_type' => 'service_provider',
            'supplier_name' => $this->faker->company() . ' Services',
        ]);
    }

    /**
     * Indicate that the supplier is from Philippines.
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
     * Create suppliers with realistic business data.
     */
    public function withBusinessInfo(): static
    {
        return $this->state(fn (array $attributes) => [
            'tax_id' => $this->faker->numerify('###-###-###'),
            'payment_terms' => $this->faker->randomElement(['Net 30', '2/10 Net 30', 'Net 15']),
            'notes' => $this->faker->realText(200),
        ]);
    }
}