<?php

use App\Models\LegacyBenefit;
use App\Models\LegacyStudent;
use App\Models\LegacyStudentBenefit;
use App\Models\LegacyUser;
use Database\Factories\LegacyBenefitFactory;
use Database\Factories\LegacyStudentFactory;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyBenefit::class, relations: [
    'deletedByUser' => LegacyUser::class,
    'createdByUser' => LegacyUser::class,
]));

EloquentModelTestCase::registerModelTests();

test('students', function () {
    $benefit = LegacyBenefitFactory::new()->create();
    $student = LegacyStudentFactory::new()->create();

    LegacyStudentBenefit::create([
        'aluno_id' => $student->cod_aluno,
        'aluno_beneficio_id' => $benefit->id,
    ]);

    $this->assertCount(1, $benefit->students);
    $this->assertInstanceOf(LegacyStudent::class, $benefit->students->first());
});
