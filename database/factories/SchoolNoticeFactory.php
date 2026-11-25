<?php

namespace Database\Factories;

use App\Models\LegacyInstitution;
use App\Models\LegacySchool;
use App\Models\LegacyUser;
use App\Models\SchoolNotice;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolNoticeFactory extends Factory
{
    protected $model = SchoolNotice::class;

    public function definition(): array
    {
        return [
            'institution_id' => fn () => LegacyInstitutionFactory::new()->current()->getKey(),
            'user_id' => fn () => LegacyUserFactory::new()->current()->getKey(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'date' => $this->faker->date(),
            'hour' => $this->faker->time(),
            'local' => $this->faker->word(),
        ];
    }

    public function forInstitution(LegacyInstitution $institution): self
    {
        return $this->state(fn () => [
            'institution_id' => $institution->getKey(),
        ]);
    }

    public function forUser(LegacyUser $user): self
    {
        return $this->state(fn () => [
            'user_id' => $user->getKey(),
        ]);
    }

    public function withSchools(LegacySchool ...$schools): self
    {
        return $this->afterCreating(function (SchoolNotice $notice) use ($schools) {
            $ids = array_map(fn (LegacySchool $school) => $school->getKey(), $schools);
            $notice->schools()->syncWithoutDetaching($ids);
        });
    }
}
