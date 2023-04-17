<?php

namespace Database\Factories;

use App\Models\ManagerRole;
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

    public function current(): ManagerRole
    {
        return ManagerRole::query()->first() ?? $this->create();
    }
}
