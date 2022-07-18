<?php

namespace Database\Factories;

use App\Models\LegacyMenuUserType;
use App_Model_NivelTipoUsuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyMenuUserTypeFactory extends Factory
{
    protected $model = LegacyMenuUserType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(
                [
                    'nivel' => $this->faker->randomElement([
                        App_Model_NivelTipoUsuario::POLI_INSTITUCIONAL,
                        App_Model_NivelTipoUsuario::INSTITUCIONAL,
                        App_Model_NivelTipoUsuario::ESCOLA,
                        App_Model_NivelTipoUsuario::BIBLIOTECA
                    ]),
                ]
            ),
            'menu_id' => MenuFactory::new()->create()->getKey(),
            'cadastra' => 1,
            'visualiza' => 1,
            'exclui' => 1,
        ];
    }

    public function admin()
    {
        return $this->state(
            [
                'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(
                    ['nivel' => App_Model_NivelTipoUsuario::POLI_INSTITUCIONAL]
                )
            ]
        );
    }

    public function institutional()
    {
        return $this->state(
            [
                'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(
                    ['nivel' => App_Model_NivelTipoUsuario::INSTITUCIONAL]
                )
            ]
        );
    }

    public function school()
    {
        return $this->state(
            [
                'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(
                    ['nivel' => App_Model_NivelTipoUsuario::ESCOLA]
                )
            ]
        );
    }

    public function library()
    {
        return $this->state(
            [
                'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(
                    ['nivel' => App_Model_NivelTipoUsuario::ESCOLA]
                )
            ]
        );
    }
}
