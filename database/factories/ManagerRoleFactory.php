<?php

namespace Database\Factories;

use App\Models\ManagerRole;
use iEducar\Modules\Educacenso\Model\SchoolManagerRole;
use Illuminate\Database\Eloquent\Factories\Factory;

class ManagerRoleFactory extends Factory
{
    protected $model = ManagerRole::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
        ];
    }

    /**
     * O cargo "Diretor(a)", com o id que o validador dos gestores reconhece
     * como diretor. Um id vindo da sequência só coincide com ele por acaso.
     */
    public function director(): ManagerRole
    {
        return ManagerRole::query()->find(SchoolManagerRole::DIRETOR) ?? ManagerRole::query()->forceCreate([
            'id' => SchoolManagerRole::DIRETOR,
            'name' => 'Diretor(a)',
        ]);
    }
}
