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
                    'id' => $individual->idpes,
                ]);

                return $individual;
            },
            'school_id' => fn () => LegacySchoolFactory::new()->create(),
            'role_id' => fn () => ManagerRoleFactory::new()->current(),
            'access_criteria_id' => fn () => ManagerAccessCriteriaFactory::new()->current(),
            'link_type_id' => fn () => ManagerLinkTypeFactory::new()->current(),
        ];
    }
}
