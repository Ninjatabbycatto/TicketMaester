<?php

namespace Database\Factories;

use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClinicFactory extends Factory
{
    protected $model = Clinic::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company . ' hospital',       // Generates a fake company/clinic name
            'address' => $this->faker->address,    // Generates a fake address
        ];
    }
}
