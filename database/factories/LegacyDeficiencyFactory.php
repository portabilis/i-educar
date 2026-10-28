<?php

namespace Database\Factories;

use App\Models\LegacyDeficiency;
use iEducar\Modules\Educacenso\Model\Deficiencias;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyDeficiencyFactory extends Factory
{
    protected $model = LegacyDeficiency::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'educacenso' => Deficiencias::BAIXA_VISAO,
            'disregards_different_rule' => false,
            'require_medical_report' => false,
        ];
    }
}
