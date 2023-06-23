<?php

namespace Database\Factories\Concerns;

use App\Models\LegacyRegistration;
use App\Models\LegacySchool;
use Database\Factories\LegacyDisciplineScoreFactory;
use Database\Factories\LegacyRegistrationScoreFactory;

class ReleaseScoresForStudentsFactory
{
    public static function fromSchool(LegacySchool $school): void
    {
        $stages = $school->stages()->count();

        $school->registrations()->get()->each(function (LegacyRegistration $registration) use ($stages) {
            $registrationScore = LegacyRegistrationScoreFactory::new()->create([
                'matricula_id' => $registration,
            ]);

            $registration->grade->allDisciplines->each(
                fn ($discipline) => LegacyDisciplineScoreFactory::new()
                    ->count($stages)
                    ->sequence(fn ($sequence) => ['etapa' => $sequence->index + 1])
                    ->create([
                        'nota_aluno_id' => $registrationScore,
                        'componente_curricular_id' => $discipline,
                    ])
            );
        });
    }
}
