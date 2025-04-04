<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\BookingService;
use App\Http\Resources\BookingResource;
use App\Http\Requests\Booking\StoreBookingRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Tag(
 *     name="Bookings",
 *     description="API Endpoints for Bookings"
 * )
 */
class BookingController extends Controller
{
    private BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * @OA\Get(
     *     path="/bookings",
     *     summary="List all bookings",
     *     tags={"Bookings"},
     *     @OA\Parameter(
     *         name="event_id",
     *         in="query",
     *         description="Filter by event ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="attendee_id",
     *         in="query",
     *         description="Filter by attendee ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of bookings"
     *     )
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        $bookings = Booking::query()
            ->when(request('event_id'), fn($query) => $query->where('event_id', request('event_id')))
            ->when(request('attendee_id'), fn($query) => $query->where('attendee_id', request('attendee_id')))
            ->with(['event', 'attendee'])
            ->paginate(10);

        return BookingResource::collection($bookings);
    }

    /**
     * @OA\Post(
     *     path="/bookings",
     *     summary="Create a new booking",
     *     tags={"Bookings"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"event_id","attendee_id","status"},
     *             @OA\Property(property="event_id", type="integer"),
     *             @OA\Property(property="attendee_id", type="integer"),
     *             @OA\Property(property="status", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Booking created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Booking")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(StoreBookingRequest $request): BookingResource
    {
        $booking = $this->bookingService->createBooking($request->validated());
        return new BookingResource($booking);
    }
}
