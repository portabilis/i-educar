<?php

use App\Models\LegacyGrade;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->route = 'api.resource.grade';

    // curso
    $this->course = LegacyCourseFactory::new()->create();

    // escolas
    $this->schools = LegacySchoolFactory::new()->count(2)->create();
    // escola
    $this->school = $this->schools->first();

    $this->schools->each(function ($school) {
        LegacyGradeFactory::new()->count(3)->create(['ref_cod_curso' => $this->course->id])->each(function ($grade) use ($school) {
            LegacySchoolGradeFactory::new()->create([
                'ref_cod_serie' => $grade->id,
                'ref_cod_escola' => $school->id,
            ]);
        });
    });

    // serie
    $this->grade = $this->course->grades()->first();
});

test('exact json match', function () {
    $response = $this->getJson(
        route(
            $this->route,
            [
                'course' => $this->course->id,
                'school' => $this->school,
            ]
        )
    );

    $grades = LegacyGrade::getResource([
        'course' => $this->course->id,
        'school' => $this->school->id,
    ]);

    $response->assertSuccessful()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                ],
            ],
        ]);

    foreach ($grades as $key => $grade) {
        $response->assertJsonFragment([
            'id' => $grade['id'],
            'name' => $grade['name'],
        ]);
    }
});

test('parametes exclude exact json match', function () {
    $grade_exclude_id = $this->grade->id;
    $school_exclude_id = $this->school->id;

    $grades = LegacyGrade::getResource([
        'course' => $this->course->id,
        'gradeExclude' => $grade_exclude_id,
        'schoolExclude' => $school_exclude_id,
    ]);

    $response = $this->getJson(
        route(
            $this->route,
            [
                'course' => $this->course->id,
                'grade_exclude' => $grade_exclude_id,
                'school_exclude' => $school_exclude_id,
            ]
        )
    );

    $response->assertSuccessful()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                ],
            ],
        ]);

    foreach ($grades as $key => $grade) {
        $response->assertJsonFragment([
            'id' => $grade['id'],
            'name' => $grade['name'],
        ]);
    }
});

test('required parameters', function () {
    $response = $this->getJson(route($this->route));

    $response->assertOk();
    $response->assertJsonCount(0, 'data');
});

test('invalid parameters', function () {
    $response = $this->getJson(route('api.resource.grade', [
        'course' => 'Curso',
        'school' => 'Escola',
        'grade_exclude' => 'Serie',
        'school_exclude' => 'Escola',
    ]));

    $response->assertOk();
    $response->assertJsonCount(0, 'data');
});
