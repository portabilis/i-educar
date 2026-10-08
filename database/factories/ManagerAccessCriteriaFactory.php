<?php

namespace Database\Factories;

use App\Models\ManagerAccessCriteria;
use Illuminate\Database\Eloquent\Factories\Factory;

class ManagerAccessCriteriaFactory extends Factory
{
    protected $model = ManagerAccessCriteria::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
        ];
    }

    /**
     * O critério "Exclusivamente por indicação/escolha da gestão", aceito em
     * escola pública e em escola privada.
     *
     * O id é fixo porque o validador dos gestores lê significado nele: 1 é
     * "proprietário", vetado em escola pública; 4, 5 e 6 são vetados em escola
     * privada. Um id vindo da sequência cairia num desses conforme o estado do
     * banco, e o teste passaria ou não dependendo de quem rodou antes.
     */
    public function indication(): ManagerAccessCriteria
    {
        return ManagerAccessCriteria::query()->find(2) ?? ManagerAccessCriteria::query()->forceCreate([
            'id' => 2,
            'name' => 'Exclusivamente por indicação/escolha da gestão',
        ]);
    }
}
