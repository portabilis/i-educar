<?php

namespace Database\Factories;

use App\Models\LegacyUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'cod_usuario' => function () {
                return LegacyEmployeeFactory::new()->create()->ref_cod_pessoa_fj;
            },
            'ref_cod_instituicao' => 1,
            'ref_funcionario_cad' => function () {
                return LegacyEmployeeFactory::new()->create()->ref_cod_pessoa_fj;
            },
            'ref_cod_tipo_usuario' => function () {
                return LegacyUserTypeFactory::new()->create()->cod_tipo_usuario;
            },
            'data_cadastro' => $this->faker->dateTime,
            'ativo' => 1,
        ];
    }

    public function unique()
    {
        return $this->state(function () {
            $user = LegacyUser::query()->first();

            if (empty($user)) {
                $user = LegacyUserFactory::new()->create();
            }

            return [
                'cod_usuario' => $user->getKey(),
                'ref_funcionario_cad' => $user->ref_funcionario_cad,
                'ref_cod_tipo_usuario' => $user->cod_tipo_usuario,
            ];
        });
    }
}
