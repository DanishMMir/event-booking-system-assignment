<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\AttendeeResource;
use App\Http\Requests\Attendee\StoreAttendeeRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AttendeeController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $attendees = Attendee::paginate(10);
        return AttendeeResource::collection($attendees);
    }

    public function store(StoreAttendeeRequest $request): AttendeeResource
    {
        $attendee = Attendee::create($request->validated());
        return new AttendeeResource($attendee);
    }

    public function show(Attendee $attendee): AttendeeResource
    {
        return new AttendeeResource($attendee);
    }

    public function destroy(Attendee $attendee): JsonResponse
    {
        $attendee->delete();
        return response()->json(null, 204);
    }
}
