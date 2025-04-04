<?php

namespace App\Services;

use App\Exceptions\BookingException;
use App\Models\Booking;
use App\Models\Event;

class BookingService
{
    public function createBooking(array $data): Booking
    {
        $event = Event::findOrFail($data['event_id']);

        if ($this->isPastEvent($event)) {
            throw new BookingException('Cannot book past events', 400);
        }

        if ($this->isEventFull($event)) {
            throw new BookingException('Event is fully booked', 400);
        }

        if ($this->hasExistingBooking($data['event_id'], $data['attendee_id'])) {
            throw new BookingException('Attendee already booked for this event', 400);
        }

        return Booking::create($data);
    }

    private function isEventFull(Event $event): bool
    {
        return $event->bookings()->count() >= $event->capacity;
    }

    private function isPastEvent(Event $event): bool
    {
        return $event->start_date->isPast();
    }

    private function hasExistingBooking(int $eventId, int $attendeeId): bool
    {
        return Booking::where('event_id', $eventId)
            ->where('attendee_id', $attendeeId)
            ->exists();
    }
}
