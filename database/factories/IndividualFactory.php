<?php

namespace Database\Factories;

use App\Models\Individual;
use Illuminate\Database\Eloquent\Factories\Factory;

class IndividualFactory extends Factory
{
    protected $model = Individual::class;

    public function definition(): array
    {
        return [
        ];
    }

    public function forView(?int $id = null): self
    {
        $attributes = $id ? ['idpes' => $id] : [];
        $model = LegacyIndividualFactory::new($attributes)->create();

        return $this->state(function (array $attributes) use ($model) {
            return [
                'id' => $model->idpes,
                'person_id' => $model->idpes,
                'created_at' => $model->data_cad,
                'mother_individual_id' => 'idpes_mae',
                'father_individual_id' => 'idpes_pai',
                'guardian_individual_id' => 'idpes_responsavel',
            ];
        });
    }
}
