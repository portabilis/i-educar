<?php

namespace Database\Factories;

use App\Models\LegacyIndividual;
use App\Models\Timeline;
use App\Services\IndividualCpfTimelineService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Timeline>
 */
class TimelineFactory extends Factory
{
    protected $model = Timeline::class;

    public function definition(): array
    {
        return [
            'model_id' => $this->faker->randomNumber(),
            'model_type' => LegacyIndividual::class,
            'type' => IndividualCpfTimelineService::TYPE,
            'payload' => [
                'user' => [
                    'id' => $this->faker->randomNumber(),
                    'name' => $this->faker->name(),
                ],
                'before' => [
                    'CPF' => null,
                ],
                'after' => [
                    'CPF' => $this->faker->numerify('###.###.###-##'),
                ],
            ],
        ];
    }
}
