<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('+1 week', '+1 month');
        $endDate = fake()->dateTimeBetween($startDate, '+2 months');

        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'country' => fake()->country(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'capacity' => fake()->numberBetween(10, 100),
        ];
    }
}
