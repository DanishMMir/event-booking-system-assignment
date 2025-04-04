<?php

namespace App\Http\Controllers;

use App\Exceptions\AttendeeException;
use App\Http\Requests\Attendee\StoreAttendeeRequest;
use App\Http\Requests\Attendee\UpdateAttendeeRequest;
use App\Http\Resources\AttendeeResource;
use App\Models\Attendee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Tag(
 *     name="Attendees",
 *     description="API Endpoints for Attendees"
 * )
 */
class AttendeeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/attendees",
     *     summary="List all attendees",
     *     tags={"Attendees"},
     *
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Filter attendees by email",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="phone", type="string"),
     *             ))
     *         )
     *     )
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        $attendees = Attendee::query()
            ->when(request('email'), fn ($query) => $query->where('email', request('email')))
            ->paginate(10);

        return AttendeeResource::collection($attendees);
    }

    /**
     * @OA\Post(
     *     path="/attendees",
     *     summary="Create a new attendee",
     *     tags={"Attendees"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name","email","phone"},
     *
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="phone", type="string"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Attendee created successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Attendee")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(StoreAttendeeRequest $request): AttendeeResource
    {
        $attendee = Attendee::create($request->validated());

        return new AttendeeResource($attendee);
    }

    /**
     * @OA\Put(
     *     path="/attendees/{attendee}",
     *     summary="Update an attendee",
     *     tags={"Attendees"},
     *
     *     @OA\Parameter(
     *         name="attendee",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="phone", type="string"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Attendee updated successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Attendee")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Attendee not found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid operation"
     *     )
     * )
     */
    public function update(UpdateAttendeeRequest $request, Attendee $attendee): AttendeeResource
    {
        $attendee->update($request->validated());

        return new AttendeeResource($attendee);
    }

    /**
     * @OA\Get(
     *     path="/attendees/{attendee}",
     *     summary="Show an attendee details",
     *     tags={"Attendees"},
     *
     *     @OA\Parameter(
     *         name="attendee",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="phone", type="string"),
     *             ))
     *         )
     *     )
     * )
     */
    public function show(Attendee $attendee): AttendeeResource
    {
        return new AttendeeResource($attendee);
    }

    /**
     * @OA\Delete(
     *     path="/attendees/{attendee}",
     *     summary="Delete an attendee",
     *     tags={"Attendees"},
     *
     *     @OA\Parameter(
     *         name="attendee",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Attendee deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Attendee not found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Cannot delete attendee with active bookings"
     *     )
     * )
     */
    public function destroy(Attendee $attendee): JsonResponse
    {
        // Check if attendee has any active bookings
        if ($attendee->bookings()->where('status', 'confirmed')->exists()) {
            throw new AttendeeException('Cannot delete attendee with active bookings', 400);
        }

        $attendee->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Attendee deleted successfully',
        ]);
    }
}
