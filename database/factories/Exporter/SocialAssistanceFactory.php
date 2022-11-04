<?php

namespace Database\Factories\Exporter;

use App\Models\Exporter\SocialAssistance;
use Illuminate\Database\Eloquent\Factories\Factory;

class SocialAssistanceFactory extends Factory
{
    protected $model = SocialAssistance::class;

    public function definition(): array
    {
        EnrollmentFactory::new()->make();
        $instance = new $this->model();

        return $instance->query()->first()->getAttributes();
    }
}
