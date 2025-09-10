<?php

namespace Database\Factories;

use App\Models\LegacyActiveLooking;
use App\Models\LegacyUser;
use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'messageable_type' => LegacyActiveLooking::class,
            'messageable_id' => LegacyActiveLookingFactory::new()->create()->id,
            'user_id' => LegacyUserFactory::new()->create()->id,
            'description' => $this->faker->paragraph(3),
        ];
    }

    public function forActiveLooking(LegacyActiveLooking $activeLooking): self
    {
        return $this->state(function (array $attributes) use ($activeLooking) {
            return [
                'messageable_type' => LegacyActiveLooking::class,
                'messageable_id' => $activeLooking->id,
            ];
        });
    }

    public function createdBy(LegacyUser $user): self
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user->id,
            ];
        });
    }

    public function withoutUser(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'user_id' => null,
            ];
        });
    }

    public function shortObservation(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'description' => $this->faker->sentence(10),
            ];
        });
    }

    public function longObservation(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'description' => $this->faker->paragraphs(5, true),
            ];
        });
    }
}
