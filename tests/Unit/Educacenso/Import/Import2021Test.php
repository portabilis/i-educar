<?php

namespace Tests\Unit\Educacenso\Import;

use App\Services\Educacenso\HandleFileService;
use App\Services\Educacenso\ImportServiceFactory;
use Illuminate\Http\UploadedFile;
use Tests\EducacensoTestCase;

class Import2021Test extends EducacensoTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->year = 2021;

        $yearImportService = ImportServiceFactory::createImportService(
            $this->year,
            '01/01/2021'
        );

        $importFileService = new HandleFileService($yearImportService, $this->user);

        $importFileService->handleFile(new UploadedFile(
            resource_path('../tests/Unit/assets/importacao_educacenso_2021.txt'),
            'importacao_educacenso_2021.txt'
        ));
    }
}
