<?php

namespace Database\Factories\Concerns;

use App\Models\LegacyRegistration;
use App\Models\LegacySchool;
use Database\Factories\LegacyDisciplineAbsenceFactory;
use Database\Factories\LegacyStudentAbsenceFactory;

class ReleaseAbsencesForStudentsFactory
{
    public static function fromSchool(LegacySchool $school): void
    {
        $stages = $school->stages()->count();

        $school->registrations()->get()->each(function (LegacyRegistration $registration) use ($stages) {
            $absence = LegacyStudentAbsenceFactory::new()->discipline()->create([
                'matricula_id' => $registration,
            ]);

            $registration->grade->allDisciplines->each(
                fn ($discipline) => LegacyDisciplineAbsenceFactory::new()
                    ->count($stages)
                    ->sequence(fn ($sequence) => ['etapa' => $sequence->index + 1])
                    ->create([
                        'falta_aluno_id' => $absence,
                        'componente_curricular_id' => $discipline,
                    ])
            );
        });
    }
}
