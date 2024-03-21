<?php

namespace Database\Factories;

use App\Models\Phone;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhoneFactory extends Factory
{
    protected $model = Phone::class;

    public function definition(): array
    {
        return [
        ];
    }

    public function forView(?int $id = null): self
    {
        $attributes = $id ? ['idpes' => $id] : [];
        $model = LegacyPhoneFactory::new($attributes)->create();

        return $this->state(function (array $attributes) use ($model) {
            return [
                'id' => $model->idpes.'-'.$model->tipo,
                'person_id' => $model->idpes,
                'type_id' => $model->tipo,
                'area_code' => $model->ddd,
                'number' => $model->fone,
                'created_by' => $model->idpes_cad,
                'updated_by' => $model->idpes_rev,
                'created_at' => $model->data_cad,
                'updated_at' => $model->data_rev,
            ];
        });
    }
}
