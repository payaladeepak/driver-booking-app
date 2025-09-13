<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_create_booking()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $payload = [
            'customer_name'  => 'Test User',
            'customer_phone' => '9999999999',
            'pickup_address' => 'Test pickup',
            'booking_date'   => now()->addDays(3)->toDateTimeString(),
        ];

        $response = $this->postJson('/api/bookings', $payload);

        // Debug dump on failure
        if ($response->status() !== 201) {
            fwrite(STDERR, "\nDEBUG RESPONSE: ".$response->status()."\n".$response->getContent()."\n\n");
        }

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'customer_name' => 'Test User',
            'customer_phone' => '9999999999',
            'pickup_address' => 'Test pickup',
            'customer_id' => $user->id,
        ]);
    }

    /** @test */
    public function authenticated_user_can_list_bookings_with_pagination()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        Booking::factory()->count(3)->create(['customer_id' => $user->id]);
        Booking::factory()->count(2)->create();

        $response = $this->getJson('/api/bookings?per_page=2');

        if ($response->status() !== 200) {
            fwrite(STDERR, "\nDEBUG RESPONSE: ".$response->status()."\n".$response->getContent()."\n\n");
        }

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('per_page'));
    }

    /** @test */
    public function authenticated_user_can_update_booking()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $booking = Booking::factory()->create([
            'customer_id' => $user->id,
            'pickup_address' => 'Old pickup',
            'status' => 'pending',
        ]);

        $payload = [
            'pickup_address' => 'New pickup address',
            'status' => 'accepted',
        ];

        $response = $this->putJson("/api/bookings/{$booking->id}", $payload);

        if ($response->status() !== 200) {
            fwrite(STDERR, "\nDEBUG RESPONSE: ".$response->status()."\n".$response->getContent()."\n\n");
        }

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $booking->id,
            'pickup_address' => 'New pickup address',
            'status' => 'accepted',
        ]);
    }
}
