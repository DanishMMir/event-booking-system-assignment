<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_events(): void
    {
        Event::factory()->count(3)->create();

        $response = $this->getJson('/api/events');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'country',
                        'start_date',
                        'end_date',
                        'capacity',
                        'available_spots',
                    ],
                ],
            ]);
    }

    public function test_can_create_event(): void
    {
        $eventData = [
            'name' => 'Test Event',
            'description' => 'Test Description',
            'country' => 'Test Country',
            'start_date' => now()->addDays(1)->toDateTimeString(),
            'end_date' => now()->addDays(2)->toDateTimeString(),
            'capacity' => 100,
        ];

        $response = $this->postJson('/api/events', $eventData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'country',
                    'start_date',
                    'end_date',
                    'capacity',
                ],
            ]);
    }

    public function test_can_update_event(): void
    {
        $event = Event::factory()->create();

        $updateData = [
            'name' => 'Updated Event Name',
            'description' => 'Updated Description',
            'country' => 'Updated Country',
            'start_date' => now()->addDays(2)->toDateTimeString(),
            'end_date' => now()->addDays(3)->toDateTimeString(),
            'capacity' => 200,
        ];

        $response = $this->putJson("/api/events/{$event->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Updated Event Name',
                    'country' => 'Updated Country',
                    'capacity' => 200,
                ],
            ]);
    }

    public function test_cannot_update_event_capacity_below_bookings(): void
    {
        $event = Event::factory()->create([
            'capacity' => 10,
        ]);

        // Create some bookings
        Booking::factory()->count(5)->create([
            'event_id' => $event->id,
        ]);

        $response = $this->putJson("/api/events/{$event->id}", [
            'capacity' => 3,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'message' => 'New capacity cannot be less than current bookings',
            ]);
    }

    public function test_can_delete_event(): void
    {
        $event = Event::factory()->create();

        $response = $this->deleteJson("/api/events/{$event->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Event deleted successfully',
            ]);

        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_cannot_delete_event_with_bookings(): void
    {
        $event = Event::factory()->create();

        // Create a booking for this event
        Booking::factory()->create([
            'event_id' => $event->id,
        ]);

        $response = $this->deleteJson("/api/events/{$event->id}");

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'message' => 'Cannot delete event with existing bookings',
            ]);

        $this->assertDatabaseHas('events', ['id' => $event->id]);
    }
}
