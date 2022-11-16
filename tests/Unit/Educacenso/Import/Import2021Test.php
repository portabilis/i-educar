<?php

namespace Tests\Unit\Educacenso\Import;

use App\Models\LegacySchool;
use App\Services\Educacenso\HandleFileService;
use App\Services\Educacenso\ImportServiceFactory;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Tests\EducacensoTestCase;

class Import2021Test extends EducacensoTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->year = 2021;
        $this->dateEnrollment = new Carbon('2021-01-01');

        $yearImportService = ImportServiceFactory::createImportService(
            $this->year,
            $this->dateEnrollment->format('d/m/Y')
        );

        $importFileService = new HandleFileService($yearImportService, $this->user);

        $importFileService->handleFile(new UploadedFile(
            resource_path('../tests/Unit/assets/importacao_educacenso_2021.txt'),
            'importacao_educacenso_2021.txt'
        ));
    }

    public function validationImportRegister00Especific()
    {
        $legacyAcademicYearStage = LegacySchool::first()->stages->first();
        $this->assertEquals($this->year . '-03-03', $legacyAcademicYearStage->data_inicio->format('Y-m-d'));
        $this->assertEquals($this->year . '-12-12', $legacyAcademicYearStage->data_fim->format('Y-m-d'));
    }
}
