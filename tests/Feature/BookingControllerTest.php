<?php

namespace Tests\Feature;

use App\Models\Attendee;
use App\Models\Booking;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_booking(): void
    {
        $event = Event::factory()->create(['capacity' => 10]);
        $attendee = Attendee::factory()->create();

        $response = $this->postJson('/api/bookings', [
            'event_id' => $event->id,
            'attendee_id' => $attendee->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'event',
                    'attendee',
                    'status',
                ],
            ]);
    }

    public function test_cannot_book_full_event(): void
    {
        $event = Event::factory()->create(['capacity' => 1]);
        $attendee1 = Attendee::factory()->create();
        $attendee2 = Attendee::factory()->create();

        // First booking
        $this->postJson('/api/bookings', [
            'event_id' => $event->id,
            'attendee_id' => $attendee1->id,
        ]);

        // Second booking should fail
        $response = $this->postJson('/api/bookings', [
            'event_id' => $event->id,
            'attendee_id' => $attendee2->id,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Event is fully booked',
            ]);
    }

    public function test_attendee_cannot_book_same_event_twice(): void
    {
        // Create an event with sufficient capacity
        $event = Event::factory()->create([
            'capacity' => 10,
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(2),
        ]);

        // Create an attendee
        $attendee = Attendee::factory()->create();

        // First booking attempt - should succeed
        $firstBookingResponse = $this->postJson('/api/bookings', [
            'event_id' => $event->id,
            'attendee_id' => $attendee->id,
        ]);

        $firstBookingResponse->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'event',
                    'attendee',
                    'status',
                ],
            ]);

        // Verify the first booking was created
        $this->assertDatabaseHas('bookings', [
            'event_id' => $event->id,
            'attendee_id' => $attendee->id,
            'status' => 'confirmed',
        ]);

        // Second booking attempt - should fail
        $secondBookingResponse = $this->postJson('/api/bookings', [
            'event_id' => $event->id,
            'attendee_id' => $attendee->id,
        ]);

        $secondBookingResponse->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'message' => 'Attendee already booked for this event',
            ]);

        // Verify only one booking exists
        $this->assertEquals(1, Booking::where([
            'event_id' => $event->id,
            'attendee_id' => $attendee->id,
        ])->count());
    }

    public function test_attendee_can_book_different_events(): void
    {
        // Create two different events
        $event1 = Event::factory()->create([
            'capacity' => 10,
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(2),
        ]);

        $event2 = Event::factory()->create([
            'capacity' => 10,
            'start_date' => now()->addDays(3),
            'end_date' => now()->addDays(4),
        ]);

        // Create an attendee
        $attendee = Attendee::factory()->create();

        // Book first event
        $firstBookingResponse = $this->postJson('/api/bookings', [
            'event_id' => $event1->id,
            'attendee_id' => $attendee->id,
        ]);

        $firstBookingResponse->assertStatus(201);

        // Book second event
        $secondBookingResponse = $this->postJson('/api/bookings', [
            'event_id' => $event2->id,
            'attendee_id' => $attendee->id,
        ]);

        $secondBookingResponse->assertStatus(201);

        // Verify both bookings exist
        $this->assertEquals(2, Booking::where('attendee_id', $attendee->id)->count());
    }
}
