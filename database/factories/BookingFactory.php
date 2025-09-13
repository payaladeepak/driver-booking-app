<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Str;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition()
    {
        return [
            // If your bookings table uses customer_id referencing users
            'customer_id' => User::factory(),
            'customer_name' => $this->faker->name(),
            'customer_phone' => $this->faker->phoneNumber(),
            'pickup_address' => $this->faker->address(),
            'drop_address' => $this->faker->optional()->address(),
            'booking_date' => $this->faker->optional()->dateTimeBetween('now', '+7 days'),
            'vehicle_type' => $this->faker->randomElement(['bike','car']),
            'driver_id' => null, // set explicitly in tests where needed
            'status' => 'pending',
            'booking_code' => strtoupper(Str::random(10)),
            // fallback fields some projects use — harmless if not present in table
            'service_type' => $this->faker->randomElement(['bike','car']),
            'amount' => $this->faker->optional()->randomFloat(2, 5, 500),
            'pickup' => $this->faker->optional()->sentence(3),
            'dropoff' => $this->faker->optional()->sentence(3),
            'pickup_lat' => null,
            'pickup_lng' => null,
            'drop_lat' => null,
            'drop_lng' => null,
            'scheduled_at' => $this->faker->optional()->dateTimeBetween('now', '+10 days'),
            'requested_at' => null,
            'confirmed_at' => null,
            'completed_at' => null,
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ];
    }
}
