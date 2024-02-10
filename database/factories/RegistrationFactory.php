<?php

namespace Database\Factories;

use App\Models\Registration;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegistrationFactory extends Factory
{
    protected $model = Registration::class;

    public function definition(): array
    {
        return [];
    }

    public function forView(?int $id = null): self
    {
        $attributes = $id ? ['ref_cod_aluno' => $id] : [];
        $model = LegacyRegistrationFactory::new($attributes)->create();

        return $this->state(function (array $attributes) use ($model) {
            return [
                'id' => $model->cod_matricula,
                'student_id' => $model->ref_cod_aluno,
                'level_id' => $model->ref_ref_cod_serie,
                'course_id' => $model->ref_cod_curso,
                'school_id' => $model->ref_ref_cod_escola,
                'created_at' => $model->data_cadastro,
                'year' => $model->ano,
                'created_by' => $model->ref_usuario_cad,
                'status' => $model->aprovado,
            ];
        });
    }
}
