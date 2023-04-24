<?php

namespace Database\Factories;

use App\Models\LegacyUserSchool;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyUserSchoolFactory extends Factory
{
    protected $model = LegacyUserSchool::class;

    public function definition(): array
    {
        return [
            'ref_cod_usuario' => fn () => LegacyUserFactory::new()->current(),
            'ref_cod_escola' => fn () => LegacySchoolFactory::new()->create(),
        ];
    }
}
