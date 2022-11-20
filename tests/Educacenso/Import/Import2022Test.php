<?php

namespace Tests\Educacenso\Import;

use App\Models\LegacyEnrollment;
use App\Models\LegacySchool;
use App\Models\LegacySchoolClassTeacher;
use App\Services\Educacenso\HandleFileService;
use App\Services\Educacenso\ImportServiceFactory;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Tests\EducacensoTestCase;

class Import2022Test extends EducacensoTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        \Artisan::call('db:seed', ['--class' => 'DefaultCadastroDeficienciaTableSeeder']);

        $this->year = 2022;
        $this->dateEnrollment = new Carbon('2022-01-01');

        $yearImportService = ImportServiceFactory::createImportService(
            $this->year,
            $this->dateEnrollment->format('d/m/Y')
        );

        $importFileService = new HandleFileService($yearImportService, $this->user);

        $importFileService->handleFile(new UploadedFile(
            resource_path('../tests/Unit/assets/importacao_educacenso_2022.txt'),
            'importacao_educacenso_2022.txt'
        ));
    }

    /** @test */
    public function import2022Specific()
    {
        $legacySchool = LegacySchool::first();
        $this->assertEquals('{1}', $legacySchool->poder_publico_parceria_convenio);
        $this->assertEquals('{6}', $legacySchool->formas_contratacao_adm_publica_e_outras_instituicoes);

        foreach ($legacySchool->schoolClasses as $schoolClass) {
            $this->assertNotNull($schoolClass->estrutura_curricular);
            $this->assertEquals(1, $schoolClass->formas_organizacao_turma);

            if (str_contains('2', $schoolClass->estrutura_curricular)) {
                $this->assertEquals('{8}', $legacySchool->estrutura_curricular);
            }
        }

        $schoollClassTeachers = LegacySchoolClassTeacher::all();

        foreach ($schoollClassTeachers as $schoollClassTeacher) {
            if (str_contains('2', $schoollClassTeacher->schoolClass->estrutura_curricular)) {
                $this->assertEquals('{8}', $schoollClassTeacher->estrutura_curricular);
            }
        }
        $enrollments = LegacyEnrollment::all();

        foreach ($enrollments as $enrollment) {
            if (count($enrollment->tipo_itinerario) > 0) {
                $this->assertCount(2, $enrollment->tipo_itinerario);
                $this->assertNotNull($enrollment->composicao_itinerario);
                $this->assertCount(1, $enrollment->composicao_itinerario);
                $this->assertEquals(5, $enrollment->composicao_itinerario[0]);
                $this->assertIsBool($enrollment->itinerario_concomitante);
                $this->assertEquals(1, $enrollment->curso_itinerario);
            }
        }
    }
}
