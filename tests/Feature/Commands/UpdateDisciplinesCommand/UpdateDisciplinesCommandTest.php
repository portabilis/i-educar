<?php

use App\Models\MigratedDiscipline;
use Database\Factories\LegacyDisciplineAbsenceFactory;
use Database\Factories\LegacyDisciplineDescriptiveOpinionFactory;
use Database\Factories\LegacyDisciplineExemptionFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyDisciplineScoreAverageFactory;
use Database\Factories\LegacyDisciplineScoreFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyRegistrationScoreFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassTeacherDisciplineFactory;
use Database\Factories\LegacySchoolClassTeacherFactory;
use Database\Factories\LegacyScoreExamFactory;
use Database\Factories\LegacyStudentAbsenceFactory;
use Database\Factories\LegacyStudentDescriptiveOpinionFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Commands\UpdateDisciplinesCommand\RecordingMoveDisciplineDataFake;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->createImportFile = function (array $rows): string {
        $filePath = tempnam(sys_get_temp_dir(), 'update-disciplines-import-');

        $this->assertNotFalse($filePath);

        unlink($filePath);
        $filePath .= '.csv';

        $handle = fopen($filePath, 'wb');

        $this->assertNotFalse($handle);

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return $filePath;
    };
});

test('update disciplines command records each valid movement and ignores non numeric rows', function (bool $includesIgnoredRows) {
    RecordingMoveDisciplineDataFake::reset();

    $disciplineFrom = LegacyDisciplineFactory::new()->create();
    $disciplineTo = LegacyDisciplineFactory::new()->create();
    $grade = LegacyGradeFactory::new()->create();
    $year = now()->year;

    $rows = [[$disciplineFrom->getKey(), $disciplineTo->getKey(), $grade->getKey(), $year]];

    if ($includesIgnoredRows) {
        array_unshift($rows, ['disciplina_origem', 'disciplina_destino', 'serie', 'ano']);
        $rows[] = ['invalido', $disciplineTo->getKey(), $grade->getKey(), $year];
    }

    $filePath = ($this->createImportFile)($rows);

    try {
        $this->artisan('update:disciplines', [
            'filename' => $filePath,
            '--copier' => [RecordingMoveDisciplineDataFake::class],
        ])->assertExitCode(0);

        $migration = MigratedDiscipline::query()
            ->where('old_discipline_id', $disciplineFrom->getKey())
            ->where('new_discipline_id', $disciplineTo->getKey())
            ->where('grade_id', $grade->getKey())
            ->where('year', $year)
            ->first();

        $this->assertNotNull($migration);
        $this->assertCount(1, RecordingMoveDisciplineDataFake::$calls);
        $this->assertSame([
            'disciplineFrom' => $disciplineFrom->getKey(),
            'disciplineTo' => $disciplineTo->getKey(),
            'year' => $year,
            'gradeId' => $grade->getKey(),
        ], RecordingMoveDisciplineDataFake::$calls[0]);
        $this->assertSame(1, MigratedDiscipline::query()
            ->where('old_discipline_id', $disciplineFrom->getKey())
            ->where('new_discipline_id', $disciplineTo->getKey())
            ->count());
    } finally {
        @unlink($filePath);
    }
})->with([
    'single valid movement row' => [false],
    'header-like and invalid rows are ignored' => [true],
]);

test('sem copiadores informados move notas medias exames pareceres faltas dispensas e vinculos do ano e da serie', function () {
    $from = LegacyDisciplineFactory::new()->create();
    $to = LegacyDisciplineFactory::new()->create();
    $schoolClass = LegacySchoolClassFactory::new()->create(['ano' => 2030]);
    $grade = $schoolClass->ref_ref_cod_serie;
    $registration = LegacyRegistrationFactory::new()->create(['ano' => 2030, 'ref_ref_cod_serie' => $grade, 'ref_ref_cod_escola' => $schoolClass->ref_ref_cod_escola, 'ref_cod_curso' => $schoolClass->ref_cod_curso]);

    $score = LegacyRegistrationScoreFactory::new()->create(['matricula_id' => $registration]);
    LegacyDisciplineScoreFactory::new()->create(['nota_aluno_id' => $score, 'componente_curricular_id' => $from]);
    LegacyDisciplineScoreAverageFactory::new()->create(['nota_aluno_id' => $score, 'componente_curricular_id' => $from]);
    LegacyScoreExamFactory::new()->create(['ref_cod_matricula' => $registration, 'ref_cod_componente_curricular' => $from]);
    $opinion = LegacyStudentDescriptiveOpinionFactory::new()->create(['matricula_id' => $registration]);
    LegacyDisciplineDescriptiveOpinionFactory::new()->create(['parecer_aluno_id' => $opinion, 'componente_curricular_id' => $from]);
    $absence = LegacyStudentAbsenceFactory::new()->create(['matricula_id' => $registration]);
    LegacyDisciplineAbsenceFactory::new()->create(['falta_aluno_id' => $absence, 'componente_curricular_id' => $from]);
    LegacyDisciplineExemptionFactory::new()->create(['ref_cod_matricula' => $registration, 'ref_cod_disciplina' => $from]);
    LegacySchoolClassTeacherDisciplineFactory::new()->create([
        'professor_turma_id' => LegacySchoolClassTeacherFactory::new()->create(['turma_id' => $schoolClass, 'ano' => 2030]),
        'componente_curricular_id' => $from,
    ]);

    $filePath = ($this->createImportFile)([[$from->getKey(), $to->getKey(), $grade, 2030]]);

    try {
        $this->artisan('update:disciplines', ['filename' => $filePath])->assertExitCode(0);
    } finally {
        @unlink($filePath);
    }

    foreach ([
        ['modules.nota_componente_curricular', 'componente_curricular_id'],
        ['modules.nota_componente_curricular_media', 'componente_curricular_id'],
        ['modules.nota_exame', 'ref_cod_componente_curricular'],
        ['modules.parecer_componente_curricular', 'componente_curricular_id'],
        ['modules.falta_componente_curricular', 'componente_curricular_id'],
        ['pmieducar.dispensa_disciplina', 'ref_cod_disciplina'],
        ['modules.professor_turma_disciplina', 'componente_curricular_id'],
    ] as [$table, $column]) {
        $this->assertSame(0, DB::table($table)->where($column, $from->getKey())->count(), $table);
        $this->assertSame(1, DB::table($table)->where($column, $to->getKey())->count(), $table);
    }
});
