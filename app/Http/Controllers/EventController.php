<?php

namespace App\Http\Controllers;

use App\Exceptions\EventException;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Models\Event;
use App\Http\Resources\EventResource;
use App\Http\Requests\Event\StoreEventRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Tag(
 *     name="Events",
 *     description="API Endpoints for Events"
 * )
 */
class EventController extends Controller
{
    /**
     * @OA\Get(
     *     path="/events",
     *     summary="List all events",
     *     tags={"Events"},
     *     @OA\Parameter(
     *         name="country",
     *         in="query",
     *         description="Filter events by country",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="country", type="string"),
     *                 @OA\Property(property="start_date", type="string", format="date-time"),
     *                 @OA\Property(property="end_date", type="string", format="date-time"),
     *                 @OA\Property(property="capacity", type="integer"),
     *                 @OA\Property(property="available_spots", type="integer")
     *             ))
     *         )
     *     )
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        $events = Event::query()
            ->when(request('country'), fn($query) => $query->where('country', request('country')))
            ->paginate(10);

        return EventResource::collection($events);
    }

    /**
     * @OA\Post(
     *     path="/events",
     *     summary="Create a new event",
     *     tags={"Events"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","description","country","start_date","end_date","capacity"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="country", type="string"),
     *             @OA\Property(property="start_date", type="string", format="date-time"),
     *             @OA\Property(property="end_date", type="string", format="date-time"),
     *             @OA\Property(property="capacity", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Event created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Event")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(StoreEventRequest $request): EventResource
    {
        $event = Event::create($request->validated());
        return new EventResource($event);
    }

    /**
     * @OA\Put(
     *     path="/events/{event}",
     *     summary="Update an event",
     *     tags={"Events"},
     *     @OA\Parameter(
     *         name="event",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="country", type="string"),
     *             @OA\Property(property="start_date", type="string", format="date-time"),
     *             @OA\Property(property="end_date", type="string", format="date-time"),
     *             @OA\Property(property="capacity", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Event")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Event not found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid operation"
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/events/{event}",
     *     summary="Delete an event",
     *     tags={"Events"},
     *     @OA\Parameter(
     *         name="event",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Event not found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Cannot delete event with bookings"
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/events/{event}",
     *     summary="Show an event details",
     *     tags={"Events"},
     *     @OA\Parameter(
     *         name="event",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="country", type="string"),
     *                 @OA\Property(property="start_date", type="string", format="date-time"),
     *                 @OA\Property(property="end_date", type="string", format="date-time"),
     *                 @OA\Property(property="capacity", type="integer"),
     *                 @OA\Property(property="available_spots", type="integer")
     *             ))
     *         )
     *     )
     * )
     */
    public function show(Event $event): EventResource
    {
        return new EventResource($event);
    }
}
