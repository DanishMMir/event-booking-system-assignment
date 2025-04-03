<?php

namespace App\Http\Controllers;

use App\Exceptions\EventException;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Models\Event;
use App\Http\Resources\EventResource;
use App\Http\Requests\Event\StoreEventRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $events = Event::query()
            ->when(request('country'), fn($query) => $query->where('country', request('country')))
            ->paginate(10);

        return EventResource::collection($events);
    }

    public function store(StoreEventRequest $request): EventResource
    {
        $event = Event::create($request->validated());
        return new EventResource($event);
    }

    public function update(UpdateEventRequest $request, Event $event): EventResource
    {
        // Check if new capacity is less than current bookings
        if (
            isset($request->validated()['capacity']) &&
            $request->validated()['capacity'] < $event->bookings()->count()
        ) {
            throw new EventException('New capacity cannot be less than current bookings');
        }

        $event->update($request->validated());

        return new EventResource($event);
    }

    public function destroy(Event $event): JsonResponse
    {
        // Check if event has any bookings
        if ($event->bookings()->exists()) {
            throw new EventException('Cannot delete event with existing bookings');
        }

        $event->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Event deleted successfully'
        ]);
    }

    public function show(Event $event): EventResource
    {
        return new EventResource($event);
    }
}
