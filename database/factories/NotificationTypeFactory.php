<?php

namespace Database\Factories;

use App\Models\NotificationType;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationTypeFactory extends Factory
{
    protected $model = NotificationType::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->numberBetween(1000, 2000),
            'name' => $this->faker->text(50),
        ];
    }
}
