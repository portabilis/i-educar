<?php

namespace Tests\Unit\Services;

use App\Jobs\FileExporterJob;
use App\Models\Enums\FileExportStatus;
use App\Models\FileRelation;
use App\Services\FileExportService;
use Database\Factories\FileExportFactory;
use Database\Factories\FileFactory;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyEvaluationRuleGradeYearFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyIndividualPictureFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyPersonFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolCourseFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Seeders\DefaultRelatorioSituacaoMatriculaTableSeeder;
use iEducar\Reports\Contracts\StudentRecordReport;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;
use ZipArchive;

class FileExportServiceTest extends TestCase
{
    use DatabaseTransactions;

    private array $args = [];

    private $export;

    private $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->export = FileExportFactory::new()->create();
        $count = DB::table('relatorio.situacao_matricula')->count();
        if ($count === 0) {
            $seed = new DefaultRelatorioSituacaoMatriculaTableSeeder;
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
        Storage::fake('local');
        Storage::disk('local')->put('student.jpg', 'content');
        $this->student = $student = LegacyStudentFactory::new()->create([
            'ref_idpes' => $person->idpes,
            'ativo' => 1,
            'url_documento' => json_encode([
                [
                    'data' => now()->format('d/m/Y'),
                    'url' => url(Storage::disk('local')->url('student.jpg')),
                ],
            ]),
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
        $this->args = [
            'year' => now()->year,
            'school' => $school->getKey(),
            'course' => $course->getKey(),
            'grade' => $grade->getKey(),
            'schoolClass' => $schoolClass->getKey(),
        ];
    }

    public function test_export_service(): void
    {
        Queue::fake();
        Queue::assertNothingPushed();
        Queue::assertNotPushed(FileExporterJob::class);
        FileExporterJob::dispatch($this->export, $this->args);
        Queue::assertPushed(FileExporterJob::class, 1);

        $zipPath = "{$this->export->getConnectionName()}/zip/{$this->export->hash}/{$this->export->filename}.zip";
        Storage::disk('local')->assertMissing($zipPath);
        $service = new FileExportService($this->export, $this->args, 'local');
        $service->setIssueStudentRecordReport(false);
        $service->execute();
        Storage::disk('local')->assertExists($zipPath);
    }

    private function zipPath(): string
    {
        return "{$this->export->getConnectionName()}/zip/{$this->export->hash}/{$this->export->filename}.zip";
    }

    private function zipEntries(): array
    {
        $zip = new ZipArchive;
        $zip->open(Storage::disk('local')->path($this->zipPath()));
        $entries = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entries[] = $zip->getNameIndex($i);
        }
        $zip->close();

        return $entries;
    }

    public function test_exporta_laudos_foto_e_a_ficha_do_aluno_e_ignora_documentos_sem_endereco(): void
    {
        Storage::disk('local')->put('laudo.pdf', 'laudo');
        Storage::disk('local')->put('foto.png', 'foto');
        $this->student->update(['url_documento' => json_encode([['data' => '01/01/2030', 'url' => '']])]);
        $this->student->medicalReports()->attach(FileFactory::new()->create(['url' => url(Storage::disk('local')->url('laudo.pdf'))])->getKey(), ['type' => FileRelation::TYPE_MEDICAL_REPORT]);
        LegacyIndividualPictureFactory::new()->create(['idpes' => $this->student->ref_idpes, 'caminho' => url(Storage::disk('local')->url('foto.png'))]);

        $this->app->instance(StudentRecordReport::class, new class implements StudentRecordReport
        {
            public array $args = [];

            public function dumps(array $options): string
            {
                return 'ficha em base64';
            }
        });

        $service = new FileExportService($this->export, $this->args, 'local');
        $service->setIssueStudentRecordReport(true);
        $service->execute();

        $folder = preg_replace('/[^\w\s]/u', '', mb_strtoupper($this->student->person->nome)) . " ({$this->student->getKey()})";

        $this->assertEqualsCanonicalizing([
            "{$folder}/Laudo-01.pdf",
            "{$folder}/Foto.png",
            "{$folder}/Ficha_do_aluno.pdf",
        ], $this->zipEntries());
        $this->assertSame(FileExportStatus::SUCCESS->value, $this->export->refresh()->status_id);
    }

    public function test_sem_alunos_exporta_um_arquivo_avisando(): void
    {
        $service = new FileExportService($this->export, ['year' => 1999] + $this->args, 'local');
        $service->setIssueStudentRecordReport(false);
        $service->execute();

        $this->assertSame(["{$this->export->filename}/nenhum_documento_exportado.txt"], $this->zipEntries());
    }

    public function test_o_job_roda_a_exportacao_e_marca_a_falha(): void
    {
        $job = new FileExporterJob($this->export, $this->args);

        $job->handle();
        Storage::disk('local')->assertExists($this->zipPath());
        $this->assertSame([$this->export->getConnectionName(), 'file-export'], $job->tags());

        $job->failed(new RuntimeException('boom'));
        $this->assertSame(FileExportStatus::ERROR->value, $this->export->refresh()->status_id);
    }
}
