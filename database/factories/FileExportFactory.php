<?php

namespace Database\Factories;

use App\Models\FileExport;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileExportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FileExport::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => fn () => LegacyUserFactory::new()->current(),
            'url' => $this->faker->url() . '/image.jpg',
            'filename' => 'Alunos_' . Carbon::now()->format('Y-m-d_H:i'),
            'size' => $this->faker->randomNumber(5),
        ];
    }
}
