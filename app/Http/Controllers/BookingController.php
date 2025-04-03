<?php

namespace App\Http\Controllers;

use App\Services\BookingService;
use App\Http\Resources\BookingResource;
use App\Http\Requests\Booking\StoreBookingRequest;

class BookingController extends Controller
{
    private BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function store(StoreBookingRequest $request): BookingResource
    {
        $booking = $this->bookingService->createBooking($request->validated());
        return new BookingResource($booking);
    }
}
