<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'text' => $this->faker->text(),
            'link' => $this->faker->url,
            'read_at' => Carbon::now(),
            'user_id' => fn () => LegacyUserFactory::new()->current(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'type_id' => fn () => NotificationTypeFactory::new()->create(),
        ];
    }
}
