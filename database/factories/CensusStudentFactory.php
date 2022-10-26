<?php

namespace Database\Factories;

use App\Models\CensusStudent;
use Illuminate\Database\Eloquent\Factories\Factory;

class CensusStudentFactory extends Factory
{
    protected $model = CensusStudent::class;

    public function definition(): array
    {
        return [
        ];
    }

    public function forView(int $id): self
    {
        $model = StudentInepFactory::new([
            'cod_aluno' => $id
        ])->create();

        return $this->state(function (array $attributes) use ($model) {
            return [
                'id' =>  $model->cod_aluno,
                'student_id' => $model->cod_aluno,
                'inep_code' => $model->cod_aluno_inep,
                'inep_name' => $model->nome_inep,
                'created_at' =>  $model->created_at,
                'updated_at' =>  $model->updated_at,
            ];
        });
    }
}
