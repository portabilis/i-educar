<?php

namespace Database\Factories;

use App\Models\ManagerLinkType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ManagerLinkTypeFactory extends Factory
{
    protected $model = ManagerLinkType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
        ];
    }

    /**
     * O vínculo "Concursado/efetivo/estável", com o id fixo da instalação
     * padrão, para o gestor criado ser o mesmo em qualquer banco.
     */
    public function permanent(): ManagerLinkType
    {
        return ManagerLinkType::query()->find(1) ?? ManagerLinkType::query()->forceCreate([
            'id' => 1,
            'name' => 'Concursado/efetivo/estável',
        ]);
    }
}
