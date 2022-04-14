<?php

namespace Database\Factories;

use App\Models\LegacySchoolClassType;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolClassTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacySchoolClassType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $name = $this->faker->colorName;
        $abbreviation = mb_substr($this->faker->colorName, 0, 5);

        return [
            'ref_usuario_cad' => LegacyUserFactory::new()->unique()->make(),
            'nm_tipo' => 'Tipo ' . $name,
            'sgl_tipo' => $abbreviation,
            'data_cadastro' => now(),
            'ref_cod_instituicao' => LegacyInstitutionFactory::new()->unique()->make(),
        ];
    }

    public function unique(): self
    {
        return $this->state(function () {
            $schoolClassType = LegacySchoolClassType::query()->first();

            if (empty($schoolClassType)) {
                $schoolClassType = LegacySchoolClassTypeFactory::new()->create();
            }

            return [
                'cod_turma_tipo' => $schoolClassType->getKey()
            ];
        });
    }
}
