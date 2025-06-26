<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition() {
        return [
            'title' => $this->faker->sentence(6),
            'description' => $this->faker->paragraph(),
            'clinic_id' => Clinic::inRandomOrder()->value('id'),
            'created_by' => User::inRandomOrder()->value('id') ?? User::factory(),
            'taken_by' => User::inRandomOrder()->value('id'), // nullable if you want: $this->faker->optional()->randomElement(...)
            'priority' => $this->faker->randomElement(['low', 'normal', 'high']),
            'backlog_time' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'inprogress_time' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'acknowledged_time' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'status' => $this->faker->randomElement(['new', 'backlogs', 'in_progress', 'acknowledged', 'completed']),
        ];
    }

}
