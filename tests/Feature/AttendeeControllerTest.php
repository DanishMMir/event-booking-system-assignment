<?php


use Tests\TestCase;
use App\Models\Event;
use App\Models\Attendee;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendeeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_attendees(): void
    {
        Attendee::factory()->count(3)->create();

        $response = $this->getJson('/api/attendees');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'phone',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    public function test_can_show_single_attendee(): void
    {
        $attendee = Attendee::factory()->create();

        $response = $this->getJson("/api/attendees/{$attendee->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $attendee->id,
                    'name' => $attendee->name,
                    'email' => $attendee->email,
                    'phone' => $attendee->phone
                ]
            ]);
    }

    public function test_show_returns_404_for_non_existent_attendee(): void
    {
        $response = $this->getJson('/api/attendees/999');

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Resource not found'
            ]);
    }

    public function test_can_create_attendee(): void
    {
        $attendee = Attendee::factory()->create();

        $response = $this->postJson('/api/attendees', $attendee);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'phone' => '1234567890'
                ]
            ]);

        $this->assertDatabaseHas('attendees', $attendee);
    }

    public function test_cannot_create_attendee_with_duplicate_email(): void
    {
        Attendee::factory()->create(['email' => 'john@example.com']);

        $attendeeData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890'
        ];

        $response = $this->postJson('/api/attendees', $attendeeData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_can_update_attendee(): void
    {
        $attendee = Attendee::factory()->create();

        $updateData = [
            'name' => 'Updated Name',
            'phone' => '0987654321'
        ];

        $response = $this->putJson("/api/attendees/{$attendee->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $attendee->id,
                    'name' => 'Updated Name',
                    'phone' => '0987654321',
                    'email' => $attendee->email
                ]
            ]);

        $this->assertDatabaseHas('attendees', [
            'id' => $attendee->id,
            'name' => 'Updated Name',
            'phone' => '0987654321'
        ]);
    }

    public function test_can_delete_attendee(): void
    {
        $attendee = Attendee::factory()->create();

        $response = $this->deleteJson("/api/attendees/{$attendee->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Attendee deleted successfully'
            ]);

        $this->assertDatabaseMissing('attendees', ['id' => $attendee->id]);
    }

    public function test_cannot_delete_attendee_with_active_bookings(): void
    {
        $attendee = Attendee::factory()->create();
        $event = Event::factory()->create();

        // Create an active booking for the attendee
        Booking::create([
            'event_id' => $event->id,
            'attendee_id' => $attendee->id,
            'status' => 'confirmed'
        ]);

        $response = $this->deleteJson("/api/attendees/{$attendee->id}");

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'message' => 'Cannot delete attendee with active bookings'
            ]);

        $this->assertDatabaseHas('attendees', ['id' => $attendee->id]);
    }

    public function test_can_search_attendees_by_email(): void
    {
        Attendee::factory()->create(['email' => 'test@example.com']);
        Attendee::factory()->create(['email' => 'other@example.com']);

        $response = $this->getJson('/api/attendees?email=test@example.com');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    [
                        'email' => 'test@example.com'
                    ]
                ]
            ]);
    }
}
