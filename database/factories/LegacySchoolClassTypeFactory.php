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
     */
    public function definition(): array
    {
        $name = $this->faker->colorName;
        $abbreviation = mb_substr($this->faker->colorName, 0, 5);

        return [
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'nm_tipo' => 'Tipo ' . $name,
            'sgl_tipo' => $abbreviation,
            'ref_cod_instituicao' => fn () => LegacyInstitutionFactory::new()->current(),
        ];
    }

    public function current(): LegacySchoolClassType
    {
        return LegacySchoolClassType::query()->first() ?? $this->create([
            'nm_tipo' => 'Tipo de Turma Padrão',
            'sgl_tipo' => 'P',
        ]);
    }

    public function unique(): self
    {
        return $this->state(function () {
            $schoolClassType = LegacySchoolClassType::query()->first();

            if (empty($schoolClassType)) {
                $schoolClassType = LegacySchoolClassTypeFactory::new()->create();
            }

            return [
                'cod_turma_tipo' => $schoolClassType->getKey(),
            ];
        });
    }
}
