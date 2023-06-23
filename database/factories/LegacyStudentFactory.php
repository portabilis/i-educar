<?php

namespace Database\Factories;

use App\Models\LegacyStudent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyStudentFactory extends Factory
{
    protected $model = LegacyStudent::class;

    public function definition(): array
    {
        return [
            'ref_idpes' => fn () => LegacyIndividualFactory::new()->create(),
            'ref_cod_religiao' => fn () => ReligionFactory::new()->create(),
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'ref_usuario_exc' => fn () => LegacyUserFactory::new()->current(),
            'tipo_responsavel' => 'a',
            'data_cadastro' => now(),
            'tipo_transporte' => 0,
        ];
    }

    public function withAge(int $age): static
    {
        return $this->afterMaking(function (LegacyStudent $student) use ($age) {
            $year = now()->year - $age;
            $date = $this->faker->dateTimeBetween("$year-01-01", "$year-12-31");

            $student->individual->update([
                'data_nasc' => $date,
            ]);
        });
    }

    public function male(): static
    {
        return $this->afterMaking(function (LegacyStudent $student) {
            $student->individual->update([
                'sexo' => 'M',
            ]);
        });
    }

    public function female(): static
    {
        return $this->afterMaking(function (LegacyStudent $student) {
            $student->individual->update([
                'sexo' => 'F',
            ]);
        });
    }

    public function inactive(): static
    {
        return $this->state(function () {
            return [
                'ativo' => 0,
                'data_exclusao' => Carbon::now(),
            ];
        });
    }

    public function notGuardian(): static
    {
        return $this->state(function () {
            return [
                'tipo_responsavel' => null,
            ];
        });
    }

    public function father(): static
    {
        return $this->state(function () {
            return [
                'ref_idpes' => fn () => LegacyIndividualFactory::new()->father()->create(),
                'tipo_responsavel' => null,
            ];
        });
    }

    public function mother(): static
    {
        return $this->state(function () {
            return [
                'ref_idpes' => fn () => LegacyIndividualFactory::new()->mother()->create(),
                'tipo_responsavel' => null,
            ];
        });
    }

    public function guardian(): static
    {
        return $this->state(function () {
            return [
                'ref_idpes' => fn () => LegacyIndividualFactory::new()->guardian()->create(),
                'tipo_responsavel' => null,
            ];
        });
    }
}
