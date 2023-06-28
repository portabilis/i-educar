<?php

namespace Database\Factories;

use App\Models\LegacyAccess;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyAccessFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyAccess::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'people_id' => function () {
                return LegacyEmployeeFactory::new()->create()->ref_cod_pessoa_fj;
            },
            'internal_ip' => $this->faker->ipv4,
            'external_ip' => $this->faker->ipv4,
            'obs' => $this->faker->paragraph,
            'success' => $this->faker->boolean,
        ];
    }
}
