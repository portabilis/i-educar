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

    public function current(): ManagerLinkType
    {
        return ManagerLinkType::query()->first() ?? $this->create();
    }
}
