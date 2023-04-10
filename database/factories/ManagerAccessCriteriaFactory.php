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

    public function current(): ManagerAccessCriteria
    {
        return ManagerAccessCriteria::query()->first() ?? $this->create();
    }
}
