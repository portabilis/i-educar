<?php

namespace Database\Factories;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = File::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'url' => $this->faker->url,
            'size' => $this->faker->numberBetween(1000, 10000),
            'original_name' => $this->faker->name(),
            'extension' => $this->faker->fileExtension,
        ];
    }
}
