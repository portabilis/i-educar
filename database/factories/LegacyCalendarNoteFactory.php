<?php

namespace Database\Factories;

use App\Models\LegacyCalendarNote;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyCalendarNoteFactory extends Factory
{
    public $model = LegacyCalendarNote::class;

    public function definition()
    {
        return [
            'ref_usuario_exc' => fn () => LegacyUserFactory::new()->current(),
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'nm_anotacao' => $this->faker->name,
            'descricao' => $this->faker->text,
            'data_cadastro' => now(),
        ];
    }
}
