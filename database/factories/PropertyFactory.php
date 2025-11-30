<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $type = $this->faker->randomElement(['house', 'apartment', 'condo', 'townhouse', 'villa', 'land', 'commercial']);
        $listingType = $this->faker->randomElement(['sale', 'rent']);

        // Amman districts
        $city = $this->faker->randomElement([
            'Abdoun',
            'Jabal Amman',
            'Jabal Al Lweibdeh',
            'Khalda',
            'Sweifieh',
            'Dabouq',
            'Shmeisani',
            'Mecca Street',
            'Al Rabiah',
            'Al Jubeiha'
        ]);

        // --------------- Pricing (based on JOD market) ---------------
        $basePrice = match ($type) {
            'land' => $this->faker->numberBetween(100000, 1000000),
            'apartment' => $this->faker->numberBetween(45000, 250000),
            'house' => $this->faker->numberBetween(120000, 800000),
            'villa' => $this->faker->numberBetween(400000, 2500000),
            'commercial' => $this->faker->numberBetween(150000, 1500000),
            default => $this->faker->numberBetween(70000, 500000),
        };

        // Monthly rent pricing
        $price = $listingType === 'rent'
            ? $this->faker->numberBetween(250, 5000)
            : $basePrice;

        $title = $this->generatePropertyTitle($type, $city);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->generateDescription($type),
            'type' => $type,
            'listing_type' => $listingType,
            'status' => $this->faker->randomElement(['draft', 'available', 'sold', 'rented', 'pending']),
            'price' => $price,
            'address' => $this->faker->streetAddress(),
            'city' => $city,
            'state' => 'Amman',
            'country' => 'Jordan',
            'postal_code' => $this->faker->optional()->postcode(),

            // Coordinates for Amman
            'latitude' => $this->getLatitudeForCity($city),
            'longitude' => $this->getLongitudeForCity($city),

            'bedrooms' => $type === 'land' ? null : $this->faker->numberBetween(1, 6),
            'bathrooms' => $type === 'land' ? null : $this->faker->numberBetween(1, 4),
            'total_area' => $this->faker->numberBetween(80, 1500), // sqm
            'built_year' => $type === 'land' ? null : $this->faker->numberBetween(1980, 2025),
            'furnished' => $this->faker->boolean(40),
            'parking' => $this->faker->boolean(80),
            'parking_spaces' => $this->faker->boolean(80) ? $this->faker->numberBetween(1, 3) : null,
            'features' => $this->generateFeatures($type),

            'images' => [],

            'meta_title' => $title . ' - Real Estate in Amman',
            'meta_description' => Str::limit($this->generateDescription($type), 155),
            'is_featured' => $this->faker->boolean(20),
            'is_active' => $this->faker->boolean(95),
            'featured_until' => $this->faker->boolean(20)
                ? $this->faker->dateTimeBetween('now', '+3 months')
                : null,

            'contact_name' => $this->faker->name(),
            'contact_phone' => $this->faker->phoneNumber(),
            'contact_email' => $this->faker->email(),
        ];
    }

    private function generatePropertyTitle(string $type, string $city): string
    {
        $adjectives = ['Modern', 'Luxury', 'Spacious', 'Elegant', 'Premium', 'Exclusive', 'Beautiful', 'High-End'];
        $adjective = $this->faker->randomElement($adjectives);

        return match ($type) {
            'house' => "{$adjective} House in {$city}",
            'apartment' => "{$adjective} Apartment in {$city}",
            'villa' => "{$adjective} Villa in {$city}",
            'condo' => "{$adjective} Condo in {$city}",
            'townhouse' => "{$adjective} Townhouse in {$city}",
            'land' => "Prime Land in {$city}",
            'commercial' => "{$adjective} Commercial Property in {$city}",
            default => "{$adjective} Property in {$city}",
        };
    }

    private function generateDescription(string $type): string
    {
        $descriptions = [
            'house' => 'A beautiful home offering spacious living areas, modern finishes, and excellent location near top schools and amenities.',
            'apartment' => 'A stylish apartment with contemporary design, secure building, and close proximity to shopping centers and cafes.',
            'villa' => 'A luxurious villa offering privacy, premium architecture, and high-end facilities in one of Amman’s elite neighborhoods.',
            'condo' => 'A modern condo with excellent building amenities and a prime location in Amman.',
            'townhouse' => 'A modern townhouse that offers comfort, privacy, and community living.',
            'land' => 'Prime investment land suitable for residential or commercial development.',
            'commercial' => 'A commercial space ideal for offices, clinics, or retail with excellent visibility.',
        ];

        return $descriptions[$type] ?? 'A premium real estate opportunity in Amman.';
    }

    private function getLatitudeForCity(string $city): float
    {
        return match ($city) {
            'Abdoun' => $this->faker->randomFloat(6, 31.946, 31.950),
            'Jabal Amman' => $this->faker->randomFloat(6, 31.949, 31.955),
            'Jabal Al Lweibdeh' => $this->faker->randomFloat(6, 31.958, 31.963),
            'Khalda' => $this->faker->randomFloat(6, 31.995, 32.002),
            'Sweifieh' => $this->faker->randomFloat(6, 31.956, 31.961),
            'Dabouq' => $this->faker->randomFloat(6, 32.014, 32.019),
            'Shmeisani' => $this->faker->randomFloat(6, 31.982, 31.987),
            'Mecca Street' => $this->faker->randomFloat(6, 31.970, 31.979),
            'Al Rabiah' => $this->faker->randomFloat(6, 31.974, 31.978),
            'Al Jubeiha' => $this->faker->randomFloat(6, 32.024, 32.028),
            default => $this->faker->randomFloat(6, 31.9, 32.05),
        };
    }

    private function getLongitudeForCity(string $city): float
    {
        return match ($city) {
            'Abdoun' => $this->faker->randomFloat(6, 35.850, 35.860),
            'Jabal Amman' => $this->faker->randomFloat(6, 35.910, 35.920),
            'Jabal Al Lweibdeh' => $this->faker->randomFloat(6, 35.910, 35.915),
            'Khalda' => $this->faker->randomFloat(6, 35.830, 35.840),
            'Sweifieh' => $this->faker->randomFloat(6, 35.860, 35.870),
            'Dabouq' => $this->faker->randomFloat(6, 35.810, 35.820),
            'Shmeisani' => $this->faker->randomFloat(6, 35.900, 35.910),
            'Mecca Street' => $this->faker->randomFloat(6, 35.850, 35.870),
            'Al Rabiah' => $this->faker->randomFloat(6, 35.880, 35.900),
            'Al Jubeiha' => $this->faker->randomFloat(6, 35.850, 35.860),
            default => $this->faker->randomFloat(6, 35.80, 35.95),
        };
    }

    private function generateFeatures(string $type): array
    {
        $common = [
            'Central Heating',
            'Split A/C',
            'Balcony',
            'Parking',
            'Security System',
            'Water Tank',
            'Solar Water Heater'
        ];

        $specific = match ($type) {
            'house', 'villa' => ['Garden', 'Maid Room', 'Laundry Room', 'Storage Room', 'Private Entrance'],
            'apartment', 'condo' => ['Elevator', 'Shared Gym', 'Shared Pool', 'Generator'],
            'townhouse' => ['Private Terrace', 'Garage', 'Small Garden'],
            'commercial' => ['Reception Area', 'Conference Room', 'Central AC', 'Backup Generator'],
            'land' => ['Main Road Access', 'Zoned for Building', 'Registered Title Deed'],
            default => ['High-Quality Finishes'],
        };

        return array_unique(
            $this->faker->randomElements(
                array_merge($common, $specific),
                $this->faker->numberBetween(3, 7)
            )
        );
    }

    // States
    public function featured(): static
    {
        return $this->state(fn() => [
            'is_featured' => true,
            'featured_until' => $this->faker->dateTimeBetween('now', '+6 months'),
        ]);
    }

    public function sold(): static
    {
        return $this->state(fn() => [
            'status' => 'sold',
        ]);
    }

    public function forRent(): static
    {
        return $this->state(fn(array $attributes) => [
            'listing_type' => 'rent',
            'price' => $this->faker->numberBetween(250, 5000),
        ]);
    }
}
