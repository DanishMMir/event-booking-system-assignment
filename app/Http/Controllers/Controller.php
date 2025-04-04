<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Event Booking System API Documentation",
 *     description="API documentation for Event Booking System",
 *
 *     @OA\Contact(
 *         email="mirdanishmajeed@gmail.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8080/api",
 *     description="Local API Server"
 * )
 */
abstract class Controller
{
    use ApiResponse, AuthorizesRequests, ValidatesRequests;
}
