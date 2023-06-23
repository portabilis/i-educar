<?php

namespace Database\Factories;

use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonFactory extends Factory
{
    protected $model = Person::class;

    public function definition(): array
    {
        return [
        ];
    }

    public function forView(): self
    {
        $model = LegacyPersonFactory::new()->create();
        $model->idpes_cad = LegacyIndividualFactory::new()->create()->idpes;
        $model->idpes_rev = $model->idpes_cad;

        return $this->state(function (array $attributes) use ($model) {
            return [
                'id' => $model->idpes,
                'name' => $model->nome,
                'created_by' => $model->idpes_cad,
                'created_at' => $model->data_cad,
                'url' => $model->url,
                'type' => static function () use ($model) {
                    return match ($model->tipo) {
                        'F' => 1,
                        'J' => 2,
                        default => 0
                    };
                },
                'updated_by' => $model->idpes_rev,
                'updated_at' => $model->data_rev,
                'email' => $model->email,
                'registry_origin' => static function () use ($model) {
                    return match ($model->origem_gravacao) {
                        'M' => 1,
                        'C' => 2,
                        'U' => 3,
                        'O' => 4
                    };
                },
            ];
        });
    }
}
