<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        $model = LegacyStudentFactory::new()->create();

        return [
            'id' => $model->cod_aluno,
            'religion_id' => $model->ref_cod_religiao,
            'individual_id' => $model->ref_idpes,
            'created_by' => $model->ref_usuario_cad,
            'deleted_by' => $model->ref_usuario_exc,
            'created_at' => $model->data_cadastro,
            'guardian_type' => 3,
            'transportation_vehicle_type' => $model->veiculo_transporte_escolar,
        ];
    }

    public function noGuardian()
    {
        $model = LegacyStudentFactory::new()->notGuardian()->create();

        return $this->state(function (array $attributes) use ($model) {
            return [
                'id' => $model->cod_aluno,
                'religion_id' => $model->ref_cod_religiao,
                'individual_id' => $model->ref_idpes,
                'created_by' => $model->ref_usuario_cad,
                'deleted_by' => $model->ref_usuario_exc,
                'created_at' => $model->data_cadastro,
                'guardian_type' => null,
            ];
        });
    }

    public function father()
    {
        $model = LegacyStudentFactory::new()->father()->create();

        return $this->state(function (array $attributes) use ($model) {
            return [
                'id' => $model->cod_aluno,
                'religion_id' => $model->ref_cod_religiao,
                'individual_id' => $model->ref_idpes,
                'created_by' => $model->ref_usuario_cad,
                'deleted_by' => $model->ref_usuario_exc,
                'created_at' => $model->data_cadastro,
                'guardian_type' => null,
            ];
        });
    }

    public function mother(): self
    {
        $model = LegacyStudentFactory::new()->mother()->create();

        return $this->state(function (array $attributes) use ($model) {
            return [
                'id' => $model->cod_aluno,
                'religion_id' => $model->ref_cod_religiao,
                'individual_id' => $model->ref_idpes,
                'created_by' => $model->ref_usuario_cad,
                'deleted_by' => $model->ref_usuario_exc,
                'created_at' => $model->data_cadastro,
                'guardian_type' => null,
            ];
        });
    }

    public function guardian(): self
    {
        $model = LegacyStudentFactory::new()->guardian()->create();

        return $this->state(function (array $attributes) use ($model) {
            return [
                'id' => $model->cod_aluno,
                'religion_id' => $model->ref_cod_religiao,
                'individual_id' => $model->ref_idpes,
                'created_by' => $model->ref_usuario_cad,
                'deleted_by' => $model->ref_usuario_exc,
                'created_at' => $model->data_cadastro,
                'guardian_type' => null,
            ];
        });
    }
}
