<?php

namespace Database\Factories;

use App\Models\LegacyStudent;
use App\Models\TransportationVehicleType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyStudentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyStudent::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ref_idpes' => static fn () => LegacyIndividualFactory::new()->create(),
            'ref_cod_religiao' => static fn () => ReligionFactory::new()->create(),
            'ref_usuario_cad' => static fn () => LegacyUserFactory::new()->current(),
            'ref_usuario_exc' => static fn () => LegacyUserFactory::new()->current(),
            'tipo_responsavel' => 'a',
            'data_cadastro' => now(),
            'veiculo_transporte_escolar' => '{'.TransportationVehicleType::VAN.'}'
        ];
    }

    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'ativo' => 0,
                'data_exclusao' => Carbon::now()
            ];
        });
    }

    public function notGuardian()
    {
        return $this->state(function (array $attributes) {
            return [
                'tipo_responsavel' => null
            ];
        });
    }

    public function father()
    {
        return $this->state(function (array $attributes) {
            return [
                'ref_idpes' => LegacyIndividualFactory::new()->father()->create(),
                'tipo_responsavel' => null
            ];
        });
    }

    public function mother()
    {
        return $this->state(function (array $attributes) {
            return [
                'ref_idpes' => LegacyIndividualFactory::new()->mother()->create(),
                'tipo_responsavel' => null
            ];
        });
    }
    public function guardian()
    {
        return $this->state(function (array $attributes) {
            return [
                'ref_idpes' => LegacyIndividualFactory::new()->guardian()->create(),
                'tipo_responsavel' => null
            ];
        });
    }
}
