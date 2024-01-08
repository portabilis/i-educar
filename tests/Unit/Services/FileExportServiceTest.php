<?php

namespace Tests\Unit\Services;

use App\Jobs\DatabaseToCsvExporter;
use App\Jobs\FileExporterJob;
use App\Services\Exporter\ExportService;
use App\Services\FileExportService;
use Carbon\Carbon;
use Database\Factories\FileExportFactory;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyEvaluationRuleGradeYearFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyPersonFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolCourseFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Seeders\DefaultRelatorioSituacaoMatriculaTableSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileExportServiceTest extends TestCase
{
    use DatabaseTransactions;

    private array $args = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->export = FileExportFactory::new()->create();

        $count = DB::table('relatorio.situacao_matricula')->count();
        if ($count === 0) {
            $seed = new DefaultRelatorioSituacaoMatriculaTableSeeder();
            $seed->run();
        }
        $institution = LegacyInstitutionFactory::new()->create();
        $school = LegacySchoolFactory::new()->create([
            'ref_cod_instituicao' => $institution->id,
        ]);
        $person = LegacyPersonFactory::new()->create();
        LegacyIndividualFactory::new()->create([
            'idpes' => $person->idpes,
            'ativo' => 1,
        ]);
        $student = LegacyStudentFactory::new()->create([
            'ref_idpes' => $person->idpes,
            'ativo' => 1,
        ]);
        $course = LegacyCourseFactory::new()->create([
            'ref_cod_instituicao' => $institution->id,
        ]);
        $grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course->id,
        ]);
        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $school->id,
            'ref_cod_curso' => $course->id,
        ]);
        LegacySchoolGradeFactory::new()->create([
            'ref_cod_escola' => $school->id,
            'ref_cod_serie' => $grade->id,
        ]);
        $registration = LegacyRegistrationFactory::new()->create([
            'ref_cod_aluno' => $student->cod_aluno,
            'ref_ref_cod_serie' => $grade->id,
            'ref_cod_curso' => $course->id,
            'ref_ref_cod_escola' => $school->id,
            'ativo' => 1,
        ]);
        LegacyEvaluationRuleGradeYearFactory::new()->create([
            'serie_id' => $grade->id,
            'ano_letivo' => now()->year,
        ]);
        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_serie' => $grade->id,
            'ref_cod_curso' => $course->id,
            'ref_ref_cod_escola' => $school->id,
            'ref_cod_instituicao' => $institution->id,
        ]);
        LegacyEnrollmentFactory::new()->active()->create([
            'ref_cod_matricula' => $registration->id,
            'ref_cod_turma' => $schoolClass->id,
        ]);

        $this->args =  [
            'year' => now()->year,
            'school' => $school->getKey(),
            'course' => $course->getKey(),
            'grade' => $grade->getKey(),
            'schoolClass' => $schoolClass->getKey()
        ];
    }

    public function testExportService(): void
    {
        Queue::fake();
        Queue::assertNothingPushed();
        Queue::assertNotPushed(FileExporterJob::class);
        FileExporterJob::dispatch($this->export, $this->args);
        Queue::assertPushed(FileExporterJob::class, 1);
    }
}
