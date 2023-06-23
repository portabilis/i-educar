<?php

namespace Database\Factories;

use App\Models\LegacyUser;
use App_Model_NivelTipoUsuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyUserFactory extends Factory
{
    protected $model = LegacyUser::class;

    public function definition(): array
    {
        return [
            'cod_usuario' => fn () => LegacyEmployeeFactory::new()->create()->ref_cod_pessoa_fj,
            'ref_cod_instituicao' => fn () => LegacyInstitutionFactory::new()->current(),
            'ref_funcionario_cad' => fn () => LegacyEmployeeFactory::new()->create()->ref_cod_pessoa_fj,
            'ref_funcionario_exc' => fn () => LegacyEmployeeFactory::new()->create()->ref_cod_pessoa_fj,
            'ref_cod_tipo_usuario' => fn () => LegacyUserTypeFactory::new()->create()->cod_tipo_usuario,
            'data_cadastro' => $this->faker->dateTime(),
            'ativo' => 1,
        ];
    }

    /**
     * Retorna o usuário padrão do sistema.
     */
    public function current(): LegacyUser
    {
        return LegacyUser::query()->first() ?? $this->create([
            'cod_usuario' => fn () => LegacyEmployeeFactory::new()->current()->ref_cod_pessoa_fj,
            'ref_funcionario_cad' => fn () => LegacyEmployeeFactory::new()->current()->ref_cod_pessoa_fj,
            'ref_funcionario_exc' => fn () => LegacyEmployeeFactory::new()->current()->ref_cod_pessoa_fj,
        ]);
    }

    /**
     * Altera o nível do usuário para "admin".
     */
    public function admin(): static
    {
        return $this->state([
            'ref_cod_tipo_usuario' => function () {
                return LegacyUserTypeFactory::new()->create([
                    'nivel' => App_Model_NivelTipoUsuario::POLI_INSTITUCIONAL,
                ]);
            },
        ]);
    }

    /**
     * Altera o nível do usuário para "institucional".
     */
    public function institutional(): static
    {
        return $this->state([
            'ref_cod_tipo_usuario' => function () {
                return LegacyUserTypeFactory::new()->create([
                    'nivel' => App_Model_NivelTipoUsuario::INSTITUCIONAL,
                ]);
            },
        ]);
    }

    /**
     * Retorna o primeiro usuário do sistema.
     *
     * @see LegacyUserFactory::current()
     * @deprecated
     */
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

    /**
     * Adiciona o nível de acesso ao usuário.
     */
    public function withAccess($process, $view = true, $modify = true, $remove = true): static
    {
        return $this->afterCreating(function (LegacyUser $user) use ($process, $view, $modify, $remove) {
            $menu = MenuFactory::new()->create(
                ['process' => $process]
            );
            LegacyMenuUserTypeFactory::new()->create([
                'menu_id' => $menu,
                'ref_cod_tipo_usuario' => $user->type,
                'cadastra' => $modify,
                'visualiza' => $view,
                'exclui' => $remove,
            ]);
        });
    }
}
