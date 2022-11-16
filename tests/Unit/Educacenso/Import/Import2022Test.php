<?php

namespace Tests\Unit\Educacenso\Import;

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

    public function validationImportRegister00Especific()
    {
        $legacyAcademicYearStage = LegacySchool::first()->stages->first();
        $this->assertEquals($this->year . '-03-01', $legacyAcademicYearStage->data_inicio->format('Y-m-d'));
        $this->assertEquals($this->year . '-12-10', $legacyAcademicYearStage->data_fim->format('Y-m-d'));
    }
}
