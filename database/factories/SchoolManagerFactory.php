<?php

namespace Database\Factories;

use App\Models\SchoolManager;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolManagerFactory extends Factory
{
    protected $model = SchoolManager::class;

    public function definition(): array
    {
        return [
            'access_criteria_description' => $this->faker->text(25),
            'chief' => $this->faker->boolean(),
            'employee_id' => static function () {
                $individual = LegacyIndividualFactory::new()->create();
                EmployeeFactory::new()->create([
                    'id' => $individual->idpes
                ]);

                return $individual;
            },
            'school_id' => static fn () => LegacySchoolFactory::new()->create(),
            'role_id' => static fn () => ManagerRoleFactory::new()->create(),
            'access_criteria_id' => static fn () => ManagerAccessCriteriaFactory::new()->create(),
            'link_type_id' => static fn () => ManagerLinkTypeFactory::new()->create(),
        ];
    }
}
