<?php

namespace Database\Factories;

use App\Models\FileRelation;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileRelationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FileRelation::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'relation_type' => 'App\Models\LegacyIndividual',
            'relation_id' => fn () => LegacyIndividualFactory::new()->create(),
            'file_id' => FileFactory::new()->create(),
        ];
    }
}
