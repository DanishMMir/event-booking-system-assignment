<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="Booking",
 *     required={"event_id", "attendee_id", "status"},
 *
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="event_id", type="integer"),
 *     @OA\Property(property="attendee_id", type="integer"),
 *     @OA\Property(property="status", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'attendee_id',
        'status',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function attendee(): BelongsTo
    {
        return $this->belongsTo(Attendee::class);
    }
}
